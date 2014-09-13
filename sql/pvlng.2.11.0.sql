--
-- v2.10.* > v2.11.0
--

DELIMITER ;;

CREATE FUNCTION `pvlng_slugify` (`in_str` varchar(200)) RETURNS varchar(200) CHARACTER SET 'utf8' NO SQL
BEGIN
    -- -----------------------------------------------------------------------
    -- Ideas from
    -- http://nastyhabit.wordpress.com/2008/09/25/mysql-slug-maker-function-aka-the-slugifier/
    -- https://github.com/falcacibar/mysql-routines-collection/blob/master/tr.func.sql
    -- with some additions
    -- -----------------------------------------------------------------------

    DECLARE x, y, z int;
    Declare str, allowed_chars, allowed_regex, translate_from, translate_to varchar(200);
    Declare allowed bool;
    Declare c varchar(1);

    SET allowed_chars  = 'abcdefghijklmnopqrstuvwxyz0123456789-';
    SET allowed_regex  = '[^a-z0-9]+';
    SET translate_from = 'áàâãåéèëêíìïîóòôõúùûñýç';
    SET translate_to   = 'aaaaaeeeeiiiioooouuunyc';

    -- Let's go
    SET str = Lower(in_str); -- Make always lowercase

    -- Simplified
    SET str = REPLACE(str, '&', ' and ');
    SET str = REPLACE(str, '@', ' at '); -- Add @ handling
    -- Add german vowels
    SET str = REPLACE(str, 'ä', 'ae');
    SET str = REPLACE(str, 'ö', 'oe');
    SET str = REPLACE(str, 'ü', 'ue');

  SET x = CHAR_LENGTH(translate_from);

    WHILE x DO
        SET str = REPLACE(str, SUBSTR(translate_from, x, 1), SUBSTR(translate_to, x, 1));
        SET x = x - 1;
    END WHILE;

    SELECT str REGEXP(allowed_regex) INTO x;
    IF x = 1 THEN
        SET z = 1;
        WHILE z <= CHAR_LENGTH(str) DO
            SET c = SUBSTRING(str, z, 1);
            SET allowed = FALSE;
            SET y = 1;

            InnerCheck:
            WHILE y <= CHAR_LENGTH(allowed_chars) DO
                IF (STRCMP(ASCII(SUBSTRING(allowed_chars, y, 1)), ASCII(c)) = 0) THEN
                    SET allowed = TRUE;
                    LEAVE InnerCheck;
                END IF;
                SET y = y + 1;
            END WHILE;

            IF !allowed THEN
                SET str = REPLACE(str, c, '-');
            END IF;
            SET z = z + 1;
        END WHILE;
    END IF;

    SELECT str REGEXP("^-|-$|'") INTO x;
    IF x = 1 THEN
        SET str = REPLACE(str, "'", '');
        SET z = CHAR_LENGTH(str);
        SET y = CHAR_LENGTH(str);

        DashCheck:
        WHILE z > 0 DO
            IF STRCMP(SUBSTRING(str, -1, 1), '-') = 0 THEN
                SET str = SUBSTRING(str, 1, y-1);
                SET y = y - 1;
            ELSE
                LEAVE DashCheck;
            END IF;
            SET z = z - 1;
        END WHILE;
    END IF;

    REPEAT
        SELECT str REGEXP('--') INTO x;
        IF x = 1 THEN
            SET str = REPLACE(str, '--', '-');
        END IF;
    UNTIL x <> 1 END REPEAT;

    RETURN str;
END;;

DELIMITER ;

CREATE TABLE `pvlng_dashboard` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL COMMENT 'Unique name',
  `data` varchar(255) NOT NULL COMMENT 'Selected channels in JSON',
  `slug` varchar(50) NOT NULL COMMENT 'Unique URL save slug',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DELIMITER ;;

CREATE TRIGGER `pvlng_dashboard_bi` BEFORE INSERT ON `pvlng_dashboard` FOR EACH ROW
BEGIN
  SELECT `pvlng_slugify`(new.`name`) INTO @slug;
  SET new.`slug` = @slug;
END;;

CREATE TRIGGER `pvlng_dashboard_bu` BEFORE UPDATE ON `pvlng_dashboard` FOR EACH ROW
BEGIN
  SELECT `pvlng_slugify`(new.`name`) INTO @slug;
  SET new.`slug` = @slug;
END;;

CREATE TRIGGER `pvlng_view_bi` BEFORE INSERT ON `pvlng_view` FOR EACH ROW
BEGIN
  SELECT `pvlng_slugify`(new.`name`) INTO @slug;
  IF (new.`public` = 0) THEN -- private
    SET new.`slug` = CONCAT('p-', @slug);
  ELSEIF (new.`public` = 2) THEN -- mobile
    SET new.`slug` = CONCAT('m-', @slug);
  ELSE -- public
    SET new.`slug` = @slug;
  END IF;
END;;

CREATE TRIGGER `pvlng_view_bu` BEFORE UPDATE ON `pvlng_view` FOR EACH ROW
BEGIN
  SELECT `pvlng_slugify`(new.`name`) INTO @slug;
  IF (new.`public` = 0) THEN -- private
    SET new.`slug` = CONCAT('p-', @slug);
  ELSEIF (new.`public` = 2) THEN -- mobile
    SET new.`slug` = CONCAT('m-', @slug);
  ELSE -- public
    SET new.`slug` = @slug;
  END IF;
END;;

DELIMITER ;

INSERT INTO `pvlng_dashboard` (`name`, `data`)
  SELECT 'Default', `value`
    FROM `pvlng_config`
   WHERE `key` = 'dashboard';

DELETE FROM `pvlng_config` WHERE `key` = 'dashboard';

-- Force slug recreation
UPDATE `pvlng_view` SET `slug`= NULL;

