-- ------------------------------------------------------
-- Initial channel data, demo views and demo dashboard
-- ------------------------------------------------------

/*!40101 SET NAMES utf8 */;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;

INSERT INTO `pvlng_channel`
(`id`, `name`, `description`, `type`, `resolution`, `unit`, `decimals`, `meter`, `cost`, `threshold`, `valid_from`, `valid_to`, `extra`) VALUES
(1, 'DO NOT TOUCH', 'Dummy for tree root', 0, 0, '', 2, 0, 0, NULL, NULL, NULL, ''),
(2, 'RANDOM Temperature sensor', '15 ... 25, &plusmn;0.1', 10, 1, '°C', 1, 0, 0, 0.1, 15, 25, ''),
(3, 'RANDOM Energy meter', '0 ... &infin;, +0.05', 10, 1000, 'Wh', 0, 1, 0.0002, 0.05, 0, 10000000000, ''),
(4, 'Dashboard', 'Dashboard group', 5, 1, '', 2, 0, 0, NULL, NULL, NULL, ''),
(5, 'Temperature sensor', 'RANDOM Temperature sensor for Dashboard', 30, 1, '°C', 1, 0, 0, NULL, 0, 40, '\"> 10 : #BFB\\n10 > 20 : #FFB\\n20 > : #FBB\"'),
(6, 'Calculations', 'Group for separation of real channels from calculations', 5, 1, '', 2, 0, 0, NULL, NULL, NULL, '');

-- Update icon column from type
UPDATE `pvlng_channel` c
   SET `icon` = (SELECT `icon` from `pvlng_type` WHERE `id` = c.`type`);

-- Update Dashboard channel icon from temperature sensor channel type
-- In the Web front end this will be done by adding the 1st child channel
UPDATE `pvlng_channel`
   SET `icon` = (SELECT `icon` from `pvlng_type` WHERE `id` = 10)
 WHERE `id` = 5;

INSERT INTO `pvlng_settings` (`scope`, `name`, `key`, `value`, `order`, `description`, `type`, `data`) VALUES
('core', '', 'Language', 'en', 10, 'Default language', 'option', 'en:English;de:Deutsch'),
('core', '', 'Title', 'PhotoVoltaic Logger new generation', 20, 'Your personal title (HTML allowed)', '', ''),
('core', '', 'SendStats', '1', 30, 'Send anonymous statistics', 'bool', ''),
('core', '', 'Latitude', '', 50, 'Location latitude<br /><small>Your geographic coordinate that specifies the north-south position (-90..90)</small>', 'num', ''),
('core', '', 'Longitude', '', 60, 'Location longitude<br /><small>Your geographic coordinate that specifies the east-west position (-180..180)</small>', 'num', ''),
('core', 'Currency', 'ISO', 'EUR', 80, 'ISO Code', 'str', ''),
('core', 'Currency', 'Symbol', '€', 81, 'Symbol', 'str', ''),
('core', 'Currency', 'Decimals', '2', 82, 'Decimals', 'num', ''),
('core', 'Currency', 'Format', '{} €', 83, 'Output format, <strong><tt>{}</tt></strong> will be replaced with value', 'str', ''),
('controller', 'Index', 'ChartHeight', '528', 10, 'Default chart height', 'num', ''),
('controller', 'Index', 'NotifyAll', '1', 30, 'Notify overall loading time for all channels', 'bool', ''),
('controller', 'Index', 'NotifyEach', '0', 40, 'Notify loading time for each channel', 'bool', ''),
('controller', 'Index', 'Refresh', '300', 20, 'Auto refresh chart each ? seconds, set 0 to disable', 'num', ''),
('controller', 'Mobile', 'ChartHeight', '320', 0, 'Default chart height', 'num', ''),
('controller', 'Tariff', 'TimesLines', '10', 0, 'Initial times lines for each taiff', 'num', ''),
('controller', 'Weather', 'APIkey', '', 0, 'Wunderground API key', '', ''),
('model', '', 'DoubleRead', '5', 0, 'Detect double readings by timestamp &plusmn;seconds<br /><small>(set 0 to disable)</small>', 'num', '0:geometric mean;1:arithmetic mean'),
('model', 'Daylight', 'Average', '0', 10, 'Calculation method for irradiation average', 'option', '0:geometric mean;1:arithmetic mean'),
('model', 'Daylight', 'CurveDays', '5', 20, 'Build average over the last ? days', 'num', ''),
('model', 'Daylight', 'SunriseIcon', '/images/sunrise.png', 30, 'Sunrise marker image', '', ''),
('model', 'Daylight', 'SunsetIcon', '/images/sunset.png', 40, 'Sunset marker image', '', ''),
('model', 'Daylight', 'ZenitIcon', '/images/zenit.png', 50, 'Sun zenit marker image', '', ''),
('model', 'Estimate', 'Marker', '/images/energy.png', 0, 'Marker image', '', ''),
('model', 'History', 'AverageDays', '5', 0, 'Build average over the last ? days', 'num', ''),
('model', 'InternalCalc', 'LifeTime', '60', 0, 'Buffer lifetime of calculated data in seconds<br /><small>(e.g. if your store most data each 5 minutes, set to 300 and so on)</small>', 'num', '');

