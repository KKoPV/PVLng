INSERT INTO `pvlng_settings` (`scope`, `name`, `key`, `value`, `order`, `description`, `type`, `data`) VALUES
('controller', 'Index', 'PresetPeriods', '--;1d;1d;1m', 40, 'Default periods for day/week/month/year buttons (separated by semicolon)', 1, ''),
('controller', 'Lists', 'PresetPeriods', '--;1d;1d;1m', 0, 'Default periods for day/week/month/year buttons (separated by semicolon)', 1, '');

INSERT INTO `pvlng_babelkit` (`code_set`, `code_lang`, `code_code`, `code_desc`, `code_order`) VALUES
('app', 'de', 'ScatterChart', 'Punkte', 0),
('app', 'en', 'ScatterChart', 'Scatter', 0),
('app', 'en', 'ScatterCharts', 'Scatter chart', 0),
('app', 'de', 'ScatterCharts', 'Streudiagramm', 0),
('app', 'de', 'yAxisChannel', 'Kanal für die Y-Achse', 0),
('app', 'en', 'yAxisChannel', 'Channel for Y axis', 0),
('app', 'de', 'xAxisChannel', 'Kanal für die X-Achse', 0),
('app', 'en', 'xAxisChannel', 'Channel for X axis', 0),
('app', 'de', 'ResetAll', 'Alles zurücksetzen', 0),
('app', 'en', 'ResetAll', 'Reset all', 0),
('app', 'de', 'PutBarsToStackInSameStack', 'Um Balken zu stapeln, gib allen Balken eines Stapels die gleiche Stapel-Nummer oder -Name', 0),
('app', 'en', 'PutBarsToStackInSameStack', 'To stack bars give all bars of ohne stack the same stack number or name', 0),
('app', 'de', 'BarStack', 'Balkenstapel', 0),
('app', 'en', 'BarStack', 'Bar stack', 0),
('app', 'de', 'Year', 'Jahr', 0),
('app', 'en', 'Year', 'Year', 0),
('app', 'de', 'Week', 'Woche', 0),
('app', 'en', 'Week', 'Week', 0),
('app', 'de', 'YearsToReadMissing', 'Wenn Du plus/minus Tage definiert hast, müssen auch die zu lesenden Jahre angeben werden', 0),
('app', 'en', 'YearsToReadMissing', 'If you defined plus/minus days, the count of years to read is required', 0),
('model', 'de', 'History_extraHint', 'Wenn plus/minus Tage angegeben sind, wieviele Jahre sollen ausgewertet werden', 0),
('model', 'en', 'History_extraHint', 'If plus/minus days defined, how many years back should be read', 0),
('model', 'en', 'History_valid_toHint', 'These are number of days to fetch foreward.\r\n(0 = until today)\r\nA value greater 0 means reading last ? years * (backward + foreward days)!', 0),
('model', 'de', 'History_extra', 'Jahre', 0),
('model', 'en', 'History_extra', 'Years', 0);

DELETE FROM `pvlng_babelkit` WHERE `code_set` = 'preset';

