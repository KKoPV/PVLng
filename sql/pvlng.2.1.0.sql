--
-- v2.0.0 --> v2.1.0
--

ALTER TABLE `pvlng_tree` DROP INDEX `lft`;
ALTER TABLE `pvlng_tree` ADD INDEX `lft_rgt` (`lft`, `rgt`);
ALTER TABLE `pvlng_tree` ADD INDEX `guid` (`guid`);

ALTER TABLE `pvlng_channel` DROP INDEX `Name-Description-Type`;

DROP TRIGGER `pvlng_reading_num_bi`;

DELIMITER ;;

CREATE TRIGGER `pvlng_reading_num_bi` BEFORE INSERT ON `pvlng_reading_num` FOR EACH ROW
IF new.`timestamp` = 0 THEN
  SET new.`timestamp` = UNIX_TIMESTAMP();
END IF;;

DELIMITER ;

DROP TRIGGER `pvlng_reading_str_bi`;

DELIMITER ;;

CREATE TRIGGER `pvlng_reading_str_bi` BEFORE INSERT ON `pvlng_reading_str` FOR EACH ROW
IF new.`timestamp` = 0 THEN
  SET new.`timestamp` = UNIX_TIMESTAMP();
END IF;;

DELIMITER ;

DROP PROCEDURE `getTimestamp`;

DELETE FROM `pvlng_config` WHERE `key` = 'TimeStep';

REPLACE INTO `pvlng_type` (`id`, `name`, `description`, `model`, `unit`, `childs`, `read`, `write`, `graph`, `icon`) VALUES
(0, 'Alias', '', 'Alias', '', 0, 0, 0, 1, '/images/ico/arrow_180.png'),
(1, 'Power plant', 'model::PowerPlant', 'Group', '', -1, 0, 0, 0, '/images/ico/building.png'),
(2, 'Inverter', 'model::Inverter', 'Group', '', -1, 0, 0, 0, '/images/ico/exclamation_frame.png'),
(3, 'Building', 'model::Building', 'Group', '', -1, 0, 0, 0, '/images/ico/home.png'),
(4, 'Multi-Sensor', 'model::MultiSensor', 'Group', '', -1, 0, 0, 0, '/images/ico/wooden_box.png'),
(5, 'Group', 'model::Group', 'Group', '', -1, 0, 0, 0, '/images/ico/folders_stack.png'),
(10, 'Random', 'model::Random', 'Random', '', 0, 1, 0, 1, '/images/ico/ghost.png'),
(11, 'Fixed value', 'model::Fix', 'Fix', '', 0, 1, 0, 1, '/images/ico/chart_arrow.png'),
(12, 'Estimate', 'model::Estimate', 'Estimate', 'Wh', 0, 1, 0, 1, '/images/ico/plug.png'),
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
(110, 'Sonnenertrag JSON', 'model::SonnenertragJSON', 'Sonnenertrag\\JSON', '', -1, 1, 0, 0, '/images/ico/sonnenertrag.png');

REPLACE INTO `pvlng_babelkit` (`code_set`, `code_lang`, `code_code`, `code_desc`) VALUES
('app', 'de', 'AdjustTemplateAfterwards', 'Korrigiere z.B. Dezimalstellen, Einheiten und Öffentlich-Kennzeichen im Nachgang.'),
('app', 'en', 'AdjustTemplateAfterwards', 'Adjust e.g. units, decimals and public settings afterwards.'),
('model', 'de', 'Topline', 'Erzeugt eine Oberlinie für Sensoren für den größten Wert im Zeitbereich'),
('model', 'en', 'Topline', 'Generates a top line for sensors for the highest value in time range'),
('app', 'de', 'AsChild', 'Als Kind-Kanal'),
('app', 'en', 'AsChild', 'As sub channel'),
('app', 'de', 'TopLevel', 'Auf oberster Ebene'),
('app', 'en', 'TopLevel', 'On top level'),
('app', 'de', 'Channel2Overview', 'Füge diesen neuen Kanal auch zur Übersicht hinzu'),
('app', 'en', 'Channel2Overview', 'Add this new channel also into overview'),
('model', 'de', 'Estimate_commentHint', 'Definiere die Erwartungswerte auf Monats- oder Tagesbasis.\r\n\r\nWenn nur Monatswerte zur Verfügung stehen (z.B. von [url=http://re.jrc.ec.europa.eu/pvgis/apps4/pvest.php]PVGIS[/url]) werden diese als Werte des 15. des Monats verwendet und die anderen Tageswerte linear interpoliert.\r\n[list][*]Monat: [font=courier]Monat:Wert[/font]\r\n[*]Tag: [font=courier]Monat-Tag:Wert[/font][/list]\r\nBeispiel für einen Januar, 4,5kWh pro Tag\r\n[list][*]Monat: [font=courier]1:4.5[/font]\r\n[*]Tag (1. Januar): [font=courier]01-01:4.5[/font][/list]'),
('model', 'en', 'Estimate_commentHint', 'Define your estimates on monthly or daily base.\r\n\r\nIf only monthly data exists (e.g from [url=http://re.jrc.ec.europa.eu/pvgis/apps4/pvest.php]PVGIS[/url]) the values will be used as of the 15th of the month and the other values will be linear interpolated to get daily values.\r\n[list][*]Monthly: [font=courier]month:value[/font]\r\n[*]Daily: [font=courier]month-day:value[/font][/list]\r\nExample for a january, 4.5kWh each day\r\n[list][*]Monthly: [font=courier]1:4.5[/font]\r\n[*]Daily (1st of january): [font=courier]01-01:4.5[/font][/list]');
