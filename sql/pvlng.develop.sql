--
-- For development branch only!
--

ALTER TABLE `pvlng_tree` DROP INDEX `lft`;
ALTER TABLE `pvlng_tree` ADD INDEX `lft_rgt` (`lft`, `rgt`);
ALTER TABLE `pvlng_tree` ADD INDEX `guid` (`guid`);

ALTER TABLE `pvlng_channel` DROP INDEX `Name-Description-Type`;

DROP TRIGGER `pvlng_reading_num_bi`;
DROP TRIGGER `pvlng_reading_str_bi`;

DELIMITER ;;

CREATE TRIGGER `pvlng_reading_num_bi` BEFORE INSERT ON `pvlng_reading_num` FOR EACH ROW
IF new.`timestamp` = 0 THEN
  SET new.`timestamp` = UNIX_TIMESTAMP();
END IF;;

CREATE TRIGGER `pvlng_reading_str_bi` BEFORE INSERT ON `pvlng_reading_str` FOR EACH ROW
IF new.`timestamp` = 0 THEN
  SET new.`timestamp` = UNIX_TIMESTAMP();
END IF;;

DELIMITER ;

DROP PROCEDURE `getTimestamp`;

DELETE FROM `pvlng_config` WHERE `key` = 'TimeStep';

REPLACE INTO `pvlng_babelkit` (`code_set`, `code_lang`, `code_code`, `code_desc`) VALUES
('app', 'de', 'AsChild', 'Als Kind-Kanal'),
('app', 'en', 'AsChild', 'As sub channel'),
('app', 'de', 'TopLevel', 'Auf oberster Ebene'),
('app', 'en', 'TopLevel', 'On top level'),
('app', 'de', 'Channel2Overview', 'Füge diesen neuen Kanal auch zur Übersicht hinzu'),
('app', 'en', 'Channel2Overview', 'Add this new channel also into overview'),
('model', 'de', 'Estimate_commentHint', 'Definiere die Erwartungswerte auf Monats- oder Tagesbasis.\r\n\r\nWenn nur Monatswerte zur Verfügung stehen (z.B. von [url=http://re.jrc.ec.europa.eu/pvgis/apps4/pvest.php]PVGIS[/url]) werden diese als Werte des 15. des Monats verwendet und die anderen Tageswerte linear interpoliert.\r\n[list][*]Monat: [font=courier]Monat:Wert[/font]\r\n[*]Tag: [font=courier]Monat-Tag:Wert[/font][/list]\r\nBeispiel für einen Januar, 4,5kWh pro Tag\r\n[list][*]Monat: [font=courier]1:4.5[/font]\r\n[*]Tag (1. Januar): [font=courier]01-01:4.5[/font][/list]'),
('model', 'en', 'Estimate_commentHint', 'Define your estimates on monthly or daily base.\r\n\r\nIf only monthly data exists (e.g from [url=http://re.jrc.ec.europa.eu/pvgis/apps4/pvest.php]PVGIS[/url]) the values will be used as of the 15th of the month and the other values will be linear interpolated to get daily values.\r\n[list][*]Monthly: [font=courier]month:value[/font]\r\n[*]Daily: [font=courier]month-day:value[/font][/list]\r\nExample for a january, 4.5kWh each day\r\n[list][*]Monthly: [font=courier]1:4.5[/font]\r\n[*]Daily (1st of january): [font=courier]01-01:4.5[/font][/list]');
