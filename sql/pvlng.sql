--
-- PVLng version
--

/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pvlng_babelkit` (
  `code_set` varchar(16) NOT NULL,
  `code_lang` varchar(5) NOT NULL,
  `code_code` varchar(32) NOT NULL,
  `code_desc` text NOT NULL,
  `code_order` smallint(6) NOT NULL DEFAULT '0',
  `code_flag` char(1) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `changed` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`code_set`,`code_lang`,`code_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 PACK_KEYS=1 COMMENT='I18N';
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pvlng_channel` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Unique Id',
  `guid` varchar(39) DEFAULT NULL COMMENT 'Unique GUID',
  `name` varchar(255) NOT NULL COMMENT 'Unique identifier',
  `description` varchar(255) NOT NULL COMMENT 'Longer description',
  `serial` varchar(30) NOT NULL,
  `channel` varchar(255) NOT NULL,
  `type` int(10) unsigned NOT NULL COMMENT 'pvlng_type -> id',
  `resolution` double NOT NULL DEFAULT '1',
  `unit` varchar(10) NOT NULL,
  `decimals` tinyint(1) unsigned NOT NULL DEFAULT '2',
  `meter` tinyint(1) unsigned NOT NULL,
  `numeric` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `offset` double NOT NULL,
  `adjust` tinyint(1) unsigned NOT NULL COMMENT 'allow auto adjustment of offset',
  `cost` double NOT NULL COMMENT 'per unit or unit * h',
  `threshold` double unsigned DEFAULT NULL,
  `valid_from` double DEFAULT NULL COMMENT 'Numeric min. acceptable value',
  `valid_to` double DEFAULT NULL COMMENT 'Numeric max. acceptable value',
  `public` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT 'Public channels don''t need API key to read',
  `comment` text NOT NULL COMMENT 'Internal comment',
  PRIMARY KEY (`id`),
  UNIQUE KEY `Name-Description-Type` (`name`,`description`,`type`),
  UNIQUE KEY `GUID` (`guid`),
  KEY `type` (`type`),
  CONSTRAINT `pvlng_channel_ibfk_2` FOREIGN KEY (`type`) REFERENCES `pvlng_type` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 PACK_KEYS=1 COMMENT='The channels defined';
/*!40101 SET character_set_client = @saved_cs_client */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 */ /*!50003 TRIGGER `pvlng_channel_bi` BEFORE INSERT ON `pvlng_channel` FOR EACH ROW
BEGIN
  SELECT `childs` INTO @CHILDS FROM `pvlng_type`
   WHERE `id` = new.`type` LIMIT 1;
  IF @CHILDS = 0 THEN SET new.`guid` = GUID(); END IF;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 */ /*!50003 TRIGGER `pvlng_channel_bd` BEFORE DELETE ON `pvlng_channel` FOR EACH ROW
BEGIN
  -- Check if channel is still in tree
  SELECT COUNT(0) INTO @COUNT FROM `pvlng_tree` WHERE `entity` = old.`id`;

  IF @COUNT > 0 THEN
    SIGNAL SQLSTATE '99999' SET MESSAGE_TEXT = 'ChannelStillInTree';
  END IF;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 */ /*!50003 TRIGGER `pvlng_channel_ad` AFTER DELETE ON `pvlng_channel` FOR EACH ROW
BEGIN

  DELETE FROM `pvlng_reading_num` WHERE `id` = old.`id`;
  DELETE FROM `pvlng_reading_str` WHERE `id` = old.`id`;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `pvlng_channel_view` (
  `id` tinyint NOT NULL,
  `guid` tinyint NOT NULL,
  `name` tinyint NOT NULL,
  `serial` tinyint NOT NULL,
  `channel` tinyint NOT NULL,
  `description` tinyint NOT NULL,
  `resolution` tinyint NOT NULL,
  `cost` tinyint NOT NULL,
  `numeric` tinyint NOT NULL,
  `offset` tinyint NOT NULL,
  `adjust` tinyint NOT NULL,
  `unit` tinyint NOT NULL,
  `decimals` tinyint NOT NULL,
  `meter` tinyint NOT NULL,
  `threshold` tinyint NOT NULL,
  `valid_from` tinyint NOT NULL,
  `valid_to` tinyint NOT NULL,
  `public` tinyint NOT NULL,
  `type_id` tinyint NOT NULL,
  `type` tinyint NOT NULL,
  `model` tinyint NOT NULL,
  `childs` tinyint NOT NULL,
  `read` tinyint NOT NULL,
  `write` tinyint NOT NULL,
  `graph` tinyint NOT NULL,
  `icon` tinyint NOT NULL
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pvlng_config` (
  `key` varchar(50) NOT NULL,
  `value` varchar(1000) NOT NULL,
  `comment` varchar(255) NOT NULL,
  `type` enum('','str','num','bool') NOT NULL DEFAULT '',
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 PACK_KEYS=1 COMMENT='Application settings';
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pvlng_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `timestamp` datetime NOT NULL,
  `scope` varchar(40) NOT NULL,
  `data` text,
  PRIMARY KEY (`id`),
  KEY `timestamp` (`timestamp`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Logging messages';
/*!40101 SET character_set_client = @saved_cs_client */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 */ /*!50003 TRIGGER `pvlng_log_bi` BEFORE INSERT ON `pvlng_log` FOR EACH ROW
SET new.`timestamp` = NOW() */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pvlng_options` (
  `key` varchar(50) NOT NULL,
  `value` varchar(1000) NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 PACK_KEYS=1 COMMENT='Key-Value-Store';
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pvlng_performance` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `action` enum('read','write') NOT NULL,
  `time` int(10) unsigned NOT NULL COMMENT 'ms',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Gather system performance';
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pvlng_performance_avg` (
  `aggregation` enum('hour','day','month','year','overall') NOT NULL,
  `action` enum('read','write') NOT NULL,
  `year` year(4) NOT NULL,
  `month` smallint(2) unsigned NOT NULL,
  `day` smallint(2) unsigned NOT NULL,
  `hour` smallint(2) unsigned NOT NULL,
  `average` int(10) unsigned NOT NULL COMMENT 'ms',
  `count` int(10) unsigned NOT NULL,
  PRIMARY KEY (`aggregation`,`action`,`year`,`month`,`day`,`hour`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `pvlng_performance_view` (
  `aggregation` tinyint NOT NULL,
  `action` tinyint NOT NULL,
  `timestamp` tinyint NOT NULL,
  `average` tinyint NOT NULL
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pvlng_reading_num` (
  `id` int(10) unsigned NOT NULL COMMENT 'pvlng_channel -> id',
  `timestamp` int(10) unsigned NOT NULL,
  `data` decimal(13,4) NOT NULL,
  PRIMARY KEY (`id`,`timestamp`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Numeric readings'
/*!50100 PARTITION BY LINEAR KEY (id)
PARTITIONS 10 */;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 */ /*!50003 TRIGGER `pvlng_reading_num_bi` BEFORE INSERT ON `pvlng_reading_num` FOR EACH ROW
CALL getTimestamp(new.`timestamp`) */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pvlng_reading_num_tmp` (
  `id` int(10) unsigned NOT NULL,
  `timestamp` int(10) unsigned NOT NULL,
  `data` decimal(13,4) NOT NULL,
  PRIMARY KEY (`id`,`timestamp`)
) ENGINE=MEMORY DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pvlng_reading_str` (
  `id` int(10) unsigned NOT NULL COMMENT 'pvlng_channel -> id',
  `timestamp` int(10) unsigned NOT NULL,
  `data` varchar(50) NOT NULL,
  PRIMARY KEY (`id`,`timestamp`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Alphanumeric readings';
/*!40101 SET character_set_client = @saved_cs_client */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 */ /*!50003 TRIGGER `pvlng_reading_str_bi` BEFORE INSERT ON `pvlng_reading_str` FOR EACH ROW
CALL getTimestamp(new.`timestamp`) */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `pvlng_statistics` (
  `database` tinyint NOT NULL,
  `table` tinyint NOT NULL,
  `data_length` tinyint NOT NULL,
  `index_length` tinyint NOT NULL,
  `length` tinyint NOT NULL,
  `data_free` tinyint NOT NULL
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pvlng_tree` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `lft` int(10) unsigned NOT NULL,
  `rgt` int(10) unsigned NOT NULL,
  `moved` tinyint(1) unsigned NOT NULL,
  `entity` int(10) unsigned NOT NULL COMMENT 'pvlng_channel -> id',
  `guid` varchar(39) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `lft` (`lft`),
  KEY `rgt` (`rgt`),
  KEY `entity` (`entity`),
  CONSTRAINT `pvlng_tree_ibfk_2` FOREIGN KEY (`entity`) REFERENCES `pvlng_channel` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 PACK_KEYS=1 COMMENT='Structured channels';
/*!40101 SET character_set_client = @saved_cs_client */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 */ /*!50003 TRIGGER `pvlng_tree_bi` BEFORE INSERT ON `pvlng_tree` FOR EACH ROW
BEGIN
  SELECT `t`.`childs`, `t`.`read`+`t`.`write`
    INTO @CHILDS, @RW
    FROM `pvlng_channel` `e`
    JOIN `pvlng_type` `t` ON `e`.`type` = `t`.`id`
   WHERE `e`.`id` = new.`entity`;
   IF @CHILDS != 0 AND @RW > 0 THEN
     SET new.`guid` = GUID();
   END IF;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 */ /*!50003 TRIGGER `pvlng_tree_bd` BEFORE DELETE ON `pvlng_tree` FOR EACH ROW
BEGIN
  -- Remove also alias channel
  SELECT `alias` INTO @ALIAS FROM `pvlng_tree_view` WHERE `id` = old.`id`;

  IF @ALIAS IS NOT NULL THEN
    DELETE FROM `pvlng_channel` WHERE `id` = @ALIAS;
  END IF;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `pvlng_tree_view` (
  `id` tinyint NOT NULL,
  `entity` tinyint NOT NULL,
  `guid` tinyint NOT NULL,
  `name` tinyint NOT NULL,
  `serial` tinyint NOT NULL,
  `channel` tinyint NOT NULL,
  `description` tinyint NOT NULL,
  `resolution` tinyint NOT NULL,
  `cost` tinyint NOT NULL,
  `meter` tinyint NOT NULL,
  `numeric` tinyint NOT NULL,
  `offset` tinyint NOT NULL,
  `adjust` tinyint NOT NULL,
  `unit` tinyint NOT NULL,
  `decimals` tinyint NOT NULL,
  `threshold` tinyint NOT NULL,
  `valid_from` tinyint NOT NULL,
  `valid_to` tinyint NOT NULL,
  `public` tinyint NOT NULL,
  `comment` tinyint NOT NULL,
  `type` tinyint NOT NULL,
  `model` tinyint NOT NULL,
  `childs` tinyint NOT NULL,
  `read` tinyint NOT NULL,
  `write` tinyint NOT NULL,
  `graph` tinyint NOT NULL,
  `icon` tinyint NOT NULL,
  `alias` tinyint NOT NULL,
  `alias_of` tinyint NOT NULL
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pvlng_type` (
  `id` int(10) unsigned NOT NULL COMMENT 'Unique Id',
  `name` varchar(60) NOT NULL,
  `description` varchar(255) NOT NULL,
  `model` varchar(30) NOT NULL DEFAULT 'NoModel',
  `unit` varchar(10) NOT NULL,
  `childs` tinyint(1) NOT NULL,
  `read` tinyint(1) unsigned NOT NULL,
  `write` tinyint(1) unsigned NOT NULL,
  `graph` tinyint(1) unsigned NOT NULL,
  `icon` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 PACK_KEYS=1 COMMENT='Channel types';
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pvlng_view` (
  `name` varchar(50) NOT NULL COMMENT 'Variant name',
  `data` text NOT NULL COMMENT 'Serialized channel data',
  `public` tinyint(1) NOT NULL COMMENT 'Public view',
  `slug` varchar(50) NOT NULL COMMENT 'URL-save slug',
  PRIMARY KEY (`name`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 PACK_KEYS=1 COMMENT='View variants';
/*!40101 SET character_set_client = @saved_cs_client */;
/*!50001 DROP TABLE IF EXISTS `pvlng_channel_view`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE  */
/*!50013   */
/*!50001 VIEW `pvlng_channel_view` AS select `c`.`id` AS `id`,`c`.`guid` AS `guid`,`c`.`name` AS `name`,`c`.`serial` AS `serial`,`c`.`channel` AS `channel`,`c`.`description` AS `description`,`c`.`resolution` AS `resolution`,`c`.`cost` AS `cost`,`c`.`numeric` AS `numeric`,`c`.`offset` AS `offset`,`c`.`adjust` AS `adjust`,`c`.`unit` AS `unit`,`c`.`decimals` AS `decimals`,`c`.`meter` AS `meter`,`c`.`threshold` AS `threshold`,`c`.`valid_from` AS `valid_from`,`c`.`valid_to` AS `valid_to`,`c`.`public` AS `public`,`t`.`id` AS `type_id`,`t`.`name` AS `type`,`t`.`model` AS `model`,`t`.`childs` AS `childs`,`t`.`read` AS `read`,`t`.`write` AS `write`,`t`.`graph` AS `graph`,`t`.`icon` AS `icon` from (`pvlng_channel` `c` join `pvlng_type` `t` on((`c`.`type` = `t`.`id`))) where (`c`.`id` <> 1) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;
/*!50001 DROP TABLE IF EXISTS `pvlng_performance_view`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE  */
/*!50013   */
/*!50001 VIEW `pvlng_performance_view` AS select `pvlng_performance_avg`.`aggregation` AS `aggregation`,`pvlng_performance_avg`.`action` AS `action`,unix_timestamp(concat(`pvlng_performance_avg`.`year`,'-',`pvlng_performance_avg`.`month`,'-',`pvlng_performance_avg`.`day`,' ',`pvlng_performance_avg`.`hour`)) AS `timestamp`,`pvlng_performance_avg`.`average` AS `average` from `pvlng_performance_avg` limit 50 */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;
/*!50001 DROP TABLE IF EXISTS `pvlng_statistics`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE  */
/*!50013   */
/*!50001 VIEW `pvlng_statistics` AS select `information_schema`.`TABLES`.`TABLE_SCHEMA` AS `database`,`information_schema`.`TABLES`.`TABLE_NAME` AS `table`,`information_schema`.`TABLES`.`DATA_LENGTH` AS `data_length`,`information_schema`.`TABLES`.`INDEX_LENGTH` AS `index_length`,(`information_schema`.`TABLES`.`DATA_LENGTH` + `information_schema`.`TABLES`.`INDEX_LENGTH`) AS `length`,`information_schema`.`TABLES`.`DATA_FREE` AS `data_free` from `information_schema`.`TABLES` where ((`information_schema`.`TABLES`.`TABLE_NAME` like 'pvlng_%') and (`information_schema`.`TABLES`.`ENGINE` is not null)) group by `information_schema`.`TABLES`.`TABLE_NAME` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;
/*!50001 DROP TABLE IF EXISTS `pvlng_tree_view`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE  */
/*!50013   */
/*!50001 VIEW `pvlng_tree_view` AS select `tree`.`id` AS `id`,`tree`.`entity` AS `entity`,if(`t`.`childs`,`tree`.`guid`,`c`.`guid`) AS `guid`,if(`co`.`id`,`co`.`name`,`c`.`name`) AS `name`,if(`co`.`id`,`co`.`serial`,`c`.`serial`) AS `serial`,`c`.`channel` AS `channel`,if(`co`.`id`,`co`.`description`,`c`.`description`) AS `description`,if(`co`.`id`,`co`.`resolution`,`c`.`resolution`) AS `resolution`,if(`co`.`id`,`co`.`cost`,`c`.`cost`) AS `cost`,if(`co`.`id`,`co`.`meter`,`c`.`meter`) AS `meter`,if(`co`.`id`,`co`.`numeric`,`c`.`numeric`) AS `numeric`,if(`co`.`id`,`co`.`offset`,`c`.`offset`) AS `offset`,if(`co`.`id`,`co`.`adjust`,`c`.`adjust`) AS `adjust`,if(`co`.`id`,`co`.`unit`,`c`.`unit`) AS `unit`,if(`co`.`id`,`co`.`decimals`,`c`.`decimals`) AS `decimals`,if(`co`.`id`,`co`.`threshold`,`c`.`threshold`) AS `threshold`,if(`co`.`id`,`co`.`valid_from`,`c`.`valid_from`) AS `valid_from`,if(`co`.`id`,`co`.`valid_to`,`c`.`valid_to`) AS `valid_to`,if(`co`.`id`,`co`.`public`,`c`.`public`) AS `public`,`c`.`comment` AS `comment`,`t`.`name` AS `type`,`t`.`model` AS `model`,`t`.`childs` AS `childs`,`t`.`read` AS `read`,`t`.`write` AS `write`,`t`.`graph` AS `graph`,`t`.`icon` AS `icon`,`ca`.`id` AS `alias`,`ta`.`id` AS `alias_of` from (((((`pvlng_tree` `tree` join `pvlng_channel` `c` on((`tree`.`entity` = `c`.`id`))) join `pvlng_type` `t` on((`c`.`type` = `t`.`id`))) left join `pvlng_channel` `ca` on(((if(`t`.`childs`,`tree`.`guid`,`c`.`guid`) = `ca`.`channel`) and (`ca`.`type` = 0)))) left join `pvlng_tree` `ta` on((`c`.`channel` = `ta`.`guid`))) left join `pvlng_channel` `co` on(((`ta`.`entity` = `co`.`id`) and (`c`.`type` = 0)))) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;
/*!50106 SET @save_time_zone= @@TIME_ZONE */ ;
DELIMITER ;;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;;
/*!50003 SET character_set_client  = utf8 */ ;;
/*!50003 SET character_set_results = utf8 */ ;;
/*!50003 SET collation_connection  = utf8_general_ci */ ;;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;;
/*!50003 SET sql_mode              = '' */ ;;
/*!50003 SET @saved_time_zone      = @@time_zone */ ;;
/*!50003 SET time_zone             = 'SYSTEM' */ ;;
/*!50106 CREATE*/ /*!50117 */ /*!50106 EVENT `aggregatePerformance` ON SCHEDULE EVERY 1 HOUR STARTS '2000-01-01 00:00:00' ON COMPLETION PRESERVE ENABLE DO CALL `aggregatePerformance`() */ ;;
/*!50003 SET time_zone             = @saved_time_zone */ ;;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;;
/*!50003 SET character_set_client  = @saved_cs_client */ ;;
/*!50003 SET character_set_results = @saved_cs_results */ ;;
/*!50003 SET collation_connection  = @saved_col_connection */ ;;
DELIMITER ;
/*!50106 SET TIME_ZONE= @save_time_zone */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
CREATE  FUNCTION `getAPIkey`() RETURNS varchar(36) CHARSET utf8
BEGIN
  SELECT `value` INTO @KEY FROM `pvlng_config` WHERE `key` = 'APIKey';
  IF @KEY IS NULL THEN

    SET @KEY = UUID();
    INSERT INTO `pvlng_config` (`key`, `value`, `comment`)
         VALUES ('APIKey', @KEY, 'API key for all PUT/POST/DELETE requests');
  END IF;
  RETURN @KEY;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
CREATE  FUNCTION `GUID`() RETURNS char(39) CHARSET utf8
BEGIN
    SET @GUID = LOWER(MD5(UUID()));
    return CONCAT( SUBSTRING(@GUID, 1,4), '-', SUBSTRING(@GUID, 5,4), '-',
                   SUBSTRING(@GUID, 9,4), '-', SUBSTRING(@GUID,13,4), '-',
                   SUBSTRING(@GUID,17,4), '-', SUBSTRING(@GUID,21,4), '-',
                   SUBSTRING(@GUID,25,4), '-', SUBSTRING(@GUID,29,4) );
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
CREATE  PROCEDURE `aggregatePerformance`()
BEGIN

    -- Build average of hours over raw data
    REPLACE INTO `pvlng_performance_avg`
    SELECT 'hour'
          ,`action`
          ,YEAR(`timestamp`)
          ,MONTH(`timestamp`)
          ,DAY(`timestamp`)
          ,HOUR(`timestamp`)
          ,AVG(`time`)
          ,COUNT(*)
      FROM `pvlng_performance`
     GROUP BY `action`
             ,YEAR(`timestamp`)
             ,DAYOFYEAR(`timestamp`)
             ,HOUR(`timestamp`);

    -- Delete raw data
    TRUNCATE `pvlng_performance`;

    -- Delete hourly data older 1 month
    DELETE FROM `pvlng_performance_avg`
     WHERE `aggregation` = "hour"
       AND FROM_UNIXTIME(UNIX_TIMESTAMP(CONCAT(`year`,'-',`month`,'-',`day`))) <
           NOW() - INTERVAL 1 MONTH;

    -- Build average of days over hours data
    REPLACE INTO `pvlng_performance_avg`
    SELECT 'day'
          ,`action`
          ,`year`
          ,`month`
          ,`day`
          ,0
          ,AVG(`average`)
          ,SUM(`count`)
      FROM `pvlng_performance_avg`
     WHERE `aggregation` = "hour"
     GROUP BY `action`
             ,`year`
             ,`month`
             ,`day`;

    -- Delete daily data older 1 year
    DELETE FROM `pvlng_performance_avg`
     WHERE `aggregation` = "day"
       AND FROM_UNIXTIME(UNIX_TIMESTAMP(CONCAT(`year`,'-',`month`,'-',`day`))) <
           NOW() - INTERVAL 1 YEAR;

    -- Build average of month over days data
    REPLACE INTO `pvlng_performance_avg`
    SELECT 'month'
          ,`action`
          ,`year`
          ,`month`
          ,0
          ,0
          ,AVG(`average`)
          ,SUM(`count`)
      FROM `pvlng_performance_avg`
     WHERE `aggregation` = "day"
     GROUP BY `action`
             ,`year`
             ,`month`;

    -- Build average of years over months data
    REPLACE INTO `pvlng_performance_avg`
    SELECT 'year'
          ,`action`
          ,`year`
          ,0
          ,0
          ,0
          ,AVG(`average`)
          ,SUM(`count`)
      FROM `pvlng_performance_avg`
     WHERE `aggregation` = "month"
     GROUP BY `action`
             ,`year`;

    -- Build overall average over year data
    REPLACE INTO `pvlng_performance_avg`
    SELECT 'overall'
          ,`action`
          ,0
          ,0
          ,0
          ,0
          ,AVG(`average`)
          ,SUM(`count`)
      FROM `pvlng_performance_avg`
     WHERE `aggregation` = "year"
     GROUP BY `action`;

END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
CREATE  PROCEDURE `getTimestamp`(INOUT `timestamp` int unsigned)
BEGIN
  IF `timestamp` = 0 THEN
    SET `timestamp` = UNIX_TIMESTAMP();
  END IF;

  SELECT `value` INTO @SECONDS FROM `pvlng_config` WHERE `key` = "TimeStep";

  SET `timestamp` = `timestamp` DIV @SECONDS * @SECONDS;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

--
-- Translations and Channel types
--
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

INSERT INTO `pvlng_babelkit` VALUES ('app','de','Actions','Aktionen',0,'','0000-00-00 00:00:00'),('app','de','ActualState','Aktueller Datenstatus',0,'','0000-00-00 00:00:00'),('app','de','Add','Hinzufügen',0,'','0000-00-00 00:00:00'),('app','de','AddAnotherChild','Einen weiteren Kanal hinzufügen',0,'','0000-00-00 00:00:00'),('app','de','AddChannel','Einen Kanal zur Hierarchie hinzufügen',0,'','0000-00-00 00:00:00'),('app','de','AddChild','Sub-Kanal hinzufügen',0,'','0000-00-00 00:00:00'),('app','de','AdminAndPasswordRequired','Benutzername und Passwort sind erforderlich!',0,'','0000-00-00 00:00:00'),('app','de','Aggregation','Aggregation',0,'','0000-00-00 00:00:00'),('app','de','AliasEntity','Alias-Kanal erstellen',0,'','0000-00-00 00:00:00'),('app','de','AliasesUpdated','Der Alias-Kanal wurden ebenfalls geändert.',0,'','0000-00-00 00:00:00'),('app','de','AliasStillExists','Es existiert bereits ein Alias-Kanal.',0,'','0000-00-00 00:00:00'),('app','de','AliasStillInTree','Dieser Kanal hat einen Alias-Kanal.\r\nDieser Alias-Kanal ist noch in der Hierarchie vorhanden, entferne ihn vorher!',0,'','2013-12-27 21:43:17'),('app','de','All','Alle',0,'','0000-00-00 00:00:00'),('app','de','AllDataWillBeRemoved','Alle Daten werden gelöscht, [color=red]alle[/color] Stamm- und [color=red]alle[/color] Betriebsdaten!',0,'','0000-00-00 00:00:00'),('app','de','Amount','Summe',0,'','0000-00-00 00:00:00'),('app','de','APIkeyRegenerated','Dein API key wurde neu generiert.',0,'','0000-00-00 00:00:00'),('app','de','APIURL','API URL',0,'','0000-00-00 00:00:00'),('app','de','AssignEntity','Sub-Kanal zuordnen',0,'','0000-00-00 00:00:00'),('app','de','Author','Autor',0,'','0000-00-00 00:00:00'),('app','de','Average','Durchschnitt',0,'','0000-00-00 00:00:00'),('app','de','Axis','Achse',0,'','0000-00-00 00:00:00'),('app','de','Back','Zurück',0,'','0000-00-00 00:00:00'),('app','de','BackToTop','Zurück nach oben',0,'','0000-00-00 00:00:00'),('app','de','BasicDate','Basisdatum',0,'','0000-00-00 00:00:00'),('app','de','Bookmark','Lesezeichen',0,'','0000-00-00 00:00:00'),('app','de','Bytes','Bytes',0,'','0000-00-00 00:00:00'),('app','de','Cancel','Abbrechen',0,'','0000-00-00 00:00:00'),('app','de','CanHaveChilds','Dieser Kanaltyp kann Sub-Kanäle haben',0,'','0000-00-00 00:00:00'),('app','de','channel','Kanal',0,'','0000-00-00 00:00:00'),('app','de','ChannelAttributes','Kanal-Attribute',0,'','0000-00-00 00:00:00'),('app','de','ChannelDeleted','Der Kanal \'%s\' wurde gelöscht.',0,'','0000-00-00 00:00:00'),('app','de','ChannelHierarchy','Kanal-Hierarchie',0,'','0000-00-00 00:00:00'),('app','de','ChannelName','Kanalname',0,'','0000-00-00 00:00:00'),('app','de','Channels','Kanäle',0,'','0000-00-00 00:00:00'),('app','de','ChannelSaved','Die Kanaldaten wurden gesichert.',0,'','0000-00-00 00:00:00'),('app','de','ChannelsHint','Übersicht über alle definierten Kanäle (Shift+F4)',0,'','2013-12-22 17:12:22'),('app','de','ChannelStillInTree','Kanal \'%s\' wird noch in der Übersicht verwendet!\r\nBitte erst dort entfernen.',0,'','0000-00-00 00:00:00'),('app','de','ChannelType','Kanaltyp',0,'','0000-00-00 00:00:00'),('app','de','Chart','Diagramm',0,'','0000-00-00 00:00:00'),('app','de','ChartHint','Anzeigen der Kanal-Diagramme (Shift+F1)',0,'','2013-12-22 17:12:46'),('app','de','ChartRefreshHint','Klick oder F6: Neu lesen aller Kanaldaten\r\nShift+Klick oder F7: Neuaufbau des gesamten Diagramms',0,'','2013-12-22 17:25:01'),('app','de','Charts','Diagramme',0,'','0000-00-00 00:00:00'),('app','de','ChartSettings','Diagrammeinstellungen',0,'','0000-00-00 00:00:00'),('app','de','ChartSettingsTip','Definiere hier die Achse, den Stil, die Farbe etc.',0,'','0000-00-00 00:00:00'),('app','de','Childs','Sub-Kanäle',0,'','0000-00-00 00:00:00'),('app','de','Clear','Leeren',0,'','0000-00-00 00:00:00'),('app','de','ClickForGUID','Klicke hier um die GUID anzuzeigen',0,'','0000-00-00 00:00:00'),('app','de','CloneEntity','Kanal kopieren',0,'','0000-00-00 00:00:00'),('app','de','Close','Schließen',0,'','0000-00-00 00:00:00'),('app','de','Collapse','Zusammenklappen',0,'','0000-00-00 00:00:00'),('app','de','CollapseAll','Alles zusammenklappen',0,'','0000-00-00 00:00:00'),('app','de','Color','Farbe',0,'','0000-00-00 00:00:00'),('app','de','Commissioning','Inbetriebnahme',0,'','0000-00-00 00:00:00'),('app','de','ConfirmDeleteEntity','Löscht den Kanal und alle existierenden Messwerte.\r\n\r\nBist Du sicher?',0,'','0000-00-00 00:00:00'),('app','de','ConfirmDeleteTreeItems','Löscht den Kanal (und eventuelle Sub-Kanäle) aus dem Baum.\r\n\r\nBist Du sicher?',0,'','0000-00-00 00:00:00'),('app','de','Consumption','Verbrauch',0,'','0000-00-00 00:00:00'),('app','de','Cost','Kosten',0,'','0000-00-00 00:00:00'),('app','de','Create','Erstellen',0,'','0000-00-00 00:00:00'),('app','de','CreateChannel','Neuen Kanal erstellen',0,'','0000-00-00 00:00:00'),('app','de','DailyAverage','Tagesdurchschnitt',0,'','0000-00-00 00:00:00'),('app','de','DailyValue','Tageswerte',0,'','0000-00-00 00:00:00'),('app','de','Dashboard','Dashboard',0,'','0000-00-00 00:00:00'),('app','de','DashboardHint','Schnellübersicht mit Gauges (Shift+F2)',0,'','2013-12-22 17:13:18'),('app','de','dashStyle','Linienart',0,'','0000-00-00 00:00:00'),('app','de','Data','Daten',0,'','0000-00-00 00:00:00'),('app','de','DataArea','Datenbereich',0,'','0000-00-00 00:00:00'),('app','de','DataExtraction','Datenabfragen',0,'','0000-00-00 00:00:00'),('app','de','DataLength','Datengröße',0,'','0000-00-00 00:00:00'),('app','de','DataState','Datenstatus',0,'','0000-00-00 00:00:00'),('app','de','DataStateHint','Einige Informationen zur Aktualität der Daten',0,'','2013-12-22 17:16:10'),('app','de','DataStorage','Datenspeicherung',0,'','0000-00-00 00:00:00'),('app','de','DataType','Datentyp',0,'','0000-00-00 00:00:00'),('app','de','Day','Tag',0,'','0000-00-00 00:00:00'),('app','de','dbField','Bezeichnung',0,'','0000-00-00 00:00:00'),('app','de','dbValue','Wert',0,'','0000-00-00 00:00:00'),('app','de','Decommissioning','Außerbetriebnahme',0,'','0000-00-00 00:00:00'),('app','de','Delete','Löschen',0,'','0000-00-00 00:00:00'),('app','de','DeleteBranch','Teilbaum löschen',0,'','0000-00-00 00:00:00'),('app','de','DeleteEntity','Kanal löschen',0,'','0000-00-00 00:00:00'),('app','de','DeleteEntityChilds','Kanal und Kind-Kanäle löschen',0,'','0000-00-00 00:00:00'),('app','de','DeleteViewFailed','Löschen des Diagramms \'%s\' ist fehlgeschlagen.',0,'','0000-00-00 00:00:00'),('app','de','Delta','Delta',0,'','0000-00-00 00:00:00'),('app','de','Description','Beschreibung',0,'','0000-00-00 00:00:00'),('app','de','DontForgetUpdateAPIKey','Vergiss nicht Deinen API-Key nach einer Neuerstellung in externen Scripten zu aktualisieren!',0,'','0000-00-00 00:00:00'),('app','de','DragBookmark','Ziehe den Link zu Deinen Lesezeichen',0,'','0000-00-00 00:00:00'),('app','de','DragPermanent','Permanent Link mit Datum\r\nZiehe den Link zu Deinen Lesezeichen',0,'','0000-00-00 00:00:00'),('app','de','DSEP',',',0,'','0000-00-00 00:00:00'),('app','de','Earning','Ertrag',0,'','0000-00-00 00:00:00'),('app','de','Edit','Bearbeiten',0,'','0000-00-00 00:00:00'),('app','de','EditChannel','Kanal bearbeiten',0,'','0000-00-00 00:00:00'),('app','de','EditEntity','Kanal bearbeiten',0,'','0000-00-00 00:00:00'),('app','de','Energy','Energie',0,'','0000-00-00 00:00:00'),('app','de','EntityType','Kanaltyp',0,'','0000-00-00 00:00:00'),('app','de','Equipment','Geräte',0,'','0000-00-00 00:00:00'),('app','de','Expand','Erweitern',0,'','0000-00-00 00:00:00'),('app','de','ExpandAll','Alles erweitern',0,'','0000-00-00 00:00:00'),('app','de','from','von',0,'','0000-00-00 00:00:00'),('app','de','GenerateAdminHash','Erstelle Administrations-Authorisierung',0,'','0000-00-00 00:00:00'),('app','de','IndexLength','Indexgröße',0,'','0000-00-00 00:00:00'),('app','de','InfoHint','Hintergrundinformationen (Shift+F5)',0,'','2013-12-22 17:16:39'),('app','de','Information','Informationen',0,'','0000-00-00 00:00:00'),('app','de','InformationHint','Informationen die zur Konfiguration zum Speichern und Abfragen benötigt werden',0,'','0000-00-00 00:00:00'),('app','de','InstalledAdapters','Installierte Adapter',0,'','0000-00-00 00:00:00'),('app','de','Inverter','Wechselrichter',0,'','0000-00-00 00:00:00'),('app','de','Irradiation','Einstrahlung',0,'','0000-00-00 00:00:00'),('app','de','JustAMoment','Einen Moment bitte ...',0,'','0000-00-00 00:00:00'),('app','de','Last','Letzte',0,'','0000-00-00 00:00:00'),('app','de','LastReading','Letzter Wert',0,'','0000-00-00 00:00:00'),('app','de','LastTimestamp','Zeitpunkt der letzten\r\nDatenaufzeichnung',0,'','0000-00-00 00:00:00'),('app','de','left','links',0,'','0000-00-00 00:00:00'),('app','de','LineBold','dick',0,'','0000-00-00 00:00:00'),('app','de','LineNormal','normal',0,'','0000-00-00 00:00:00'),('app','de','LineWidth','Linienstärke',0,'','0000-00-00 00:00:00'),('app','de','Load','Laden',0,'','0000-00-00 00:00:00'),('app','de','Log','Log',0,'','0000-00-00 00:00:00'),('app','de','LogHint','Log-Einträge',0,'','0000-00-00 00:00:00'),('app','de','Login','Anmelden',0,'','0000-00-00 00:00:00'),('app','de','Logout','Abmelden',0,'','0000-00-00 00:00:00'),('app','de','LogoutSuccessful','[b]%s[/b] wurde erfolgreich abgemeldet.',0,'','0000-00-00 00:00:00'),('app','de','Manufacturer','Hersteller',0,'','0000-00-00 00:00:00'),('app','de','MarkExtremes','Markiere Extremwerte',0,'','0000-00-00 00:00:00'),('app','de','max','max',0,'','0000-00-00 00:00:00'),('app','de','Message','Nachricht',0,'','0000-00-00 00:00:00'),('app','de','min','min',0,'','0000-00-00 00:00:00'),('app','de','MissingAPIkey','API key ist erforderlich!',0,'','0000-00-00 00:00:00'),('app','de','MobileVariantHint','Wenn Du PVLng auf mobilen Geräten nutzen möchtest, definiere mindestens ein Diagramm [b]@mobile[/b] als Standard-Diagramm.\r\nNur Diagramme beginnend mit einem [b]@[/b] sind mobil verfügbar.\r\n(Mobile Diagramme sind immer öffentlich!)',0,'','0000-00-00 00:00:00'),('app','de','Model','Modell',0,'','0000-00-00 00:00:00'),('app','de','Month','Monat',0,'','0000-00-00 00:00:00'),('app','de','MonthlyAverage','Monatsdurchschnitt',0,'','0000-00-00 00:00:00'),('app','de','MoveChannel','Kanal verschieben',0,'','0000-00-00 00:00:00'),('app','de','MoveChannelHowMuchRows','Um wie viele Positionen (auf gleicher Ebene) soll der Kanal verschoben werden?',0,'','0000-00-00 00:00:00'),('app','de','MoveChannelStartEnd','an den Anfang / das Ende',0,'','0000-00-00 00:00:00'),('app','de','MoveEntityDown','Verschiebe Kanal nach unten',0,'','0000-00-00 00:00:00'),('app','de','MoveEntityLeft','Verschiebe Kanal eine Ebene höher',0,'','0000-00-00 00:00:00'),('app','de','MoveEntityRight','Verschiebe Kanal eine Ebene tiefer',0,'','0000-00-00 00:00:00'),('app','de','MoveEntityUp','Verschiebe Kanal nach oben',0,'','0000-00-00 00:00:00'),('app','de','Name','Name',0,'','0000-00-00 00:00:00'),('app','de','NameRequired','Der Name ist erforderlich.',0,'','0000-00-00 00:00:00'),('app','de','New','Neu',0,'','0000-00-00 00:00:00'),('app','de','NextDay','Nächster Tag',0,'','0000-00-00 00:00:00'),('app','de','No','Nein',0,'','0000-00-00 00:00:00'),('app','de','NoChannelsSelectedYet','Es wurden noch keine Kanäle oder ein Diagramm zur Anzeige ausgewählt.',0,'','0000-00-00 00:00:00'),('app','de','NoDataAvailable','Keine Daten verfügbar',0,'','0000-00-00 00:00:00'),('app','de','None','Keine',0,'','0000-00-00 00:00:00'),('app','de','NotAuthorized','Nicht autorisiert! Es wurde ein falscher API key übermittelt.',0,'','0000-00-00 00:00:00'),('app','de','NoViewSelectedYet','Es wurde noch kein Diagramm zur Anzeige ausgewählt.',0,'','0000-00-00 00:00:00'),('app','de','Ok','Ok',0,'','0000-00-00 00:00:00'),('app','de','or','oder',0,'','0000-00-00 00:00:00'),('app','de','Overview','Übersicht',0,'','0000-00-00 00:00:00'),('app','de','OverviewHint','Übersicht über Deine Geräte und deren Hirarchie (Shift+F3)',0,'','2013-12-22 17:14:58'),('app','de','Overwrite','Überschreiben',0,'','0000-00-00 00:00:00'),('app','de','Parameter','Parameter',0,'','0000-00-00 00:00:00'),('app','de','Password','Passwort',0,'','0000-00-00 00:00:00'),('app','de','PasswordsNotEqual','Die Passworte sind nicht identisch.',0,'','0000-00-00 00:00:00'),('app','de','PerformanceRatio','Wirkungsgrad',0,'','0000-00-00 00:00:00'),('app','de','Period','Zeitraum',0,'','0000-00-00 00:00:00'),('app','de','PlantDescriptionHint','Beschreibung der Installation (Shift+F6)',0,'','2013-12-22 17:18:02'),('app','de','Positions','Position(en)',0,'','0000-00-00 00:00:00'),('app','de','Power','Leistung',0,'','0000-00-00 00:00:00'),('app','de','Presentation','Darstellung',0,'','0000-00-00 00:00:00'),('app','de','PrevDay','Vorheriger Tag',0,'','0000-00-00 00:00:00'),('app','de','PrivateChannel','Nicht-öffentlicher Kanal',0,'','0000-00-00 00:00:00'),('app','de','proceed','weiter',0,'','2013-12-27 17:15:43'),('app','de','Production','Produktion',0,'','0000-00-00 00:00:00'),('app','de','public','öffentlich',0,'','0000-00-00 00:00:00'),('app','de','publicHint','Öffentliche Diagramme sind von nicht eingeloggten Besuchern anzeigbar.',0,'','0000-00-00 00:00:00'),('app','de','ReadableEntity','Lesbarer Kanal',0,'','0000-00-00 00:00:00'),('app','de','Readings','Messwerte',0,'','0000-00-00 00:00:00'),('app','de','RecordCount','Anzahl Datensätze',0,'','0000-00-00 00:00:00'),('app','de','Redisplay','Anzeigen',0,'','0000-00-00 00:00:00'),('app','de','Refresh','Aktualisieren',0,'','0000-00-00 00:00:00'),('app','de','Regenerate','Regenerieren',0,'','0000-00-00 00:00:00'),('app','de','RequestTypes','Anfragetypen',0,'','0000-00-00 00:00:00'),('app','de','Required','Erforderlich',0,'','0000-00-00 00:00:00'),('app','de','resetZoom','Vergrößerung zurücksetzen',0,'','0000-00-00 00:00:00'),('app','de','resetZoomTitle','Setze Vergrößerung auf 1:1 zurück',0,'','0000-00-00 00:00:00'),('app','de','right','rechts',0,'','0000-00-00 00:00:00'),('app','de','Save','Sichern',0,'','0000-00-00 00:00:00'),('app','de','Scope','Bereich',0,'','0000-00-00 00:00:00'),('app','de','SeeAdapters','Siehe unten welche Adapter installiert sind.',0,'','0000-00-00 00:00:00'),('app','de','SeeAPIReference','Für mehr Informationen, siehe in die [url=http://pvlng.com/index.html?API.html]API-Referenz[/url].',0,'','0000-00-00 00:00:00'),('app','de','Select','Auswählen',0,'','0000-00-00 00:00:00'),('app','de','SelectEntity','Kanal auswählen',0,'','0000-00-00 00:00:00'),('app','de','SelectEntityType','Auswahl Kanaltyp',0,'','0000-00-00 00:00:00'),('app','de','Selection','Auswahl',0,'','0000-00-00 00:00:00'),('app','de','SelectView','Diagramm auswählen',0,'','0000-00-00 00:00:00'),('app','de','Send','Absenden',0,'','0000-00-00 00:00:00'),('app','de','Serial','Seriennummer',0,'','0000-00-00 00:00:00'),('app','de','SerialRequired','Die Serialnummer ist erforderlich',0,'','0000-00-00 00:00:00'),('app','de','SerialStillExists','Die Serialnummer existiert bereits.',0,'','0000-00-00 00:00:00'),('app','de','SeriesType','Datenreihendarstellung',0,'','0000-00-00 00:00:00'),('app','de','SetAxisMinZero','Setze Y-Achsen-Minimum auf 0',0,'','0000-00-00 00:00:00'),('app','de','ShowConsumption','Periodenwerte',0,'','0000-00-00 00:00:00'),('app','de','ShowConsumptionHint','Zeigt für Meter-Kanäle die Daten pro Periode und nicht den Gesamtwert über die Zeit',0,'','0000-00-00 00:00:00'),('app','de','Statistics','Statistik',0,'','0000-00-00 00:00:00'),('app','de','StayLoggedIn','Angemeldet bleiben',0,'','0000-00-00 00:00:00'),('app','de','Stick','Anheften',0,'','0000-00-00 00:00:00'),('app','de','SuppressZero','Unterdrücke 0-Werte',0,'','0000-00-00 00:00:00'),('app','de','Sure','Sicher',0,'','0000-00-00 00:00:00'),('app','de','SystemInformation','Systeminformationen',0,'','0000-00-00 00:00:00'),('app','de','Temperature','Temperatur',0,'','0000-00-00 00:00:00'),('app','de','TemperatureDifference','Temperaturdifferenz',0,'','0000-00-00 00:00:00'),('app','de','TemperatureModules','Modultemperatur',0,'','0000-00-00 00:00:00'),('app','de','TemperatureOutside','Außentemperatur',0,'','0000-00-00 00:00:00'),('app','de','ThinLine','dünn',0,'','0000-00-00 00:00:00'),('app','de','Threshold','Grenzwert',0,'','0000-00-00 00:00:00'),('app','de','Timestamp','Timestamp',0,'','0000-00-00 00:00:00'),('app','de','to','bis',0,'','0000-00-00 00:00:00'),('app','de','Today','Heute',0,'','0000-00-00 00:00:00'),('app','de','ToggleChannels','Kanäle ein-/ausklappen',0,'','0000-00-00 00:00:00'),('app','de','toggleGUIDs','Kanal-GUIDs anzeigen',0,'','0000-00-00 00:00:00'),('app','de','Total','Gesamt',0,'','0000-00-00 00:00:00'),('app','de','TotalRows','Datensatzanzahl',0,'','0000-00-00 00:00:00'),('app','de','TotalSize','Gesamtgröße',0,'','0000-00-00 00:00:00'),('app','de','TSEP','.',0,'','0000-00-00 00:00:00'),('app','de','Type','Typ',0,'','0000-00-00 00:00:00'),('app','de','Unit','Einheit',0,'','0000-00-00 00:00:00'),('app','de','UnknownUser','Unbekannter Benutzer oder falsches Passwort.',0,'','0000-00-00 00:00:00'),('app','de','UnknownView','Unbekanntes Diagramm: \'%s\'',0,'','0000-00-00 00:00:00'),('app','de','UseNegativeColor','Nutze andere Farbe für Werte unterhalb Grenzwert',0,'','0000-00-00 00:00:00'),('app','de','Value','Wert',0,'','0000-00-00 00:00:00'),('app','de','Variant','Diagramm',0,'','0000-00-00 00:00:00'),('app','de','Variants','Diagramme',0,'','0000-00-00 00:00:00'),('app','de','VariantsPublic','Öffentliche Diagramme',0,'','0000-00-00 00:00:00'),('app','de','ViewDeleted','Diagramm \'%s\' gelöscht.',0,'','0000-00-00 00:00:00'),('app','de','Voltage','Spannung',0,'','0000-00-00 00:00:00'),('app','de','WeeklyAverage','Wochendurchschnitt',0,'','0000-00-00 00:00:00'),('app','de','Welcome','Wilkommen %s!',0,'','0000-00-00 00:00:00'),('app','de','WelcomeToAdministration','Willkommen in Deinem PVLng Administrationsbereich.',0,'','0000-00-00 00:00:00'),('app','de','WritableEntity','Schreibbarer Kanal',0,'','0000-00-00 00:00:00'),('app','de','YearlyAverage','Jahresdurchschnitt',0,'','0000-00-00 00:00:00'),('app','de','Yes','Ja',0,'','0000-00-00 00:00:00'),('app','de','YourAPIcode','API-Schlüssel für den Daten-Update\r\n\r\n[i](Halte Deinen API-Schlüssel immer geheim)[/i]',0,'','0000-00-00 00:00:00'),('app','en','Actions','Actions',0,'','0000-00-00 00:00:00'),('app','en','ActualState','Actual data state',0,'','0000-00-00 00:00:00'),('app','en','Add','Add',0,'','0000-00-00 00:00:00'),('app','en','AddAnotherChild','Add another channel',0,'','0000-00-00 00:00:00'),('app','en','AddChannel','Add a channel to the hierarchy',0,'','0000-00-00 00:00:00'),('app','en','AddChild','Add child channel',0,'','0000-00-00 00:00:00'),('app','en','AdminAndPasswordRequired','User name and password required!',0,'','0000-00-00 00:00:00'),('app','en','Aggregation','Aggregation',0,'','0000-00-00 00:00:00'),('app','en','AliasEntity','Create alias channel',0,'','0000-00-00 00:00:00'),('app','en','AliasesUpdated','The alias channel was also updated.',0,'','0000-00-00 00:00:00'),('app','en','AliasStillExists','An alias channel still exists.',0,'','0000-00-00 00:00:00'),('app','en','AliasStillInTree','This channel have an alias channel defined.\r\nThis alias channel is still in tree, remove the alias before!',0,'','2013-12-27 21:43:17'),('app','en','All','All',0,'','0000-00-00 00:00:00'),('app','en','AllDataWillBeRemoved','All data will be removed, all master data and [color=red]all[/color] operating data!',0,'','0000-00-00 00:00:00'),('app','en','Amount','Amount',0,'','0000-00-00 00:00:00'),('app','en','APIkeyRegenerated','Your API key was regenerated.',0,'','0000-00-00 00:00:00'),('app','en','APIURL','API URL',0,'','0000-00-00 00:00:00'),('app','en','AssignEntity','Assign sub channel',0,'','0000-00-00 00:00:00'),('app','en','Author','Author',0,'','0000-00-00 00:00:00'),('app','en','Average','Average',0,'','0000-00-00 00:00:00'),('app','en','Axis','Axis',0,'','0000-00-00 00:00:00'),('app','en','Back','Back',0,'','0000-00-00 00:00:00'),('app','en','BackToTop','Back to top',0,'','0000-00-00 00:00:00'),('app','en','BasicDate','Basic date',0,'','0000-00-00 00:00:00'),('app','en','Bookmark','Bookmark',0,'','0000-00-00 00:00:00'),('app','en','Bytes','Bytes',0,'','0000-00-00 00:00:00'),('app','en','Cancel','Cancel',0,'','0000-00-00 00:00:00'),('app','en','CanHaveChilds','This channel type can have childs',0,'','0000-00-00 00:00:00'),('app','en','channel','Channel',0,'','0000-00-00 00:00:00'),('app','en','ChannelAttributes','Channel attributes',0,'','0000-00-00 00:00:00'),('app','en','ChannelDeleted','Channel \'%s\' deleted.',0,'','0000-00-00 00:00:00'),('app','en','ChannelHierarchy','Channel hierarchy\r\n',0,'','0000-00-00 00:00:00'),('app','en','ChannelName','Channel name',0,'','0000-00-00 00:00:00'),('app','en','Channels','Channels',0,'','0000-00-00 00:00:00'),('app','en','ChannelSaved','Channel data saved.',0,'','0000-00-00 00:00:00'),('app','en','ChannelsHint','Overview of all defined channels (Shift+F4)',0,'','2013-12-22 17:12:22'),('app','en','ChannelStillInTree','Channel \'%s\' is still used in overview!\r\nPlease remove it there first.',0,'','0000-00-00 00:00:00'),('app','en','ChannelType','Channel type',0,'','0000-00-00 00:00:00'),('app','en','Chart','Chart',0,'','0000-00-00 00:00:00'),('app','en','ChartHint','Display channel charts (Shift+F1)',0,'','2013-12-22 17:12:46'),('app','en','ChartRefreshHint','Click or F6: Reread chart channel data\r\nShift+Click or F7: Rebuild the whole chart',0,'','2013-12-22 17:25:01'),('app','en','Charts','Charts',0,'','0000-00-00 00:00:00'),('app','en','ChartSettings','Chart settings',0,'','0000-00-00 00:00:00'),('app','en','ChartSettingsTip','Define axis, presentaion style, color etc. here',0,'','0000-00-00 00:00:00'),('app','en','Childs','Childs',0,'','0000-00-00 00:00:00'),('app','en','Clear','Clear',0,'','0000-00-00 00:00:00'),('app','en','ClickForGUID','Click here to show GUID',0,'','0000-00-00 00:00:00'),('app','en','CloneEntity','Copy channel',0,'','0000-00-00 00:00:00'),('app','en','Close','Close',0,'','0000-00-00 00:00:00'),('app','en','Collapse','Collapse',0,'','0000-00-00 00:00:00'),('app','en','CollapseAll','CollapseAll',0,'','0000-00-00 00:00:00'),('app','en','Color','Color',0,'','0000-00-00 00:00:00'),('app','en','Commissioning','Commissioning',0,'','0000-00-00 00:00:00'),('app','en','ConfirmDeleteEntity','Delete channel and all existing measuring data.\r\n\r\nAre you sure?',0,'','0000-00-00 00:00:00'),('app','en','ConfirmDeleteTreeItems','Delete channel (and may be all sub channels) from tree.\r\n\r\nAre you sure?',0,'','0000-00-00 00:00:00'),('app','en','Consumption','Consumption',0,'','0000-00-00 00:00:00'),('app','en','Cost','Cost',0,'','0000-00-00 00:00:00'),('app','en','Create','Create',0,'','0000-00-00 00:00:00'),('app','en','CreateChannel','Create new channel',0,'','0000-00-00 00:00:00'),('app','en','DailyAverage','Daily average',0,'','0000-00-00 00:00:00'),('app','en','DailyValue','Daily values',0,'','0000-00-00 00:00:00'),('app','en','Dashboard','Dashboard',0,'','0000-00-00 00:00:00'),('app','en','DashboardHint','Quick overview with gauges (Shift+F2)',0,'','2013-12-22 17:13:18'),('app','en','dashStyle','Dash style',0,'','0000-00-00 00:00:00'),('app','en','Data','Data',0,'','0000-00-00 00:00:00'),('app','en','DataArea','Data area',0,'','0000-00-00 00:00:00'),('app','en','DataExtraction','Data extraction',0,'','0000-00-00 00:00:00'),('app','en','DataLength','Data size',0,'','0000-00-00 00:00:00'),('app','en','DataState','Data state',0,'','0000-00-00 00:00:00'),('app','en','DataStateHint','Some information about the data health',0,'','2013-12-22 17:16:10'),('app','en','DataStorage','Data storage',0,'','0000-00-00 00:00:00'),('app','en','DataType','Data type',0,'','0000-00-00 00:00:00'),('app','en','Day','Day',0,'','0000-00-00 00:00:00'),('app','en','dbField','Identifier',0,'','0000-00-00 00:00:00'),('app','en','dbValue','Value',0,'','0000-00-00 00:00:00'),('app','en','Decommissioning','Decommissioning',0,'','0000-00-00 00:00:00'),('app','en','Delete','Delete',0,'','0000-00-00 00:00:00'),('app','en','DeleteBranch','Delete branch',0,'','0000-00-00 00:00:00'),('app','en','DeleteEntity','Delete channel',0,'','0000-00-00 00:00:00'),('app','en','DeleteEntityChilds','Delete channel with sub channels',0,'','0000-00-00 00:00:00'),('app','en','DeleteViewFailed','Delete chart \'%s\' failed.',0,'','0000-00-00 00:00:00'),('app','en','Delta','Delta',0,'','0000-00-00 00:00:00'),('app','en','Description','Description',0,'','0000-00-00 00:00:00'),('app','en','DontForgetUpdateAPIKey','Don\'t forget to update the API key in extranl scripts after recreation!',0,'','0000-00-00 00:00:00'),('app','en','DragBookmark','Drag the link to your bookmarks',0,'','0000-00-00 00:00:00'),('app','en','DragPermanent','Permanent link with dates\r\nDrag the link to your bookmarks',0,'','0000-00-00 00:00:00'),('app','en','DSEP','.',0,'','0000-00-00 00:00:00'),('app','en','Earning','Earning',0,'','0000-00-00 00:00:00'),('app','en','Edit','Edit',0,'','0000-00-00 00:00:00'),('app','en','EditChannel','Edit channel',0,'','0000-00-00 00:00:00'),('app','en','EditEntity','Edit channel',0,'','0000-00-00 00:00:00'),('app','en','Energy','Energy',0,'','0000-00-00 00:00:00'),('app','en','EntityType','Channel type',0,'','0000-00-00 00:00:00'),('app','en','Equipment','Equipment',0,'','0000-00-00 00:00:00'),('app','en','Expand','Expand',0,'','0000-00-00 00:00:00'),('app','en','ExpandAll','ExpandAll',0,'','0000-00-00 00:00:00'),('app','en','from','from',0,'','0000-00-00 00:00:00'),('app','en','GenerateAdminHash','Create admininistration authorization',0,'','0000-00-00 00:00:00'),('app','en','IndexLength','Index size',0,'','0000-00-00 00:00:00'),('app','en','InfoHint','Background information (Shift+F5)',0,'','2013-12-22 17:16:39'),('app','en','Information','Information',0,'','0000-00-00 00:00:00'),('app','en','InformationHint','Information required for configuring storage and extractions',0,'','0000-00-00 00:00:00'),('app','en','InstalledAdapters','Installed adapters',0,'','0000-00-00 00:00:00'),('app','en','Inverter','Inverter',0,'','0000-00-00 00:00:00'),('app','en','Irradiation','Irradiation',0,'','0000-00-00 00:00:00'),('app','en','JustAMoment','Just a moment please ...',0,'','0000-00-00 00:00:00'),('app','en','Last','Last',0,'','0000-00-00 00:00:00'),('app','en','LastReading','Last reading',0,'','0000-00-00 00:00:00'),('app','en','LastTimestamp','Time stamp of\r\nlast data recording',0,'','0000-00-00 00:00:00'),('app','en','left','left',0,'','0000-00-00 00:00:00'),('app','en','LineBold','thick',0,'','0000-00-00 00:00:00'),('app','en','LineNormal','normal',0,'','0000-00-00 00:00:00'),('app','en','LineWidth','Line width',0,'','0000-00-00 00:00:00'),('app','en','Load','Load',0,'','0000-00-00 00:00:00'),('app','en','Log','Log',0,'','0000-00-00 00:00:00'),('app','en','LogHint','Log entries',0,'','0000-00-00 00:00:00'),('app','en','Login','Login',0,'','0000-00-00 00:00:00'),('app','en','Logout','Logout',0,'','0000-00-00 00:00:00'),('app','en','LogoutSuccessful','[b]%s[/b] logged out successful.',0,'','0000-00-00 00:00:00'),('app','en','Manufacturer','Manufacturer',0,'','0000-00-00 00:00:00'),('app','en','MarkExtremes','Mark extremes',0,'','0000-00-00 00:00:00'),('app','en','max','max',0,'','0000-00-00 00:00:00'),('app','en','Message','Message',0,'','0000-00-00 00:00:00'),('app','en','min','min',0,'','0000-00-00 00:00:00'),('app','en','MissingAPIkey','Missing API key!',0,'','0000-00-00 00:00:00'),('app','en','MobileVariantHint','If you plan to use PVLng on mobile devices, define at least a chart [b]@mobile[/b] as default chart.\r\nOnly charts starting with a [b]@[/b] will be available mobile.\r\n(Mobile charts are public by default!) ',0,'','0000-00-00 00:00:00'),('app','en','Model','Model',0,'','0000-00-00 00:00:00'),('app','en','Month','Month',0,'','0000-00-00 00:00:00'),('app','en','MonthlyAverage','Monthly average',0,'','0000-00-00 00:00:00'),('app','en','MoveChannel','Move channel',0,'','0000-00-00 00:00:00'),('app','en','MoveChannelHowMuchRows','Move how many positions (on same level)?',0,'','0000-00-00 00:00:00'),('app','en','MoveChannelStartEnd','to the start / the end',0,'','0000-00-00 00:00:00'),('app','en','MoveEntityDown','Move channel down',0,'','0000-00-00 00:00:00'),('app','en','MoveEntityLeft','Move channel one level up',0,'','0000-00-00 00:00:00'),('app','en','MoveEntityRight','Move channel one level down',0,'','0000-00-00 00:00:00'),('app','en','MoveEntityUp','Move channel up',0,'','0000-00-00 00:00:00'),('app','en','Name','Name',0,'','0000-00-00 00:00:00'),('app','en','NameRequired','The name is required.',0,'','0000-00-00 00:00:00'),('app','en','New','New',0,'','0000-00-00 00:00:00'),('app','en','NextDay','Next day',0,'','0000-00-00 00:00:00'),('app','en','No','No',0,'','0000-00-00 00:00:00'),('app','en','NoChannelsSelectedYet','There are no channels or a chart selected yet to view.',0,'','0000-00-00 00:00:00'),('app','en','NoDataAvailable','No data available',0,'','0000-00-00 00:00:00'),('app','en','None','None',0,'','0000-00-00 00:00:00'),('app','en','NotAuthorized','Not authorized! A wrong API key was submitted.',0,'','0000-00-00 00:00:00'),('app','en','NoViewSelectedYet','There is no chart selected yet to view.',0,'','0000-00-00 00:00:00'),('app','en','Ok','Ok',0,'','0000-00-00 00:00:00'),('app','en','or','or',0,'','0000-00-00 00:00:00'),('app','en','Overview','Overview',0,'','0000-00-00 00:00:00'),('app','en','OverviewHint','Overview of your equipments and relationship (Shift+F3)',0,'','2013-12-22 17:14:58'),('app','en','Overwrite','Overwrite',0,'','0000-00-00 00:00:00'),('app','en','Parameter','Parameter',0,'','0000-00-00 00:00:00'),('app','en','Password','Password',0,'','0000-00-00 00:00:00'),('app','en','PasswordsNotEqual','The passwords are not equal.',0,'','0000-00-00 00:00:00'),('app','en','PerformanceRatio','Performance ratio',0,'','0000-00-00 00:00:00'),('app','en','Period','Period',0,'','0000-00-00 00:00:00'),('app','en','PlantDescriptionHint','Description of installation (Shift+F6)',0,'','2013-12-22 17:18:01'),('app','en','Positions','Position(s)',0,'','0000-00-00 00:00:00'),('app','en','Power','Power',0,'','0000-00-00 00:00:00'),('app','en','Presentation','Presentation',0,'','0000-00-00 00:00:00'),('app','en','PrevDay','Previous day',0,'','0000-00-00 00:00:00'),('app','en','PrivateChannel','No public channel',0,'','0000-00-00 00:00:00'),('app','en','proceed','proceed',0,'','2013-12-27 17:15:42'),('app','en','Production','Production',0,'','0000-00-00 00:00:00'),('app','en','public','public',0,'','0000-00-00 00:00:00'),('app','en','publicHint','Public charts are accessible by not logged in visitors.',0,'','0000-00-00 00:00:00'),('app','en','ReadableEntity','Readable channel',0,'','0000-00-00 00:00:00'),('app','en','Readings','Readings',0,'','0000-00-00 00:00:00'),('app','en','RecordCount','Record count',0,'','0000-00-00 00:00:00'),('app','en','Redisplay','Display',0,'','0000-00-00 00:00:00'),('app','en','Refresh','Refresh',0,'','0000-00-00 00:00:00'),('app','en','Regenerate','Regenerate',0,'','0000-00-00 00:00:00'),('app','en','RequestTypes','Request types',0,'','0000-00-00 00:00:00'),('app','en','Required','Required',0,'','0000-00-00 00:00:00'),('app','en','resetZoom','Reset zoom',0,'','0000-00-00 00:00:00'),('app','en','resetZoomTitle','Reset zoom to 1:1',0,'','0000-00-00 00:00:00'),('app','en','right','right',0,'','0000-00-00 00:00:00'),('app','en','Save','Save',0,'','0000-00-00 00:00:00'),('app','en','Scope','Scope',0,'','0000-00-00 00:00:00'),('app','en','SeeAdapters','See below which adapters are installed.',0,'','0000-00-00 00:00:00'),('app','en','SeeAPIReference','For more information take a look into the [url=http://pvlng.com/index.html?API.html]API reference[/url].',0,'','0000-00-00 00:00:00'),('app','en','Select','Select',0,'','0000-00-00 00:00:00'),('app','en','SelectEntity','Select channel',0,'','0000-00-00 00:00:00'),('app','en','SelectEntityType','Select channel type',0,'','0000-00-00 00:00:00'),('app','en','Selection','Selection',0,'','0000-00-00 00:00:00'),('app','en','SelectView','Select chart',0,'','0000-00-00 00:00:00'),('app','en','Send','Send',0,'','0000-00-00 00:00:00'),('app','en','Serial','Serial number',0,'','0000-00-00 00:00:00'),('app','en','SerialRequired','Serial number is required',0,'','0000-00-00 00:00:00'),('app','en','SerialStillExists','This serial number still exists.',0,'','0000-00-00 00:00:00'),('app','en','SeriesType','Series display type',0,'','0000-00-00 00:00:00'),('app','en','SetAxisMinZero','Set Y axis min. to 0',0,'','0000-00-00 00:00:00'),('app','en','ShowConsumption','Period values',0,'','0000-00-00 00:00:00'),('app','en','ShowConsumptionHint','Shows for meter channels the data per selected aggregation period and not the total over time',0,'','0000-00-00 00:00:00'),('app','en','Statistics','Statistics',0,'','0000-00-00 00:00:00'),('app','en','StayLoggedIn','Remember me',0,'','0000-00-00 00:00:00'),('app','en','Stick','Stick',0,'','0000-00-00 00:00:00'),('app','en','SuppressZero','Suppress zero values',0,'','0000-00-00 00:00:00'),('app','en','Sure','Sure',0,'','0000-00-00 00:00:00'),('app','en','SystemInformation','System information',0,'','0000-00-00 00:00:00'),('app','en','Temperature','Temperature',0,'','0000-00-00 00:00:00'),('app','en','TemperatureDifference','Temperature difference',0,'','0000-00-00 00:00:00'),('app','en','TemperatureModules','Temperature modules',0,'','0000-00-00 00:00:00'),('app','en','TemperatureOutside','Temperature outside',0,'','0000-00-00 00:00:00'),('app','en','ThinLine','thin',0,'','0000-00-00 00:00:00'),('app','en','Threshold','Threshold',0,'','0000-00-00 00:00:00'),('app','en','Timestamp','Timestamp',0,'','0000-00-00 00:00:00'),('app','en','to','to',0,'','0000-00-00 00:00:00'),('app','en','Today','Today',0,'','0000-00-00 00:00:00'),('app','en','ToggleChannels','Expand/collapse channels',0,'','0000-00-00 00:00:00'),('app','en','toggleGUIDs','Show channel GUIDs',0,'','0000-00-00 00:00:00'),('app','en','Total','Total',0,'','0000-00-00 00:00:00'),('app','en','TotalRows','Total rows',0,'','0000-00-00 00:00:00'),('app','en','TotalSize','Total size',0,'','0000-00-00 00:00:00'),('app','en','TSEP',',',0,'','0000-00-00 00:00:00'),('app','en','Type','Type',0,'','0000-00-00 00:00:00'),('app','en','Unit','Unit',0,'','0000-00-00 00:00:00'),('app','en','UnknownUser','Unknown user or wrong password.',0,'','0000-00-00 00:00:00'),('app','en','UnknownView','Unknown chart: \'%s\'',0,'','0000-00-00 00:00:00'),('app','en','UseNegativeColor','Use different color for values below threshold',0,'','0000-00-00 00:00:00'),('app','en','Value','Value',0,'','0000-00-00 00:00:00'),('app','en','Variant','Chart',0,'','0000-00-00 00:00:00'),('app','en','Variants','Charts',0,'','0000-00-00 00:00:00'),('app','en','VariantsPublic','Public charts',0,'','0000-00-00 00:00:00'),('app','en','ViewDeleted','Chart \'%s\' deleted.',0,'','0000-00-00 00:00:00'),('app','en','Voltage','Voltage',0,'','0000-00-00 00:00:00'),('app','en','WeeklyAverage','Weekly average',0,'','0000-00-00 00:00:00'),('app','en','Welcome','Welcome %s!',0,'','0000-00-00 00:00:00'),('app','en','WelcomeToAdministration','Welcome in your PVLng administration area.',0,'','0000-00-00 00:00:00'),('app','en','WritableEntity','Writable channel',0,'','0000-00-00 00:00:00'),('app','en','YearlyAverage','Yearly average',0,'','0000-00-00 00:00:00'),('app','en','Yes','Yes',0,'','0000-00-00 00:00:00'),('app','en','YourAPIcode','API key for updating your data\r\n\r\n[i](Always keep your API key secret)[/i]',0,'','0000-00-00 00:00:00'),('channel','de','adjust','Offset anpassen',0,'','0000-00-00 00:00:00'),('channel','de','adjustHint','Passt den Kanal-Offset automatisch an, wenn der aktuelle Messwert kleiner als der letzte gespeicherte Messwert ist aber <> 0.\r\nWird nur bei Meter-Kanälen benutzt.\r\nSetze das Kennzeichen, wenn Dein Mess-Equipment manchmal seinen Stand verliert/zurücksetzt.',0,'','0000-00-00 00:00:00'),('channel','de','channel','Kanal',0,'','0000-00-00 00:00:00'),('channel','de','channelHint','Kanalname bei Multi-Sensoren',0,'','0000-00-00 00:00:00'),('channel','de','comment','Kommentar',0,'','0000-00-00 00:00:00'),('channel','de','commentHint','interner Kommentar',0,'','0000-00-00 00:00:00'),('channel','de','cost','Kosten',0,'','0000-00-00 00:00:00'),('channel','de','costHint','Kosten pro Einheit, nur bei Meter-Kanälen',0,'','0000-00-00 00:00:00'),('channel','de','decimals','Dezimalstellen',0,'','0000-00-00 00:00:00'),('channel','de','decimalsHint','Für die Wert-Ausgabe',0,'','0000-00-00 00:00:00'),('channel','de','description','Beschreibung',0,'','0000-00-00 00:00:00'),('channel','de','descriptionHint','Langtext',0,'','0000-00-00 00:00:00'),('channel','de','Help','Hinweis',0,'','0000-00-00 00:00:00'),('channel','de','meter','Meter',0,'','0000-00-00 00:00:00'),('channel','de','meterHint','Meter-Kanäle speichern nur aufsteigende Werte',0,'','0000-00-00 00:00:00'),('channel','de','Name','Name',0,'','0000-00-00 00:00:00'),('channel','de','nameHint','Eindeutiger Kanalname',0,'','0000-00-00 00:00:00'),('channel','de','numeric','Numerische Werte',0,'','0000-00-00 00:00:00'),('channel','de','numericHint','Der Kanal hat numerische oder Alphanumerische Daten?',0,'','0000-00-00 00:00:00'),('channel','de','offset','Offset',0,'','0000-00-00 00:00:00'),('channel','de','offsetHint','Mittels dieses Offsets werden die realen Messwerte während des Auslesens korrigiert.',0,'','0000-00-00 00:00:00'),('channel','de','Param','Parameter',0,'','0000-00-00 00:00:00'),('channel','de','ParamIsRequired','Parameter \'%s\' ist erforderlich!',0,'','0000-00-00 00:00:00'),('channel','de','public','Öffentlich',0,'','0000-00-00 00:00:00'),('channel','de','publicHint','Nicht-öffentliche Kanäle sind für nicht eingeloggte Besucher oder ohne API key nicht ansprechbar.',0,'','0000-00-00 00:00:00'),('channel','de','resolution','Auflösung',0,'','0000-00-00 00:00:00'),('channel','de','resolutionHint','Auflösung bei Datenextraktion',0,'','0000-00-00 00:00:00'),('channel','de','Serial','Seriennummer',0,'','0000-00-00 00:00:00'),('channel','de','serialHint','Eindeutige Sensor-Serialnummer',0,'','0000-00-00 00:00:00'),('channel','de','threshold','Schwellwert',0,'','0000-00-00 00:00:00'),('channel','de','thresholdHint','Ein Messwert ist nur gültig, wenn er sich um +- Schwellwert vom letzten gespeicherten Messwert unterscheidet.',0,'','0000-00-00 00:00:00'),('channel','de','unit','Einheit',0,'','0000-00-00 00:00:00'),('channel','de','unitHint','Einheit des Kanals',0,'','0000-00-00 00:00:00'),('channel','de','valid_from','Unterer Grenzwert',0,'','0000-00-00 00:00:00'),('channel','de','valid_fromHint','Werte sind nur gültig, wenn sie größer oder gleich dieses Wertes sind.\r\nBeim Speichern werden ungültige Werte verworfen, beim Auslesen kleiner Werte auf den Grenzwert gesetzt.',0,'','0000-00-00 00:00:00'),('channel','de','valid_to','Oberer Grenzwert',0,'','0000-00-00 00:00:00'),('channel','de','valid_toHint','Werte sind nur gültig, wenn sie kleiner oder gleich dieses Wertes sind.\r\nBeim Speichern werden ungültige Werte verworfen, beim Auslesen größere Werte auf den Grenzwert gesetzt.',0,'','0000-00-00 00:00:00'),('channel','de','Value','Parameterwert',0,'','0000-00-00 00:00:00'),('channel','en','adjust','Adjust offset',0,'','0000-00-00 00:00:00'),('channel','en','adjustHint','Adjust channel offset automatic, if the actual reading value is lower than last reading but <> 0.\r\nUsed only for meter channels.\r\nUse this, if your measuring equipment sometimes looses/resets its counter.',0,'','0000-00-00 00:00:00'),('channel','en','channel','Channel',0,'','0000-00-00 00:00:00'),('channel','en','channelHint','Channel name for multi sensors',0,'','0000-00-00 00:00:00'),('channel','en','comment','Comment',0,'','0000-00-00 00:00:00'),('channel','en','commentHint','Internal comment',0,'','0000-00-00 00:00:00'),('channel','en','cost','Cost',0,'','0000-00-00 00:00:00'),('channel','en','costHint','Cost per unit, for meter channels only',0,'','0000-00-00 00:00:00'),('channel','en','decimals','Decimals',0,'','0000-00-00 00:00:00'),('channel','en','decimalsHint','Decimals for value output',0,'','0000-00-00 00:00:00'),('channel','en','description','Description',0,'','0000-00-00 00:00:00'),('channel','en','descriptionHint','Long description',0,'','0000-00-00 00:00:00'),('channel','en','Help','Hint',0,'','0000-00-00 00:00:00'),('channel','en','meter','Meter',0,'','0000-00-00 00:00:00'),('channel','en','meterHint','Meter channels stores raising values',0,'','0000-00-00 00:00:00'),('channel','en','Name','Name',0,'','0000-00-00 00:00:00'),('channel','en','nameHint','Unique channel name',0,'','0000-00-00 00:00:00'),('channel','en','numeric','Numeric values',0,'','0000-00-00 00:00:00'),('channel','en','numericHint','Channels have numeric or alphanumeric data?',0,'','0000-00-00 00:00:00'),('channel','en','offset','Offset',0,'','0000-00-00 00:00:00'),('channel','en','offsetHint','Apply this value during readout to the reading values to correct them.',0,'','0000-00-00 00:00:00'),('channel','en','Param','Parameter',0,'','0000-00-00 00:00:00'),('channel','en','ParamIsRequired','Parameter \'%s\' is required!',0,'','0000-00-00 00:00:00'),('channel','en','public','Public',0,'','0000-00-00 00:00:00'),('channel','en','publicHint','Non public channels are not accessible for not logged in visitors or without API key.',0,'','0000-00-00 00:00:00'),('channel','en','resolution','Resolution',0,'','0000-00-00 00:00:00'),('channel','en','resolutionHint','Resolution for data readout',0,'','0000-00-00 00:00:00'),('channel','en','Serial','Serial number',0,'','0000-00-00 00:00:00'),('channel','en','serialHint','Unique sensor serial number',0,'','0000-00-00 00:00:00'),('channel','en','threshold','Threshold',0,'','0000-00-00 00:00:00'),('channel','en','thresholdHint','A reading is only accepted, if the value is +- threshold from last reading.',0,'','0000-00-00 00:00:00'),('channel','en','unit','Unit',0,'','0000-00-00 00:00:00'),('channel','en','unitHint','Channel unit',0,'','0000-00-00 00:00:00'),('channel','en','valid_from','Valid from',0,'','0000-00-00 00:00:00'),('channel','en','valid_fromHint','Readings are only valid if they are greater or equal this limit.\r\nOn saving are invalid values skipped, on reading lower values will be set to this limit.',0,'','0000-00-00 00:00:00'),('channel','en','valid_to','Valid to',0,'','0000-00-00 00:00:00'),('channel','en','valid_toHint','Readings are only valid if they are lower or equal this limit.\r\nOn saving are invalid values skipped, on reading greater values will be set to this limit.',0,'','0000-00-00 00:00:00'),('channel','en','Value','Parameter value',0,'','0000-00-00 00:00:00'),('code_admin','en','app','multi=1',0,'','0000-00-00 00:00:00'),('code_admin','en','channel','multi=1',0,'','0000-00-00 00:00:00'),('code_admin','en','code_admin','param=1 slave=1',0,'','0000-00-00 00:00:00'),('code_admin','en','EquiVars','slave=1',0,'','0000-00-00 00:00:00'),('code_admin','en','inverter','multi=1',0,'','0000-00-00 00:00:00'),('code_admin','en','model','multi=1',0,'','0000-00-00 00:00:00'),('code_admin','en','plant','multi=1',0,'','0000-00-00 00:00:00'),('code_admin','en','sensor','multi=1',0,'','0000-00-00 00:00:00'),('code_admin','en','var','multi=1',0,'','0000-00-00 00:00:00'),('code_lang','de','de','Deutsch',0,'','0000-00-00 00:00:00'),('code_lang','de','en','Englisch',-1,'','0000-00-00 00:00:00'),('code_lang','en','de','german',0,'','0000-00-00 00:00:00'),('code_lang','en','en','english',-1,'','0000-00-00 00:00:00'),('code_set','de','app','Anwendung',0,'','0000-00-00 00:00:00'),('code_set','de','channel','Kanal',0,'','0000-00-00 00:00:00'),('code_set','de','code_admin','Code admin',-1,'','0000-00-00 00:00:00'),('code_set','de','code_lang','Sprache',-2,'','0000-00-00 00:00:00'),('code_set','de','code_set','Code set',-3,'','0000-00-00 00:00:00'),('code_set','de','day','Tag',0,'','0000-00-00 00:00:00'),('code_set','de','day1','Tag (1)',0,'','0000-00-00 00:00:00'),('code_set','de','day2','Tag (2)',0,'','0000-00-00 00:00:00'),('code_set','de','day3','Tag (3)',0,'','0000-00-00 00:00:00'),('code_set','de','locale','Lokalisierung',0,'','0000-00-00 00:00:00'),('code_set','de','model','Model',0,'','0000-00-00 00:00:00'),('code_set','de','month','Monat',0,'','0000-00-00 00:00:00'),('code_set','de','month3','Monat (3)',0,'','0000-00-00 00:00:00'),('code_set','de','period','Periode',0,'','0000-00-00 00:00:00'),('code_set','en','app','Application',100,'','0000-00-00 00:00:00'),('code_set','en','channel','Channel',101,'','0000-00-00 00:00:00'),('code_set','en','code_admin','code admin',-1,'','0000-00-00 00:00:00'),('code_set','en','code_lang','language',-2,'','0000-00-00 00:00:00'),('code_set','en','code_set','code set',-3,'','0000-00-00 00:00:00'),('code_set','en','day','day',0,'','0000-00-00 00:00:00'),('code_set','en','day1','day (1)',0,'','0000-00-00 00:00:00'),('code_set','en','day2','day (2)',0,'','0000-00-00 00:00:00'),('code_set','en','day3','day (3)',0,'','0000-00-00 00:00:00'),('code_set','en','locale','Locales',0,'','0000-00-00 00:00:00'),('code_set','en','model','Model',102,'','0000-00-00 00:00:00'),('code_set','en','month','month',0,'','0000-00-00 00:00:00'),('code_set','en','month3','month (3)',0,'','0000-00-00 00:00:00'),('code_set','en','period','Period',0,'','0000-00-00 00:00:00'),('day','de','0','Sonntag',0,'','0000-00-00 00:00:00'),('day','de','1','Montag',0,'','0000-00-00 00:00:00'),('day','de','2','Dienstag',0,'','0000-00-00 00:00:00'),('day','de','3','Mittwoch',0,'','0000-00-00 00:00:00'),('day','de','4','Donnerstag',0,'','0000-00-00 00:00:00'),('day','de','5','Freitag',0,'','0000-00-00 00:00:00'),('day','de','6','Samstag',0,'','0000-00-00 00:00:00'),('day','en','0','Sunday',0,'','0000-00-00 00:00:00'),('day','en','1','Monday',1,'','0000-00-00 00:00:00'),('day','en','2','Tuesday',2,'','0000-00-00 00:00:00'),('day','en','3','Wednesday',3,'','0000-00-00 00:00:00'),('day','en','4','Thursday',4,'','0000-00-00 00:00:00'),('day','en','5','Friday',5,'','0000-00-00 00:00:00'),('day','en','6','Saturday',6,'','0000-00-00 00:00:00'),('day1','de','0','S',0,'','0000-00-00 00:00:00'),('day1','de','1','M',0,'','0000-00-00 00:00:00'),('day1','de','2','D',0,'','0000-00-00 00:00:00'),('day1','de','3','M',0,'','0000-00-00 00:00:00'),('day1','de','4','D',0,'','0000-00-00 00:00:00'),('day1','de','5','F',0,'','0000-00-00 00:00:00'),('day1','de','6','S',0,'','0000-00-00 00:00:00'),('day1','en','0','S',0,'','0000-00-00 00:00:00'),('day1','en','1','M',1,'','0000-00-00 00:00:00'),('day1','en','2','T',2,'','0000-00-00 00:00:00'),('day1','en','3','W',3,'','0000-00-00 00:00:00'),('day1','en','4','T',4,'','0000-00-00 00:00:00'),('day1','en','5','F',5,'','0000-00-00 00:00:00'),('day1','en','6','S',6,'','0000-00-00 00:00:00'),('day2','de','0','So',0,'','0000-00-00 00:00:00'),('day2','de','1','Mo',0,'','0000-00-00 00:00:00'),('day2','de','2','Di',0,'','0000-00-00 00:00:00'),('day2','de','3','Mi',0,'','0000-00-00 00:00:00'),('day2','de','4','Do',0,'','0000-00-00 00:00:00'),('day2','de','5','Fr',0,'','0000-00-00 00:00:00'),('day2','de','6','Sa',0,'','0000-00-00 00:00:00'),('day2','en','0','Su',0,'','0000-00-00 00:00:00'),('day2','en','1','Mo',1,'','0000-00-00 00:00:00'),('day2','en','2','Tu',2,'','0000-00-00 00:00:00'),('day2','en','3','We',3,'','0000-00-00 00:00:00'),('day2','en','4','Th',4,'','0000-00-00 00:00:00'),('day2','en','5','Fr',5,'','0000-00-00 00:00:00'),('day2','en','6','Sa',6,'','0000-00-00 00:00:00'),('day3','de','0','Son',0,'','0000-00-00 00:00:00'),('day3','de','1','Mon',0,'','0000-00-00 00:00:00'),('day3','de','2','Die',0,'','0000-00-00 00:00:00'),('day3','de','3','Mit',0,'','0000-00-00 00:00:00'),('day3','de','4','Don',0,'','0000-00-00 00:00:00'),('day3','de','5','Fre',0,'','0000-00-00 00:00:00'),('day3','de','6','Sam',0,'','0000-00-00 00:00:00'),('day3','en','0','Sun',0,'','0000-00-00 00:00:00'),('day3','en','1','Mon',1,'','0000-00-00 00:00:00'),('day3','en','2','Tue',2,'','0000-00-00 00:00:00'),('day3','en','3','Wed',3,'','0000-00-00 00:00:00'),('day3','en','4','Thu',4,'','0000-00-00 00:00:00'),('day3','en','5','Fri',5,'','0000-00-00 00:00:00'),('day3','en','6','Sat',6,'','0000-00-00 00:00:00'),('locale','de','Date','d.m.Y',0,'','0000-00-00 00:00:00'),('locale','de','DateDefault','d.m.Y',0,'','0000-00-00 00:00:00'),('locale','de','DateFull','l, j. F Y',0,'','0000-00-00 00:00:00'),('locale','de','DateLong','j. F Y',0,'','0000-00-00 00:00:00'),('locale','de','DateMedium','j. M Y',0,'','0000-00-00 00:00:00'),('locale','de','DateShort','j.n.y',0,'','0000-00-00 00:00:00'),('locale','de','DateTime','d.m.Y H:i:s',0,'','0000-00-00 00:00:00'),('locale','de','DateTimeDefault','d.m.Y / H:i:s',0,'','0000-00-00 00:00:00'),('locale','de','DateTimeFull','l, j. F Y, H:i \\U\\h\\r T O',0,'','0000-00-00 00:00:00'),('locale','de','DateTimeLong','j. F Y, H:i:s T O',0,'','0000-00-00 00:00:00'),('locale','de','DateTimeMedium','j. M Y / H:i:s',0,'','0000-00-00 00:00:00'),('locale','de','DateTimeShort','j.n.y / G:i',0,'','0000-00-00 00:00:00'),('locale','de','DecimalPoint',',',0,'','0000-00-00 00:00:00'),('locale','de','locales','de_DE@euro,de_DE,de,ge',0,'','0000-00-00 00:00:00'),('locale','de','MonthDefault','m.Y',0,'','0000-00-00 00:00:00'),('locale','de','MonthLong','F Y',0,'','0000-00-00 00:00:00'),('locale','de','MonthShort','m.y',0,'','0000-00-00 00:00:00'),('locale','de','ThousandSeparator','.',0,'','0000-00-00 00:00:00'),('locale','de','Time','H:i:s',0,'','0000-00-00 00:00:00'),('locale','de','TimeDefault','H:i:s',0,'','0000-00-00 00:00:00'),('locale','de','TimeFull','H:i \\U\\h\\r T O',0,'','0000-00-00 00:00:00'),('locale','de','TimeLong','H:i:s T O',0,'','0000-00-00 00:00:00'),('locale','de','TimeMedium','H:i:s',0,'','0000-00-00 00:00:00'),('locale','de','TimeShort','H:i',0,'','0000-00-00 00:00:00'),('locale','de','YearDefault','Y',0,'','0000-00-00 00:00:00'),('locale','de','YearShort','y',0,'','0000-00-00 00:00:00'),('locale','en','Date','d-M-Y',0,'','0000-00-00 00:00:00'),('locale','en','DateDefault','d-M-Y',0,'','0000-00-00 00:00:00'),('locale','en','DateFull','l, d F Y',0,'','0000-00-00 00:00:00'),('locale','en','DateLong','d F Y',0,'','0000-00-00 00:00:00'),('locale','en','DateMedium','d-M-Y',0,'','0000-00-00 00:00:00'),('locale','en','DateShort','d/m/y',0,'','0000-00-00 00:00:00'),('locale','en','DateTime','d-M-Y H:i:s',0,'','0000-00-00 00:00:00'),('locale','en','DateTimeDefault','d-M-Y H:i:s',0,'','0000-00-00 00:00:00'),('locale','en','DateTimeFull','l, d F Y, H:i \\o\\\'\\c\\l\\o\\c\\k T O',0,'','0000-00-00 00:00:00'),('locale','en','DateTimeLong','d F Y, H:i:s T O',0,'','0000-00-00 00:00:00'),('locale','en','DateTimeMedium','d-M-Y H:i:s',0,'','0000-00-00 00:00:00'),('locale','en','DateTimeShort','d/m/y G:i',0,'','0000-00-00 00:00:00'),('locale','en','DecimalPoint','.',0,'','0000-00-00 00:00:00'),('locale','en','locales','en_EN,en',0,'','0000-00-00 00:00:00'),('locale','en','MonthDefault','m.Y',0,'','0000-00-00 00:00:00'),('locale','en','MonthLong','F Y',0,'','0000-00-00 00:00:00'),('locale','en','MonthShort','m.y',0,'','0000-00-00 00:00:00'),('locale','en','ThousandSeparator',',',0,'','0000-00-00 00:00:00'),('locale','en','Time','H:i:s',0,'','0000-00-00 00:00:00'),('locale','en','TimeDefault','H:i:s',0,'','0000-00-00 00:00:00'),('locale','en','TimeFull','H:i \\o\\\'\\c\\l\\o\\c\\k T O',0,'','0000-00-00 00:00:00'),('locale','en','TimeLong','H:i:s T O',0,'','0000-00-00 00:00:00'),('locale','en','TimeMedium','H:i:s',0,'','0000-00-00 00:00:00'),('locale','en','TimeShort','H:i',0,'','0000-00-00 00:00:00'),('locale','en','YearDefault','Y',0,'','0000-00-00 00:00:00'),('locale','en','YearShort','y',0,'','0000-00-00 00:00:00'),('model','de','Alias_channel','GUID',0,'','0000-00-00 00:00:00'),('model','de','Alias_channelHint','GUID des Orignalkanals aus der Übersicht',0,'','0000-00-00 00:00:00'),('model','de','History_valid_from','Tage zurück',0,'','0000-00-00 00:00:00'),('model','de','History_valid_from_Hint','Um diese Tage werden die Daten rückwärts gelesen.',0,'','0000-00-00 00:00:00'),('model','de','History_valid_to','Tage vorwärts',0,'','0000-00-00 00:00:00'),('model','en','Alias_channel','GUID',0,'','0000-00-00 00:00:00'),('model','en','Alias_channelHint','GUID of original channel from overview',0,'','0000-00-00 00:00:00'),('model','en','History_valid_from','Days backwards',0,'','0000-00-00 00:00:00'),('model','en','History_valid_from_Hint','These are number of days to fetch backwards.',0,'','0000-00-00 00:00:00'),('model','en','History_valid_to','Days foreward',0,'','0000-00-00 00:00:00'),('month','de','1','Januar',0,'','0000-00-00 00:00:00'),('month','de','10','Oktober',0,'','0000-00-00 00:00:00'),('month','de','11','November',0,'','0000-00-00 00:00:00'),('month','de','12','Dezember',0,'','0000-00-00 00:00:00'),('month','de','2','Februar',0,'','0000-00-00 00:00:00'),('month','de','3','März',0,'','0000-00-00 00:00:00'),('month','de','4','April',0,'','0000-00-00 00:00:00'),('month','de','5','Mai',0,'','0000-00-00 00:00:00'),('month','de','6','Juni',0,'','0000-00-00 00:00:00'),('month','de','7','Juli',0,'','0000-00-00 00:00:00'),('month','de','8','August',0,'','0000-00-00 00:00:00'),('month','de','9','September',0,'','0000-00-00 00:00:00'),('month','en','1','January',1,'','0000-00-00 00:00:00'),('month','en','10','October',10,'','0000-00-00 00:00:00'),('month','en','11','November',11,'','0000-00-00 00:00:00'),('month','en','12','December',12,'','0000-00-00 00:00:00'),('month','en','2','February',2,'','0000-00-00 00:00:00'),('month','en','3','March',3,'','0000-00-00 00:00:00'),('month','en','4','April',4,'','0000-00-00 00:00:00'),('month','en','5','May',5,'','0000-00-00 00:00:00'),('month','en','6','June',6,'','0000-00-00 00:00:00'),('month','en','7','July',7,'','0000-00-00 00:00:00'),('month','en','8','August',8,'','0000-00-00 00:00:00'),('month','en','9','September',9,'','0000-00-00 00:00:00'),('month3','de','1','Jan',0,'','0000-00-00 00:00:00'),('month3','de','10','Okt',0,'','0000-00-00 00:00:00'),('month3','de','11','Nov',0,'','0000-00-00 00:00:00'),('month3','de','12','Dez',0,'','0000-00-00 00:00:00'),('month3','de','2','Feb',0,'','0000-00-00 00:00:00'),('month3','de','3','Mär',0,'','0000-00-00 00:00:00'),('month3','de','4','Apr',0,'','0000-00-00 00:00:00'),('month3','de','5','Mai',0,'','0000-00-00 00:00:00'),('month3','de','6','Jun',0,'','0000-00-00 00:00:00'),('month3','de','7','Jul',0,'','0000-00-00 00:00:00'),('month3','de','8','Aug',0,'','0000-00-00 00:00:00'),('month3','de','9','Sep',0,'','0000-00-00 00:00:00'),('month3','en','1','Jan',1,'','0000-00-00 00:00:00'),('month3','en','10','Oct',10,'','0000-00-00 00:00:00'),('month3','en','11','Nov',11,'','0000-00-00 00:00:00'),('month3','en','12','Dec',12,'','0000-00-00 00:00:00'),('month3','en','2','Feb',2,'','0000-00-00 00:00:00'),('month3','en','3','Mar',3,'','0000-00-00 00:00:00'),('month3','en','4','Apr',4,'','0000-00-00 00:00:00'),('month3','en','5','May',5,'','0000-00-00 00:00:00'),('month3','en','6','Jun',6,'','0000-00-00 00:00:00'),('month3','en','7','Jul',7,'','0000-00-00 00:00:00'),('month3','en','8','Aug',8,'','0000-00-00 00:00:00'),('month3','en','9','Sep',9,'','0000-00-00 00:00:00'),('period','de','d','Tag',0,'','0000-00-00 00:00:00'),('period','de','h','Stunde',0,'','0000-00-00 00:00:00'),('period','de','i','Minute',0,'','0000-00-00 00:00:00'),('period','de','m','Monat',0,'','0000-00-00 00:00:00'),('period','de','q','Quartal',0,'','0000-00-00 00:00:00'),('period','de','w','Woche',0,'','0000-00-00 00:00:00'),('period','de','y','Jahr',0,'','0000-00-00 00:00:00'),('period','en','d','Day',2,'','0000-00-00 00:00:00'),('period','en','h','Hour',1,'','0000-00-00 00:00:00'),('period','en','i','Minute',0,'','0000-00-00 00:00:00'),('period','en','m','Month',4,'','0000-00-00 00:00:00'),('period','en','q','Quarter',5,'','0000-00-00 00:00:00'),('period','en','w','Week',3,'','0000-00-00 00:00:00'),('period','en','y','Year',6,'','0000-00-00 00:00:00');

INSERT INTO `pvlng_type` VALUES (0,'Alias','Alias channel acts like the referenced channel','Alias','',0,0,0,1,'/images/ico/arrow_180.png'),(1,'Power plant','A power plant groups mostly inverters','NoModel','',-1,0,0,0,'/images/ico/building.png'),(2,'Inverter','A Inverter groups mostly energy, voltage and current channels','NoModel','',-1,0,0,0,'/images/ico/exclamation_frame.png'),(3,'Building','A building groups several other things','NoModel','',-1,0,0,0,'/images/ico/home.png'),(4,'Multi-Sensor','A sensor with multiple channels','NoModel','',-1,0,0,0,'/images/ico/wooden_box.png'),(5,'Group','A generic group','NoModel','',-1,0,0,0,'/images/ico/folders_stack.png'),(10,'Random','A random channel delivers data \"valid_from\" ... \"valid_to\" with variance +-\"threshold\"','Random','',0,1,0,1,'/images/ico/ghost.png'),(11,'Fixed value','Use this to display a horz. line','Fix','',0,1,0,1,'/images/ico/chart_arrow.png'),(12,'Estimate','Show the the daily estimate of production','Estimate','Wh',0,1,0,1,'/images/ico/plug.png'),(15,'Ratio calculator','A ratio calculator calc the ration between 2 child entities (groups or channels)','Ratio','%',2,1,0,1,'/images/ico/edit_percent.png'),(16,'Accumulator','An accumulator groups channels with same type','Accumulator','',-1,1,0,1,'/images/ico/calculator_scientific.png'),(17,'Differentiator','An differentiator groups channels with same type','Differentiator','',-1,1,0,1,'/images/ico/calculator_scientific.png'),(18,'Full Differentiator','An differentiator groups channels with same type','DifferentiatorFull','',-1,1,0,1,'/images/ico/calculator_scientific.png'),(19,'Sensor to meter','Transform data of a sensor to meter data','SensorToMeter','',1,1,0,1,'/images/ico/calculator_scientific.png'),(20,'Import / Export','Calculates imort or export by consumption and production','InternalConsumption','',2,1,0,1,'/images/ico/calculator_scientific.png'),(21,'Average','An average calculator of channels with the same type','Average','',-1,1,0,1,'/images/ico/calculator_scientific.png'),(22,'Calculator','Uses resolution to perform only a calculation','Calculator','',1,1,0,1,'/images/ico/calculator_scientific.png'),(23,'History','Uses historic data, last x days, same days last years for display','History','',1,1,0,1,'/images/ico/calculator_scientific.png'),(24,'Baseline','Shows a baseline for sensors on lowest value','Baseline','',1,1,0,1,'/images/ico/calculator_scientific.png'),(30,'Dashboard channel','A proxy channel for dashboard display','Dashboard','',1,1,0,1,'/images/ico/dashboard.png'),(40,'SMA Sunny Webbox','Accept JSON from a SMA Sunny Webbox','SMA\\Webbox','',-1,0,1,0,'/images/ico/sma_webbox.png'),(41,'SMA Inverter','Accept JSON from a SMA Webbox','SMA\\Webbox','',-1,0,1,0,'/images/ico/sma_inverter.png'),(42,'SMA Sensorbox','Accept JSON from a SMA Webbox','SMA\\Webbox','',-1,0,1,0,'/images/ico/sma_sensorbox.png'),(50,'Energy meter, absolute','A enegry meter counts production or consumption','Meter','Wh',0,1,1,1,'/images/ico/plug.png'),(51,'Power sensor','A power sensor tracks actual consumption or production','Sensor','W',0,1,1,1,'/images/ico/plug.png'),(52,'Voltage sensor','A voltage sensor tracks actual voltage','Sensor','V',0,1,1,1,'/images/ico/dashboard.png'),(53,'Current sensor','An current sensor tracks actual current','Sensor','A',0,1,1,1,'/images/ico/lightning.png'),(54,'Gas sensor','A gas sensor tracks actual consumption or production','Sensor','m³/h',0,1,1,1,'/images/ico/fire.png'),(55,'Heat sensor','A heat sensor tracks actual consumption or production','Sensor','W',0,1,1,1,'/images/ico/fire_big.png'),(56,'Humidity sensor','A humidity sensor tracks actual humitiy','Sensor','%',0,1,1,1,'/images/ico/weather_cloud.png'),(57,'Luminosity sensor','A luminosity sensor tracks actual luminosity','Sensor','lm',0,1,1,1,'/images/ico/light_bulb.png'),(58,'Pressure sensor','A pressure sensor tracks actual pressure','Sensor','hPa',0,1,1,1,'/images/ico/umbrella.png'),(59,'Radiation sensor','A radiation sensor tracks actual radiation','Sensor','µSV/h',0,1,1,1,'/images/ico/brightness.png'),(60,'Temperature sensor','A temperature sensor tracks actual temperature','Sensor','°C',0,1,1,1,'/images/ico/application_monitor.png'),(61,'Valve sensor','A valve sensor tracks actual valve position','Sensor','°',0,1,1,1,'/images/ico/wheel.png'),(62,'Water sensor','A water sensor tracks actual water consumption or production','Sensor','m³/h',0,1,1,1,'/images/ico/water.png'),(63,'Windspeed sensor','A windspeed sensor tracks actual windspeed','Sensor','m/s',0,1,1,1,'/images/ico/paper_plane.png'),(64,'Irradiation sensor','An irradiation sensor tracks actual irradiation','Sensor','W/m²',0,1,1,1,'/images/ico/brightness.png'),(65,'Time','Counts time based data, e.g working hours','Meter','h',0,1,1,1,'/images/ico/clock.png'),(66,'Frequency sensor','A frequency sensor tracks actual frequencies','Sensor','Hz',0,1,1,1,'/images/ico/dashboard.png'),(70,'Gas meter','A gas maeter tracks consumption or production over time','Meter','m³',0,1,1,1,'/images/ico/fire.png'),(71,'Radiation meter','A radiation meter tracks radiation over time','Meter','µSV',0,1,1,1,'/images/ico/brightness.png'),(72,'Water meter','A water meter tracks water consumption or production over time','Meter','m³',0,1,1,1,'/images/ico/water.png'),(90,'Power sensor counter','A power sensor counter tracks actual consumption or production','Counter','W',0,1,1,1,'/images/ico/plug.png'),(91,'Switch','A switch tracks only state changes','Switcher','',0,1,1,1,'/images/ico/ui_check_boxes.png'),(100,'PV-Log Plant','Readout plant data for PV-Log JSON import','PVLog\\Plant','',-1,1,0,0,'/images/ico/pv_log_sum.png'),(101,'PV-Log Inverter','Readout inverter data for PV-Log JSON import','PVLog\\Inverter','',-1,1,0,0,'/images/ico/pv_log.png'),(102,'PV-Log Plant (r2)','Readout plant data for PV-Log JSON import','PVLog2\\Plant','',-1,1,0,0,'/images/ico/pv_log_sum.png'),(103,'PV-Log Inverter (r2)','Readout inverter data for PV-Log JSON import','PVLog2\\Inverter','',-1,0,0,0,'/images/ico/pv_log.png'),(110,'Sonnenertrag JSON','Readout plant/inverter data for Sonnenertrag JSON import','Sonnenertrag\\JSON','',-1,1,0,0,'/images/ico/sonnenertrag.png');
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- ------------------------------------------------------
-- Initial channel data and demo views
-- ------------------------------------------------------

INSERT INTO `pvlng_channel`
(`id`, `name`, `description`, `type`, `resolution`, `unit`, `decimals`, `meter`, `cost`, `threshold`, `valid_from`, `valid_to`, `comment`) VALUES
(1, 'DO NOT TOUCH', 'Dummy for tree root',    0, 0, '', 2, 0, 0, NULL, NULL, NULL, ''),
(2, 'RANDOM Temperature sensor', '15 ... 25, &plusmn;0.1', 10, 1, '°C', 1, 0, 0, 0.1, 15, 25, ''),
(3, 'RANDOM Energy meter', '0 ... &infin;, +0.05',   10, 1000, 'Wh', 0, 1, 0.0002, 0.05, 0, 10000000000, ''),
(4, 'Dashboard', 'Dashboard group', 5, 1, '', 2, 0, 0, NULL, NULL, NULL, ''),
(5, 'Temperature sensor', 'RANDOM Temperature sensor for Dashboard', 30, 1, '°C', 1, 0, 0, NULL, 0, 40, '> 10 : #BFB\n10 > 20 : #FFB\n20 > : #FBB');

INSERT INTO `pvlng_config` (`key`, `value`, `comment`, `type`) VALUES
('Currency', 'EUR', 'Costs currency', 'str'),
('CurrencyDecimals', 2, 'Costs currency decimals', 'num'),
('LogInvalid', 0, 'Log invalid values', 'str'),
('TimeStep', 60, 'Reading time step in seconds', 'num');

INSERT INTO `pvlng_tree` (`id`, `lft`, `rgt`, `entity`) VALUES
(1, 1, 12, 1), (2, 2, 3, 2), (3, 4, 5, 3), (4, 6, 11, 4), (5, 7, 10, 5), (6, 8, 9, 2);

INSERT INTO `pvlng_view` (`name`, `data`, `public`, `slug`) VALUES
('Demo', '{\"p\":\"\",\"2\":\"{\\\"axis\\\":1,\\\"type\\\":\\\"spline\\\",\\\"consumption\\\":false,\\\"style\\\":\\\"Solid\\\",\\\"width\\\":2,\\\"color\\\":\\\"#4572a7\\\",\\\"coloruseneg\\\":true,\\\"colorneg\\\":\\\"#db843d\\\",\\\"threshold\\\":20,\\\"min\\\":false,\\\"max\\\":false}\",\"3\":\"{\\\"axis\\\":2,\\\"type\\\":\\\"spline\\\",\\\"consumption\\\":false,\\\"style\\\":\\\"Solid\\\",\\\"width\\\":2,\\\"color\\\":\\\"#404040\\\",\\\"coloruseneg\\\":false,\\\"colorneg\\\":\\\"#404040\\\",\\\"threshold\\\":0,\\\"min\\\":false,\\\"max\\\":false}\"}', 0, 'demo');

INSERT INTO `pvlng_config` (`key`, `value`) VALUES
('dashboard', '[5]');

-- ------------------------------------------------------
-- Generate and show API key
-- ------------------------------------------------------

SELECT `getAPIkey`() AS `PVLng API key`;
