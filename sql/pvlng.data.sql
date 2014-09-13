-- ------------------------------------------------------
-- Initial channel data, demo view and demo dashboard
-- ------------------------------------------------------

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

INSERT INTO `pvlng_config` (`key`, `value`, `comment`, `type`) VALUES
('Currency', 'EUR', 'Costs currency', 'str'),
('CurrencyDecimals', 2, 'Costs currency decimals', 'num'),
('DoubleRead', 5, 'Detect double readings by timestamp -+ seconds, set 0 to disable', 'num'),
('LogInvalid', 0, 'Log invalid values', 'str');

INSERT INTO `pvlng_tree` (`id`, `lft`, `rgt`, `entity`) VALUES
(1, 1, 12, 1), (2, 2, 3, 2), (3, 4, 5, 3), (4, 6, 11, 4), (5, 7, 10, 5), (6, 8, 9, 2);

INSERT INTO `pvlng_view` (`name`, `public`, `data`) VALUES
('Demo', 1, '{\"2\":\"{\\\"v\\\":2,\\\"axis\\\":1,\\\"type\\\":\\\"spline\\\",\\\"style\\\":\\\"Solid\\\",\\\"width\\\":2,\\\"color\\\":\\\"#4572a7\\\",\\\"colorusediff\\\":1,\\\"colordiff\\\":\\\"#db843d\\\",\\\"consumption\\\":false,\\\"threshold\\\":20,\\\"min\\\":false,\\\"max\\\":false,\\\"last\\\":false,\\\"all\\\":false,\\\"time1\\\":\\\"00:00\\\",\\\"time2\\\":\\\"24:00\\\",\\\"legend\\\":true,\\\"position\\\":0}\",\"3\":\"{\\\"v\\\":2,\\\"axis\\\":2,\\\"type\\\":\\\"areaspline\\\",\\\"style\\\":\\\"Solid\\\",\\\"width\\\":2,\\\"color\\\":\\\"#89a54e\\\",\\\"colorusediff\\\":0,\\\"colordiff\\\":\\\"#404040\\\",\\\"consumption\\\":false,\\\"threshold\\\":0,\\\"min\\\":false,\\\"max\\\":false,\\\"last\\\":false,\\\"all\\\":false,\\\"time1\\\":\\\"00:00\\\",\\\"time2\\\":\\\"24:00\\\",\\\"legend\\\":true,\\\"position\\\":0}\",\"p\":\"1i\"}');

INSERT INTO `pvlng_dashboard` (`name`, `public`, `data`) VALUES
('Demo', 1, '[5]');

-- Fianally generate and show API key
SELECT `getAPIkey`() AS `Your PVLng API key:`;
