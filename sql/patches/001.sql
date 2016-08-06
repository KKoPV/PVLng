ALTER TABLE `pvlng_config` DROP `type`;

ALTER TABLE `pvlng_babelkit` CHANGE `code_code` `code_code` varchar(50) NOT NULL AFTER `code_lang`;

ALTER TABLE `pvlng_view` ADD INDEX `public` (`public`);

ALTER TABLE `pvlng_reading_num` ADD INDEX `timestamp` (`timestamp`);
ALTER TABLE `pvlng_reading_str` ADD INDEX `timestamp` (`timestamp`);

UPDATE `pvlng_type` SET `childs` = -1 WHERE `id` = 29;
UPDATE `pvlng_type` SET `icon` = '/images/ico/fronius.png' WHERE `id` = 43 OR `id` = 44;
UPDATE `pvlng_type` SET `name` = 'Energy meter absolute' WHERE `id` = 50;

INSERT INTO `pvlng_type` (`id`, `name`, `description`, `model`, `unit`, `type`, `childs`, `read`, `write`, `graph`, `icon`) VALUES
(6, 'Inverter string', 'model::Group', 'Channel', '', 'group', -1, 0, 0, 0, '/images/ico/solar-panel.png'),
(7, 'Solar Edge Plant', 'model::SolarEdgeInverter', 'SE\\Inverter', '', 'group', -1, 0, 1, 0, '/images/ico/solar_edge.png'),
(69, 'Sensor', 'model::Sensor', 'Channel', '', 'sensor', 0, 1, 1, 1, '/images/ico/system-monitor.png'),
(74, 'Meter', 'model::Meter', 'Channel', '', 'meter', 0, 1, 1, 1, '/images/ico/chart-up.png');

CREATE OR REPLACE VIEW `pvlng_type_icons` AS
select `pvlng_type`.`icon` AS `icon`,group_concat(`pvlng_type`.`name` order by `pvlng_type`.`name` ASC separator ',') AS `name` from `pvlng_type` where (`pvlng_type`.`id` <> 0) group by `pvlng_type`.`icon` order by group_concat(`pvlng_type`.`name` order by `pvlng_type`.`name` ASC separator ',');

-- Aliases must get their own GUIDs in hierarchy

DROP TRIGGER `pvlng_tree_bi`;
DELIMITER ;;
CREATE TRIGGER `pvlng_tree_bi` BEFORE INSERT ON `pvlng_tree` FOR EACH ROW
BEGIN
  SELECT `e`.`type`, `t`.`childs`, `t`.`read`+`t`.`write`
    INTO @TYPE, @CHILDS, @RW
    FROM `pvlng_channel` `e`
    JOIN `pvlng_type` `t` ON `e`.`type` = `t`.`id`
   WHERE `e`.`id` = new.`entity`;
   -- Aliases get always an own GUID
   IF @TYPE = 0 OR (@CHILDS != 0 AND @RW > 0) THEN
     SET new.`guid` = GUID();
   END IF;
END;;
DELIMITER ;

UPDATE `pvlng_tree` h, `pvlng_channel` c
   SET h.`guid` = GUID()
 WHERE h.`entity` = c.`id` and c.`type` = 0 AND h.`guid` IS NULL;

-- ------------------------------------------------

DELIMITER ;;
CREATE FUNCTION `pvlng_id` () RETURNS int
BEGIN
  SELECT `value` INTO @ID FROM `pvlng_config` WHERE `key` = 'Installation';
  IF @ID IS NULL THEN
    -- Range of 100000 .. 999999
    SELECT 100000 + ROUND(RAND()*900000) INTO @ID;
    INSERT INTO `pvlng_config` (`key`, `value`, `comment`, `type`)
       VALUES ('Installation', @ID, 'Unique PVLng installation Id', 'num');
  END IF;
  RETURN @ID;
END;;
DELIMITER ;

