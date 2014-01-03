-- ------------------------------------------------------
-- Initial channel data and demo views
-- ------------------------------------------------------

INSERT INTO `pvlng_channel`
(`id`, `name`, `description`, `type`, `resolution`, `unit`, `decimals`, `meter`, `cost`, `threshold`, `valid_from`, `valid_to`, `comment`) VALUES
(1, 'DO NOT TOUCH', 'Dummy for tree root',    0, 0, '', 2, 0, 0, NULL, NULL, NULL, ''),
(2, 'RANDOM Temperature sensor', '15 ... 25, &plusmn;0.1', 10, 1, '°C', 1, 0, 0, 0.1, 15, 25, ''),
(3, 'RANDOM Energy meter', '0 ... &infin;, +0.05',   10, 1000, 'Wh', 0, 1, 0.0002, 0.05, 0, 10000000000, ''),
(4, 'Dashboard', 'Dashboard group', 5, 1, '', 2, 0, 0, NULL, NULL, NULL, ''),
(5, 'Temperature sensor', 'RANDOM Temperature sensor for Dashboard', 30, 1, '°C', 1, 0, 0, NULL, 0, 40, '> 10 : #BFB\n10 > 20 : #FFB\n20 > : #FBB');

INSERT INTO `pvlng_config` (`key`, `value`, `comment`, `type`) VALUES
('Currency', 'EUR', 'Costs currency', 'str'),
('CurrencyDecimals', 2, 'Costs currency decimals', 'num'),
('LogInvalid', 0, 'Log invalid values', 'str'),
('TimeStep', 60, 'Reading time step in seconds', 'num');

INSERT INTO `pvlng_tree` (`id`, `lft`, `rgt`, `entity`) VALUES
(1, 1, 12, 1), (2, 2, 3, 2), (3, 4, 5, 3), (4, 6, 11, 4), (5, 7, 10, 5), (6, 8, 9, 2);

INSERT INTO `pvlng_view` (`name`, `data`, `public`, `slug`) VALUES
('Demo', '{\"p\":\"\",\"2\":\"{\\\"axis\\\":1,\\\"type\\\":\\\"spline\\\",\\\"consumption\\\":false,\\\"style\\\":\\\"Solid\\\",\\\"width\\\":2,\\\"color\\\":\\\"#4572a7\\\",\\\"coloruseneg\\\":true,\\\"colorneg\\\":\\\"#db843d\\\",\\\"threshold\\\":20,\\\"min\\\":false,\\\"max\\\":false}\",\"3\":\"{\\\"axis\\\":2,\\\"type\\\":\\\"spline\\\",\\\"consumption\\\":false,\\\"style\\\":\\\"Solid\\\",\\\"width\\\":2,\\\"color\\\":\\\"#404040\\\",\\\"coloruseneg\\\":false,\\\"colorneg\\\":\\\"#404040\\\",\\\"threshold\\\":0,\\\"min\\\":false,\\\"max\\\":false}\"}', 0, 'demo');

INSERT INTO `pvlng_config` (`key`, `value`) VALUES
('dashboard', '[5]');

-- ------------------------------------------------------
-- Generate and show API key
-- ------------------------------------------------------

SELECT `getAPIkey`() AS `PVLng API key`;
