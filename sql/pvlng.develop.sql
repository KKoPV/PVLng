--
-- For development branch only!
--

DELIMITER ;;

CREATE PROCEDURE `_update_2_12_0`()
BEGIN
  UPDATE `pvlng_channel` SET `type` = 41 WHERE `type` = 40;
  SELECT COUNT(1) INTO @C FROM `pvlng_type` WHERE `id` = 40;
  IF @C > 0 THEN
    DELETE FROM `pvlng_type` WHERE `id` = 40;
  END IF;
END;;

DELIMITER ;

CALL `_update_2_12_0`();

DROP PROCEDURE `_update_2_12_0`;
