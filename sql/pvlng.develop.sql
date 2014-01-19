--
-- For development branch only!
--

ALTER TABLE `pvlng_channel` ADD `extra` text NOT NULL COMMENT 'Not visible field for models to store extra info' AFTER `public`;

DROP TRIGGER `pvlng_reading_num_bi`;

DELIMITER ;;

CREATE TRIGGER `pvlng_reading_num_bi` BEFORE INSERT ON `pvlng_reading_num` FOR EACH ROW
IF new.`timestamp` = 0 THEN

  SET @NOW = UNIX_TIMESTAMP();
  SELECT IFNULL(`value`,0) INTO @VALUE FROM `pvlng_config` WHERE `key` = 'DoubleRead';

  IF @VALUE <> 0 THEN
    SELECT COUNT(*) INTO @FOUND FROM `pvlng_reading_num` WHERE `id` = new.`id` AND `timestamp` BETWEEN @NOW-@VALUE AND @NOW+@VALUE;
    IF @FOUND THEN
      SIGNAL SQLSTATE '99999' SET MESSAGE_TEXT = 'DoubleRead';
    END IF;
  END IF;

  SET new.`timestamp` = @NOW;

END IF;;

DELIMITER ;

DROP TRIGGER `pvlng_reading_str_bi`;

DELIMITER ;;

CREATE TRIGGER `pvlng_reading_num_bi` BEFORE INSERT ON `pvlng_reading_num` FOR EACH ROW
IF new.`timestamp` = 0 THEN

  SET @NOW = UNIX_TIMESTAMP();
  SELECT IFNULL(`value`,0) INTO @VALUE FROM `pvlng_config` WHERE `key` = 'DoubleRead';

  IF @VALUE <> 0 THEN
    SELECT COUNT(*) INTO @FOUND FROM `pvlng_reading_str` WHERE `id` = new.`id` AND `timestamp` BETWEEN @NOW-@VALUE AND @NOW+@VALUE;
    IF @FOUND THEN
      SIGNAL SQLSTATE '99999' SET MESSAGE_TEXT = 'DoubleRead';
    END IF;
  END IF;

  SET new.`timestamp` = @NOW;

END IF;;

DELIMITER ;


REPLACE INTO `pvlng_config` (`key`, `value`, `comment`, `type`) VALUES
('db', 5, 'Database version number', 0),
('DoubleRead', 5, 'Detect double readings by timestamp -+ seconds, set 0 to disable', 2);