CREATE TABLE `pvlng_tariff` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `comment` varchar(250) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `Tariff name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `pvlng_tariff_date` (
  `id` int(10) unsigned NOT NULL COMMENT 'pvlng_tariff -> id',
  `date` date NOT NULL COMMENT 'Start date for this tariff (incl.) ',
  `cost` float DEFAULT NULL COMMENT 'Fix costs per day, e.g. EUR / kWh',
  PRIMARY KEY (`id`,`date`),
  KEY `date` (`date`),
  CONSTRAINT `pvlng_tariff_date_ibfk_1` FOREIGN KEY (`id`) REFERENCES `pvlng_tariff` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `pvlng_tariff_time` (
  `id` int(10) unsigned NOT NULL COMMENT 'pvlng_tariff_date -> id',
  `date` date NOT NULL COMMENT 'pvlng_tariff_date -> date',
  `time` time NOT NULL COMMENT 'Starting time (incl.)',
  `days` set('1','2','3','4','5','6','7') NOT NULL COMMENT '1 Mo .. 7 Su',
  `tariff` float DEFAULT NULL COMMENT 'e.g. EUR / kWh',
  `comment` varchar(250) NOT NULL,
  PRIMARY KEY (`id`,`date`,`time`,`days`),
  KEY `days` (`days`),
  KEY `date` (`date`),
  KEY `time` (`time`),
  CONSTRAINT `pvlng_tariff_time_ibfk_1` FOREIGN KEY (`id`, `date`) REFERENCES `pvlng_tariff_date` (`id`, `date`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE VIEW `pvlng_tariff_view` AS select `t1`.`id` AS `id`,`t1`.`name` AS `name`,`t1`.`comment` AS `tariff_comment`,`t2`.`date` AS `date`,`t2`.`cost` AS `cost`,`t3`.`time` AS `time`,`t3`.`days` AS `days`,`t3`.`tariff` AS `tariff`,`t3`.`comment` AS `time_comment` from ((`pvlng_tariff` `t1` left join `pvlng_tariff_date` `t2` on((`t1`.`id` = `t2`.`id`))) left join `pvlng_tariff_time` `t3` on(((`t2`.`id` = `t3`.`id`) and (`t2`.`date` = `t3`.`date`))));

DELIMITER ;;

CREATE FUNCTION `pvlng_tariff` (`in_id` int unsigned, `in_date` date, `in_time` varchar(10)) RETURNS decimal(9,3)
BEGIN
    SELECT DAYOFWEEK(in_date)-1 INTO @dow;
    IF @dow = 0 THEN
        -- Correct sundays id
        SET @dow = 7;
    END IF;
    RETURN (
        SELECT t2.`tariff`
          FROM `pvlng_tariff` t1
          JOIN `pvlng_tariff_time` t2 USING(`id`)
         WHERE t1.`id` = in_id
           AND t2.`date` <= in_date
           AND t2.`time` <= in_time
           AND FIND_IN_SET(@dow, t2.`days`)
         ORDER BY `date` DESC, `time`DESC
         LIMIT 1
    );
END;;

CREATE PROCEDURE `pvlng_tariff_day` (IN `in_id` int unsigned, IN `in_date` date)
BEGIN
    -- Result set
    DECLARE p_start time DEFAULT 0;
    DECLARE p_end time;
    DECLARE p_tariff decimal(9,3);
    -- Select into
    DECLARE t decimal(9,3);
    DECLARE _cursor CURSOR FOR
      SELECT `time`, `tariff`
        FROM `pvlng_tariff_time`
       WHERE `id`= in_id
         AND FIND_IN_SET(IF(DAYOFWEEK(in_date)=1,7,DAYOFWEEK(in_date)-1), days)
         AND `date` = (
            SELECT MAX(`date`)
              FROM `pvlng_tariff_time`
             WHERE `id`= in_id
               AND `date`<= in_date
            );
    DECLARE CONTINUE HANDLER FOR NOT FOUND SET @done = TRUE;
    OPEN _cursor; LOOPROWS: LOOP
        IF @done THEN
            SELECT p_start AS start, '24:00:00' AS end, p_tariff AS tariff;
            CLOSE _cursor;
            LEAVE LOOPROWS;
        END IF;
        FETCH _cursor INTO p_end, t;
        if p_end <> p_start THEN
            SELECT p_start AS start, p_end AS end, p_tariff AS tariff;
        END IF;
        -- Remeber values
        SET p_start = p_end;
        SET p_tariff = t;
    END LOOP;
END;;

DELIMITER ;

ALTER TABLE `pvlng_channel`
    CHANGE `cost` `cost` double NULL COMMENT 'per unit or unit * h' AFTER `adjust`,
    ADD `tariff` int unsigned NULL AFTER `cost`,
    ADD `icon` varchar(255) NOT NULL,
    ADD FOREIGN KEY (`tariff`) REFERENCES `pvlng_tariff` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

UPDATE `pvlng_channel` c
   SET `icon` = (SELECT `icon` from `pvlng_type` WHERE `id` = c.`type`);

ALTER TABLE `pvlng_type` ADD `type` enum('group','general','numeric','sensor','meter') NOT NULL AFTER `unit`;

SET foreign_key_checks = 0;

REPLACE INTO `pvlng_type` (`id`, `name`, `description`, `model`, `unit`, `type`, `childs`, `read`, `write`, `graph`, `icon`) VALUES
(0, 'Alias', 'model::Alias', 'Channel', '', 'general', 0, 0, 0, 0, ''),
(1, 'Power plant', 'model::PowerPlant', 'Channel', '', 'group', -1, 0, 0, 0, '/images/ico/building.png'),
(2, 'Inverter', 'model::Inverter', 'Channel', '', 'group', -1, 0, 0, 0, '/images/ico/exclamation_frame.png'),
(3, 'Building', 'model::Building', 'Channel', '', 'group', -1, 0, 0, 0, '/images/ico/home.png'),
(4, 'Multi-Sensor', 'model::MultiSensor', 'Channel', '', 'group', -1, 0, 0, 0, '/images/ico/wooden_box.png'),
(5, 'Group', 'model::Group', 'Channel', '', 'group', -1, 0, 0, 0, '/images/ico/folders_stack.png'),
(10, 'Random', 'model::Random', 'Random', '', 'numeric', 0, 1, 0, 1, '/images/ico/ghost.png'),
(11, 'Fixed value', 'model::Fix', 'Fix', '', 'sensor', 0, 1, 0, 1, '/images/ico/chart_arrow.png'),
(12, 'Estimate', 'model::Estimate', 'Estimate', 'Wh', 'sensor', 0, 1, 0, 1, '/images/ico/plug.png'),
(13, 'Daylight', 'model::Daylight', 'Daylight', '', 'sensor', 0, 1, 0, 1, '/images/ico/picture-sunset.png'),
(15, 'Ratio calculator', 'model::Ratio', 'Ratio', '%', 'sensor', 2, 1, 0, 1, '/images/ico/edit_percent.png'),
(16, 'Accumulator', 'model::Accumulator', 'Accumulator', '', 'numeric', -1, 1, 0, 1, '/images/ico/calculator_scientific.png'),
(17, 'Differentiator', 'model::Differentiator', 'Differentiator', '', 'numeric', -1, 1, 0, 1, '/images/ico/calculator_scientific.png'),
(18, 'Full Differentiator', 'model::DifferentiatorFull', 'DifferentiatorFull', '', 'numeric', -1, 1, 0, 1, '/images/ico/calculator_scientific.png'),
(19, 'Sensor to meter', 'model::SensorToMeter', 'SensorToMeter', '', 'meter', 1, 1, 0, 1, '/images/ico/calculator_scientific.png'),
(20, 'Import / Export', 'model::ImportExport', 'InternalConsumption', '', 'meter', 2, 1, 0, 1, '/images/ico/calculator_scientific.png'),
(21, 'Average', 'model::Average', 'Average', '', 'numeric', -1, 1, 0, 1, '/images/ico/calculator_scientific.png'),
(22, 'Calculator', 'model::Calculator', 'Calculator', '', 'numeric', 1, 1, 0, 1, '/images/ico/calculator_scientific.png'),
(23, 'History', 'model::History', 'History', '', 'numeric', 1, 1, 0, 1, '/images/ico/calculator_scientific.png'),
(24, 'Baseline', 'model::Baseline', 'Baseline', '', 'sensor', 1, 1, 0, 1, '/images/ico/calculator_scientific.png'),
(25, 'Topline', 'model::Topline', 'Topline', '', 'sensor', 1, 1, 0, 1, '/images/ico/calculator_scientific.png'),
(26, 'Meter to sensor', 'model::MeterToSensor', 'MeterToSensor', '', 'sensor', 1, 1, 0, 1, '/images/ico/calculator_scientific.png'),
(27, 'Full Accumulator', 'model::AccumulatorFull', 'AccumulatorFull', '', 'numeric', -1, 1, 0, 1, '/images/ico/calculator_scientific.png'),
(28, 'Selector', 'model::Selector', 'Selector', '', 'numeric', 2, 1, 0, 1, '/images/ico/ui_check_boxes.png'),
(30, 'Dashboard channel', 'model::Dashboard', 'Dashboard', '', 'numeric', 1, 1, 0, 1, '/images/ico/dashboard.png'),
(40, 'SMA Sunny Webbox', 'model::SMAWebbox', 'SMA\\Webbox', '', 'group', -1, 0, 1, 0, '/images/ico/sma_webbox.png'),
(41, 'SMA Inverter', 'model::SMAInverter', 'SMA\\Webbox', '', 'group', -1, 0, 1, 0, '/images/ico/sma_inverter.png'),
(42, 'SMA Sensorbox', 'model::SMASensorbox', 'SMA\\Webbox', '', 'group', -1, 0, 1, 0, '/images/ico/sma_sensorbox.png'),
(43, 'Fronius Inverter', 'model::FroniusSolarNet', 'Fronius\\SolarNet', '', 'group', -1, 0, 1, 0, '/images/ico/fronius_inverter.png'),
(44, 'Fronius Sensorbox', 'model::FroniusSolarNet', 'Fronius\\SolarNet', '', 'group', -1, 0, 1, 0, '/images/ico/fronius_sensorbox.png'),
(45, 'OpenWeatherMap', 'model::OpenWeatherMap', 'JSON', '', 'group', -1, 0, 1, 0, '/images/ico/OpenWeatherMap.png'),
(46, 'Wunderground', 'model::Wunderground', 'JSON', '', 'group', -1, 0, 1, 0, '/images/ico/Wunderground.png'),
(50, 'Energy meter, absolute', 'model::EnergyMeter', 'Channel', 'Wh', 'meter', 0, 1, 1, 1, '/images/ico/plug.png'),
(51, 'Power sensor', 'model::PowerSensor', 'Channel', 'W', 'sensor', 0, 1, 1, 1, '/images/ico/plug.png'),
(52, 'Voltage sensor', 'model::Voltage', 'Channel', 'V', 'sensor', 0, 1, 1, 1, '/images/ico/dashboard.png'),
(53, 'Current sensor', 'model::CurrentSensor', 'Channel', 'A', 'sensor', 0, 1, 1, 1, '/images/ico/lightning.png'),
(54, 'Gas sensor', 'model::GasSensor', 'Channel', 'm³/h', 'sensor', 0, 1, 1, 1, '/images/ico/fire.png'),
(55, 'Heat sensor', 'model::HeatSensor', 'Channel', 'W', 'sensor', 0, 1, 1, 1, '/images/ico/fire_big.png'),
(56, 'Humidity sensor', 'model::Humidity', 'Channel', '%', 'sensor', 0, 1, 1, 1, '/images/ico/weather_cloud.png'),
(57, 'Luminosity sensor', 'model::Luminosity', 'Channel', 'lm', 'sensor', 0, 1, 1, 1, '/images/ico/light_bulb.png'),
(58, 'Pressure sensor', 'model::Pressure', 'Channel', 'hPa', 'sensor', 0, 1, 1, 1, '/images/ico/umbrella.png'),
(59, 'Radiation sensor', 'model::RadiationSensor', 'Channel', 'µSV', 'sensor', 0, 1, 1, 1, '/images/ico/radioactivity.png'),
(60, 'Temperature sensor', 'model::Temperature', 'Channel', '°C', 'sensor', 0, 1, 1, 1, '/images/ico/thermometer.png'),
(61, 'Valve sensor', 'model::Valve', 'Channel', '°', 'sensor', 0, 1, 1, 1, '/images/ico/wheel.png'),
(62, 'Water sensor', 'model::WaterSensor', 'Channel', 'm³/h', 'sensor', 0, 1, 1, 1, '/images/ico/water.png'),
(63, 'Windspeed sensor', 'model::Windspeed', 'Channel', 'm/s', 'sensor', 0, 1, 1, 1, '/images/ico/paper_plane.png'),
(64, 'Irradiation sensor', 'model::Irradiation', 'Channel', 'W/m²', 'sensor', 0, 1, 1, 1, '/images/ico/brightness.png'),
(65, 'Timer', 'model::Timer', 'Channel', 'h', 'meter', 0, 1, 1, 1, '/images/ico/clock.png'),
(66, 'Frequency sensor', 'model::FrequencySensor', 'Channel', 'Hz', 'sensor', 0, 1, 1, 1, '/images/ico/dashboard.png'),
(67, 'Winddirection sensor', 'model::Winddirection', 'Channel', '°', 'sensor', 0, 1, 1, 1, '/images/ico/wheel.png'),
(68, 'Rainfall sensor', 'model::RainfallSensor', 'Channel', 'mm/h', 'sensor', 0, 1, 1, 1, '/images/ico/umbrella.png'),
(70, 'Gas meter', 'model::GasMeter', 'Channel', 'm³', 'meter', 0, 1, 1, 1, '/images/ico/fire.png'),
(71, 'Radiation meter', 'model::RadiationMeter', 'Channel', 'µSV/h', 'meter', 0, 1, 1, 1, '/images/ico/radioactivity.png'),
(72, 'Water meter', 'model::WaterMeter', 'Channel', 'm³', 'meter', 0, 1, 1, 1, '/images/ico/water.png'),
(73, 'Rainfall meter', 'model::RainfallMeter', 'Channel', 'mm', 'meter', 0, 1, 1, 1, '/images/ico/umbrella.png'),
(90, 'Power sensor counter', 'model::PowerCounter', 'Counter', 'W', 'sensor', 0, 1, 1, 1, '/images/ico/plug.png'),
(91, 'Switch', 'model::Switch', 'Switcher', '', 'general', 0, 1, 1, 1, '/images/ico/ui_check_boxes.png'),
(99, 'Database usage', 'Database usage', 'DatabaseUsage', 'rows', 'sensor', 0, 1, 0, 1, '/images/ico/database.png'),
(100, 'PV-Log Plant', 'model::PVLogPlant', 'PVLog\\Plant', '', 'group', -1, 1, 0, 0, '/images/ico/pv_log_sum.png'),
(101, 'PV-Log Inverter', 'model::PVLogInverter', 'PVLog\\Inverter', '', 'group', -1, 1, 0, 0, '/images/ico/pv_log.png'),
(102, 'PV-Log Plant (r2)', 'model::PVLogPlant2', 'PVLog2\\Plant', '', 'group', -1, 1, 0, 0, '/images/ico/pv_log_sum.png'),
(103, 'PV-Log Inverter (r2)', 'model::PVLogInverter2', 'PVLog2\\Inverter', '', 'group', -1, 0, 0, 0, '/images/ico/pv_log.png'),
(110, 'Sonnenertrag JSON', 'model::SonnenertragJSON', 'Sonnenertrag\\JSON', '', 'group', -1, 1, 0, 0, '/images/ico/sonnenertrag.png');

SET foreign_key_checks = 1;

UPDATE pvlng_channel c1, pvlng_channel c2, pvlng_tree t1, pvlng_tree t2, pvlng_type tt1, pvlng_type tt2
  SET c1.icon = tt2.icon
WHERE t1.lft+1 = t2.lft AND t1.entity = c1.id AND t2.entity = c2.id AND tt1.id = c1.type AND tt2.id = c2.type AND tt2.id <> 0 AND tt1.type <> 'group';

CREATE OR REPLACE VIEW `pvlng_channel_view` AS select `c`.`id` AS `id`,`c`.`guid` AS `guid`,if(`a`.`id`,`a`.`name`,`c`.`name`) AS `name`,if(`a`.`id`,`a`.`serial`,`c`.`serial`) AS `serial`,`c`.`channel` AS `channel`,if(`a`.`id`,`a`.`description`,`c`.`description`) AS `description`,if(`a`.`id`,`a`.`resolution`,`c`.`resolution`) AS `resolution`,if(`a`.`id`,`a`.`cost`,`c`.`cost`) AS `cost`,if(`a`.`id`,`a`.`numeric`,`c`.`numeric`) AS `numeric`,if(`a`.`id`,`a`.`offset`,`c`.`offset`) AS `offset`,if(`a`.`id`,`a`.`adjust`,`c`.`adjust`) AS `adjust`,if(`a`.`id`,`a`.`unit`,`c`.`unit`) AS `unit`,if(`a`.`id`,`a`.`decimals`,`c`.`decimals`) AS `decimals`,if(`a`.`id`,`a`.`meter`,`c`.`meter`) AS `meter`,if(`a`.`id`,`a`.`threshold`,`c`.`threshold`) AS `threshold`,if(`a`.`id`,`a`.`valid_from`,`c`.`valid_from`) AS `valid_from`,if(`a`.`id`,`a`.`valid_to`,`c`.`valid_to`) AS `valid_to`,if(`a`.`id`,`a`.`public`,`c`.`public`) AS `public`,`t`.`id` AS `type_id`,`t`.`name` AS `type`,`t`.`model` AS `model`,`t`.`childs` AS `childs`,if(`ta`.`id`,`ta`.`read`,`t`.`read`) AS `read`,`t`.`write` AS `write`,if(`ta`.`id`,`ta`.`graph`,`t`.`graph`) AS `graph`,if(`a`.`id`,`a`.`icon`,`c`.`icon`) AS `icon` from ((((`pvlng_channel` `c` join `pvlng_type` `t` on((`c`.`type` = `t`.`id`))) left join `pvlng_tree` `tr` on((`c`.`channel` = `tr`.`guid`))) left join `pvlng_channel` `a` on((`tr`.`entity` = `a`.`id`))) left join `pvlng_type` `ta` on((`a`.`type` = `ta`.`id`))) where (`c`.`id` <> 1);
CREATE OR REPLACE VIEW `pvlng_tree_view` AS select `n`.`id` AS `id`,`n`.`entity` AS `entity`,if(`t`.`childs`,`n`.`guid`,`c`.`guid`) AS `guid`,if(`co`.`id`,`co`.`name`,`c`.`name`) AS `name`,if(`co`.`id`,`co`.`serial`,`c`.`serial`) AS `serial`,`c`.`channel` AS `channel`,if(`co`.`id`,`co`.`description`,`c`.`description`) AS `description`,if(`co`.`id`,`co`.`resolution`,`c`.`resolution`) AS `resolution`,if(`co`.`id`,`co`.`cost`,`c`.`cost`) AS `cost`,if(`co`.`id`,`co`.`meter`,`c`.`meter`) AS `meter`,if(`co`.`id`,`co`.`numeric`,`c`.`numeric`) AS `numeric`,if(`co`.`id`,`co`.`offset`,`c`.`offset`) AS `offset`,if(`co`.`id`,`co`.`adjust`,`c`.`adjust`) AS `adjust`,if(`co`.`id`,`co`.`unit`,`c`.`unit`) AS `unit`,if(`co`.`id`,`co`.`decimals`,`c`.`decimals`) AS `decimals`,if(`co`.`id`,`co`.`threshold`,`c`.`threshold`) AS `threshold`,if(`co`.`id`,`co`.`valid_from`,`c`.`valid_from`) AS `valid_from`,if(`co`.`id`,`co`.`valid_to`,`c`.`valid_to`) AS `valid_to`,if(`co`.`id`,`co`.`public`,`c`.`public`) AS `public`,if(`co`.`id`,`co`.`extra`,`c`.`extra`) AS `extra`,if(`co`.`id`,`co`.`comment`,`c`.`comment`) AS `comment`,`t`.`id` AS `type_id`,`t`.`name` AS `type`,`t`.`model` AS `model`,`t`.`childs` AS `childs`,`t`.`read` AS `read`,`t`.`write` AS `write`,`t`.`graph` AS `graph`,if(`co`.`id`,`co`.`icon`,`c`.`icon`) AS `icon`,`ca`.`id` AS `alias`,`ta`.`id` AS `alias_of`,(((count(0) - 1) + (`n`.`lft` > 1)) + 1) AS `level`,round((((`n`.`rgt` - `n`.`lft`) - 1) / 2),0) AS `haschilds`,((((min(`p`.`rgt`) - `n`.`rgt`) - (`n`.`lft` > 1)) / 2) > 0) AS `lower`,((`n`.`lft` - max(`p`.`lft`)) > 1) AS `upper` from ((((((`pvlng_tree` `n` join `pvlng_tree` `p`) join `pvlng_channel` `c` on((`n`.`entity` = `c`.`id`))) join `pvlng_type` `t` on((`c`.`type` = `t`.`id`))) left join `pvlng_channel` `ca` on(((if(`t`.`childs`,`n`.`guid`,`c`.`guid`) = `ca`.`channel`) and (`ca`.`type` = 0)))) left join `pvlng_tree` `ta` on((`c`.`channel` = `ta`.`guid`))) left join `pvlng_channel` `co` on(((`ta`.`entity` = `co`.`id`) and (`c`.`type` = 0)))) where ((`n`.`lft` between `p`.`lft` and `p`.`rgt`) and ((`p`.`id` <> `n`.`id`) or (`n`.`lft` = 1))) group by `n`.`id` order by `n`.`lft`;

UPDATE `pvlng_babelkit` SET `code_code` = 'Dashboard_extra' WHERE `code_set` = 'model' AND `code_code` = 'Dashboard_colors';
UPDATE `pvlng_babelkit` SET `code_code` = 'Dashboard_extraHint' WHERE `code_set` = 'model' AND `code_code` = 'Dashboard_colorsHint';

REPLACE INTO `pvlng_babelkit` (`code_set`, `code_lang`, `code_code`, `code_desc`, `code_order`) VALUES
('app', 'de', 'ShowGUID', 'Kanal-GUID anzeigen', 0),
('app', 'en', 'ShowGUID', 'Show channel GUID', 0),
('app', 'de', 'TariffsHint', 'Tages- oder tageszeitabhängige Tarife', 0),
('app', 'en', 'TariffsHint', 'Day or day time based tariffs', 0),
('app', 'de', 'ReadWritableEntity', 'Schreib- und lesbarer Kanal', 0),
('app', 'en', 'ReadWritableEntity', 'Writable and readable channel', 0),
('model', 'de', 'Estimate_extra', 'Erwartungswerte', 0),
('model', 'de', 'Estimate_extraHint', 'Definiere die Erwartungswerte in [b]kWh[/b] auf Monats- oder Tagesbasis.\r\n\r\nWenn nur Monatswerte zur Verfügung stehen (z.B. von [url=http://re.jrc.ec.europa.eu/pvgis/apps4/pvest.php]PVGIS[/url]) werden diese als Werte des 15. des Monats verwendet und die anderen Tageswerte linear interpoliert.\r\n[list][*]Monat: [font=courier]Monat:Wert[/font]\r\n[*]Tag: [font=courier]Monat-Tag:Wert[/font][/list]\r\nBeispiel für einen Januar, 4,5kWh pro Tag\r\n[list][*]Monat: [font=courier]1:4.5[/font]\r\n[*]Tag (1. Januar): [font=courier]01-01:4.5[/font][/list]', 0),
('model', 'en', 'Estimate_extra', 'Estimates', 0),
('model', 'en', 'Estimate_extraHint', 'Define your estimates in [b]kilo watt hours[/b] on monthly or daily base.\r\n\r\nIf only monthly data exists (e.g from [url=http://re.jrc.ec.europa.eu/pvgis/apps4/pvest.php]PVGIS[/url]) the values will be used as of the 15th of the month and the other values will be linear interpolated to get daily values.\r\n[list][*]Monthly: [font=courier]month:value[/font]\r\n[*]Daily: [font=courier]month-day:value[/font][/list]\r\nExample for a january, 4.5kWh each day\r\n[list][*]Monthly: [font=courier]1:4.5[/font]\r\n[*]Daily (1st of january): [font=courier]01-01:4.5[/font][/list]', 0),
('app', 'en', 'ScatterCandidate', 'This channel have no unit, may be \"Scatter\" could be a good presentation.', 0),
('app', 'de', 'ChannelsLoaded', 'Kanäle geladen', 0),
('app', 'en', 'ChannelsLoaded', 'channels loaded', 0),
('app', 'de', 'AcceptChild', '1:\"%2$s\" akzeptiert nur einen Sub-Kanal!||\r\nn:\"%2$s\" akzeptiert nur %1$d Sub-Kanäle!', 0),
('app', 'en', 'AcceptChild', '1:\"%2$s\" accepts only one child at all!||\r\nn:\"%2$s\" accepts only %1$d childs at all!', 0),
('app', 'de', 'ScatterCandidate', 'Dieser Kanal hat keine Einheit, die Darstellung als \"Punkte\" könnte am geeignetsten sein.', 0),
('model', 'de', 'Daylight_extra', 'Einstrahlungssensor', 0),
('model', 'en', 'Daylight_extra', 'Irradiation sensor', 0),
('model', 'de', 'Daylight_extraHint', 'Wenn eine Kurve gezeichnet werden soll, muss hier ein Einstrahlungssensors angegeben werden.\r\nDie Kurve wird dann anhand des Durchschnittes der Eintrahlungs-Maximalwerte der letzen 5 Tage errechnet.', 0),
('model', 'de', 'Daylight_IrradiationIsRequired', 'Für die Darstellung als Kurve ist ein Einstrahlungssensor-Kanal erforderlich', 0),
('model', 'en', 'Daylight_IrradiationIsRequired', 'To display a curve, a irradiation sensor channel is required', 0),
('app', 'de', 'Curve', 'Kurve', 0),
('app', 'en', 'Curve', 'Curve', 0),
('model', 'en', 'Daylight_extraHint', 'If a curve should displayed, an irradiation sensor must here be provided.\r\nThe curve will then calulated by the average of the max. irradiation values of the last 5 days.', 0),
('app', 'de', 'NoChartMatch', 'Kein Diagramm gefunden', 0),
('channel', 'de', 'NoValidGUID', 'Kein gültiges GUID-Format', 0),
('channel', 'en', 'NoValidGUID', 'No valid GUID format', 0),
('channel', 'de', 'NoChannelForGUID', 'Es existiert kein Kanal mit dieser GUID', 0),
('channel', 'en', 'NoChannelForGUID', 'No channel exists with this GUID', 0),
('app', 'de', 'SelectChart', 'Diagramm auswählen', 0),
('app', 'en', 'SelectChart', 'Select chart', 0),
('app', 'en', 'NoChartMatch', 'No chart match', 0),
('app', 'de', 'NoChannelMatch', 'Kein Kanal enthält', 0),
('app', 'en', 'NoChannelMatch', 'No channel match', 0),
('model', 'de', 'Dashboard_extra', 'Farbbänder', 0),
('model', 'de', 'Dashboard_extraHint', 'Definiere hier die Farbbänder für die Achse. ([url=http://pvlng.com/Dashboard_module#Channel_definition]Anleitung[/url])', 0),
('model', 'en', 'Dashboard_extra', 'Color bands', 0),
('model', 'en', 'Dashboard_extraHint', 'Define here the color bands for the axis. ([url=http://pvlng.com/Dashboard_module#Channel_definition]Instructions[/url])', 0),
('app', 'de', 'LoginToken', 'Permanentes Login-Token, nur für diese Computer-IP!', 0),
('app', 'en', 'LoginToken', 'Permanent login token, for this computer IP only!', 0),
('app', 'de', 'Required', 'erforderlich', 0),
('app', 'en', 'Required', 'required', 0),
('app', 'de', 'Legend', 'Legende', 0),
('app', 'en', 'Legend', 'Legend', 0),
('app', 'de', 'ChangeTypeHint', 'Der Kanaltyp kann nur zu einem mit den gleichen Eigenschaften geändert werden (Anzahl Kind-Kanäle, lesen/schreiben)', 0),
('app', 'en', 'ChangeTypeHint', 'The channel type can only be changed to one with the same attributes (sub channel count, read/write)', 0),
('app', 'de', 'ChangeType', 'Kanaltyp', 0),
('app', 'en', 'ChangeType', 'Channel type', 0),
('app', 'de', 'Change', 'Ändern', 0),
('app', 'en', 'Change', 'Change', 0),
('app', 'de', 'DashboardHint', 'Schnellübersichten mit Gauges', 0),
('app', 'en', 'DashboardHint', 'Quick overviews with gauges', 0),
('app', 'en', 'Dashboards', 'Dashboards', 0),
('app', 'de', 'Dashboards', 'Dashboards', 0),
('app', 'de', 'DashboardIntro', 'Bitte wähle die Kanäle zur Anzeige aus.\r\n\r\nWenn die Tabelle unten leer ist, hast Du noch keine Kanäle vom Typ \"Dashboard channel\" definiert.', 0),
('app', 'en', 'DashboardIntro', 'Please select your channels to display.\r\n\r\nIf the table below is empty, you have not defined channels of type \"Dashboard channel\" yet.', 0),
('app', 'de', 'CreateTariff', 'Tarif erstellen', 0),
('app', 'de', 'CreateChannel', 'Kanal erstellen', 0),
('app', 'en', 'CreateTariff', 'Create tariff', 0),
('app', 'en', 'CreateChannel', 'Create channel', 0),
('app', 'de', 'CreateDashboardChannel', 'Dashboard-Kanal erstellen', 0),
('app', 'en', 'CreateDashboardChannel', 'Create Dashboard channel', 0),
('app', 'de', 'DragRowsToReorder', 'Ziehe die Zeilen um die Reihenfolge zu ändern', 0),
('app', 'en', 'DragRowsToReorder', 'Drag rows to change channel order', 0),
('app', 'de', 'FixCostPerDay', 'Fixe Kosten pro Tag', 0),
('app', 'en', 'FixCostPerDay', 'Fix cost per day', 0),
('app', 'de', 'ClickToDeleteRow', 'Zeile löschen', 0),
('app', 'en', 'ClickToDeleteRow', 'Delete row', 0),
('app', 'de', 'FixCostDay', 'Feste Kosten pro Tag', 0),
('app', 'en', 'FixCostDay', 'Fixed cost per day', 0),
('app', 'en', 'RemoveTariffIfUsed', 'If the tariff is used in a channel, it will be removed there.', 0),
('app', 'de', 'RemoveTariffIfUsed', 'Wenn der Tarif in einem Kanal benutzt wird, wird er dort entfernt.', 0),
('app', 'de', 'DeleteTariffDate', 'Daten für dieses Startdatum löschen', 0),
('app', 'en', 'DeleteTariffDate', 'Delete data for this start date', 0),
('app', 'de', 'CloneTariffDate', 'Zeiten für diese Startzeit kopieren', 0),
('app', 'en', 'CloneTariffDate', 'Clone data for this start date', 0),
('app', 'de', 'CopyDates', 'Zeiten kopieren', 0),
('app', 'de', 'EditTariffDate', 'Tarif-Zeitscheibe ändern', 0),
('app', 'de', 'DeleteTariff', 'Tarif löschen', 0),
('app', 'en', 'DeleteTariff', 'Delete tariff', 0),
('app', 'de', 'CloneTariff', 'Tarif kopieren', 0),
('app', 'en', 'CloneTariff', 'Clone tariff', 0),
('app', 'de', 'EditTariff', 'Tarif-Stammdaten ändern', 0),
('app', 'en', 'EditTariff', 'Edit tariff master data', 0),
('app', 'de', 'AddTariffDate', 'Neuen Startdatumsbereich anlegen', 0),
('app', 'en', 'AddTariffDate', 'Add new start date data', 0),
('channel', 'de', 'tariffHint', 'Wenn Du verschiedene Tarife über den Tag/Woche hast, ordne hier einen [url=/tariff]entsprechenden Tarif[/url] zu. (Für konstante Beträge benutze das Kosten-Attribut für bessere Performanz)\r\nWenn ein Tarif zugeordnet ist wird das Kosten-Attribut übersteuert!', 0),
('channel', 'en', 'tariffHint', 'If you have different costs over day/week for this channel, assign an [url=/tariff]appropriate tariff[/url] here. (For constant costs use the cost attribute for better performance)\r\nIf a tariff is assigned, it will overrule a cost value!', 0),
('channel', 'de', 'tariff', 'Tarif', 0),
('channel', 'en', 'tariff', 'Tariff', 0),
('app', 'de', 'CopyOf', 'Kopie von', 0),
('app', 'en', 'CopyOf', 'Copy of', 0),
('app', 'de', 'Confirm', 'Bestätigen', 0),
('app', 'en', 'Confirm', 'Confirm', 0),
('app', 'de', 'AreYouSure', 'Bist Du sicher?!', 0),
('app', 'en', 'AreYouSure', 'Are you sure?!', 0),
('app', 'de', 'TariffThisWeek', 'Tarife diese Woche', 0),
('app', 'en', 'TariffThisWeek', 'Tariffs this week', 0),
('app', 'de', 'EndTime', 'Endezeit', 0),
('app', 'en', 'EndTime', 'End time', 0),
('app', 'de', 'Date', 'Datum', 0),
('app', 'en', 'Date', 'Date', 0),
('app', 'de', 'TariffDatesCopied', 'Tarif-Zeitbereiche wurden kopiert', 0),
('app', 'en', 'TariffDatesCopied', 'Tariff dates was copied', 0),
('app', 'de', 'TariffCreated', 'Tarif wurde angelegt', 0),
('app', 'en', 'TariffCreated', 'Tariff was created', 0),
('app', 'en', 'EditTariffDate', 'Edit tariff date time set', 0),
('app', 'en', 'CopyDates', 'Copy date records', 0),
('app', 'de', 'Tariffs', 'Tarife', 0),
('app', 'en', 'Tariffs', 'Tariffs', 0),
('app', 'de', 'StartingTimes', 'Startzeitpunkte', 0),
('app', 'en', 'StartingTimes', 'Starting times', 0),
('app', 'de', 'Comment', 'Kommentar', 0),
('app', 'en', 'Comment', 'Comment', 0),
('app', 'de', 'TimeDaysTariffRequired', 'Nur Zeilen mit einer Startzeit, mindestens einem Wochentag und einem Tarif werden als gültig betrachtet.', 0),
('app', 'en', 'TimeDaysTariffRequired', 'Only rows with a start time, at least one weekday and a tariff will be valid.', 0),
('app', 'de', 'Copy', 'Kopieren', 0),
('app', 'en', 'Copy', 'Copy', 0),
('app', 'de', 'Show', 'Anzeigen', 0),
('app', 'en', 'Show', 'Show', 0),
('app', 'de', 'Weekdays', 'Wochentage', 0),
('app', 'en', 'Weekdays', 'Weekdays', 0),
('app', 'de', 'StartTime', 'Startzeit', 0),
('app', 'en', 'StartTime', 'Start time', 0),
('app', 'de', 'Tariff', 'Tarif', 0),
('app', 'en', 'Tariff', 'Tariff', 0),
('app', 'de', 'NewStartDate', 'Neues Startdatum', 0),
('app', 'en', 'NewStartDate', 'New start date', 0),
('app', 'de', 'copyTo', 'nach', 0),
('app', 'en', 'copyTo', 'to', 0),
('app', 'de', 'StartDate', 'Startdatum', 0),
('app', 'en', 'StartDate', 'Start date', 0),
('model', 'de', 'AliasHelp', 'Ein Alias verhält sich genau so wie seine originale Gruppe', 0),
('model', 'en', 'AliasHelp', 'An alias act in the same way as its original channel group', 0),
('model', 'de', 'Selector_thresholdHint', 'Nur Werte oberhalb des Schwellwertes bewirken die Ausgabe der Werte des zweiten Kind-Kanals', 0),
('model', 'en', 'Selector_thresholdHint', 'Only values above the threshold trigger the output of the second sub channel', 0),
('model', 'de', 'SelectorHelp', 'Der erste Kind-Kanal ist der selektierende Kanal, Werte unterhalb des Grenzwertes setzen den Output auf 0, Werte darüber geben den Wert des zweiten Kind-Kanals aus. Der zweite Kind-Kanal ist der Datenkanal, seine Werte Werte werden in Abhängigkeit des ersten Kind-Kanals ausgegeben oder nicht.', 0),
('model', 'en', 'SelectorHelp', 'The first sub channel is the selective channel, values below threshold set the output to 0, values above do just pass the value of the second sub channel through. Second sub channel is the data channel, its values are passed through or not based on the first sub channel.', 0),
('model', 'de', 'Selector', 'Gibt Werte in Abhängigkeit des ersten Kind-Kanals aus', 0),
('model', 'en', 'Selector', 'Calculates the output in dependence of first sub channel', 0),
('app', 'de', 'ClearSearch', 'Suchbegriff löschen', 0),
('app', 'de', 'Information', 'Information', 0),
('app', 'de', 'DragDropHelp', '- Ziehe eine Gruppe oder Kanal hierher für oberste Ebene\r\n- Benutze Strg-Klick um Kanäle zu kopieren\r\n- Gruppen können nicht kopiert werden, erstelle einen Alias und nutze diesen', 0),
('app', 'de', 'CantCopyGroups', 'Du kannst keine Gruppen kopieren!\r\nErstelle bitte einen Alias für diese und nutze ihn.', 0),
('app', 'en', 'ClearSearch', 'Clear search term', 0),
('app', 'en', 'DragDropHelp', '- Drag a group or channel here for append to top level\r\n- Use Ctrl+Click to start copy of channel\r\n- You can\'t copy groups, create an alias and use this instead', 0),
('app', 'en', 'CantCopyGroups', 'You can\'t copy groups!\r\nCreate an alias and use this instead.', 0),
('model', 'de', 'Accumulator', 'Summiert die Messwerte aller Sub-Kanäle für den gleichen Zeitpunkt und ignoriert alle Datensätze, wo mindestens ein Wert pro Zeitpunkt fehlt.', 0),
('model', 'de', 'AccumulatorFull', 'Summiert die Messwerte aller Sub-Kanäle für den gleichen Zeitpunkt, summiert die Werte auch, wenn ein Wert für einen Zeitpunkt fehlt.', 0),
('model', 'en', 'Accumulator', 'Build the sum of readings of all child channels for same timestamp and ignores data sets, where at least one for a timestamp ist missing.', 0),
('model', 'en', 'AccumulatorFull', 'Build the sum of readings of all child channels for same timestamp, works for all timestamps, also if one data set is missing.', 0),
('app', 'de', 'SeeAPIReference', 'Für mehr Informationen, siehe in die [url=http://pvlng.com/API]API-Referenz[/url].', 0),
('app', 'en', 'SeeAPIReference', 'For more information take a look into the [url=http://pvlng.com/API]API reference[/url].', 0),
('app', 'de', 'ClickAndPressCtrlC', 'Klicke und drücke Strg+C zum kopieren', 0),
('app', 'en', 'ClickAndPressCtrlC', 'Click and press Ctrl+C to copy', 0),
('app', 'de', 'DuringDaylight', 'Nur zwischen Sonnenauf- und untergang', 0),
('app', 'en', 'DuringDaylight', 'Between sunrise and sunset only', 0),
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
('preset', 'de', '1i', '1 Minute', 0),
('preset', 'en', '1i', '1 Minute', 101),
('app', 'de', 'UnsavedChanges', 'Du hast ungesicherte Änderungen für Dein Diagramm', 0),
('app', 'en', 'UnsavedChanges', 'You have unsaved changes for your chart', 0),
('app', 'de', 'DeleteReadingConfirm', 'Willst Du diesen Messwert wirklich löschen?!', 0),
('app', 'en', 'DeleteReadingConfirm', 'Do you really want delete this reading value?!', 0),
('app', 'de', 'ReadingDeleted', 'Messwert wurde gelöscht', 0),
('app', 'en', 'ReadingDeleted', 'Reading data deleted', 0),
('app', 'de', 'UseNegativeColor', 'Farbe für Werte unterhalb Grenzwert', 0),
('app', 'en', 'UseNegativeColor', 'Color for values below threshold', 0),
('app', 'de', 'below', 'unter', 0),
('app', 'en', 'below', 'below', 0),
('app', 'de', 'LineLongDashDotDot', 'Strich-Punkt-Punkt', 0),
('app', 'en', 'LineLongDashDotDot', 'dash-dot-dot', 0),
('app', 'de', 'LinesDashed', 'getrichelt', 0),
('app', 'de', 'LinesDotted', 'gepunktet', 0),
('app', 'de', 'LinesDashedDottedDotted', 'Strich-Punkt-Punkt', 0),
('app', 'en', 'LinesDashedDottedDotted', 'dash-dot-dot', 0),
('app', 'de', 'LinesDashedDotted', 'Strich-Punkt', 0),
('app', 'en', 'LinesDashedDotted', 'dash-dot', 0),
('app', 'en', 'LinesDotted', 'Dotted', 0),
('app', 'en', 'LinesDashed', 'Dashed', 0),
('app', 'de', 'LineLongDash', 'gestrichelt lang', 0),
('app', 'en', 'LineLongDash', 'dashed long', 0),
('app', 'de', 'LineShortDashDotDot', 'Strich-Punkt-Punkt kurz', 0);