INSERT INTO `pvlng_babelkit` (`code_set`, `code_lang`, `code_code`, `code_desc`, `code_order`) VALUES
('preset', 'de', '--', '--- keine ---', 0),
('preset', 'de', '10i', '10 Minuten', 0),
('preset', 'de', '10s', '10 Sekunden', 0),
('preset', 'de', '10y', 'Dekade', 0),
('preset', 'de', '12h', '12 Stunden', 0),
('preset', 'de', '14d', '14 Tage', 0),
('preset', 'de', '15i', '15 Minuten', 0),
('preset', 'de', '15s', '15 Sekunden', 0),
('preset', 'de', '1d', '1 Tag', 0),
('preset', 'de', '1h', '1 Stunde', 0),
('preset', 'de', '1i', '1 Minute', 0),
('preset', 'de', '1m', '1 Monat', 0),
('preset', 'de', '1q', '1 Quartal', 0),
('preset', 'de', '1w', '1 Woche', 0),
('preset', 'de', '1y', '1 Jahr', 0),
('preset', 'de', '20i', '20 Minuten', 0),
('preset', 'de', '2h', '2 Stunden', 0),
('preset', 'de', '2i', '2 Minuten', 0),
('preset', 'de', '2m', '2 Monate', 0),
('preset', 'de', '2q', '2 Quartale', 0),
('preset', 'de', '2w', '2 Wochen', 0),
('preset', 'de', '30i', '30 Minuten', 0),
('preset', 'de', '30s', '30 Sekunden', 0),
('preset', 'de', '4h', '4 Stunden', 0),
('preset', 'de', '4m', '4 Monate', 0),
('preset', 'de', '5i', '5 Minuten', 0),
('preset', 'de', '6h', '6 Stunden', 0),
('preset', 'de', '7d', '7 Tage', 0),
('preset', 'de', '8h', '8 Stunden', 0),
('preset', 'de', 'd', '::Tage::', 0),
('preset', 'de', 'h', '::Stunden::', 0),
('preset', 'de', 'i', '::Minuten::', 0),
('preset', 'de', 'm', '::Monate::', 0),
('preset', 'de', 'q', '::Quartale::', 0),
('preset', 'de', 's', '::Sekunden::', 0),
('preset', 'de', 'w', '::Wochen::', 0),
('preset', 'de', 'y', '::Jahre::', 0),
('preset', 'en', '--', '--- none ---', 0),
('preset', 'en', '10i', '10 Minutes', 210),
('preset', 'en', '10s', '10 Seconds', 110),
('preset', 'en', '10y', 'Decade', 810),
('preset', 'en', '12h', '12 Hours', 312),
('preset', 'en', '14d', '14 Days', 414),
('preset', 'en', '15i', '15 Minutes', 215),
('preset', 'en', '15s', '15 Seconds', 115),
('preset', 'en', '1d', '1 Day', 401),
('preset', 'en', '1h', '1 Hour', 301),
('preset', 'en', '1i', '1 Minute', 201),
('preset', 'en', '1m', '1 Month', 601),
('preset', 'en', '1q', '1 Quarter', 701),
('preset', 'en', '1w', '1 Week', 501),
('preset', 'en', '1y', '1 Year', 801),
('preset', 'en', '20i', '20 Minutes', 220),
('preset', 'en', '2h', '2 Hours', 302),
('preset', 'en', '2i', '2 Minutes', 202),
('preset', 'en', '2m', '2 Months', 602),
('preset', 'en', '2q', '2 Quarters', 702),
('preset', 'en', '2w', '2 Weeks', 502),
('preset', 'en', '30i', '30 Minutes', 230),
('preset', 'en', '30s', '30 Seconds', 130),
('preset', 'en', '4h', '4 Hours', 304),
('preset', 'en', '4m', '4 Month', 604),
('preset', 'en', '5i', '5 Minutes', 205),
('preset', 'en', '6h', '6 Hours', 306),
('preset', 'en', '7d', '7 Days', 407),
('preset', 'en', '8h', '8 Hours', 308),
('preset', 'en', 'd', '::Days::', 400),
('preset', 'en', 'h', '::Hours::', 300),
('preset', 'en', 'i', '::Minutes::', 200),
('preset', 'en', 'm', '::Months::', 600),
('preset', 'en', 'q', '::Quarters::', 700),
('preset', 'en', 's', '::Seconds::', 100),
('preset', 'en', 'w', '::Weeks::', 500),
('preset', 'en', 'y', '::Years::', 800);

DROP INDEX `id` ON `pvlng_reading_num`;

-- New feature: pre-calc power channels to meter
UPDATE `pvlng_channel` SET `extra` = '"0"' WHERE `type` = 51;

DROP TRIGGER `pvlng_reading_num_ai`;
DELIMITER ;;
CREATE TRIGGER `pvlng_reading_num_ai` AFTER INSERT ON `pvlng_reading_num` FOR EACH ROW
BEGIN

    REPLACE INTO `pvlng_reading_last` VALUES (new.`id`, new.`timestamp`, new.`data`);

    -- Pre-calculated meter channel for sensors
    SELECT extra
      INTO @extra
      FROM `pvlng_channel`
     WHERE `id` = new.`id`
       AND type = 51;

    IF @extra = '"1"' THEN

        SELECT `timestamp`, `data`
          INTO @timestamp, @last
          FROM `pvlng_reading_num_calc`
         WHERE `id` = new.`id`
           AND `timestamp` = (
                   SELECT MAX(`timestamp`)
                     FROM `pvlng_reading_num_calc`
                    WHERE `id` = new.`id`
                );

        IF @timestamp IS NULL THEN
            INSERT INTO `pvlng_reading_num_calc` VALUES(new.`id`, new.`timestamp`, 0);
        ELSE
            INSERT INTO `pvlng_reading_num_calc` VALUES(new.`id`, new.`timestamp`, @last + (new.`timestamp` - @timestamp) / 3600 * new.`data`);
        END IF;

    END IF;

