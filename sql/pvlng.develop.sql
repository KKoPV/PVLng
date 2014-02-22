--
-- For development branch only!
--

ALTER TABLE `pvlng_performance` DROP `id`;

DELETE FROM `pvlng_babelkit` WHERE `code_set` = 'preset' AND `code_code` = '60i';

REPLACE INTO `pvlng_type` (`id`, `name`, `description`, `model`, `unit`, `childs`, `read`, `write`, `graph`, `icon`)
VALUES (46, 'Wunderground', 'model::Wunderground', 'JSON', '', -1, 0, 1, 0, '/images/ico/Wunderground.png');

INSERT INTO `pvlng_babelkit` (`code_set`, `code_lang`, `code_code`, `code_desc`, `code_order`) VALUES
('preset', 'de', '1i', '1 Minute', 0),
('preset', 'en', '1i', '1 Minute', 101);