INSERT INTO `pvlng_tree` (`id`, `lft`, `rgt`, `entity`) VALUES
(1, 1, 12, 1), (2, 2, 3, 2), (3, 4, 5, 3), (4, 6, 11, 4), (5, 7, 10, 5), (6, 8, 9, 2);

INSERT INTO `pvlng_view` (`name`, `public`, `data`, `slug`) VALUES
('Demo - Simpel Sensor and Meter', 1, '{\"2\":\"{\\\"v\\\":2,\\\"axis\\\":2,\\\"type\\\":\\\"areaspline\\\",\\\"style\\\":\\\"Solid\\\",\\\"width\\\":2,\\\"color\\\":\\\"#4572a7\\\",\\\"colorusediff\\\":0,\\\"colordiff\\\":\\\"#404040\\\",\\\"consumption\\\":false,\\\"threshold\\\":0,\\\"min\\\":false,\\\"max\\\":false,\\\"last\\\":true,\\\"all\\\":false,\\\"time1\\\":\\\"00:00\\\",\\\"time2\\\":\\\"24:00\\\",\\\"daylight\\\":false,\\\"daylight_grace\\\":0,\\\"legend\\\":true,\\\"position\\\":0,\\\"hidden\\\":false,\\\"outline\\\":false}\",\"3\":\"{\\\"v\\\":2,\\\"axis\\\":1,\\\"type\\\":\\\"spline\\\",\\\"style\\\":\\\"Solid\\\",\\\"width\\\":2,\\\"color\\\":\\\"#89a54e\\\",\\\"colorusediff\\\":-1,\\\"colordiff\\\":\\\"#db843d\\\",\\\"consumption\\\":false,\\\"threshold\\\":20,\\\"min\\\":true,\\\"max\\\":true,\\\"last\\\":false,\\\"all\\\":false,\\\"time1\\\":\\\"00:00\\\",\\\"time2\\\":\\\"24:00\\\",\\\"daylight\\\":false,\\\"daylight_grace\\\":0,\\\"legend\\\":true,\\\"position\\\":1,\\\"hidden\\\":false,\\\"outline\\\":true}\",\"p\":\"5i\"}', 'demo-1-simply-sensor-and-meter'),
('Demo - Sensor with min/max', 1, '{\"2\":\"{\\\"v\\\":2,\\\"axis\\\":2,\\\"type\\\":\\\"areasplinerange\\\",\\\"style\\\":\\\"Solid\\\",\\\"width\\\":2,\\\"color\\\":\\\"#4572a7\\\",\\\"colorusediff\\\":0,\\\"colordiff\\\":\\\"#404040\\\",\\\"consumption\\\":false,\\\"threshold\\\":0,\\\"min\\\":false,\\\"max\\\":false,\\\"last\\\":true,\\\"all\\\":false,\\\"time1\\\":\\\"00:00\\\",\\\"time2\\\":\\\"24:00\\\",\\\"daylight\\\":false,\\\"daylight_grace\\\":0,\\\"legend\\\":true,\\\"position\\\":0,\\\"hidden\\\":false,\\\"outline\\\":false}\",\"p\":\"20i\"}', 'demo-2-sensor-with-min-max'),
('Demo - Daily values of Meter', 1, '{\"3\":\"{\\\"v\\\":2,\\\"axis\\\":1,\\\"type\\\":\\\"bar\\\",\\\"style\\\":\\\"Solid\\\",\\\"width\\\":2,\\\"color\\\":\\\"#89a54e\\\",\\\"colorusediff\\\":0,\\\"colordiff\\\":\\\"#000\\\",\\\"consumption\\\":true,\\\"threshold\\\":20,\\\"min\\\":false,\\\"max\\\":true,\\\"last\\\":false,\\\"all\\\":false,\\\"time1\\\":\\\"00:00\\\",\\\"time2\\\":\\\"24:00\\\",\\\"daylight\\\":false,\\\"daylight_grace\\\":0,\\\"legend\\\":true,\\\"position\\\":1,\\\"hidden\\\":false,\\\"outline\\\":true}\",\"p\":\"1d\"}', 'demo-daily-values-of-meter');

INSERT INTO `pvlng_dashboard` (`name`, `public`, `data`) VALUES ('Temperatur', 1, '[5]');

-- Fianally generate and show API key
SELECT `getAPIkey`() AS `Your PVLng API key:`;
