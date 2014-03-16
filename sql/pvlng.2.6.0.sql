--
-- v2.5.0 -> v2.6.0
--

ALTER TABLE `pvlng_performance` DROP `id`;

-- Update mobile views to new logic
-- Remove leading @ from name, set public to 2 and change slug
UPDATE `pvlng_view`
   SET `name` = SUBSTR(`name`, 2), `public` = 2, `slug` = CONCAT(SUBSTR(`slug`, 2), '-mobile')
 WHERE SUBSTR(`name`, 1, 1) = '@' AND `public`= 1;

ALTER TABLE `pvlng_view`
    CHANGE `public` `public` tinyint(1) unsigned NOT NULL COMMENT 'Public view' AFTER `name`,
    CHANGE `data` `data` text COLLATE 'utf8_general_ci' NOT NULL COMMENT 'Serialized channel data' AFTER `public`;

ALTER TABLE `pvlng_view`
    ADD PRIMARY KEY `name_public` (`name`, `public`),
    DROP INDEX `PRIMARY`;

DELETE FROM `pvlng_babelkit` WHERE `code_set` = 'preset' AND `code_code` = '60i';

REPLACE INTO `pvlng_type` (`id`, `name`, `description`, `model`, `unit`, `childs`, `read`, `write`, `graph`, `icon`)
VALUES (46, 'Wunderground', 'model::Wunderground', 'JSON', '', -1, 0, 1, 0, '/images/ico/Wunderground.png');

DELETE FROM `pvlng_babelkit` WHERE `code_code` LIKE 'Dashboard_comment%';

REPLACE INTO `pvlng_babelkit` (`code_set`, `code_lang`, `code_code`, `code_desc`, `code_order`) VALUES
('app', 'de', 'publicHint', '- Öffentliche Diagramme sind von nicht eingeloggten Besuchern anzeigbar.\r\n- Diagramme für Mobilgeräte sind für nicht eingeloggte Besucher nur im Mobilmodus sichtbar, private Kanäle werden dabei nicht angezeigt.', 0),
('app', 'en', 'publicHint', '- Public charts are accessible by not logged in visitors.\r\n- Mobile charts are only visible for not logged in users in mobile mode, private channels will be suppressed.', 0),
('app', 'de', 'of', 'von', 0),
('app', 'en', 'of', 'of', 0),
('app', 'de', 'Page', 'Seite', 0),
('app', 'en', 'Page', 'Page', 0),
('app', 'de', 'private', 'privat', 0),
('app', 'en', 'private', 'private', 0),
('app', 'de', 'MobileChart', 'für Mobilgeräte', 0),
('app', 'en', 'MobileChart', 'chart for mobiles', 0),
('app', 'de', 'PublicChart', 'öffentliches Diagramm', 0),
('app', 'en', 'PublicChart', 'public chart', 0),
('app', 'de', 'PrivateChart', 'nicht-öffentliches Diagramm', 0),
('app', 'en', 'PrivateChart', 'private chart', 0),
('app', 'de', 'as', 'als', 0),
('app', 'en', 'as', 'as', 0),
('app', 'de', 'TimeRange', 'Zeitbereich', 0),
('app', 'en', 'TimeRange', 'Time range', 0),
('model', 'de', 'Dashboard_colors', 'Farbbänder', 0),
('model', 'de', 'Dashboard_colorsHint', 'Definiere hier die Farbbänder für die Achse. ([url=http://pvlng.com/Dashboard_module#Channel_definition]Anleitung[/url])', 0),
('model', 'en', 'Dashboard_colors', 'Color bands', 0),
('model', 'en', 'Dashboard_colorsHint', 'Define here the color bands for the axis. ([url=http://pvlng.com/Dashboard_module#Channel_definition]Instructions[/url])', 0),
('preset', 'de', '1i', '1 Minute', 0),
('preset', 'en', '1i', '1 Minute', 101),
('app', 'de', 'UnsavedChanges', 'Du hast ungesicherte Änderungen für Dein Diagramm', 0),
('app', 'en', 'UnsavedChanges', 'You have unsaved changes for your chart', 0);
