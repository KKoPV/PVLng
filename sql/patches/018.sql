INSERT INTO `pvlng_type` (`id`, `name`, `description`, `model`, `unit`, `type`, `childs`, `read`, `write`, `graph`, `icon`, `obsolete`)
VALUES (96, 'Dark Sky', 'model::DarkSky', 'DarkSky', '', 'group', -1, 0, 1, 0, '/images/ico/DarkSky.png', 0);

ALTER TABLE `pvlng_settings`
CHANGE `type` `type` enum('str','short','num','bool','option') COLLATE 'utf8_general_ci' NOT NULL DEFAULT 'str' AFTER `order`;

UPDATE `pvlng_settings` SET `type` = 'short' WHERE `scope` = 'core' AND `name` = '' AND `key` = 'Latitude';
UPDATE `pvlng_settings` SET `type` = 'short' WHERE `scope` = 'core' AND `name` = '' AND `key` = 'Longitude';
UPDATE `pvlng_settings` SET `type` = 'short' WHERE `scope` = 'core' AND `name` = 'Currency' AND `key` = 'Format';
UPDATE `pvlng_settings` SET `type` = 'short' WHERE `scope` = 'core' AND `name` = 'Currency' AND `key` = 'ISO';
UPDATE `pvlng_settings` SET `type` = 'short' WHERE `scope` = 'core' AND `name` = 'Currency' AND `key` = 'Symbol';
UPDATE `pvlng_settings` SET `type` = 'short' WHERE `scope` = 'controller' AND `name` = 'Index' AND `key` = 'PresetPeriods';
UPDATE `pvlng_settings` SET `type` = 'short' WHERE `scope` = 'controller' AND `name` = 'Lists' AND `key` = 'PresetPeriods';

CREATE FUNCTION `pvlng_mysql_version` () RETURNS varchar(127) RETURN CONCAT(@@version, " ", @@version_comment);
