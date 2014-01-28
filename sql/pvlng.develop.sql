--
-- For development branch only!
--

DROP TRIGGER `pvlng_changes_bi`;

DELIMITER ;;
CREATE TRIGGER `pvlng_changes_bi` BEFORE INSERT ON `pvlng_changes` FOR EACH ROW
IF new.`timestamp`= 0 THEN
  SET new.`timestamp` = UNIX_TIMESTAMP();
END IF;;
DELIMITER ;

DROP PROCEDURE `pvlng_changed`;

DELIMITER ;;
CREATE PROCEDURE `pvlng_changed` (IN `in_table` varchar(50), IN `in_key` varchar(50), IN `in_field` varchar(50), IN `in_timestamp` int unsigned, IN `in_old` varchar(255), IN `in_new` varchar(255))
IF in_old <> in_new THEN
  INSERT INTO `pvlng_changes`
  (`table`, `key`, `field`, `timestamp`, `old`, `new`)
  VALUES
  (in_table, in_key, in_field, in_timestamp, in_old, in_new);
END IF;;
DELIMITER ;

DROP TRIGGER `pvlng_channel_au`;

DELIMITER ;;
CREATE TRIGGER `pvlng_channel_au` AFTER UPDATE ON `pvlng_channel` FOR EACH ROW
BEGIN
  IF new.`adjust` = 1 THEN
     CALL `pvlng_changed`('channel', new.`id`, 'offset', 0, old.`offset`, new.`offset`);
  END IF;
END;;
DELIMITER ;

REPLACE INTO `pvlng_babelkit` (`code_set`, `code_lang`, `code_code`, `code_desc`) VALUES
('app', 'de', 'SelectChannel', 'Kanal auswählen'),
('app', 'en', 'SelectChannel', 'Select channel'),
('app', 'de', 'ListExportCSVHint', 'Export aller Werte als Komma-getrennte Datei'),
('app', 'de', 'ListExportTSVHint', 'Export aller Werte als Tab-getrennte Datei'),
('app', 'de', 'ListExportTextHint', 'Export aller Werte als Leerzeichen-getrennte Datei'),
('app', 'en', 'ListExportCSVHint', 'Export all data as Comma-Separated file'),
('app', 'en', 'ListExportTSVHint', 'Export all data as Tab-Separated file'),
('app', 'en', 'ListExportTextHint', 'Export all data as Space-Separated file'),
('app', 'de', 'RowCountHint', 'Anzahl der Zeilen über die verdichtet wurde'),
('app', 'en', 'RowCountHint', 'Number of rows which was consolidated'),
('app', 'de', 'RowCount', 'Zeilenzahl'),
('app', 'en', 'RowCount', 'Row count'),
('app', 'de', 'Reading', 'Messwert'),
('app', 'en', 'Reading', 'Reading value'),
('app', 'de', 'DateTime', 'Datum / Zeit'),
('app', 'en', 'DateTime', 'Date / Time'),
('app', 'de', 'List', 'Liste'),
('app', 'de', 'ListHint', 'Messwerte als Tabelle'),
('app', 'en', 'List', 'List'),
('app', 'en', 'ListHint', 'Measuring data as table'),
('channel', 'de', 'ParamMustInteger', 'Der Wert muss ganzzahlig sein'),
('channel', 'en', 'ParamMustInteger', 'Value must be an integer'),
('channel', 'de', 'ParamMustNumeric', 'Wert muss numerisch sein'),
('channel', 'en', 'ParamMustNumeric', 'Value must be numeric'),
('channel', 'de', 'ParamIsRequired', 'Wert erforderlich'),
('channel', 'en', 'ParamIsRequired', 'Value required');
