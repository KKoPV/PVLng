--
-- v2.8.* > v2.9.0
--

ALTER TABLE `pvlng_type` ADD INDEX `childs` (`childs`), ADD INDEX `read` (`read`), ADD INDEX `write` (`write`);

INSERT INTO `pvlng_type` (`id`, `name`, `description`, `model`, `unit`, `childs`, `read`, `write`, `graph`, `icon`) VALUES
(26, 'Meter to sensor', 'model::MeterToSensor', 'MeterToSensor', '', 1, 1, 0, 1, '/images/ico/calculator_scientific.png'),
(27, 'Full Accumulator', 'model::AccumulatorFull', 'AccumulatorFull', '', -1, 1, 0, 1, '/images/ico/calculator_scientific.png');

REPLACE INTO `pvlng_babelkit` (`code_set`, `code_lang`, `code_code`, `code_desc`, `code_order`) VALUES
('model', 'de', 'Accumulator', 'Summiert die Messwerte aller Sub-Kanäle für den gleichen Zeitpunkt und ignoriert alle Datensätze, wo mindestens ein Wert pro Zeitpunkt fehlt.', 0),
('model', 'en', 'Accumulator', 'Build the sum of readings of all child channels for same timestamp and ignores data sets, where at least one for a timestamp ist missing.', 0),
('model', 'de', 'AccumulatorFull', 'Summiert die Messwerte aller Sub-Kanäle für den gleichen Zeitpunkt, summiert die Werte auch, wenn ein Wert für einen Zeitpunkt fehlt.', 0),
('model', 'en', 'AccumulatorFull', 'Build the sum of readings of all child channels for same timestamp, works for all timestamps, also if one data set is missing.', 0),
('app', 'de', 'ClearSearch', 'Suchbegriff löschen', 0),
('app', 'en', 'ClearSearch', 'Clear search term', 0);
