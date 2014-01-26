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

DROP PROCEDURE `pvlng_changed`; -- 0.000 s

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
