--
-- For development branch only!
--

ALTER TABLE `pvlng_channel` ADD `extra` text NOT NULL COMMENT 'Not visible field for models to store extra info' AFTER `public`;

CREATE OR REPLACE VIEW `pvlng_tree_view` AS
select `n`.`id` AS `id`,`n`.`entity` AS `entity`,if(`t`.`childs`,`n`.`guid`,`c`.`guid`) AS `guid`,if(`co`.`id`,`co`.`name`,`c`.`name`) AS `name`,if(`co`.`id`,`co`.`serial`,`c`.`serial`) AS `serial`,`c`.`channel` AS `channel`,if(`co`.`id`,`co`.`description`,`c`.`description`) AS `description`,if(`co`.`id`,`co`.`resolution`,`c`.`resolution`) AS `resolution`,if(`co`.`id`,`co`.`cost`,`c`.`cost`) AS `cost`,if(`co`.`id`,`co`.`meter`,`c`.`meter`) AS `meter`,if(`co`.`id`,`co`.`numeric`,`c`.`numeric`) AS `numeric`,if(`co`.`id`,`co`.`offset`,`c`.`offset`) AS `offset`,if(`co`.`id`,`co`.`adjust`,`c`.`adjust`) AS `adjust`,if(`co`.`id`,`co`.`unit`,`c`.`unit`) AS `unit`,if(`co`.`id`,`co`.`decimals`,`c`.`decimals`) AS `decimals`,if(`co`.`id`,`co`.`threshold`,`c`.`threshold`) AS `threshold`,if(`co`.`id`,`co`.`valid_from`,`c`.`valid_from`) AS `valid_from`,if(`co`.`id`,`co`.`valid_to`,`c`.`valid_to`) AS `valid_to`,if(`co`.`id`,`co`.`public`,`c`.`public`) AS `public`,c.extra,`c`.`comment` AS `comment`,`t`.`name` AS `type`,`t`.`model` AS `model`,`t`.`childs` AS `childs`,`t`.`read` AS `read`,`t`.`write` AS `write`,`t`.`graph` AS `graph`,`t`.`icon` AS `icon`,`ca`.`id` AS `alias`,`ta`.`id` AS `alias_of`,(((count(0) - 1) + (`n`.`lft` > 1)) + 1) AS `level`,round((((`n`.`rgt` - `n`.`lft`) - 1) / 2),0) AS `haschilds`,((((min(`p`.`rgt`) - `n`.`rgt`) - (`n`.`lft` > 1)) / 2) > 0) AS `lower`,((`n`.`lft` - max(`p`.`lft`)) > 1) AS `upper` from ((((((`pvlng_tree` `n` join `pvlng_tree` `p`) join `pvlng_channel` `c` on((`n`.`entity` = `c`.`id`))) join `pvlng_type` `t` on((`c`.`type` = `t`.`id`))) left join `pvlng_channel` `ca` on(((if(`t`.`childs`,`n`.`guid`,`c`.`guid`) = `ca`.`channel`) and (`ca`.`type` = 0)))) left join `pvlng_tree` `ta` on((`c`.`channel` = `ta`.`guid`))) left join `pvlng_channel` `co` on(((`ta`.`entity` = `co`.`id`) and (`c`.`type` = 0)))) where ((`n`.`lft` between `p`.`lft` and `p`.`rgt`) and ((`p`.`id` <> `n`.`id`) or (`n`.`lft` = 1))) group by `n`.`id` order by `n`.`lft`;

DROP TRIGGER `pvlng_reading_num_bi`;

DELIMITER ;;

CREATE TRIGGER `pvlng_reading_num_bi` BEFORE INSERT ON `pvlng_reading_num` FOR EACH ROW
IF new.`timestamp` = 0 THEN

  SET @NOW = UNIX_TIMESTAMP();
  SELECT IFNULL(`value`,0) INTO @VALUE FROM `pvlng_config` WHERE `key` = 'DoubleRead';

  IF @VALUE <> 0 THEN
    SELECT COUNT(*) INTO @FOUND FROM `pvlng_reading_num` WHERE `id` = new.`id` AND `timestamp` BETWEEN @NOW-@VALUE AND @NOW+@VALUE;
    IF @FOUND THEN
      SIGNAL SQLSTATE '99999' SET MESSAGE_TEXT = 'DoubleRead';
    END IF;
  END IF;

  SET new.`timestamp` = @NOW;

