-- Speed up some look-ups
ALTER TABLE `pvlng_reading_num` ADD INDEX `id` (`id`);
ALTER TABLE `pvlng_reading_str` ADD INDEX `id` (`id`);

-- GUID is always 39 characters long
ALTER TABLE `pvlng_channel`
    CHANGE `guid` `guid` char(39) NULL COMMENT 'Unique GUID' AFTER `id`;

DROP TABLE IF EXISTS `pvlng_reading_tmp`;
CREATE TABLE `pvlng_reading_tmp` (
  `id` smallint(5) unsigned NOT NULL COMMENT 'pvlng_channel -> id',
  `start` int(10) unsigned NOT NULL COMMENT 'Generated for start .. end',
  `end` int(10) unsigned NOT NULL COMMENT 'Generated for start .. end',
  `lifetime` mediumint(8) unsigned NOT NULL COMMENT 'Lifetime of data',
  `uid` smallint(5) unsigned NOT NULL COMMENT 'Temporary data Id',
  `created` int(10) NOT NULL COMMENT 'Record created',
  PRIMARY KEY (`id`,`start`,`end`),
  UNIQUE KEY `uid` (`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Buffer and remember internal calculated data';

DROP TRIGGER `pvlng_reading_tmp_bi`;

DELIMITER ;;

CREATE FUNCTION `pvlng_APIkey`() RETURNS varchar(36) CHARSET utf8
BEGIN
    SELECT `value` INTO @KEY FROM `pvlng_config` WHERE `key` = 'APIKey';
    IF @KEY IS NULL THEN
        SET @KEY = UUID();
        INSERT INTO `pvlng_config`
                    (`key`, `value`, `comment`)
             VALUES ('APIKey', @KEY, 'API key for all PUT/POST/DELETE requests');
    END IF;
    RETURN @KEY;
END;;

CREATE FUNCTION `pvlng_bool`(`in_val` char(5)) RETURNS enum('0','1') CHARSET utf8
    NO SQL
BEGIN
    --
    -- Valid (not case-sensitive) values for TRUE (return as 1): 1,x,on,y,yes,true
    --
    SET in_val = LOWER(in_val);
    IF in_val = 1 OR in_val = 'x' OR in_val = 'on' OR
       in_val = 'yes' OR in_val = 'y' OR
       in_val = 'true' THEN
        RETURN 1;
    END IF;
    RETURN 0;
END;;

CREATE FUNCTION `pvlng_guid`() RETURNS char(39) CHARSET utf8
BEGIN
    SET @GUID = MD5(UUID());
    -- Build 8 blocks 4 chars each, devided by a hyphen
    RETURN CONCAT_WS( '-',
        SUBSTRING(@GUID, 1,4), SUBSTRING(@GUID, 5,4), SUBSTRING(@GUID, 9,4),
        SUBSTRING(@GUID,13,4), SUBSTRING(@GUID,17,4), SUBSTRING(@GUID,21,4),
        SUBSTRING(@GUID,25,4), SUBSTRING(@GUID,29,4)
    );
END;;

DROP FUNCTION IF EXISTS `pvlng_reading_tmp_start`;;
CREATE FUNCTION `pvlng_reading_tmp_start`(`in_id` smallint unsigned, `in_start` int unsigned, `in_end` int unsigned, `in_lifetime` mediumint unsigned) RETURNS smallint(6)
BEGIN
    -- Handler for "Duplicate entry '%s' for key %d"
    DECLARE EXIT HANDLER FOR 1062
    -- Insert failed, so check existing data
    -- created < 0 - other process is just creating the data,
    --               return 0 as "have to wait" marker
    -- created > 0 - return uid to mark correct data
    RETURN (
        SELECT IF(`created` < 0, 0, `uid`)
          FROM `pvlng_reading_tmp`
         WHERE `id` = in_id AND `start` = in_start AND `end` = in_end
    );

    DELETE FROM `pvlng_reading_tmp`
            -- Remove out-dated data older 1 day
     WHERE `created` BETWEEN 0 AND UNIX_TIMESTAMP()-86400
            -- Remove hanging calulations
        OR `created` < 0 AND -`created` < UNIX_TIMESTAMP()-`lifetime`
            -- Older than lifetime for this Id
        OR `id` = in_id AND `created` BETWEEN 0 AND UNIX_TIMESTAMP()-`lifetime`-1;

    SET @UID = 1 + FLOOR(RAND()*32766);

    -- Try to insert initial row
    INSERT INTO `pvlng_reading_tmp` VALUES ( in_id, in_start, in_end, in_lifetime, @UID, -UNIX_TIMESTAMP() );

    -- Insert succeeded, return neg. uid as marker to create data
    RETURN -@UID;
END;;

DROP FUNCTION IF EXISTS `pvlng_save_data`;;
CREATE FUNCTION `pvlng_save_data`(`in_id` int unsigned, `in_timestamp` int unsigned, `in_data` char(100)) RETURNS tinyint(1)
    MODIFIES SQL DATA
BEGIN
    -- Return codes
    --  0 : Not inserted - double read
    -- -1 : Not inserted - outside valid range
    -- -2 : Not inserted - outside threshold
    --  1 : Data inserted

    -- Channel attributes
    SELECT `numeric`, `meter`, `offset`, `adjust`, IFNULL(`threshold`, 0), `valid_from`, `valid_to`
      INTO @numeric,  @meter,  @offset,  @adjust,  @threshold,             @valid_from,  @valid_to
      FROM `pvlng_channel`
     WHERE `id` = in_id;

    -- Double readings
    SELECT IFNULL(`value`,0) INTO @doubleRead
      FROM `pvlng_settings`
     WHERE `scope` = 'model' AND `key` = 'DoubleRead';

    IF in_timestamp = 0 THEN
        SET in_timestamp = UNIX_TIMESTAMP();
    END IF;

    -- Tests for numeric channels only
    IF @numeric THEN

        IF @doubleRead > 0 THEN
            SELECT COUNT(`id`) INTO @FOUND
              FROM `pvlng_reading_num`
             WHERE `id` = in_id
               AND `timestamp` BETWEEN in_timestamp - @doubleRead AND in_timestamp + @doubleRead;
            IF @FOUND THEN
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

        -- Check threshold range against average of last in_avg rows, at least 3!
        IF @meter = 0 AND @threshold > 0 THEN

            -- Use at 3 rows backwards
            SELECT AVG(`data`) INTO @avg
              FROM (SELECT `data`
                      FROM `pvlng_reading_num`
                     WHERE `id` = in_id
                     ORDER BY `timestamp` DESC
                     LIMIT 3) a;

            IF @avg IS NOT NULL AND
               ( @avg < in_data-@threshold OR @avg > in_data+@threshold ) THEN
                -- Outside threshold
                RETURN -2;
            END IF;
        END IF;

        -- Check meter channel adjustment
        IF @meter AND @offset AND @adjust THEN

            -- Get last reading before this timestamp
            SELECT `data` INTO @last
              FROM `pvlng_reading_num`
             WHERE `id` = in_id
               AND `timestamp` < in_timestamp
             ORDER BY `timestamp` DESC
             LIMIT 1;

            if @last IS NOT NULL AND @last < in_data THEN

                -- Get last offset before timestamp, if exists
                SELECT IFNULL(`old`, 0) INTO @offset_before
                  FROM `pvlng_changes`
                 WHERE `table` = 'channel'
                   AND `key` = in_id
                   AND `timestamp` < in_timestamp
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

        IF @doubleRead > 0 THEN
            SELECT COUNT(`id`) INTO @FOUND
              FROM `pvlng_reading_str`
             WHERE `id` = in_id
               AND `timestamp` BETWEEN in_timestamp - @doubleRead AND in_timestamp + @doubleRead;
            IF @FOUND THEN
                -- We got at least 1 row in time range, ignore
                RETURN 0;
            END IF;
        END IF;

        -- All fine, insert
        INSERT INTO `pvlng_reading_str` VALUES (in_id, in_timestamp, in_data);

    END IF;

    RETURN 1;

END;;

DROP FUNCTION IF EXISTS `pvlng_timestamp`;;
CREATE FUNCTION `pvlng_timestamp`(`in_ms` char(1)) RETURNS bigint(13)
    NO SQL
BEGIN
    IF pvlng_bool(in_ms) = 0 THEN
        -- Seconds
        RETURN UNIX_TIMESTAMP();
    END IF;

    -- Micro seconds, http://stackoverflow.com/a/25889615
    RETURN (
        SELECT CONV(
                   CONCAT(SUBSTRING(uid,16,3), SUBSTRING(uid,10,4), SUBSTRING(uid,1,8)),
                   16, 10
               ) DIV 10000 - 141427 * 24 * 60 * 60 * 1000
          FROM (SELECT UUID() uid) t
    );
END;;

DROP PROCEDURE IF EXISTS `pvlng_reading_tmp_done`;;
CREATE PROCEDURE `pvlng_reading_tmp_done`(IN `in_uid` smallint unsigned)
BEGIN
    -- Mark entry done for further reads
    UPDATE `pvlng_reading_tmp`
       SET `created` = UNIX_TIMESTAMP()
     WHERE `uid` = in_uid;
END;;

DELIMITER ;
