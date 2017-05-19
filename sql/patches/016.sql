ALTER TABLE `pvlng_tree`
CHANGE `lft` `lft` smallint(5) NOT NULL DEFAULT '0' AFTER `id`,
CHANGE `rgt` `rgt` smallint(5) NOT NULL DEFAULT '0' AFTER `lft`;

INSERT INTO `pvlng_type` (`id`, `name`, `description`, `model`, `unit`, `type`, `childs`, `read`, `write`, `graph`, `icon`)
VALUES (95, 'LaCrosse WS2300', 'model::WS2300', 'WS2300', '', 'group', -1, 0, 1, 0,	'/images/ico/ws2300.png');

DROP PROCEDURE `pvlng_model_baseline`;
DELIMITER ;;
CREATE PROCEDURE `pvlng_model_baseline` (IN `in_uid` smallint unsigned, IN `in_child` smallint unsigned)
BEGIN
  DECLARE iStart INT;
  DECLARE iEnd INT;
  DECLARE iTimestampMin INT;
  DECLARE iTimestampMax INT;
  DECLARE fData DECIMAL(13,4);

  SELECT start, `end`
    INTO iStart, iEnd
    FROM pvlng_reading_tmp
   WHERE uid = in_uid;

  SELECT MIN(`timestamp`), MAX(`timestamp`), MIN(data)
    INTO iTimestampMin,    iTimestampMax,     fData
    FROM pvlng_reading_num
   WHERE id = in_child AND timestamp BETWEEN iStart AND iEnd;

  IF NOT ISNULL(fData) THEN
    INSERT INTO pvlng_reading_num_tmp
         VALUES (in_uid, iTimestampMin, fData),
                (in_uid, iTimestampMax-1, fData);
  END IF;
END;;
DELIMITER ;

DROP PROCEDURE `pvlng_model_averageline`;
DELIMITER ;;
CREATE PROCEDURE `pvlng_model_averageline` (IN `in_uid` smallint unsigned, IN `in_child` smallint unsigned, IN `in_p` tinyint)
BEGIN
    -- Calulated with the Hölder mean fomulas
    -- http://en.wikipedia.org/wiki/H%C3%B6lder_mean
    -- in_p ==  1 - arithmetic
    -- in_p == -1 - harmonic

    DECLARE iStart INT;
    DECLARE iEnd INT;
    DECLARE iCount INT DEFAULT 0;
    DECLARE fSum DECIMAL(13,4) DEFAULT 0;
    DECLARE fData DECIMAL(13,4);
    DECLARE fAvg DECIMAL(13,4);
    DECLARE bDone INT DEFAULT 0;

    DECLARE curs CURSOR FOR
     SELECT data
       FROM pvlng_reading_num
      WHERE id = in_child
        AND timestamp BETWEEN iStart AND iEnd;

    DECLARE CONTINUE HANDLER FOR NOT FOUND SET bDone = 1;

    SELECT start, `end`
      INTO iStart, iEnd
      FROM pvlng_reading_tmp
     WHERE uid = in_uid;

    OPEN curs;

    REPEAT

        FETCH curs INTO fData;

        IF bDone = 0 THEN
            SET iCount = iCount + 1;
            SET fSum = fSum + POW(fData, in_p);
        END IF;

    UNTIL bDone END REPEAT;

    CLOSE curs;

    if iCount > 0 THEN

        SET fAvg = POW(fSum / iCount, 1 / in_p);

        -- Get real min. and max. timestamps in selection range
        SELECT MIN(timestamp), MAX(timestamp)
          INTO iStart, iEnd
          FROM pvlng_reading_num
         WHERE id = in_child
           AND timestamp BETWEEN iStart AND iEnd;

        INSERT INTO pvlng_reading_num_tmp
             VALUES (in_uid, iStart, fAvg), (in_uid, iEnd-1, fAvg);

    END IF;

END;;

DELIMITER ;

DELIMITER ;;
CREATE EVENT `pvlng_bulk_insert` ON SCHEDULE EVERY '15' SECOND STARTS '2000-01-01 00:00:00' ON COMPLETION PRESERVE ENABLE DO
BEGIN

    START TRANSACTION;

    INSERT INTO `pvlng_reading_num`
    SELECT `id`, `timestamp`, `data`
      FROM `pvlng_reading_buffer`
     WHERE `numeric` = 1
        ON DUPLICATE KEY UPDATE `data` = VALUES(data);

    INSERT INTO `pvlng_reading_str`
    SELECT `id`, `timestamp`, `data`
      FROM `pvlng_reading_buffer`
     WHERE `numeric` = 0
        ON DUPLICATE KEY UPDATE `data` = VALUES(data);

    TRUNCATE `pvlng_reading_buffer`;

    COMMIT;

END;;
DELIMITER ;

DROP FUNCTION `pvlng_reading_tmp_start`;

DELIMITER ;;
CREATE FUNCTION `pvlng_reading_tmp_start` (`in_id` smallint unsigned, `in_start` int unsigned, `in_end` int unsigned, `in_lifetime` mediumint unsigned) RETURNS smallint(6)
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
             WHERE `id`    = in_id
               AND `start` = in_start
               AND `end`   = in_end
        );

    -- Remove outdated records, older than lifetime for this Id and time range
    DELETE FROM `pvlng_reading_tmp`
     WHERE `id`    = in_id
       AND `start` = in_start
       AND `end`   = in_end
       AND `created` BETWEEN 0 AND UNIX_TIMESTAMP() - LEAST(in_lifetime, `lifetime`) - 1;

    SET @UID = 1 + FLOOR(RAND()*32766);

    -- Try to insert initial row
    INSERT INTO `pvlng_reading_tmp`
         VALUES (in_id, in_start, in_end, in_lifetime, @UID, -UNIX_TIMESTAMP());

    -- Insert succeeded, return neg. uid as marker to create data
    RETURN -@UID;
END;;
DELIMITER ;

INSERT INTO `pvlng_settings` (`scope`, `name`, `key`, `order`, `type`) VALUES ('core', 'API', 'Domain', 10, 1)