END IF;;

DELIMITER ;

DROP TRIGGER `pvlng_reading_str_bi`;

DELIMITER ;;

CREATE TRIGGER `pvlng_reading_num_bi` BEFORE INSERT ON `pvlng_reading_num` FOR EACH ROW
IF new.`timestamp` = 0 THEN

  SET @NOW = UNIX_TIMESTAMP();
  SELECT IFNULL(`value`,0) INTO @VALUE FROM `pvlng_config` WHERE `key` = 'DoubleRead';

  IF @VALUE <> 0 THEN
    SELECT COUNT(*) INTO @FOUND FROM `pvlng_reading_str` WHERE `id` = new.`id` AND `timestamp` BETWEEN @NOW-@VALUE AND @NOW+@VALUE;
    IF @FOUND THEN
      SIGNAL SQLSTATE '99999' SET MESSAGE_TEXT = 'DoubleRead';
    END IF;
  END IF;

  SET new.`timestamp` = @NOW;

END IF;;

DELIMITER ;

CREATE TABLE `pvlng_changes` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `table` enum('babelkit','channel','config','log','options','performance','performance_avg','reading_num','reading_str','tree','type','view') NOT NULL COMMENT 'Table name',
  `key` varchar(50) NOT NULL COMMENT 'Primary key value(s), for composed keys separated by "::"',
  `field` varchar(50) NOT NULL COMMENT 'Field name',
  `timestamp` int(10) unsigned NOT NULL COMMENT 'When was changed',
  `old` varchar(256) NOT NULL COMMENT 'Old value',
  `new` varchar(256) NOT NULL COMMENT 'New value',
  PRIMARY KEY (`id`),
  KEY `table` (`table`,`key`,`field`,`timestamp`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DELIMITER ;;

CREATE TRIGGER `pvlng_changes_bi` BEFORE INSERT ON `pvlng_changes` FOR EACH ROW
SET new.`timestamp` = UNIX_TIMESTAMP();;

CREATE TRIGGER `pvlng_channel_au` AFTER UPDATE ON `pvlng_channel` FOR EACH ROW
BEGIN
  IF new.`adjust` = 1 THEN
     CALL `pvlng_changed`('channel', new.`id`, 'offset', old.`offset`, new.`offset`);
  END IF;
END;;

CREATE PROCEDURE `pvlng_changed`(IN `in_table` varchar(50), IN `in_key` varchar(50), IN `in_field` varchar(50), IN `in_old` varchar(255), IN `in_new` varchar(255))
IF in_old <> in_new THEN
  INSERT INTO `pvlng_changes` (`table`, `key`, `field`, `old`, `new`) VALUES (in_table, in_key, in_field, in_old, in_new);
END IF;;

DELIMITER ;

INSERT INTO `pvlng_type` (`id`, `name`, `description`, `model`, `unit`, `childs`, `read`, `write`, `graph`, `icon`) VALUES
(0, 'Alias', '', 'Alias', '', 0, 0, 0, 1, '/images/ico/arrow_180.png'),
(1, 'Power plant', 'model::PowerPlant', 'Group', '', -1, 0, 0, 0, '/images/ico/building.png'),
(2, 'Inverter', 'model::Inverter', 'Group', '', -1, 0, 0, 0, '/images/ico/exclamation_frame.png'),
(3, 'Building', 'model::Building', 'Group', '', -1, 0, 0, 0, '/images/ico/home.png'),
(4, 'Multi-Sensor', 'model::MultiSensor', 'Group', '', -1, 0, 0, 0, '/images/ico/wooden_box.png'),
(5, 'Group', 'model::Group', 'Group', '', -1, 0, 0, 0, '/images/ico/folders_stack.png'),
(10, 'Random', 'model::Random', 'Random', '', 0, 1, 0, 1, '/images/ico/ghost.png'),
(11, 'Fixed value', 'model::Fix', 'Fix', '', 0, 1, 0, 1, '/images/ico/chart_arrow.png'),
(12, 'Estimate', 'model::Estimate', 'Estimate', 'Wh', 0, 1, 0, 1, '/images/ico/plug.png'),
(13, 'Daylight', 'model::Daylight', 'Daylight', '', 0, 1, 0, 1, '/images/ico/picture-sunset.png'),
(15, 'Ratio calculator', 'model::Ratio', 'Ratio', '%', 2, 1, 0, 1, '/images/ico/edit_percent.png'),
(16, 'Accumulator', 'model::Accumulator', 'Accumulator', '', -1, 1, 0, 1, '/images/ico/calculator_scientific.png'),
(17, 'Differentiator', 'model::Differentiator', 'Differentiator', '', -1, 1, 0, 1, '/images/ico/calculator_scientific.png'),
(18, 'Full Differentiator', 'model::DifferentiatorFull', 'DifferentiatorFull', '', -1, 1, 0, 1, '/images/ico/calculator_scientific.png'),
(19, 'Sensor to meter', 'model::SensorToMeter', 'SensorToMeter', '', 1, 1, 0, 1, '/images/ico/calculator_scientific.png'),
(20, 'Import / Export', 'model::ImportExport', 'InternalConsumption', '', 2, 1, 0, 1, '/images/ico/calculator_scientific.png'),
(21, 'Average', 'model::Average', 'Average', '', -1, 1, 0, 1, '/images/ico/calculator_scientific.png'),
(22, 'Calculator', 'model::Calculator', 'Calculator', '', 1, 1, 0, 1, '/images/ico/calculator_scientific.png'),
(23, 'History', 'model::History', 'History', '', 1, 1, 0, 1, '/images/ico/calculator_scientific.png'),
(24, 'Baseline', 'model::Baseline', 'Baseline', '', 1, 1, 0, 1, '/images/ico/calculator_scientific.png'),
(25, 'Topline', 'model::Topline', 'Topline', '', 1, 1, 0, 1, '/images/ico/calculator_scientific.png'),
(30, 'Dashboard channel', 'model::Dashboard', 'Dashboard', '', 1, 1, 0, 1, '/images/ico/dashboard.png'),
(40, 'SMA Sunny Webbox', 'model::SMAWebbox', 'SMA\\Webbox', '', -1, 0, 1, 0, '/images/ico/sma_webbox.png'),
(41, 'SMA Inverter', 'model::SMAInverter', 'SMA\\Webbox', '', -1, 0, 1, 0, '/images/ico/sma_inverter.png'),
(42, 'SMA Sensorbox', 'model::SMASensorbox', 'SMA\\Webbox', '', -1, 0, 1, 0, '/images/ico/sma_sensorbox.png'),
(43, 'Fronius Inverter', 'model::FroniusSolarNet', 'Fronius\\SolarNet', '', -1, 0, 1, 0, '/images/ico/fronius_inverter.png'),
(44, 'Fronius Sensorbox', 'model::FroniusSolarNet', 'Fronius\\SolarNet', '', -1, 0, 1, 0, '/images/ico/fronius_sensorbox.png'),
(50, 'Energy meter, absolute', 'model::EnergyMeter', 'Meter', 'Wh', 0, 1, 1, 1, '/images/ico/plug.png'),
(51, 'Power sensor', 'model::PowerSensor', 'Sensor', 'W', 0, 1, 1, 1, '/images/ico/plug.png'),
(52, 'Voltage sensor', 'model::Voltage', 'Sensor', 'V', 0, 1, 1, 1, '/images/ico/dashboard.png'),
(53, 'Current sensor', 'model::CurrentSensor', 'Sensor', 'A', 0, 1, 1, 1, '/images/ico/lightning.png'),
(54, 'Gas sensor', 'model::GasSensor', 'Sensor', 'm³/h', 0, 1, 1, 1, '/images/ico/fire.png'),
(55, 'Heat sensor', 'model::HeatSensor', 'Sensor', 'W', 0, 1, 1, 1, '/images/ico/fire_big.png'),
(56, 'Humidity sensor', 'model::Humidity', 'Sensor', '%', 0, 1, 1, 1, '/images/ico/weather_cloud.png'),
(57, 'Luminosity sensor', 'model::Luminosity', 'Sensor', 'lm', 0, 1, 1, 1, '/images/ico/light_bulb.png'),
(58, 'Pressure sensor', 'model::Pressure', 'Sensor', 'hPa', 0, 1, 1, 1, '/images/ico/umbrella.png'),
(59, 'Radiation sensor', 'model::RadiationSensor', 'Sensor', 'µSV', 0, 1, 1, 1, '/images/ico/radioactivity.png'),
(60, 'Temperature sensor', 'model::Temperature', 'Sensor', '°C', 0, 1, 1, 1, '/images/ico/thermometer.png'),
(61, 'Valve sensor', 'model::Valve', 'Sensor', '°', 0, 1, 1, 1, '/images/ico/wheel.png'),
(62, 'Water sensor', 'model::WaterSensor', 'Sensor', 'm³/h', 0, 1, 1, 1, '/images/ico/water.png'),
(63, 'Windspeed sensor', 'model::Windspeed', 'Sensor', 'm/s', 0, 1, 1, 1, '/images/ico/paper_plane.png'),
(64, 'Irradiation sensor', 'model::Irradiation', 'Sensor', 'W/m²', 0, 1, 1, 1, '/images/ico/brightness.png'),
(65, 'Timer', 'model::Timer', 'Meter', 'h', 0, 1, 1, 1, '/images/ico/clock.png'),
(66, 'Frequency sensor', 'model::FrequencySensor', 'Sensor', 'Hz', 0, 1, 1, 1, '/images/ico/dashboard.png'),
(67, 'Winddirection sensor', 'model::Winddirection', 'Sensor', '°', 0, 1, 1, 1, '/images/ico/wheel.png'),
(68, 'Rainfall sensor', 'model::RainfallSensor', 'Sensor', 'mm/h', 0, 1, 1, 1, '/images/ico/umbrella.png'),
(70, 'Gas meter', 'model::GasMeter', 'Meter', 'm³', 0, 1, 1, 1, '/images/ico/fire.png'),
(71, 'Radiation meter', 'model::RadiationMeter', 'Meter', 'µSV/h', 0, 1, 1, 1, '/images/ico/radioactivity.png'),
(72, 'Water meter', 'model::WaterMeter', 'Meter', 'm³', 0, 1, 1, 1, '/images/ico/water.png'),
(73, 'Rainfall meter', 'model::RainfallMeter', 'Meter', 'mm', 0, 1, 1, 1, '/images/ico/umbrella.png'),
(90, 'Power sensor counter', 'model::PowerCounter', 'Counter', 'W', 0, 1, 1, 1, '/images/ico/plug.png'),
(91, 'Switch', 'model::Switch', 'Switcher', '', 0, 1, 1, 1, '/images/ico/ui_check_boxes.png'),
(100, 'PV-Log Plant', 'model::PVLogPlant', 'PVLog\\Plant', '', -1, 1, 0, 0, '/images/ico/pv_log_sum.png'),
(101, 'PV-Log Inverter', 'model::PVLogInverter', 'PVLog\\Inverter', '', -1, 1, 0, 0, '/images/ico/pv_log.png'),
(102, 'PV-Log Plant (r2)', 'model::PVLogPlant2', 'PVLog2\\Plant', '', -1, 1, 0, 0, '/images/ico/pv_log_sum.png'),
(103, 'PV-Log Inverter (r2)', 'model::PVLogInverter2', 'PVLog2\\Inverter', '', -1, 0, 0, 0, '/images/ico/pv_log.png'),
(110, 'Sonnenertrag JSON', 'model::SonnenertragJSON', 'Sonnenertrag\\JSON', '', -1, 1, 0, 0, '/images/ico/sonnenertrag.png')
ON DUPLICATE KEY UPDATE `id` = VALUES(`id`), `name` = VALUES(`name`), `description` = VALUES(`description`), `model` = VALUES(`model`), `unit` = VALUES(`unit`), `childs` = VALUES(`childs`), `read` = VALUES(`read`), `write` = VALUES(`write`), `graph` = VALUES(`graph`), `icon` = VALUES(`icon`);

REPLACE INTO `pvlng_config` (`key`, `value`, `comment`, `type`) VALUES
('DoubleRead', 5, 'Detect double readings by timestamp -+ seconds, set 0 to disable', 2);

REPLACE INTO `pvlng_babelkit` (`code_set`, `code_lang`, `code_code`, `code_desc`) VALUES
('channel', 'de', 'estimatesHint', 'Definiere die Erwartungswerte in [b]kWh[/b] auf Monats- oder Tagesbasis.\r\n\r\nWenn nur Monatswerte zur Verfügung stehen (z.B. von [url=http://re.jrc.ec.europa.eu/pvgis/apps4/pvest.php]PVGIS[/url]) werden diese als Werte des 15. des Monats verwendet und die anderen Tageswerte linear interpoliert.\r\n[list][*]Monat: [font=courier]Monat:Wert[/font]\r\n[*]Tag: [font=courier]Monat-Tag:Wert[/font][/list]\r\nBeispiel für einen Januar, 4,5kWh pro Tag\r\n[list][*]Monat: [font=courier]1:4.5[/font]\r\n[*]Tag (1. Januar): [font=courier]01-01:4.5[/font][/list]'),
('channel', 'en', 'estimatesHint', 'Define your estimates in [b]kilo watt hours[/b] on monthly or daily base.\r\n\r\nIf only monthly data exists (e.g from [url=http://re.jrc.ec.europa.eu/pvgis/apps4/pvest.php]PVGIS[/url]) the values will be used as of the 15th of the month and the other values will be linear interpolated to get daily values.\r\n[list][*]Monthly: [font=courier]month:value[/font]\r\n[*]Daily: [font=courier]month-day:value[/font][/list]\r\nExample for a january, 4.5kWh each day\r\n[list][*]Monthly: [font=courier]1:4.5[/font]\r\n[*]Daily (1st of january): [font=courier]01-01:4.5[/font][/list]'),
('channel', 'de', 'estimates', 'Erwartungswerte'),
('channel', 'en', 'estimates', 'Estimates'),
('channel', 'de', 'latitude', 'Breitengrad'),
('channel', 'de', 'longitude', 'Längengrad'),
('channel', 'de', 'latitudeHint', 'Standort der Anlage\r\nStandard ist Norden, gib einen negativen Werte für Süden an'),
('channel', 'de', 'longitudeHint', 'Standort der Anlage\r\nStandard ist Osten, gib einen negativen Werte für Westen an'),
('channel', 'en', 'latitude', 'Latitude'),
('channel', 'en', 'longitude', 'Longitude'),
('channel', 'en', 'latitudeHint', 'Location of plant\r\nDefaults to North, pass in a negative value for South'),
('channel', 'en', 'longitudeHint', 'Location of plant\r\nDefaults to East, pass in a negative value for West'),
('app', 'de', 'CreateTreeWithoutReqest', 'Hier werden alle Kanäle und die gesamte Kanal-Hierarchie ohne weitere Nachfrage erstellt.'),
('app', 'en', 'CreateTreeWithoutReqest', 'This will create all channels and the whole channel hierarchy without further request.'),
('channel', 'de', 'ParamIsRequired', 'Wert erforderlich!'),
('channel', 'en', 'ParamIsRequired', 'Value required!'),
('model', 'de', 'FroniusSolarNet_channelHint', 'Equipment-Typ, definiert die unterstützten Kanal-Arten'),
('model', 'en', 'FroniusSolarNet_channelHint', 'Equipment type, defines the supported channels'),
('model', 'de', 'FroniusSolarNet_serial', 'Device Id'),
('model', 'de', 'FroniusSolarNet_channel', 'Typ'),
('model', 'en', 'FroniusSolarNet_serial', 'Device Id'),
('model', 'en', 'FroniusSolarNet_channel', 'Type'),
('model', 'en', 'FroniusSolarNet_serialHint', 'Inverter or SensorCard Id in Fronius Solar Net'),
('model', 'de', 'FroniusSolarNet_serialHint', 'Wechselrichter- oder SensorCard-Id im Fronius Solar Net'),
('model', 'en', 'FroniusSolarNet', 'Accept JSON data for a [url=http://www.fronius.com/cps/rde/xchg/SID-E3D1267B-7210CC3C/fronius_international/hs.xsl/83_318_ENG_HTML.htm]Fronius inverter[/url], either from a request of[tt]GetInverterRealtimeData.cgi[/tt] with [tt]Scope = Device[/tt] and [tt]DataCollection = CommonInverterData[/tt] or\r\n[tt]GetSensorRealtimeData.cgi[/tt] with [tt]Scope = Device[/tt] and [tt]DataCollection = NowSensorData[/tt]'),
('model', 'de', 'FroniusSolarNet', 'Akzeptiert JSON-Daten für einen [url=http://www.fronius.com/cps/rde/xchg/SID-E3D1267B-7210CC3C/fronius_international/hs.xsl/83_318_ENG_HTML.htm]Fronius Wechselrichter[/url] von einer Abfrage von\r\n[tt]GetInverterRealtimeData.cgi[/tt] mit [tt]Scope = Device[/tt] und [tt]DataCollection = CommonInverterData[/tt] oder\r\n[tt]GetSensorRealtimeData.cgi[/tt] mit [tt]Scope = Device[/tt] und [tt]DataCollection = NowSensorData[/tt]'),
('preset', 'de', '--', 'keine'),
('preset', 'en', '--', 'none'),
('app', 'de', 'MarkExtremes', 'Markiere Messwerte'),
('app', 'en', 'MarkExtremes', 'Mark reading values'),
('app', 'de', 'lastone', 'letzter'),
('app', 'en', 'lastone', 'last'),
('app', 'de', 'UseOwnConsolidation', 'Benutze einen eigenen Verdichtungzeitraum\r\n(Dieser wird aber nicht in den Varianten-Einstellungen gespeichert)'),
('app', 'en', 'UseOwnConsolidation', 'Use your own consolidation period\r\n(But this will not saved in variant settings)'),
('preset', 'de', '-', 'Verdichtung?'),
('preset', 'en', '-', 'Consolidation?'),
('preset', 'de', '2i', '2 Minuten'),
('preset', 'en', '2i', '2 Minutes'),
('preset', 'de', '2m', '2 Monate'),
('preset', 'de', '2q', '2 Quartale'),
('preset', 'de', '2w', '2 Wochen'),
('preset', 'de', '4h', '4 Stunden'),
('preset', 'de', '6h', '6 Stunden'),
('preset', 'de', '7d', '7 Tage'),
('preset', 'de', '8h', '8 Stunden'),
('preset', 'de', '20i', '20 Minuten'),
('preset', 'de', '30i', '30 Minuten'),
('preset', 'de', '60i', '60 Minuten'),
('preset', 'en', '2m', '2 Month'),
('preset', 'en', '2q', '2 Quarters'),
('preset', 'en', '2w', '2 Weeks'),
('preset', 'en', '4h', '4 Hours'),
('preset', 'en', '6h', '6 Hours'),
('preset', 'en', '7d', '7 Days'),
('preset', 'en', '8h', '8 Hours'),
('preset', 'en', '20i', '20 Minutes'),
('preset', 'en', '30i', '30 Minutes'),
('preset', 'en', '60i', '60 Minutes'),
('preset', 'de', '2h', '2 Stunden'),
('preset', 'de', '12h', '12 Stunden'),
('preset', 'de', '14d', '14 Tage'),
('preset', 'en', '2h', '2 Hours'),
('preset', 'en', '12h', '12 Hours'),
('preset', 'en', '14d', '14 Days'),
('preset', 'de', '1d', '1 Tag'),
('preset', 'de', '1h', '1 Stunde'),
('preset', 'de', '1m', '1 Monat'),
('preset', 'de', '1q', '1 Quartal'),
('preset', 'de', '1w', '1 Woche'),
('preset', 'de', '1y', '1 Jahr'),
('preset', 'de', '10y', 'Dekade'),
('preset', 'en', '1d', '1 Day'),
('preset', 'en', '1h', '1 Hour'),
('preset', 'en', '1m', '1 Month'),
('preset', 'en', '1q', '1 Quarter'),
('preset', 'en', '1w', '1 Week'),
('preset', 'en', '1y', '1 Year'),
('preset', 'en', '10y', 'Decade'),
('preset', 'de', '10i', '10 Minuten'),
('preset', 'en', '10i', '10 Minutes'),
('code_set', 'de', 'preset', 'Voreinstellung'),
('code_set', 'en', 'preset', 'Preset');
