INSERT INTO `pvlng_settings` (`scope`, `name`, `key`, `value`, `order`, `description`, `type`)
VALUES ( 'core', '', 'KeepLogs', 365, 70, 'Hold entries in log table for ? days', 2);

DELIMITER ;;
ALTER EVENT `pvlng_gc` ON SCHEDULE EVERY '1' DAY STARTS '2000-01-01'
ON COMPLETION PRESERVE RENAME TO `pvlng_daily` ENABLE COMMENT 'Daily tasks' DO
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

    SELECT `value` INTO @keep FROM `pvlng_settings`
     WHERE `scope` = 'core' AND `name` = '' AND `key` = 'KeepLogs';

    -- Remove outdated log entries
    DELETE FROM `pvlng_log` WHERE UNIX_TIMESTAMP(`timestamp`) < UNIX_TIMESTAMP() - IFNULL(@keep, 365)*60*60*24;

END;;

CREATE EVENT `pvlng_weekly` ON SCHEDULE EVERY '1' WEEK STARTS '2000-01-02 23:00'
ON COMPLETION PRESERVE ENABLE COMMENT 'Weekly tasks' DO
BEGIN
    -- Optimze working tables
    OPTIMIZE TABLE `pvlng_reading_tmp`, `pvlng_reading_last`, `pvlng_log`;
END;;
DELIMITER ;

DELETE FROM `pvlng_settings` WHERE `scope` = 'controller' AND `name` = 'Index' AND `key` = 'NotifyEach';

UPDATE `pvlng_babelkit` SET `code_order` = `code_order`*10+20 WHERE `code_set` = 'period' AND `code_lang` = 'en';

INSERT INTO `pvlng_babelkit`
(`code_set`, `code_lang`, `code_code`, `code_desc`, `code_order`)
VALUES
('period', 'en', 's', 'Second',  10),
('period', 'de', 's', 'Sekunde', 0);

UPDATE `pvlng_babelkit` SET `code_order` = `code_order`+100 WHERE `code_set` = 'preset' AND `code_lang` = 'en';
UPDATE `pvlng_babelkit` SET `code_order` = 0 WHERE `code_set` = 'preset' AND `code_lang` = 'en' AND `code_code` = '--';

INSERT INTO `pvlng_babelkit` (`code_set`, `code_lang`, `code_code`, `code_desc`, `code_order`) VALUES
('preset', 'en', 's',   '::Seconds::', 100),
('preset', 'en', '10s', '10 Seconds', 110),
('preset', 'en', '15s', '15 Seconds', 115),
('preset', 'en', '30s', '30 Seconds', 130),
('preset', 'de', 's',   '::Sekunden::', 0),
('preset', 'de', '10s', '10 Sekunden', 0),
('preset', 'de', '15s', '15 Sekunden', 0),
('preset', 'de', '30s', '30 Sekunden', 0);
