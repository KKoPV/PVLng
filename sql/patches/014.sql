DROP TRIGGER `pvlng_reading_num_bi`;

DELIMITER ;;

CREATE TRIGGER `pvlng_reading_num_bi` BEFORE INSERT ON `pvlng_reading_num` FOR EACH ROW
BEGIN
    IF new.`timestamp` = 0 THEN
        SET new.`timestamp` = UNIX_TIMESTAMP();
    END IF;

    SELECT IFNULL(`value`,0) INTO @sec
      FROM `pvlng_settings`
     WHERE `scope` = 'model' AND `name` = '' AND `key` = 'DoubleRead';

    IF @sec > 0 THEN
        SELECT COUNT(*) INTO @cnt
          FROM `pvlng_reading_num`
         WHERE `id` = new.`id`
           --  Don't check timestamp itself, only the range arround it
           AND `timestamp` BETWEEN new.`timestamp`-@sec AND new.`timestamp`-1
           AND `timestamp` BETWEEN new.`timestamp`+1 AND new.`timestamp`+@sec;

        IF @cnt THEN
            -- Throw an error
            SIGNAL SQLSTATE '99999' SET MESSAGE_TEXT = 'DoubleRead';
        END IF;
    END IF;
END
;;

DELIMITER ;
