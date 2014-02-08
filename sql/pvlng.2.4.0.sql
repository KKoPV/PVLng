--
-- v2.3.0 --> v2.4.0
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

REPLACE INTO `v_pvlng_babelkit` (`code_set`, `code_lang`, `code_code`, `code_desc`) VALUES
('app', 'de', 'DeleteReadingConfirm', 'Willst Du diesen Messert wirklich löschen?!'),
('app', 'en', 'DeleteReadingConfirm', 'Do you really want delete this reading value?!'),
('app', 'de', 'DeleteReading', 'Messwert löschen'),
('app', 'en', 'DeleteReading', 'Delete reading value'),
('app', 'en', 'Add', 'Add'),
('preset', 'de', '4m', '4 Monate'),
('preset', 'en', '4m', '4 Month'),
('preset', 'en', '7d', '7 Days'),
('preset', 'de', '1d', '1 Tag'),
('preset', 'de', '7d', '7 Tage'),
('preset', 'de', '14d', '14 Tage'),
('preset', 'en', '1d', '1 Day'),
('preset', 'en', '14d', '14 Days'),
('preset', 'de', '1q', '1 Quartal'),
('preset', 'de', '2q', '2 Quartale'),
('preset', 'en', '1q', '1 Quarter'),
('preset', 'en', '2q', '2 Quarters'),
('preset', 'de', '1y', '1 Jahr'),
('preset', 'en', '1y', '1 Year'),
('preset', 'de', '1m', '1 Monat'),
('preset', 'de', '2m', '2 Monate'),
('preset', 'en', '1m', '1 Month'),
('preset', 'en', '2m', '2 Months'),
('preset', 'de', '1w', '1 Woche'),
('preset', 'de', '2w', '2 Wochen'),
('preset', 'en', '1w', '1 Week'),
('preset', 'en', '2w', '2 Weeks'),
('preset', 'de', '4h', '4 Stunden'),
('preset', 'de', '6h', '6 Stunden'),
('preset', 'de', '8h', '8 Stunden'),
('preset', 'en', '1h', '1 Hour'),
('preset', 'en', '2h', '2 Hours'),
('preset', 'en', '4h', '4 Hours'),
('preset', 'en', '6h', '6 Hours'),
('preset', 'en', '8h', '8 Hours'),
('preset', 'en', '12h', '12 Hours'),
('preset', 'de', '1h', '1 Stunde'),
('preset', 'de', '2h', '2 Stunden'),
('preset', 'de', '12h', '12 Stunden'),
('preset', 'de', '60i', '60 Minuten'),
('preset', 'en', '60i', '60 Minutes'),
('preset', 'de', '2i', '2 Minuten'),
('preset', 'de', '5i', '5 Minuten'),
('preset', 'de', '20i', '20 Minuten'),
('preset', 'de', '30i', '30 Minuten'),
('preset', 'en', '2i', '2 Minutes'),
('preset', 'en', '5i', '5 Minutes'),
('preset', 'en', '10i', '10 Minutes'),
('preset', 'en', '20i', '20 Minutes'),
('preset', 'en', '30i', '30 Minutes'),
('preset', 'de', '10i', '10 Minuten'),
('preset', 'en', 'm', '::Months::'),
('preset', 'en', 'q', '::Quarters::'),
('preset', 'en', 'y', '::Years::'),
('preset', 'de', 'd', '::Tage::'),
('preset', 'de', 'h', '::Stunden::'),
('preset', 'de', 'i', '::Minuten::'),
('preset', 'de', 'q', '::Quartale::'),
('preset', 'de', 'w', '::Wochen::'),
('preset', 'en', 'd', '::Days::'),
('preset', 'en', 'h', '::Hours::'),
('preset', 'en', 'i', '::Minuten::'),
('preset', 'en', 'w', '::Weeks::'),
('preset', 'de', 'y', '::Jahre::'),
('preset', 'de', 'm', '::Monate::'),
('preset', 'en', '10y', 'Decade'),
('model', 'de', 'Daylight_irradiationHint', 'Wenn eine Kurve gezeichnet werden soll, muss hier die GUID eines Einstrahlungs-Sensors angegeben werden.\r\nDie Kurve wird dann anhand des Durchschnittes der Eintrahlungs-Maximalwerte der letzen 5 Tage errechnet.'),
('model', 'en', 'Daylight_irradiationHint', 'If a curve should displayed, a GUID of an irradiation sensor must provided.\r\nThe curve will then calulated by the average of the max. irradiation values of the last 5 days.'),
('model', 'de', 'Daylight_irradiation', 'Einstrahlungs-Kanal GUID'),
('model', 'en', 'Daylight_irradiation', 'Irradiation channel GUID'),
('model', 'en', 'Daylight_resolution', 'Display'),
('model', 'de', 'Daylight_resolution', 'Anzeige'),
('model', 'de', 'Daylight_resolutionHint', 'Anzeige als Sonnenaufgangs-/untergangs-Marker oder als Kurve über die Zeit'),
('model', 'en', 'Daylight_resolutionHint', 'Show as sunrise/sunset markers or as curve over time'),
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
