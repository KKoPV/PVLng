--
-- For development branch only!
--

ALTER TABLE `pvlng_tree` DROP INDEX `lft`;
ALTER TABLE `pvlng_tree` ADD INDEX `lft_rgt` (`lft`, `rgt`);
ALTER TABLE `pvlng_tree` ADD INDEX `guid` (`guid`);

ALTER TABLE `pvlng_channel` DROP INDEX `Name-Description-Type`;

DROP PROCEDURE `getTimestamp`;

DELIMITER ;;

CREATE PROCEDURE `getTimestamp` (INOUT `timestamp` int unsigned)
IF `timestamp` = 0 THEN
  SET `timestamp` = UNIX_TIMESTAMP();
END IF;;

DELIMITER ;

REPLACE INTO `pvlng_babelkit` (`code_set`, `code_lang`, `code_code`, `code_desc`, `code_order`, `code_flag`, `changed`) VALUES
('app',	'de',	'AsChild',	'Als Kind-Kanal',	0,	'',	'2014-01-09 15:31:25'),
('app',	'en',	'AsChild',	'As sub channel',	0,	'',	'2014-01-09 15:31:25'),
('app',	'de',	'TopLevel',	'Auf oberster Ebene',	0,	'',	'2014-01-09 15:30:08'),
('app',	'en',	'TopLevel',	'On top level',	0,	'',	'2014-01-09 15:30:08'),
('app',	'de',	'Channel2Overview',	'Füge diesen neuen Kanal auch zur Übersicht hinzu',	0,	'',	'2014-01-09 15:29:43'),
('app',	'en',	'Channel2Overview',	'Add this new channel also into overview',	0,	'',	'2014-01-09 15:29:43'),
('model',	'de',	'Estimate_commentHint',	'Definiere die Erwartungswerte auf Monats- oder Tagesbasis.\r\n\r\nWenn nur Monatswerte zur Verfügung stehen (z.B. von [url=http://re.jrc.ec.europa.eu/pvgis/apps4/pvest.php]PVGIS[/url]) werden diese als Werte des 15. des Monats verwendet und die anderen Tageswerte linear interpoliert.\r\n[list][*]Monat: [font=courier]Monat:Wert[/font]\r\n[*]Tag: [font=courier]Monat-Tag:Wert[/font][/list]\r\nBeispiel für einen Januar, 4,5kWh pro Tag\r\n[list][*]Monat: [font=courier]1:4.5[/font]\r\n[*]Tag (1. Januar): [font=courier]01-01:4.5[/font][/list]',	0,	'',	'2014-01-06 22:05:14'),
('model',	'en',	'Estimate_commentHint',	'Define your estimates on monthly or daily base.\r\n\r\nIf only monthly data exists (e.g from [url=http://re.jrc.ec.europa.eu/pvgis/apps4/pvest.php]PVGIS[/url]) the values will be used as of the 15th of the month and the other values will be linear interpolated to get daily values.\r\n[list][*]Monthly: [font=courier]month:value[/font]\r\n[*]Daily: [font=courier]month-day:value[/font][/list]\r\nExample for a january, 4.5kWh each day\r\n[list][*]Monthly: [font=courier]1:4.5[/font]\r\n[*]Daily (1st of january): [font=courier]01-01:4.5[/font][/list]',	0,	'',	'2014-01-06 22:05:14');