END;;
DELIMITER ;

DROP TRIGGER `pvlng_reading_num_bi`;

DELIMITER ;;

CREATE TRIGGER `pvlng_reading_num_bi` BEFORE INSERT ON `pvlng_reading_num` FOR EACH ROW
BEGIN

    IF new.`timestamp` = 0 THEN
        SET new.`timestamp` = UNIX_TIMESTAMP();
    END IF;

    SELECT IFNULL(`value`,0) INTO @SEC
      FROM `pvlng_settings`
     WHERE `scope` = 'model' AND `name` = '' AND `key` = 'DoubleRead';

    IF @SEC > 0 THEN
        SELECT COUNT(*) INTO @FOUND
          FROM `pvlng_reading_num`
         WHERE `id` = new.`id` AND `timestamp` BETWEEN new.`timestamp`-@SEC AND new.`timestamp`+@SEC;

        IF @FOUND THEN
            SIGNAL SQLSTATE '99999' SET MESSAGE_TEXT = 'DoubleRead';
        END IF;
    END IF;

END;;

DELIMITER ;

DROP TRIGGER `pvlng_reading_str_bi`;

DELIMITER ;;

CREATE TRIGGER `pvlng_reading_str_bi` BEFORE INSERT ON `pvlng_reading_str` FOR EACH ROW
BEGIN

    IF new.`timestamp` = 0 THEN
        SET new.`timestamp` = UNIX_TIMESTAMP();
    END IF;

    SELECT IFNULL(`value`,0) INTO @SEC
      FROM `pvlng_settings`
     WHERE `scope` = 'model' AND `name` = '' AND `key` = 'DoubleRead';

    IF @SEC > 0 THEN
        SELECT COUNT(*) INTO @FOUND
          FROM `pvlng_reading_str`
         WHERE `id` = new.`id` AND `timestamp` BETWEEN new.`timestamp`-@SEC AND new.`timestamp`+@SEC;

        IF @FOUND THEN
            SIGNAL SQLSTATE '99999' SET MESSAGE_TEXT = 'DoubleRead';
        END IF;
    END IF;

END;;

DELIMITER ;

DROP TRIGGER `pvlng_view_bi`;

DELIMITER ;;

CREATE TRIGGER `pvlng_view_bi` BEFORE INSERT ON `pvlng_view` FOR EACH ROW
BEGIN
    SET @slug = `pvlng_slugify`(new.`name`);

    IF (new.`public` = 0) THEN -- private
        SET new.`slug` = CONCAT('p-', @slug);
    ELSEIF (new.`public` = 2) THEN -- mobile
        SET new.`slug` = CONCAT('m-', @slug);
    ELSEIF (new.`public` = 3) THEN -- scatter
        SET new.`slug` = CONCAT('s-', @slug);
    ELSE -- public
        SET new.`slug` = @slug;
    END IF;
END;;

DELIMITER ;

DELIMITER ;;

