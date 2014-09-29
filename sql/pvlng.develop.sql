--
-- For development branch only!
--

ALTER TABLE `pvlng_view` ADD INDEX `public` (`public`);

INSERT INTO `pvlng_type` (`id`, `name`, `description`, `model`, `unit`, `type`, `childs`, `read`, `write`, `graph`, `icon`) VALUES
(6, 'Inverter string', 'model::Group', 'Channel', '', 'group', -1, 0, 0, 0, '/images/ico/solar-panel.png'),
(7, 'Solar Edge Plant', 'model::SolarEdgeInverter', 'SE\\Inverter', '', 'group', -1, 0, 1, 0, '/images/ico/solar_edge.png'),
(74, 'Irradiation forecast', 'model::ClearSky', 'IrradiationForecast', 'W/m²', 'sensor', 0, 1, 1, 1, '/images/ico/brightness.png');

-- ------------------------------------------------
-- Aliases must get their own GUIDs in hierarchy

DROP TRIGGER `pvlng_tree_bi`;
DELIMITER ;;
CREATE TRIGGER `pvlng_tree_bi` BEFORE INSERT ON `pvlng_tree` FOR EACH ROW
BEGIN
  SELECT `e`.`type`, `t`.`childs`, `t`.`read`+`t`.`write`
    INTO @TYPE, @CHILDS, @RW
    FROM `pvlng_channel` `e`
    JOIN `pvlng_type` `t` ON `e`.`type` = `t`.`id`
   WHERE `e`.`id` = new.`entity`;
   -- Aliases get always an own GUID
   IF @TYPE = 0 OR (@CHILDS != 0 AND @RW > 0) THEN
     SET new.`guid` = GUID();
   END IF;
END;;
DELIMITER ;

UPDATE `pvlng_tree` h, `pvlng_channel` c
   SET h.`guid` = GUID()
 WHERE h.`entity` = c.`id` and c.`type` = 0 AND h.`guid` IS NULL;

--
-- ------------------------------------------------

DELIMITER ;;
CREATE FUNCTION `pvlng_id` () RETURNS int
BEGIN
  SELECT `value` INTO @ID FROM `pvlng_config` WHERE `key` = 'Installation';
  IF @ID IS NULL THEN
    -- Range of 100000 .. 999999
    SELECT 100000 + ROUND(RAND()*900000) INTO @ID;
    INSERT INTO `pvlng_config` (`key`, `value`, `comment`, `type`)
       VALUES ('Installation', @ID, 'Unique PVLng installation Id', 'num');
  END IF;
  RETURN @ID;
END;;
DELIMITER ;

CREATE OR REPLACE VIEW `pvlng_performance_view` AS
select `aggregation`,`action`,unix_timestamp(concat(`year`,'-',`month`,'-',`day`,' ',`hour`)) AS `timestamp`,`average` from `pvlng_performance_avg`;
