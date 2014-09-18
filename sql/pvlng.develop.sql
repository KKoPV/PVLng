--
-- For development branch only!
--

INSERT INTO `pvlng_type`
(`id`, `name`, `description`, `model`, `type`, `childs`, `read`, `write`, `graph`, `icon`)
VALUES
(7, 'Solar Edge Plant', 'model::SolarEdgeInverter', 'SE\\Inverter', 1, -1, 0, 1, 0, '/images/ico/solar_edge.png');

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
