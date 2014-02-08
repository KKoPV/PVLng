--
-- For development branch only!
--

CREATE VIEW `pvlng_reading_count` AS select `pvlng_reading_num`.`id` AS `id`,max(`pvlng_reading_num`.`timestamp`) AS `timestamp`,count(`pvlng_reading_num`.`id`) AS `readings` from `pvlng_reading_num` group by `pvlng_reading_num`.`id` union select `pvlng_reading_str`.`id` AS `id`,max(`pvlng_reading_str`.`timestamp`) AS `MAX(``timestamp``)`,count(`pvlng_reading_str`.`id`) AS `COUNT(id)` from `pvlng_reading_str` group by `pvlng_reading_str`.`id`;
CREATE VIEW `pvlng_reading_statistics` AS select `c`.`guid` AS `guid`,`c`.`name` AS `name`,`c`.`description` AS `description`,`c`.`serial` AS `serial`,`c`.`channel` AS `channel`,`c`.`unit` AS `unit`,`t`.`name` AS `type`,`t`.`icon` AS `icon`,from_unixtime(`u`.`timestamp`) AS `datetime`,ifnull(`u`.`readings`,0) AS `readings` from ((`pvlng_channel` `c` join `pvlng_type` `t` on((`c`.`type` = `t`.`id`))) left join `pvlng_reading_count` `u` on((`c`.`id` = `u`.`id`))) where ((`t`.`childs` = 0) and `t`.`write`); -- 0.037 s

ALTER TABLE `pvlng_channel` DROP FOREIGN KEY `pvlng_channel_ibfk_2`;
ALTER TABLE `pvlng_tree` DROP FOREIGN KEY `pvlng_tree_ibfk_2`;

ALTER TABLE `pvlng_tree`
CHANGE `id` `id` smallint unsigned NOT NULL AUTO_INCREMENT,
CHANGE `lft` `lft` smallint unsigned NOT NULL,
CHANGE `rgt` `rgt` smallint unsigned NOT NULL,
CHANGE `entity` `entity` smallint unsigned NOT NULL COMMENT 'pvlng_channel -> id';

ALTER TABLE `pvlng_channel`
CHANGE `id` `id` smallint unsigned NOT NULL AUTO_INCREMENT,
CHANGE `type` `type` smallint unsigned NOT NULL COMMENT 'pvlng_type -> id';

ALTER TABLE `pvlng_tree` ADD FOREIGN KEY (`entity`) REFERENCES `pvlng_channel` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE `pvlng_type` CHANGE `id` `id` smallint unsigned NOT NULL;


ALTER TABLE `pvlng_reading_num` CHANGE `id` `id` smallint unsigned NOT NULL;
ALTER TABLE `pvlng_reading_str` CHANGE `id` `id` smallint unsigned NOT NULL;

ALTER TABLE `pvlng_channel` ADD FOREIGN KEY (`type`) REFERENCES `pvlng_type` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE;


INSERT INTO `pvlng_type` (`id`, `name`, `description`, `model`, `unit`, `childs`, `read`, `write`, `graph`, `icon`) VALUES
(45, 'OpenWeatherMap', 'model::OpenWeatherMap', 'JSON', '', -1, 0, 1, 0, '/images/ico/OpenWeatherMap.png');


DELETE FROM `pvlng_babelkit`
 WHERE (`code_set` = 'preset' AND `code_code` = '-')
    OR (`code_set` = 'model'  AND `code_code` = 'DaylightHelp');

REPLACE INTO `pvlng_babelkit` (`code_set`, `code_lang`, `code_code`, `code_desc`) VALUES
('model', 'de', 'EstimateHelp', 'Sollte als \"Scatter\" (Zielmarke) im Diagramm angezeigt werden'),
('model', 'en', 'EstimateHelp', 'Should be shown as \"Scatter\" (target marker) in chart'),
('code_set', 'de', 'preset', 'Verdichtung'),
('code_set', 'en', 'preset', 'Consolidation'),
('app', 'en', 'InfoHint', 'Background information'),
('app', 'en', 'OverviewHint', 'Overview of your equipments and relationship'),
('app', 'en', 'DashboardHint', 'Quick overview with gauges'),
('app', 'en', 'PlantDescriptionHint', 'Description of installation'),
('app', 'de', 'InfoHint', 'Hintergrundinformationen'),
('app', 'de', 'ChartHint', 'Anzeigen der Kanal-Diagramme'),
('app', 'de', 'ChannelsHint', 'Übersicht über alle definierten Kanäle'),
('app', 'de', 'OverviewHint', 'Übersicht über Deine Geräte und deren Hirarchie'),
('app', 'de', 'DashboardHint', 'Schnellübersicht mit Gauges'),
('app', 'de', 'PlantDescriptionHint', 'Beschreibung der Installation'),
('app', 'en', 'ChartHint', 'Display channel charts'),
('app', 'en', 'ChannelsHint', 'Overview of all defined channels'),
('app', 'de', 'ChartTodayHint', 'Setzt beide Datumsfelder auf heute und lädt das Diagramm neu'),
('app', 'en', 'ChartTodayHint', 'Set both date fields to today and reload chart'),
('preset', 'de', '--', '--- keine ---'),
('preset', 'en', '--', '--- none ---');
