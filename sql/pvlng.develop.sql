--
-- For development branch only!
--

UPDATE `pvlng_channel` SET `type` = 41 WHERE `type` = 40;

SELECT COUNT(1) INTO @C FROM `pvlng_type` WHERE `id` = 40;
IF @C > 0 THEN
  DELETE FROM `pvlng_type` WHERE `id` = 40;
END IF;

REPLACE INTO `pvlng_type` (`id`, `name`, `description`, `model`, `unit`, `type`, `childs`, `read`, `write`, `graph`, `icon`) VALUES
(6,  'Inverter string',  'model::Group',  'Channel',  '',  'group',  -1,  0,  0,  0,  '/images/ico/solar-panel.png');
