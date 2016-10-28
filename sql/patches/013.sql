DROP FUNCTION `pvlng_guid`;

DELIMITER ;;

CREATE FUNCTION `pvlng_guid` () RETURNS char(39) CHARACTER SET 'utf8'
BEGIN
    SET @GUID = LOWER(MD5(UUID()));
    -- Build 8 blocks 4 chars each, devided by a hyphen
    RETURN CONCAT_WS( '-',
        SUBSTRING(@GUID, 1,4), SUBSTRING(@GUID, 5,4), SUBSTRING(@GUID, 9,4),
        SUBSTRING(@GUID,13,4), SUBSTRING(@GUID,17,4), SUBSTRING(@GUID,21,4),
        SUBSTRING(@GUID,25,4), SUBSTRING(@GUID,29,4)
    );
END;;

DELIMITER ;

DROP TRIGGER `pvlng_tree_bi`;

DELIMITER ;;

CREATE TRIGGER `pvlng_tree_bi` BEFORE INSERT ON `pvlng_tree` FOR EACH ROW
BEGIN
    SELECT `e`.`type`, `t`.`childs`
      INTO @TYPE, @CHILDS
      FROM `pvlng_channel` `e`
      JOIN `pvlng_type` `t` ON `e`.`type` = `t`.`id`
     WHERE `e`.`id` = new.`entity`;

     IF @TYPE = 0 OR @CHILDS != 0 THEN
         -- Aliases get always an own GUID
         SET new.`guid` = pvlng_guid();
     END IF;
END;;

DELIMITER ;

DROP TRIGGER `pvlng_tree_bd`;

DELIMITER ;;

CREATE TRIGGER `pvlng_tree_ad` AFTER DELETE ON `pvlng_tree` FOR EACH ROW
BEGIN
    -- Remove also alias channel
    SELECT `alias` INTO @ALIAS FROM `pvlng_tree_view` WHERE `id` = old.`id`;

    IF @ALIAS IS NOT NULL THEN
        DELETE FROM `pvlng_channel` WHERE `id` = @ALIAS;
    END IF;
END;;

DELIMITER ;

DROP TRIGGER `pvlng_channel_bi`;

DELIMITER ;;

CREATE TRIGGER `pvlng_channel_bi` BEFORE INSERT ON `pvlng_channel` FOR EACH ROW
BEGIN
    SELECT `childs`
      INTO @CHILDS FROM `pvlng_type`
     WHERE `id` = new.`type` LIMIT 1;

    IF @CHILDS = 0 THEN
        SET new.`guid` = pvlng_guid();
    END IF;
END;;

DELIMITER ;

DROP FUNCTION `GUID`;

DELIMITER ;;

CREATE FUNCTION `pvlng_api_key` () RETURNS varchar(36) CHARACTER SET 'utf8'
BEGIN
    SELECT `value` INTO @KEY FROM `pvlng_config` WHERE `key` = 'APIKey';
    IF @KEY IS NULL THEN
        SET @KEY = UUID();
        INSERT INTO `pvlng_config` (`key`, `value`, `comment`)
             VALUES ('APIKey', @KEY, 'API key for all PUT/POST/DELETE requests');
    END IF;
    RETURN @KEY;
END;;

DELIMITER ;

DROP FUNCTION `pvlng_APIkey`;
DROP FUNCTION `getAPIkey`;
DROP FUNCTION `pvlng_save_num`;

DROP FUNCTION `pvlng_save_data`;

DELIMITER ;;