CREATE OR REPLACE VIEW `pvlng_performance_view` AS
select `aggregation`,`action`,unix_timestamp(concat(`year`,'-',`month`,'-',`day`,' ',`hour`)) AS `timestamp`,`average` from `pvlng_performance_avg`;

CREATE TABLE `pvlng_settings` (
  `scope` enum('core','controller','model') NOT NULL,
  `name` varchar(100) NOT NULL,
  `key` varchar(100) NOT NULL,
  `value` varchar(100) NOT NULL,
  `order` tinyint(3) unsigned NOT NULL,
  `description` varchar(1000) NOT NULL,
  `type` enum('str','num','bool','option') NOT NULL,
  `data` varchar(255) NOT NULL,
  PRIMARY KEY (`scope`,`name`,`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 PACK_KEYS=1 COMMENT='Application settings';

INSERT INTO `pvlng_settings` (`scope`, `name`, `key`, `value`, `order`, `description`, `type`, `data`) VALUES
('core', '', 'Language', 'en', 10, 'Default language', 'option', 'en:English;de:Deutsch'),
('core', '', 'Latitude', '', 50, 'Location latitude<br /><small>Your geographic coordinate that specifies the north-south position (-90..90)</small>', 'num', ''),
('core', '', 'Longitude', '', 60, 'Location longitude<br /><small>Your geographic coordinate that specifies the east-west position (-180..180)</small>', 'num', ''),
('core', '', 'SendStats', '1', 30, 'Send anonymous statistics', 'bool', ''),
('core', '', 'Title', 'PhotoVoltaic Logger new generation', 20, 'Your personal title (HTML allowed)', 'str', ''),
('controller', 'Index', 'ChartHeight', '528', 10, 'Default chart height', 'num', ''),
('controller', 'Index', 'NotifyAll', '1', 30, 'Notify overall loading time for all channels', 'bool', ''),
('controller', 'Index', 'NotifyEach', '0', 40, 'Notify loading time for each channel', 'bool', ''),
('controller', 'Index', 'Refresh', '300', 20, 'Auto refresh chart each ? seconds, set 0 to disable', 'num', ''),
('controller', 'Mobile', 'ChartHeight', '320', 0, 'Default chart height', 'num', ''),
('controller', 'Tariff', 'TimesLines', '10', 0, 'Initial times lines for each taiff', 'num', ''),
('controller', 'Weather', 'APIkey', '', 0, 'Wunderground API key', 'str', ''),
('model', '', 'DoubleRead', '5', 0, 'Detect double readings by timestamp &plusmn;seconds<br /><small>(set 0 to disable)</small>', 'num', '0:geometric mean;1:arithmetic mean'),
('model', 'Daylight', 'Average', '0', 10, 'Calculation method for irradiation average', 'option', '0:geometric mean;1:arithmetic mean'),
('model', 'Daylight', 'CurveDays', '5', 20, 'Build average over the last ? days', 'num', ''),
('model', 'Daylight', 'SunriseIcon', '/images/sunrise.png', 30, 'Sunrise marker image', 'str', ''),
('model', 'Daylight', 'SunsetIcon', '/images/sunset.png', 40, 'Sunset marker image', 'str', ''),
('model', 'Daylight', 'ZenitIcon', '/images/zenit.png', 50, 'Sun zenit marker image', 'str', ''),
('model', 'Estimate', 'Marker', '/images/energy.png', 0, 'Marker image', 'str', ''),
('model', 'History', 'AverageDays', '5', 0, 'Build average over the last ? days', 'num', ''),
('model', 'InternalCalc', 'LifeTime', '60', 0, 'Buffer lifetime of calculated data in seconds<br /><small>(e.g. if your store most data each 5 minutes, set to 300 and so on)</small>', 'num', '');

CREATE VIEW `pvlng_settings_keys` AS select concat(`pvlng_settings`.`scope`,if((`pvlng_settings`.`name` <> ''),concat('.',`pvlng_settings`.`name`),''),'.',`pvlng_settings`.`key`) AS `key`,`pvlng_settings`.`value` AS `value` from `pvlng_settings`;

-- #######################################################################################

CREATE TABLE `pvlng_reading_tmp` (
  `id` smallint(5) unsigned NOT NULL COMMENT 'pvlng_channel -> id',
  `start` int(10) unsigned NOT NULL COMMENT 'Generated for start .. end',
  `end` int(10) unsigned NOT NULL COMMENT 'Generated for start .. end',
  `uid` smallint(5) unsigned NOT NULL COMMENT 'Tempory data Id',
  `created` int(10) unsigned NOT NULL COMMENT 'Record created',
  `lifetime` mediumint(8) unsigned NOT NULL COMMENT 'Lifetime of data',
  PRIMARY KEY (`id`,`start`,`end`),
  UNIQUE KEY `uid` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Buffer and remember internal calculated data';

DELIMITER ;;

CREATE TRIGGER `pvlng_reading_tmp_bi` BEFORE INSERT ON `pvlng_reading_tmp` FOR EACH ROW
SET new.`uid` = 1 + FLOOR(RAND()*32766);;

CREATE TRIGGER `pvlng_reading_tmp_ad` AFTER DELETE ON `pvlng_reading_tmp` FOR EACH ROW
BEGIN
  DELETE FROM `pvlng_reading_num_tmp`WHERE `id` = old.`uid`;
  DELETE FROM `pvlng_reading_str_tmp`WHERE `id` = old.`uid`;
END;;

CREATE FUNCTION `pvlng_reading_tmp_start`(`in_id` smallint unsigned, `in_start` int unsigned, `in_end` int unsigned, `in_lifetime` mediumint unsigned) RETURNS smallint(6)
BEGIN
    -- Insert failed, so check existing data
    -- created = 0 - other process is just creating the data,
    --               return 0 as "have to wait" marker
    -- created > 0 - return uid to mark correct data
    DECLARE EXIT HANDLER FOR 1062 -- Duplicate entry '%s' for key %d
    RETURN (
        SELECT IF(`created` = 0, 0, `uid`)
          FROM `pvlng_reading_tmp`
         WHERE `id` = in_id AND `start` = in_start AND `end` = in_end
    );

    -- Mark out-dated data, set invalid timestamps, remove them later in pvlng_reading_tmp_done
    UPDATE `pvlng_reading_tmp`
       SET `start` = `start`+1, `end` = `end`+1
     WHERE `id` = in_id AND `created` BETWEEN 1 AND UNIX_TIMESTAMP()-`lifetime`
        OR `created` BETWEEN 1 AND UNIX_TIMESTAMP()-86400;

    -- Try to insert initial row
    INSERT INTO `pvlng_reading_tmp` ( `id`, `start`, `end`, `lifetime` )
         VALUES ( in_id, in_start, in_end, in_lifetime );

    -- Insert succeeded, return neg. uid as marker to create data
    RETURN (
        SELECT -`uid` FROM `pvlng_reading_tmp`
         WHERE `id` = in_id AND `start` = in_start AND `end` = in_end
    );
END;;

CREATE PROCEDURE `pvlng_reading_tmp_done`(IN `in_uid` smallint unsigned)
BEGIN
    -- Mark as done
    UPDATE `pvlng_reading_tmp` SET `created` = UNIX_TIMESTAMP() WHERE `uid` = in_uid;

    -- Read original channel Id
    SELECT DISTINCT `id` INTO @ID FROM `pvlng_reading_tmp` WHERE `uid` = in_uid;

    -- Remove out-dated data for this Id or older 1 day, NOT DELETE created == 0 :-)
    DELETE FROM `pvlng_reading_tmp`
     WHERE `id` = @ID AND `created` BETWEEN 1 AND UNIX_TIMESTAMP()-`lifetime`
        OR `created` BETWEEN 1 AND UNIX_TIMESTAMP()-86400;
END;;

DELIMITER ;
