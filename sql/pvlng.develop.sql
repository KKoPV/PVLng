--
-- For development branch only!
--

ALTER TABLE `pvlng_performance` DROP `id`;

DELETE FROM `pvlng_babelkit` WHERE `code_set` = 'preset' AND `code_code` = '60i';

REPLACE INTO `pvlng_type` (`id`, `name`, `description`, `model`, `unit`, `childs`, `read`, `write`, `graph`, `icon`)
VALUES (46, 'Wunderground', 'model::Wunderground', 'JSON', '', -1, 0, 1, 0, '/images/ico/Wunderground.png');

DELETE FROM `pvlng_babelkit` WHERE `code_code` LIKE 'Dashboard_comment%';

REPLACE INTO `pvlng_babelkit` (`code_set`, `code_lang`, `code_code`, `code_desc`, `code_order`) VALUES
('preset', 'de', '1i', '1 Minute', 0),
('preset', 'en', '1i', '1 Minute', 101),
('model', 'de', 'Dashboard_colors', 'Farbbänder', 0),
('model', 'en', 'Dashboard_colorsHint', 'Define here the color bands for the axis. ([url=http://pvlng.com/Dashboard_module#Channel_definition]Instructions[/url])', 0),
('model', 'de', 'Dashboard_colorsHint', 'Definiere hier die Farbbänder für die Achse. ([url=http://pvlng.com/Dashboard_module#Channel_definition]Anleitung[/url])', 0),
('model', 'en', 'Dashboard_colors', 'Color bands', 0);