CREATE FUNCTION `pvlng_save_data` (`in_id` int unsigned, `in_timestamp` int unsigned, `in_data` char(255))
RETURNS tinyint(1)
MODIFIES SQL DATA
BEGIN
    -- Return codes
    --  0 : Not inserted - double read
    -- -1 : Not inserted - outside valid range
    -- -2 : Not inserted - outside threshold
    --  1 : Data inserted

    IF in_timestamp = 0 THEN
        SET in_timestamp = UNIX_TIMESTAMP();
    END IF;

    -- Channel attributes
    SELECT `numeric`, `meter`, `offset`, `adjust`, IFNULL(`threshold`, 0), `valid_from`, `valid_to`
      INTO @numeric,  @meter,  @offset,  @adjust,  @threshold,             @valid_from,  @valid_to
      FROM `pvlng_channel`
     WHERE `id` = in_id;

    -- Double readings
    SELECT IFNULL(`value`,0) INTO @DoubleRead
      FROM `pvlng_settings`
     WHERE `scope` = 'model' AND `name` = '' AND `key` = 'DoubleRead';

    -- Tests for numeric channels only
    IF @numeric THEN

        IF @DoubleRead > 0 THEN
            SELECT COUNT(1)
              INTO @found
              FROM `pvlng_reading_num`
             WHERE `id` = in_id
               AND `timestamp` BETWEEN in_timestamp - @DoubleRead AND in_timestamp + @DoubleRead;
            IF @found THEN
                -- We got at least 1 row in time range, ignore
                RETURN 0;
            END IF;
        END IF;

        -- Make data numeric
        SET in_data = +in_data;

        -- Check valid range
        if (@valid_from IS NOT NULL AND in_data < @valid_from) OR
           (@valid_to   IS NOT NULL AND in_data > @valid_to) THEN
            -- Outside valid range
            RETURN -1;
        END IF;

        -- Check threshold range against average of last in_avg rows, at least 5!
        IF @meter = 0 AND @threshold > 0 THEN

            -- Use at 5 rows backwards
            SELECT AVG(`data`)
              INTO @avg
              FROM (SELECT `data`
                      FROM `pvlng_reading_num`
                     WHERE `id` = in_id
                     ORDER BY `timestamp` DESC
                     LIMIT 5) a;

            IF @avg IS NOT NULL AND
               (@avg < in_data - @threshold OR @avg > in_data + @threshold) THEN
                -- Outside threshold
                RETURN -2;
            END IF;
        END IF;

        -- Check meter channel adjustment
        IF @meter AND @adjust THEN

            -- Get last reading before this timestamp
            SELECT `data` INTO @last
              FROM `pvlng_reading_num`
             WHERE `id` = in_id AND `timestamp` < in_timestamp
             ORDER BY `timestamp` DESC
             LIMIT 1;

            if @last IS NOT NULL AND @last < in_data THEN

                -- Get last offset before timestamp, if exists
                SELECT IFNULL(`old`, 0)
                  INTO @offset_before
                  FROM `pvlng_changes`
                 WHERE `table` = 'channel' AND `key` = in_id AND `timestamp` < in_timestamp
                 ORDER BY `timestamp` DESC
                 LIMIT 1;

                -- Check, if this reading would adjust the offset
                IF @offset_before <> 0 THEN

                    SET @delta = @offset_before - @offset;

                    -- Reading must adjust the offset, update channel
                    UPDATE `pvlng_channel`
                       SET `offset` = `offset` + @delta
                     WHERE `id` = in_id;

                    -- Tramsform any further reading to reflect new offset
                    UPDATE `pvlng_reading_num`
                       SET `data` = `data` + @delta
                     WHERE `id` = in_id
                       AND `timestamp` > in_timestamp;

                    -- Adjust in_data before write
                    SET in_data = id_data + @delta;

                END IF;

            END IF;

        END IF;

        -- All fine, insert
        INSERT INTO `pvlng_reading_num` VALUES (in_id, in_timestamp, in_data);

    ELSE

        IF @DoubleRead > 0 THEN
            SELECT COUNT(`id`)
              INTO @found
              FROM `pvlng_reading_str`
             WHERE `id` = in_id AND `timestamp` BETWEEN in_timestamp - @DoubleRead AND in_timestamp + @DoubleRead;
            IF @found THEN
                -- We got at least 1 row in time range, ignore
                RETURN 0;
            END IF;
        END IF;

        -- All fine, insert
        INSERT INTO `pvlng_reading_str` VALUES (in_id, in_timestamp, in_data);

    END IF;

    RETURN 1;

END;;

ALTER EVENT `pvlng_daily`
ON SCHEDULE EVERY '1' DAY STARTS '2000-01-01 00:00:00' ON COMPLETION PRESERVE
ENABLE COMMENT 'Daily tasks' DO
BEGIN

    -- Remove outdated calculated rows
    DELETE FROM `pvlng_reading_tmp`
            -- Remove out-dated data older 1 day
     WHERE `created` BETWEEN 0 AND UNIX_TIMESTAMP()-86400
            -- Remove hanging calulations, 300 sec. must be enough...
        OR `created` < 0 AND -`created` < UNIX_TIMESTAMP()-300;

    -- Remove orphan calculated rows
    DELETE FROM `pvlng_reading_num_tmp` WHERE `id` NOT IN (SELECT `uid` FROM `pvlng_reading_tmp`);
    DELETE FROM `pvlng_reading_str_tmp` WHERE `id` NOT IN (SELECT `uid` FROM `pvlng_reading_tmp`);

    -- Purge log
    SELECT `value` INTO @keep FROM `pvlng_settings`
     WHERE `scope` = 'core' AND `name` = '' AND `key` = 'KeepLogs';

    DELETE FROM `pvlng_log` WHERE UNIX_TIMESTAMP(`timestamp`) < UNIX_TIMESTAMP() - IFNULL(@keep, 365)*60*60*24;

END;;

DELIMITER ;