CREATE PROCEDURE `pvlng_scatter` (IN `in_id_x` smallint unsigned, IN `in_id_y` smallint unsigned, IN `in_start` int unsigned, IN `in_end` int unsigned)
BEGIN

    CREATE TABLE IF NOT EXISTS `pvlng_reading_num_scatter` (
      `id` smallint(5) unsigned NOT NULL,
      `timestamp` int(10) unsigned NOT NULL,
      `data` decimal(13,4) NOT NULL,
      PRIMARY KEY (`id`,`timestamp`),
      KEY `timestamp` (`timestamp`)
    ) ENGINE=Memory;

    SET @uid = FLOOR(RAND()*32766);

    INSERT INTO `pvlng_reading_num_scatter`
    SELECT @uid, `timestamp` DIV 60 * 60, AVG(`data`)
      FROM `pvlng_reading_num`
     WHERE `id` = in_id_x AND `timestamp` BETWEEN in_start AND in_end
     GROUP BY `timestamp` DIV 60;

    INSERT INTO `pvlng_reading_num_scatter`
    SELECT @uid+1, `timestamp` DIV 60 * 60, AVG(`data`)
      FROM `pvlng_reading_num`
     WHERE `id` = in_id_y AND `timestamp` BETWEEN in_start AND in_end
     GROUP BY `timestamp` DIV 60;

    SELECT x.data AS x
         , y.data AS y
         , count(1) as c
         , FROM_UNIXTIME(x.`timestamp`, '%c') AS m
         , FROM_UNIXTIME(x.`timestamp`, '%k') AS h
      FROM `pvlng_reading_num_scatter` x
      JOIN `pvlng_reading_num_scatter` y
        ON x.`id` = @uid
       AND y.`id` = @uid+1
       AND x.`timestamp` = y.`timestamp`
     GROUP BY x, y, h;

    DELETE FROM `pvlng_reading_num_scatter` WHERE `id` = @uid OR `id` = @uid+1;

END;;

DELIMITER ;

