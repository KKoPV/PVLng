--
-- For development branch only!
--

ALTER TABLE `pvlng_babelkit` CHANGE `code_code` `code_code` varchar(50) NOT NULL AFTER `code_lang`;

ALTER TABLE `pvlng_view` ADD INDEX `public` (`public`);

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

-- ------------------------------------------------
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

--
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
  `type` enum('','num','bool','option') NOT NULL,
  `data` varchar(255) NOT NULL,
  PRIMARY KEY (`scope`,`name`,`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 PACK_KEYS=1 COMMENT='Application settings';

INSERT INTO `pvlng_settings` (`scope`, `name`, `key`, `value`, `order`, `description`, `type`, `data`) VALUES
('core', '', 'Cookie', 'PVLng', 40, 'Session cookie name', '', ''),
('core', '', 'Language', 'en', 10, 'Default language', 'option', 'en:English;de:Deutsch'),
('core', '', 'SendStats', '1', 30, 'Send anonymous statistics', 'bool', ''),
('core', '', 'Title', 'PhotoVoltaic Logger new generation', 20, 'Your personal title', '', ''),
('core', '', 'Latitude', '51.5486', 50, 'Location latitude<br /><small>Your geographic coordinate that specifies the north-south position (-90..90)</small>', 'num', ''),
('core', '', 'Longitude', '12.1333', 60, 'Location longitude<br /><small>Your geographic coordinate that specifies the east-west position (-180..180)</small>', 'num', ''),
('controller', 'Index', 'ChartHeight', '528', 10, 'Default chart height', 'num', ''),
('controller', 'Index', 'NotifyAll', '1', 30, 'Notify overall loading time for all channels', 'bool', ''),
('controller', 'Index', 'NotifyEach', '0', 40, 'Notify loading time for each channel', 'bool', ''),
('controller', 'Index', 'Refresh', '300', 20, 'Auto refresh chart each ? seconds, set 0 to disable', 'num', ''),
('controller', 'Mobile', 'ChartHeight', '320', 0, 'Default chart height', 'num', ''),
('controller', 'Tariff', 'TimesLines', '10', 0, 'Initial times lines for each taiff', 'num', ''),
('controller', 'Weather', 'APIkey', '', 0, 'Wunderground API key', '', ''),
('model', 'Daylight', 'Average', '0', 10, 'Calculation method for irradiation average', 'option', '0:geometric mean;1:arithmetic mean'),
('model', 'Daylight', 'CurveDays', '5', 20, 'Build average over the last ? days', 'num', ''),
('model', 'Daylight', 'SunriseIcon', '/images/sunrise.png', 30, 'Sunrise marker image', '', ''),
('model', 'Daylight', 'SunsetIcon', '/images/sunset.png', 40, 'Sunset marker image', '', ''),
('model', 'Daylight', 'ZenitIcon', '/images/zenit.png', 50, 'Sun zenit marker image', '', ''),
('model', 'Estimate', 'Marker', '/images/energy.png', 0, 'Marker image', '', ''),
('model', 'History', 'AverageDays', '5', 0, 'Build average over the last ? days', 'num', '');

CREATE VIEW `pvlng_settings_keys` AS select concat(`pvlng_settings`.`scope`,if((`pvlng_settings`.`name` <> ''),concat('.',`pvlng_settings`.`name`),''),'.',`pvlng_settings`.`key`) AS `key`,`pvlng_settings`.`value` AS `value` from `pvlng_settings`;

CREATE TABLE `pvlng_reading_tmp` (
  `id` smallint(5) unsigned NOT NULL COMMENT 'pvlng_channel -> id',
  `start` int(10) unsigned NOT NULL COMMENT 'Generated for start .. end',
  `end` int(10) unsigned NOT NULL COMMENT 'Generated for start .. end',
  `created` int(10) unsigned NOT NULL COMMENT 'Record created',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Buffer and remember internal calculated data';

INSERT INTO `pvlng_reading_tmp` (`id`, `start`, `end`, `created`) VALUES
(40,	1413064800,	1413151200,	1413142806),
(77,	1413064800,	1413151200,	1413142812),
(213,	1413064800,	1413151200,	1413141754);

DELIMITER ;;

CREATE TRIGGER `pvlng_reading_tmp_ad` AFTER DELETE ON `pvlng_reading_tmp` FOR EACH ROW
BEGIN
  DELETE FROM `pvlng_reading_num_tmp`WHERE `id` = old.`id`;
  DELETE FROM `pvlng_reading_str_tmp`WHERE `id` = old.`id`;
END;;

CREATE FUNCTION `pvlng_reading_tmp`(`in_id` smallint unsigned, `in_start` int unsigned, `in_end` int unsigned, `in_lifetime` mediumint unsigned, `in_mode` tinyint(1) unsigned) RETURNS int(10) unsigned
BEGIN
    -- Insert failed, so check existing data
    -- start = 0 - other process is just creating the data, return 1 as "have to wait" marker
    -- not valid start end range - return 0 to recreate data
    DECLARE EXIT HANDLER FOR 1062 -- Duplicate entry '%s' for key %d
    RETURN (
        SELECT IFNULL(IF(`start` = 0, 1, `created`), 0) FROM `pvlng_reading_tmp`
        WHERE `id`= in_id AND `start` <> 0 AND `start` <= in_start AND `end` >= in_end
    );

    IF in_mode = 0 THEN
        -- Remove out-data and hanging data
        DELETE FROM `pvlng_reading_tmp`
         WHERE `id`= in_id  AND `created` + in_lifetime < UNIX_TIMESTAMP();

        -- Try to insert marker
        INSERT INTO `pvlng_reading_tmp` (`id`, `created`) VALUES ( in_id, UNIX_TIMESTAMP() );
        -- Insert succeeded, caller is the one to create the data
        RETURN 0;
    ELSE
        UPDATE `pvlng_reading_tmp`
           SET `start` = in_start, `end` = in_end, `created` = UNIX_TIMESTAMP()
         WHERE `id` = in_id;
        RETURN 0;
    END IF;
END;;

DELIMITER ;

ALTER TABLE `pvlng_reading_num` ADD INDEX `timestamp` (`timestamp`);
ALTER TABLE `pvlng_reading_str` ADD INDEX `timestamp` (`timestamp`);