REPLACE INTO `pvlng_babelkit` (`code_set`, `code_lang`, `code_code`, `code_desc`) VALUES
('channel',	'de',	'estimatesHint',	'Definiere die Erwartungswerte in [b]kWh[/b] auf Monats- oder Tagesbasis.\r\n\r\nWenn nur Monatswerte zur Verfügung stehen (z.B. von [url=http://re.jrc.ec.europa.eu/pvgis/apps4/pvest.php]PVGIS[/url]) werden diese als Werte des 15. des Monats verwendet und die anderen Tageswerte linear interpoliert.\r\n[list][*]Monat: [font=courier]Monat:Wert[/font]\r\n[*]Tag: [font=courier]Monat-Tag:Wert[/font][/list]\r\nBeispiel für einen Januar, 4,5kWh pro Tag\r\n[list][*]Monat: [font=courier]1:4.5[/font]\r\n[*]Tag (1. Januar): [font=courier]01-01:4.5[/font][/list]'),
('channel',	'en',	'estimatesHint',	'Define your estimates in [b]kilo watt hours[/b] on monthly or daily base.\r\n\r\nIf only monthly data exists (e.g from [url=http://re.jrc.ec.europa.eu/pvgis/apps4/pvest.php]PVGIS[/url]) the values will be used as of the 15th of the month and the other values will be linear interpolated to get daily values.\r\n[list][*]Monthly: [font=courier]month:value[/font]\r\n[*]Daily: [font=courier]month-day:value[/font][/list]\r\nExample for a january, 4.5kWh each day\r\n[list][*]Monthly: [font=courier]1:4.5[/font]\r\n[*]Daily (1st of january): [font=courier]01-01:4.5[/font][/list]'),
('channel',	'de',	'estimates',	'Erwartungswerte'),
('channel',	'en',	'estimates',	'Estimates'),
('channel',	'de',	'latitude',	'Breitengrad'),
('channel',	'de',	'longitude',	'Längengrad'),
('channel',	'de',	'latitudeHint',	'Standort der Anlage\r\nStandard ist Norden, gib einen negativen Werte für Süden an'),
('channel',	'de',	'longitudeHint',	'Standort der Anlage\r\nStandard ist Osten, gib einen negativen Werte für Westen an'),
('channel',	'en',	'latitude',	'Latitude'),
('channel',	'en',	'longitude',	'Longitude'),
('channel',	'en',	'latitudeHint',	'Location of plant\r\nDefaults to North, pass in a negative value for South'),
('channel',	'en',	'longitudeHint',	'Location of plant\r\nDefaults to East, pass in a negative value for West'),
('app',	'de',	'CreateTreeWithoutReqest',	'Hier werden alle Kanäle und die gesamte Kanal-Hierarchie ohne weitere Nachfrage erstellt.'),
('app',	'en',	'CreateTreeWithoutReqest',	'This will create all channels and the whole channel hierarchy without further request.'),
('channel',	'de',	'ParamIsRequired',	'Wert erforderlich!'),
('channel',	'en',	'ParamIsRequired',	'Value required!'),
('model',	'de',	'FroniusSolarNet_channelHint',	'Equipment-Typ, definiert die unterstützten Kanal-Arten'),
('model',	'en',	'FroniusSolarNet_channelHint',	'Equipment type, defines the supported channels'),
('model',	'de',	'FroniusSolarNet_serial',	'Device Id'),
('model',	'de',	'FroniusSolarNet_channel',	'Typ'),
('model',	'en',	'FroniusSolarNet_serial',	'Device Id'),
('model',	'en',	'FroniusSolarNet_channel',	'Type'),
('model',	'en',	'FroniusSolarNet_serialHint',	'Inverter or SensorCard Id in Fronius Solar Net'),
('model',	'de',	'FroniusSolarNet_serialHint',	'Wechselrichter- oder SensorCard-Id im Fronius Solar Net'),
('model',	'en',	'FroniusSolarNet',	'Accept JSON data for a [url=http://www.fronius.com/cps/rde/xchg/SID-E3D1267B-7210CC3C/fronius_international/hs.xsl/83_318_ENG_HTML.htm]Fronius inverter[/url], either from a request of[tt]GetInverterRealtimeData.cgi[/tt] with [tt]Scope = Device[/tt] and [tt]DataCollection = CommonInverterData[/tt] or\r\n[tt]GetSensorRealtimeData.cgi[/tt] with [tt]Scope = Device[/tt] and [tt]DataCollection = NowSensorData[/tt]'),
('model',	'de',	'FroniusSolarNet',	'Akzeptiert JSON-Daten für einen [url=http://www.fronius.com/cps/rde/xchg/SID-E3D1267B-7210CC3C/fronius_international/hs.xsl/83_318_ENG_HTML.htm]Fronius Wechselrichter[/url] von einer Abfrage von\r\n[tt]GetInverterRealtimeData.cgi[/tt] mit [tt]Scope = Device[/tt] und [tt]DataCollection = CommonInverterData[/tt] oder\r\n[tt]GetSensorRealtimeData.cgi[/tt] mit [tt]Scope = Device[/tt] und [tt]DataCollection = NowSensorData[/tt]'),
('preset',	'de',	'--',	'keine'),
('preset',	'en',	'--',	'none'),
('app', 'de', 'MarkExtremes', 'Markiere Messwerte'),
('app', 'en', 'MarkExtremes', 'Mark reading values'),
('app', 'de', 'lastone', 'letzter'),
('app', 'en', 'lastone', 'last'),
('app', 'de', 'UseOwnConsolidation', 'Benutze einen eigenen Verdichtungzeitraum\r\n(Dieser wird aber nicht in den Varianten-Einstellungen gespeichert)'),
('app', 'en', 'UseOwnConsolidation', 'Use your own consolidation period\r\n(But this will not saved in variant settings)'),
('preset', 'de', '-', 'Verdichtung?'),
('preset', 'en', '-', 'Consolidation?'),
('preset', 'de', '2i', '2 Minuten'),
('preset', 'en', '2i', '2 Minutes'),
('preset', 'de', '2m', '2 Monate'),
('preset', 'de', '2q', '2 Quartale'),
('preset', 'de', '2w', '2 Wochen'),
('preset', 'de', '4h', '4 Stunden'),
('preset', 'de', '6h', '6 Stunden'),
('preset', 'de', '7d', '7 Tage'),
('preset', 'de', '8h', '8 Stunden'),
('preset', 'de', '20i', '20 Minuten'),
('preset', 'de', '30i', '30 Minuten'),
('preset', 'de', '60i', '60 Minuten'),
('preset', 'en', '2m', '2 Month'),
('preset', 'en', '2q', '2 Quarters'),
('preset', 'en', '2w', '2 Weeks'),
('preset', 'en', '4h', '4 Hours'),
('preset', 'en', '6h', '6 Hours'),
('preset', 'en', '7d', '7 Days'),
('preset', 'en', '8h', '8 Hours'),
('preset', 'en', '20i', '20 Minutes'),
('preset', 'en', '30i', '30 Minutes'),
('preset', 'en', '60i', '60 Minutes'),
('preset', 'de', '2h', '2 Stunden'),
('preset', 'de', '12h', '12 Stunden'),
('preset', 'de', '14d', '14 Tage'),
('preset', 'en', '2h', '2 Hours'),
('preset', 'en', '12h', '12 Hours'),
('preset', 'en', '14d', '14 Days'),
('preset', 'de', '1d', '1 Tag'),
('preset', 'de', '1h', '1 Stunde'),
('preset', 'de', '1m', '1 Monat'),
('preset', 'de', '1q', '1 Quartal'),
('preset', 'de', '1w', '1 Woche'),
('preset', 'de', '1y', '1 Jahr'),
('preset', 'de', '10y', 'Dekade'),
('preset', 'en', '1d', '1 Day'),
('preset', 'en', '1h', '1 Hour'),
('preset', 'en', '1m', '1 Month'),
('preset', 'en', '1q', '1 Quarter'),
('preset', 'en', '1w', '1 Week'),
('preset', 'en', '1y', '1 Year'),
('preset', 'en', '10y', 'Decade'),
('preset', 'de', '10i', '10 Minuten'),
('preset', 'en', '10i', '10 Minutes'),
('code_set', 'de', 'preset', 'Voreinstellung'),
('code_set', 'en', 'preset', 'Preset');