REPLACE INTO `pvlng_babelkit` (`code_set`, `code_lang`, `code_code`, `code_desc`, `code_order`) VALUES
('code_admin', 'en', 'settings', 'multi=1', 0),
('code_set', 'de', 'settings', 'Einstellungen', 0),
('code_set', 'en', 'settings', 'Settings', 103),
('settings', 'de', 'model__DoubleRead', 'Erkenne doppelte Daten per Zeitstempelvergleich &plusmn;Sekunden<br>\r\n<small>(setze auf 0 zum deaktivieren)</small>', 0),
('settings', 'en', 'model__DoubleRead', 'Detect double readings by timestamp &plusmn;seconds<br>\r\n<small>(set 0 to disable)</small>', 0),
('settings', 'de', 'model_InternalCalc_LifeTime', 'Speicherdauer für berechnete Daten in Sekunden<br>\r\n\r\n<small>(Wenn z.B. Deine Daten eine Auflösung von 5 Min. haben, setze es auf 300 usw.)</small>', 0),
('settings', 'en', 'model_InternalCalc_LifeTime', 'Buffer lifetime of calculated data in seconds<br>\r\n\r\n<small>(e.g. if your store most data each 5 minutes, set to 300 and so on)</small>', 0),
('settings', 'de', 'model_History_AverageDays', 'Berechne Durchschnitt für die letzten ? Tage', 0),
('settings', 'de', 'model_Estimate_Marker', 'Bild für den Marker', 0),
('settings', 'de', 'model_Daylight_ZenitIcon', 'Bild für den Sonnenzenit-Marker', 0),
('settings', 'de', 'model_Daylight_SunsetIcon', 'Bild für den Sonnenuntergangs-Marker', 0),
('settings', 'de', 'model_Daylight_SunriseIcon', 'Bild für den Sonnenaufgangs-Marker', 0),
('settings', 'de', 'model_Daylight_CurveDays', 'Berechne Durchschnitt für die letzten ? Tage', 0),
('settings', 'de', 'model_Daylight_Average', 'Berechnungsmethode für die Einstrahlungsvorhersage', 0),
('settings', 'de', 'core__TokenLogin', 'Erlaube Login mit einem IP spezifischen Token', 0),
('settings', 'de', 'core__Title', 'Dein persönlicher Titel (HTML ist erlaubt)', 0),
('settings', 'de', 'core__SendStats', 'Sende anonyme Statistiken', 0),
('settings', 'de', 'core__Password', 'Passwort', 0),
('settings', 'de', 'core__Latitude', 'Standort-Latitude (<a href=\"/location\" target=\"_blank\">oder Suche</a>)<br>\r\n<small>geographische Nord-Süd Koordinate (-90..90)</small>', 0),
('settings', 'de', 'core__Longitude', 'Standort-Longitude (<a href=\"/location\" target=\"_blank\">oder Suche</a>)<br>\r\n<small>geographische Ost-West Koordinate (-180..180)</small>', 0),
('settings', 'en', 'core__Latitude', 'Location latitude (<a href=\"/location\" target=\"_blank\">or search</a>)<br>\r\n<small>Your geographic coordinate that specifies the north-south position (-90..90)</small>', 0),
('settings', 'de', 'core__Language', 'Standardsprache', 0),
('settings', 'de', 'core__KeepLogs', 'Halte Log-Einträge für ? Tage', 0),
('settings', 'de', 'core__EmptyDatabaseAllowed', 'Aktiviere die Funktion zum Löschen aller Messdaten aus der Datenbank.<br>\r\nKanäle und die Kanalhierarchie werden <strong>nicht</strong> gelöscht!<br>\r\n<strong style=\"color:red\">Erst nach Aktivierung ist die Bereinigung möglich!</strong>', 0),
('settings', 'en', 'core__EmptyDatabaseAllowed', 'Enable function for deletion of all measuring data from database.<br>\r\nChannels and channel hierarchy will <strong>not</strong> be deleted!<br>\r\n<strong style=\"color:red\">The deletion is only after activation possible!</strong>', 0),
('settings', 'de', 'core_Currency_Format', 'Ausgabeformat, <strong><tt>{}</tt></strong> wird durch den Wert ersetzt', 0),
('settings', 'de', 'core_Currency_Decimals', 'Nachkommastellen', 0),
('settings', 'de', 'controller_Tariff_TimesLines', 'Initiale Leerzeilen pro Tarif', 0),
('settings', 'de', 'controller_Mobile_ChartHeight', 'Standard Diagrammhöhe', 0),
('settings', 'de', 'controller_Lists_PresetPeriods', 'Standard-Perioden für die Tag/Woche/Monat/Jahr Buttons (Semikolon getrennt)', 0),
('settings', 'de', 'controller_Index_Refresh', 'Diagramm automatisch neu laden aller ? Sekunden, setze auf 0 zum deaktivieren\r\n(Nur wenn Fenster im Vordergrund)', 0),
('settings', 'en', 'controller_Index_Refresh', 'Auto refresh chart each ? seconds, set 0 to disable\r\n(Only if window is in foreground)', 0),
('settings', 'de', 'controller_Index_PresetPeriods', 'Standard-Perioden für die Tag/Woche/Monat/Jahr Buttons (Semikolon getrennt)', 0),
('settings', 'de', 'controller_Index_NotifyAll', 'Zeige die Ladezeit für die Datenkanäle', 0),
('settings', 'de', 'controller_Index_ChartHeight', 'Standard Diagrammhöhe', 0),
('settings', 'en', 'core__Title', 'Your personal title (HTML allowed)', 0),
('settings', 'en', 'core__KeepLogs', 'Hold entries in log table for ? days', 0),
('settings', 'en', 'core__Language', 'Default language', 0),
('settings', 'en', 'core__Password', 'Password', 0),
('settings', 'en', 'core__Longitude', 'Location longitude (<a href=\"/location\" target=\"_blank\">or search</a>)<br /><small>Your geographic coordinate that specifies the east-west position (-180..180)</small>', 0),
('settings', 'en', 'core__SendStats', 'Send anonymous statistics', 0),
('settings', 'en', 'core__TokenLogin', 'Allow administrator login by IP bound login token', 0),
('settings', 'en', 'core_Currency_ISO', 'ISO Code', 0),
('settings', 'en', 'core_Currency_Format', 'Output format, <strong><tt>{}</tt></strong> will be replaced with value', 0),
('settings', 'en', 'core_Currency_Symbol', 'Symbol', 0),
('settings', 'en', 'model_Estimate_Marker', 'Marker image', 0),
('settings', 'en', 'core_Currency_Decimals', 'Decimals', 0),
('settings', 'en', 'model_Daylight_Average', 'Calculation method for irradiation average', 0),
('settings', 'en', 'model_Daylight_CurveDays', 'Build average over the last ? days', 0),
('settings', 'en', 'model_Daylight_ZenitIcon', 'Sun zenit marker image', 0),
('settings', 'en', 'controller_Weather_APIkey', 'Wunderground API key', 0),
('settings', 'en', 'model_Daylight_SunsetIcon', 'Sunset marker image', 0),
('settings', 'en', 'model_History_AverageDays', 'Build average over the last ? days', 0),
('settings', 'en', 'controller_Index_NotifyAll', 'Notify overall loading time for all channels', 0),
('settings', 'en', 'model_Daylight_SunriseIcon', 'Sunrise marker image', 0),
('settings', 'en', 'controller_Index_ChartHeight', 'Default chart height', 0),
('settings', 'en', 'controller_Tariff_TimesLines', 'Initial times lines for each taiff', 0),
('settings', 'en', 'controller_Mobile_ChartHeight', 'Default chart height', 0),
('settings', 'en', 'controller_Index_PresetPeriods', 'Default periods for day/week/month/year buttons (separated by semicolon)', 0),
('settings', 'en', 'controller_Lists_PresetPeriods', 'Default periods for day/week/month/year buttons (separated by semicolon)', 0),
('app', 'de', 'SettingsMenu', 'Einstellungen', 0),
('app', 'de', 'RepeatPasswordForChange', 'Nur ausfüllen, wenn es geändert werden soll!', 0),
('app', 'en', 'RepeatPasswordForChange', 'Fill only to change it!', 0),
('app', 'de', 'RepeatPassword', 'Passwort wiederholen', 0),
('app', 'en', 'RepeatPassword', 'repeat password', 0),
('app', 'de', 'Hour', 'Stunde', 0),
('app', 'en', 'Hour', 'Hour', 0),
('app', 'de', 'DecimalsForMarkers', 'Dezimalstellen für Punkt-Beschriftungen', 0),
('app', 'en', 'DecimalsForMarkers', 'Decimals for marker texts', 0),
('app', 'de', 'ScatterChart', 'Punkte', 0),
('app', 'en', 'ScatterChart', 'Scatter', 0),
('app', 'en', 'ScatterCharts', 'Scatter chart', 0),
('app', 'de', 'ScatterCharts', 'Streudiagramm', 0),
('app', 'de', 'yAxisChannel', 'Kanal für die Y-Achse', 0),
('app', 'en', 'yAxisChannel', 'Channel for Y axis', 0),
('app', 'de', 'xAxisChannel', 'Kanal für die X-Achse', 0),
('app', 'en', 'xAxisChannel', 'Channel for X axis', 0),
('app', 'de', 'ResetAll', 'Alles zurücksetzen', 0),
('app', 'en', 'ResetAll', 'Reset all', 0),
('app', 'de', 'PutBarsToStackInSameStack', 'Um Balken zu stapeln, gib allen Balken eines Stapels die gleiche Stapel-Nummer oder -Name', 0),
('app', 'en', 'PutBarsToStackInSameStack', 'To stack bars give all bars of ohne stack the same stack number or name', 0),
('app', 'de', 'BarStack', 'Balkenstapel', 0),
('app', 'en', 'BarStack', 'Bar stack', 0),
('app', 'de', 'Year', 'Jahr', 0),
('app', 'en', 'Year', 'Year', 0),
('app', 'de', 'Week', 'Woche', 0),
('app', 'en', 'Week', 'Week', 0),
('app', 'de', 'YearsToReadMissing', 'Wenn Du plus/minus Tage definiert hast, müssen auch die zu lesenden Jahre angeben werden', 0),
('app', 'en', 'YearsToReadMissing', 'If you defined plus/minus days, the count of years to read is required', 0),
('model', 'de', 'History_extraHint', 'Wenn plus/minus Tage angegeben sind, wieviele Jahre sollen ausgewertet werden', 0),
('model', 'en', 'History_extraHint', 'If plus/minus days defined, how many years back should be read', 0),
('model', 'en', 'History_valid_toHint', 'These are number of days to fetch foreward.\r\n(0 = until today)\r\nA value greater 0 means reading last ? years * (backward + foreward days)!', 0),
('model', 'de', 'History_extra', 'Jahre', 0),
('model', 'en', 'History_extra', 'Years', 0);

ALTER TABLE `pvlng_settings` DROP `description`;

REPLACE INTO `pvlng_babelkit` (`code_set`, `code_lang`, `code_code`, `code_desc`) VALUES
('app', 'de', 'ClickDragShiftPan', 'Mausrad drehen oder Doppelklicken zum Vergrößern/Verkleinern, Maus klicken und halten zum Verschieben.'),
('app', 'en', 'ClickDragShiftPan', 'Scroll mouse wheel or double click to zoom, click and hold to pan.');
