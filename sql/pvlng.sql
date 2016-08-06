-- ------------------------------------------------------
-- PVLng v2.13.0
--
-- ------------------------------------------------------
-- MySQL dump 10.13  Distrib 5.5.50, for debian-linux-gnu (i686)
--
-- Host: localhost    Database: pvlng
-- ------------------------------------------------------
-- Server version 5.5.50-0+deb8u1-log

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `pvlng_babelkit`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pvlng_babelkit` (
  `code_set` varchar(16) NOT NULL DEFAULT '',
  `code_lang` varchar(5) NOT NULL DEFAULT '',
  `code_code` varchar(50) NOT NULL DEFAULT '',
  `code_desc` text NOT NULL,
  `code_order` smallint(6) NOT NULL DEFAULT '0',
  `code_flag` char(1) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL DEFAULT '',
  `changed` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`code_set`,`code_lang`,`code_code`),
  KEY `code_set_code_code` (`code_set`,`code_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 PACK_KEYS=1 COMMENT='I18N';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `pvlng_babelkit_desc`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pvlng_babelkit_desc` (
  `code_set` varchar(16) NOT NULL DEFAULT '',
  `code_code` varchar(32) NOT NULL DEFAULT '',
  `code_desc` text NOT NULL,
  `changed` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`code_set`,`code_code`),
  CONSTRAINT `pvlng_babelkit_desc_ibfk_1` FOREIGN KEY (`code_set`, `code_code`) REFERENCES `pvlng_babelkit` (`code_set`, `code_code`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 PACK_KEYS=1 COMMENT='I18N';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `pvlng_changes`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pvlng_changes` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `table` enum('babelkit','channel','config','log','options','performance','performance_avg','reading_num','reading_str','tree','type','view') NOT NULL COMMENT 'Table name',
  `key` varchar(50) NOT NULL DEFAULT '' COMMENT 'Primary key value(s), for composed keys separated by "::" ',
  `field` varchar(50) NOT NULL DEFAULT '' COMMENT 'Field name',
  `timestamp` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'When was changed',
  `old` varchar(256) NOT NULL DEFAULT '' COMMENT 'Old value',
  `new` varchar(256) NOT NULL DEFAULT '' COMMENT 'New value',
  PRIMARY KEY (`id`),
  KEY `table` (`table`,`key`,`field`,`timestamp`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
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
/*!50003 CREATE*/ /*!50017 */ /*!50003 TRIGGER `pvlng_changes_bi` BEFORE INSERT ON `pvlng_changes` FOR EACH ROW
IF new.`timestamp`= 0 THEN
  SET new.`timestamp` = UNIX_TIMESTAMP();
END IF */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `pvlng_channel`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pvlng_channel` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `guid` char(39) DEFAULT NULL COMMENT 'Unique GUID',
  `name` varchar(255) NOT NULL DEFAULT '' COMMENT 'Unique identifier',
  `description` varchar(255) NOT NULL DEFAULT '' COMMENT 'Longer description',
  `serial` varchar(30) NOT NULL DEFAULT '',
  `channel` varchar(255) NOT NULL DEFAULT '',
  `type` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT 'pvlng_type -> id',
  `resolution` double NOT NULL DEFAULT '1',
  `unit` varchar(10) NOT NULL DEFAULT '',
  `decimals` tinyint(1) unsigned NOT NULL DEFAULT '2',
  `meter` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `numeric` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `offset` double NOT NULL DEFAULT '0',
  `adjust` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT 'allow auto adjustment of offset',
  `cost` double DEFAULT NULL COMMENT 'per unit or unit * h',
  `tariff` int(10) unsigned DEFAULT NULL,
  `threshold` double unsigned DEFAULT NULL,
  `valid_from` double DEFAULT NULL COMMENT 'Numeric min. acceptable value',
  `valid_to` double DEFAULT NULL COMMENT 'Numeric max. acceptable value',
  `public` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT 'Public channels don''t need API key to read',
  `tags` text NOT NULL COMMENT 'scope:value tags, one per line',
  `extra` text NOT NULL COMMENT 'Not visible field for models to store extra info',
  `comment` text NOT NULL COMMENT 'Internal comment',
  `icon` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE KEY `GUID` (`guid`),
  KEY `type` (`type`),
  KEY `tariff` (`tariff`),
  CONSTRAINT `pvlng_channel_ibfk_1` FOREIGN KEY (`type`) REFERENCES `pvlng_type` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `pvlng_channel_ibfk_2` FOREIGN KEY (`tariff`) REFERENCES `pvlng_tariff` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
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
/*!50003 CREATE*/ /*!50017 */ /*!50003 TRIGGER `pvlng_channel_au` AFTER UPDATE ON `pvlng_channel` FOR EACH ROW
BEGIN
  IF new.`adjust` = 1 THEN
     CALL `pvlng_changed`('channel', new.`id`, 'offset', 0, old.`offset`, new.`offset`);
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

--
-- Temporary table structure for view `pvlng_channel_view`
--

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
  `icon` tinyint NOT NULL,
  `tree` tinyint NOT NULL
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `pvlng_config`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pvlng_config` (
  `key` varchar(50) NOT NULL DEFAULT '',
  `value` varchar(1000) NOT NULL DEFAULT '',
  `comment` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 PACK_KEYS=1 COMMENT='Application settings';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `pvlng_dashboard`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pvlng_dashboard` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL DEFAULT '' COMMENT 'Unique name',
  `data` varchar(255) NOT NULL DEFAULT '' COMMENT 'Selected channels in JSON',
  `public` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `slug` varchar(50) NOT NULL DEFAULT '' COMMENT 'Unique URL save slug',
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
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
/*!50003 CREATE*/ /*!50017 */ /*!50003 TRIGGER `pvlng_dashboard_bi` BEFORE INSERT ON `pvlng_dashboard` FOR EACH ROW
BEGIN
  SELECT `pvlng_slugify`(new.`name`) INTO @slug;
  SET new.`slug` = @slug;
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
/*!50003 CREATE*/ /*!50017 */ /*!50003 TRIGGER `pvlng_dashboard_bu` BEFORE UPDATE ON `pvlng_dashboard` FOR EACH ROW
BEGIN
  SELECT `pvlng_slugify`(new.`name`) INTO @slug;
  SET new.`slug` = @slug;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `pvlng_log`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pvlng_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `timestamp` datetime NOT NULL DEFAULT '2000-01-01 00:00:00',
  `scope` varchar(40) NOT NULL DEFAULT '',
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

--
-- Table structure for table `pvlng_options`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pvlng_options` (
  `key` varchar(50) NOT NULL DEFAULT '',
  `value` varchar(1000) NOT NULL DEFAULT '',
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 PACK_KEYS=1 COMMENT='Key-Value-Store';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `pvlng_performance`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pvlng_performance` (
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `action` enum('read','write') NOT NULL DEFAULT 'read',
  `time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'ms',
  KEY `timestamp` (`timestamp`)
) ENGINE=MEMORY DEFAULT CHARSET=utf8 COMMENT='Gather system performance';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `pvlng_performance_avg`
--

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

--
-- Temporary table structure for view `pvlng_performance_view`
--

SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `pvlng_performance_view` (
  `aggregation` tinyint NOT NULL,
  `action` tinyint NOT NULL,
  `timestamp` tinyint NOT NULL,
  `average` tinyint NOT NULL
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `pvlng_reading_count`
--

SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `pvlng_reading_count` (
  `id` tinyint NOT NULL,
  `timestamp` tinyint NOT NULL,
  `readings` tinyint NOT NULL
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `pvlng_reading_last`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pvlng_reading_last` (
  `id` smallint(5) unsigned NOT NULL,
  `timestamp` int(10) unsigned NOT NULL,
  `data` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Numeric readings';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `pvlng_reading_num`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pvlng_reading_num` (
  `id` smallint(5) unsigned NOT NULL,
  `timestamp` int(10) unsigned NOT NULL,
  `data` decimal(13,4) NOT NULL,
  PRIMARY KEY (`id`,`timestamp`),
  KEY `timestamp` (`timestamp`),
  KEY `id` (`id`)
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
IF new.`timestamp` = 0 THEN

    SET @NOW = UNIX_TIMESTAMP();

    SELECT IFNULL(`value`,0) INTO @SEC
      FROM `pvlng_settings`
     WHERE `scope` = 'model' AND `name` = '' AND `key` = 'DoubleRead';

    IF @SEC > 0 THEN
        SELECT COUNT(*) INTO @FOUND
          FROM `pvlng_reading_num`
         WHERE `id` = new.`id` AND `timestamp` BETWEEN @NOW-@SEC AND @NOW+@SEC;

        IF @FOUND THEN
            SIGNAL SQLSTATE '99999' SET MESSAGE_TEXT = 'DoubleRead';
        END IF;
    END IF;

    SET new.`timestamp` = @NOW;

END IF */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 */ /*!50003 TRIGGER `pvlng_reading_num_ai` AFTER INSERT ON `pvlng_reading_num` FOR EACH ROW
BEGIN
    REPLACE INTO `pvlng_reading_last` VALUES (new.`id`, new.`timestamp`, new.`data`);
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `pvlng_reading_num_calc`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pvlng_reading_num_calc` (
  `id` smallint(5) unsigned NOT NULL DEFAULT '0',
  `timestamp` int(10) unsigned NOT NULL DEFAULT '0',
  `data` decimal(13,4) NOT NULL DEFAULT '0.0000',
  PRIMARY KEY (`id`,`timestamp`),
  KEY `timestamp` (`timestamp`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Numeric readings'
/*!50100 PARTITION BY LINEAR KEY (id)
PARTITIONS 10 */;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 */ /*!50003 TRIGGER `pvlng_reading_num_calc_bi` BEFORE INSERT ON `pvlng_reading_num_calc` FOR EACH ROW
IF new.`timestamp` = 0 THEN

    SET new.`timestamp` = UNIX_TIMESTAMP();

END IF */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `pvlng_reading_num_tmp`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pvlng_reading_num_tmp` (
  `id` smallint(5) unsigned NOT NULL DEFAULT '0',
  `timestamp` int(11) NOT NULL DEFAULT '0',
  `data` decimal(13,4) NOT NULL DEFAULT '0.0000',
  PRIMARY KEY (`id`,`timestamp`)
) ENGINE=MEMORY DEFAULT CHARSET=utf8
/*!50100 PARTITION BY LINEAR KEY (id)
PARTITIONS 10 */;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Temporary table structure for view `pvlng_reading_statistics`
--

SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `pvlng_reading_statistics` (
  `guid` tinyint NOT NULL,
  `name` tinyint NOT NULL,
  `description` tinyint NOT NULL,
  `serial` tinyint NOT NULL,
  `channel` tinyint NOT NULL,
  `unit` tinyint NOT NULL,
  `type` tinyint NOT NULL,
  `icon` tinyint NOT NULL,
  `datetime` tinyint NOT NULL,
  `readings` tinyint NOT NULL
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `pvlng_reading_str`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pvlng_reading_str` (
  `id` smallint(5) unsigned NOT NULL,
  `timestamp` int(10) unsigned NOT NULL,
  `data` varchar(50) NOT NULL,
  PRIMARY KEY (`id`,`timestamp`),
  KEY `timestamp` (`timestamp`),
  KEY `id` (`id`)
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
IF new.`timestamp` = 0 THEN

    SET @NOW = UNIX_TIMESTAMP();

    SELECT IFNULL(`value`,0) INTO @SEC
      FROM `pvlng_settings`
     WHERE `scope` = 'model' AND `name` = '' AND `key` = 'DoubleRead';

    IF @SEC > 0 THEN
        SELECT COUNT(*) INTO @FOUND
          FROM `pvlng_reading_str`
         WHERE `id` = new.`id` AND `timestamp` BETWEEN @NOW-@SEC AND @NOW+@SEC;

        IF @FOUND THEN
            SIGNAL SQLSTATE '99999' SET MESSAGE_TEXT = 'DoubleRead';
        END IF;
    END IF;

    SET new.`timestamp` = @NOW;

END IF */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 */ /*!50003 TRIGGER `pvlng_reading_str_ai` AFTER INSERT ON `pvlng_reading_str` FOR EACH ROW
BEGIN
    REPLACE INTO `pvlng_reading_last` VALUES (new.`id`, new.`timestamp`, new.`data`);
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `pvlng_reading_str_tmp`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pvlng_reading_str_tmp` (
  `id` smallint(5) unsigned NOT NULL DEFAULT '0',
  `timestamp` int(11) NOT NULL DEFAULT '0',
  `data` varchar(50) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`,`timestamp`)
) ENGINE=MEMORY DEFAULT CHARSET=utf8
/*!50100 PARTITION BY LINEAR KEY (id)
PARTITIONS 10 */;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `pvlng_reading_tmp`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pvlng_reading_tmp` (
  `id` smallint(5) unsigned NOT NULL COMMENT 'pvlng_channel -> id',
  `start` int(10) unsigned NOT NULL COMMENT 'Generated for start .. end',
  `end` int(10) unsigned NOT NULL COMMENT 'Generated for start .. end',
  `lifetime` mediumint(8) unsigned NOT NULL COMMENT 'Lifetime of data',
  `uid` smallint(5) unsigned NOT NULL COMMENT 'Tempory data Id',
  `created` int(10) NOT NULL COMMENT 'Record created',
  PRIMARY KEY (`id`,`start`,`end`),
  UNIQUE KEY `uid` (`uid`),
  KEY `created` (`created`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Buffer and remember internal calculated data';
/*!40101 SET character_set_client = @saved_cs_client */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 */ /*!50003 TRIGGER `pvlng_reading_tmp_ad` AFTER DELETE ON `pvlng_reading_tmp` FOR EACH ROW
BEGIN
    DELETE FROM `pvlng_reading_num_tmp` WHERE `id` = old.`uid`;
    DELETE FROM `pvlng_reading_str_tmp` WHERE `id` = old.`uid`;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Temporary table structure for view `pvlng_reading_tmp_view`
--

SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `pvlng_reading_tmp_view` (
  `id` tinyint NOT NULL,
  `start` tinyint NOT NULL,
  `end` tinyint NOT NULL,
  `lifetime` tinyint NOT NULL,
  `uid` tinyint NOT NULL,
  `created` tinyint NOT NULL
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `pvlng_settings`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pvlng_settings` (
  `scope` enum('core','controller','model') NOT NULL DEFAULT 'core',
  `name` varchar(100) NOT NULL DEFAULT '',
  `key` varchar(100) NOT NULL DEFAULT '',
  `value` varchar(100) NOT NULL DEFAULT '',
  `order` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `description` varchar(1000) NOT NULL DEFAULT '',
  `type` enum('str','num','bool','option') NOT NULL DEFAULT 'str',
  `data` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`scope`,`name`,`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 PACK_KEYS=1 COMMENT='Application settings';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Temporary table structure for view `pvlng_settings_keys`
--

SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `pvlng_settings_keys` (
  `key` tinyint NOT NULL,
  `value` tinyint NOT NULL
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `pvlng_statistics`
--

SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `pvlng_statistics` (
  `database` tinyint NOT NULL,
  `table` tinyint NOT NULL,
  `data_length` tinyint NOT NULL,
  `data_length_mb` tinyint NOT NULL,
  `index_length` tinyint NOT NULL,
  `length` tinyint NOT NULL,
  `data_free` tinyint NOT NULL
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `pvlng_tariff`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pvlng_tariff` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL DEFAULT '',
  `comment` varchar(250) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE KEY `Tariff name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `pvlng_tariff_date`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pvlng_tariff_date` (
  `id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'pvlng_tariff -> id',
  `date` date NOT NULL DEFAULT '2000-01-01' COMMENT 'Start date for this tariff (incl.) ',
  `cost` float DEFAULT '0' COMMENT 'Fix costs per day, e.g. EUR / kWh',
  PRIMARY KEY (`id`,`date`),
  KEY `date` (`date`),
  CONSTRAINT `pvlng_tariff_date_ibfk_2` FOREIGN KEY (`id`) REFERENCES `pvlng_tariff` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `pvlng_tariff_time`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pvlng_tariff_time` (
  `id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'pvlng_tariff_date -> id',
  `date` date NOT NULL DEFAULT '2000-01-01' COMMENT 'pvlng_tariff_date -> date',
  `time` time NOT NULL DEFAULT '00:00:00' COMMENT 'Starting time (incl.)',
  `days` set('1','2','3','4','5','6','7') NOT NULL DEFAULT '1' COMMENT '1 Mo .. 7 Su',
  `tariff` float DEFAULT NULL COMMENT 'e.g. EUR / kWh',
  `comment` varchar(250) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`,`date`,`time`,`days`),
  KEY `days` (`days`),
  KEY `date` (`date`),
  KEY `time` (`time`),
  CONSTRAINT `pvlng_tariff_time_ibfk_1` FOREIGN KEY (`id`, `date`) REFERENCES `pvlng_tariff_date` (`id`, `date`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Temporary table structure for view `pvlng_tariff_view`
--

SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `pvlng_tariff_view` (
  `id` tinyint NOT NULL,
  `name` tinyint NOT NULL,
  `tariff_comment` tinyint NOT NULL,
  `date` tinyint NOT NULL,
  `cost` tinyint NOT NULL,
  `time` tinyint NOT NULL,
  `days` tinyint NOT NULL,
  `tariff` tinyint NOT NULL,
  `time_comment` tinyint NOT NULL
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `pvlng_tree`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pvlng_tree` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `lft` smallint(5) unsigned NOT NULL DEFAULT '0',
  `rgt` smallint(5) unsigned NOT NULL DEFAULT '0',
  `moved` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `entity` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT 'pvlng_channel -> id',
  `guid` varchar(39) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `rgt` (`rgt`),
  KEY `entity` (`entity`),
  KEY `lft_rgt` (`lft`,`rgt`),
  KEY `guid` (`guid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 PACK_KEYS=1 COMMENT='Structured channels';
/*!40101 SET character_set_client = @saved_cs_client */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 */ /*!50003 TRIGGER `pvlng_tree_bi` BEFORE INSERT ON `pvlng_tree` FOR EACH ROW
BEGIN
  SELECT `e`.`type`, `t`.`childs`
    INTO @TYPE, @CHILDS
    FROM `pvlng_channel` `e`
    JOIN `pvlng_type` `t` ON `e`.`type` = `t`.`id`
   WHERE `e`.`id` = new.`entity`;
   IF @TYPE = 0 OR @CHILDS != 0 THEN
    -- Aliases get always an own GUID
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

--
-- Temporary table structure for view `pvlng_tree_view`
--

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
  `tags` tinyint NOT NULL,
  `extra` tinyint NOT NULL,
  `comment` tinyint NOT NULL,
  `type_id` tinyint NOT NULL,
  `type` tinyint NOT NULL,
  `model` tinyint NOT NULL,
  `childs` tinyint NOT NULL,
  `read` tinyint NOT NULL,
  `write` tinyint NOT NULL,
  `graph` tinyint NOT NULL,
  `icon` tinyint NOT NULL,
  `alias` tinyint NOT NULL,
  `alias_of` tinyint NOT NULL,
  `entity_of` tinyint NOT NULL,
  `level` tinyint NOT NULL,
  `haschilds` tinyint NOT NULL,
  `lower` tinyint NOT NULL,
  `upper` tinyint NOT NULL
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `pvlng_type`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pvlng_type` (
  `id` smallint(5) unsigned NOT NULL DEFAULT '0',
  `name` varchar(60) NOT NULL DEFAULT '',
  `description` varchar(255) NOT NULL DEFAULT '',
  `model` varchar(30) NOT NULL DEFAULT 'Group',
  `unit` varchar(10) NOT NULL DEFAULT '',
  `type` enum('group','general','numeric','sensor','meter') NOT NULL DEFAULT 'group',
  `childs` tinyint(1) NOT NULL DEFAULT '0',
  `read` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `write` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `graph` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `icon` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`),
  KEY `childs` (`childs`),
  KEY `read` (`read`),
  KEY `write` (`write`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 PACK_KEYS=1 COMMENT='Channel types';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Temporary table structure for view `pvlng_type_icons`
--

SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `pvlng_type_icons` (
  `icon` tinyint NOT NULL,
  `name` tinyint NOT NULL
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `pvlng_view`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pvlng_view` (
  `name` varchar(50) NOT NULL DEFAULT '' COMMENT 'Chart name',
  `public` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT 'View type (private/public/mobile)',
  `data` text NOT NULL COMMENT 'Serialized channel data',
  `slug` varchar(50) NOT NULL DEFAULT '' COMMENT 'URL-save slug',
  PRIMARY KEY (`name`,`public`),
  UNIQUE KEY `slug` (`slug`),
  KEY `public` (`public`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 PACK_KEYS=1 COMMENT='View variants';
/*!40101 SET character_set_client = @saved_cs_client */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 */ /*!50003 TRIGGER `pvlng_view_bi` BEFORE INSERT ON `pvlng_view` FOR EACH ROW
BEGIN
    SET @slug = `pvlng_slugify`(new.`name`);

    IF (new.`public` = 0) THEN -- private
        SET new.`slug` = CONCAT('p-', @slug);
    ELSEIF (new.`public` = 2) THEN -- mobile
        SET new.`slug` = CONCAT('m-', @slug);
    ELSE -- public
        SET new.`slug` = @slug;
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
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 */ /*!50003 TRIGGER `pvlng_view_bu` BEFORE UPDATE ON `pvlng_view` FOR EACH ROW
BEGIN
    SET @slug = `pvlng_slugify`(new.`name`);

    IF (new.`public` = 0) THEN -- private
        SET new.`slug` = CONCAT('p-', @slug);
    ELSEIF (new.`public` = 2) THEN -- mobile
        SET new.`slug` = CONCAT('m-', @slug);
    ELSE -- public
        SET new.`slug` = @slug;
    END IF;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Final view structure for view `pvlng_channel_view`
--

/*!50001 DROP TABLE IF EXISTS `pvlng_channel_view`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_general_ci */;
/*!50001 CREATE  */
/*!50013   */
/*!50001 VIEW `pvlng_channel_view` AS select `c`.`id` AS `id`,`c`.`guid` AS `guid`,if(`a`.`id`,`a`.`name`,`c`.`name`) AS `name`,if(`a`.`id`,`a`.`serial`,`c`.`serial`) AS `serial`,`c`.`channel` AS `channel`,if(`a`.`id`,`a`.`description`,`c`.`description`) AS `description`,if(`a`.`id`,`a`.`resolution`,`c`.`resolution`) AS `resolution`,if(`a`.`id`,`a`.`cost`,`c`.`cost`) AS `cost`,if(`a`.`id`,`a`.`numeric`,`c`.`numeric`) AS `numeric`,if(`a`.`id`,`a`.`offset`,`c`.`offset`) AS `offset`,if(`a`.`id`,`a`.`adjust`,`c`.`adjust`) AS `adjust`,if(`a`.`id`,`a`.`unit`,`c`.`unit`) AS `unit`,if(`a`.`id`,`a`.`decimals`,`c`.`decimals`) AS `decimals`,if(`a`.`id`,`a`.`meter`,`c`.`meter`) AS `meter`,if(`a`.`id`,`a`.`threshold`,`c`.`threshold`) AS `threshold`,if(`a`.`id`,`a`.`valid_from`,`c`.`valid_from`) AS `valid_from`,if(`a`.`id`,`a`.`valid_to`,`c`.`valid_to`) AS `valid_to`,if(`a`.`id`,`a`.`public`,`c`.`public`) AS `public`,`t`.`id` AS `type_id`,`t`.`name` AS `type`,`t`.`model` AS `model`,`t`.`childs` AS `childs`,if(`ta`.`id`,`ta`.`read`,`t`.`read`) AS `read`,`t`.`write` AS `write`,if(`ta`.`id`,`ta`.`graph`,`t`.`graph`) AS `graph`,if(`a`.`id`,`a`.`icon`,`c`.`icon`) AS `icon`,(select count(1) from `pvlng_tree` where (`pvlng_tree`.`entity` = `c`.`id`)) AS `tree` from ((((`pvlng_channel` `c` join `pvlng_type` `t` on((`c`.`type` = `t`.`id`))) left join `pvlng_tree` `tr` on((`c`.`channel` = `tr`.`guid`))) left join `pvlng_channel` `a` on((`tr`.`entity` = `a`.`id`))) left join `pvlng_type` `ta` on((`a`.`type` = `ta`.`id`))) where (`c`.`id` <> 1) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `pvlng_performance_view`
--

/*!50001 DROP TABLE IF EXISTS `pvlng_performance_view`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_general_ci */;
/*!50001 CREATE  */
/*!50013   */
/*!50001 VIEW `pvlng_performance_view` AS select `pvlng_performance_avg`.`aggregation` AS `aggregation`,`pvlng_performance_avg`.`action` AS `action`,unix_timestamp(concat(`pvlng_performance_avg`.`year`,'-',`pvlng_performance_avg`.`month`,'-',`pvlng_performance_avg`.`day`,' ',`pvlng_performance_avg`.`hour`)) AS `timestamp`,`pvlng_performance_avg`.`average` AS `average` from `pvlng_performance_avg` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `pvlng_reading_count`
--

/*!50001 DROP TABLE IF EXISTS `pvlng_reading_count`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE  */
/*!50013   */
/*!50001 VIEW `pvlng_reading_count` AS select `pvlng_reading_num`.`id` AS `id`,max(`pvlng_reading_num`.`timestamp`) AS `timestamp`,count(`pvlng_reading_num`.`id`) AS `readings` from `pvlng_reading_num` group by `pvlng_reading_num`.`id` union select `pvlng_reading_str`.`id` AS `id`,max(`pvlng_reading_str`.`timestamp`) AS `MAX(``timestamp``)`,count(`pvlng_reading_str`.`id`) AS `COUNT(id)` from `pvlng_reading_str` group by `pvlng_reading_str`.`id` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `pvlng_reading_statistics`
--

/*!50001 DROP TABLE IF EXISTS `pvlng_reading_statistics`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE  */
/*!50013   */
/*!50001 VIEW `pvlng_reading_statistics` AS select `c`.`guid` AS `guid`,`c`.`name` AS `name`,`c`.`description` AS `description`,`c`.`serial` AS `serial`,`c`.`channel` AS `channel`,`c`.`unit` AS `unit`,`t`.`name` AS `type`,`t`.`icon` AS `icon`,from_unixtime(`u`.`timestamp`) AS `datetime`,ifnull(`u`.`readings`,0) AS `readings` from ((`pvlng_channel` `c` join `pvlng_type` `t` on((`c`.`type` = `t`.`id`))) left join `pvlng_reading_count` `u` on((`c`.`id` = `u`.`id`))) where ((`t`.`childs` = 0) and `t`.`write`) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `pvlng_reading_tmp_view`
--

/*!50001 DROP TABLE IF EXISTS `pvlng_reading_tmp_view`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_general_ci */;
/*!50001 CREATE  */
/*!50013   */
/*!50001 VIEW `pvlng_reading_tmp_view` AS select `pvlng_reading_tmp`.`id` AS `id`,`pvlng_reading_tmp`.`start` AS `start`,`pvlng_reading_tmp`.`end` AS `end`,`pvlng_reading_tmp`.`lifetime` AS `lifetime`,`pvlng_reading_tmp`.`uid` AS `uid`,from_unixtime(`pvlng_reading_tmp`.`created`) AS `created` from `pvlng_reading_tmp` order by `pvlng_reading_tmp`.`created` desc */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `pvlng_settings_keys`
--

/*!50001 DROP TABLE IF EXISTS `pvlng_settings_keys`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE  */
/*!50013   */
/*!50001 VIEW `pvlng_settings_keys` AS select concat(`pvlng_settings`.`scope`,if((`pvlng_settings`.`name` <> ''),concat('.',`pvlng_settings`.`name`),''),'.',`pvlng_settings`.`key`) AS `key`,`pvlng_settings`.`value` AS `value` from `pvlng_settings` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `pvlng_statistics`
--

/*!50001 DROP TABLE IF EXISTS `pvlng_statistics`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE  */
/*!50013   */
/*!50001 VIEW `pvlng_statistics` AS select `information_schema`.`TABLES`.`TABLE_SCHEMA` AS `database`,`information_schema`.`TABLES`.`TABLE_NAME` AS `table`,`information_schema`.`TABLES`.`DATA_LENGTH` AS `data_length`,((`information_schema`.`TABLES`.`DATA_LENGTH` / 1024) / 1024) AS `data_length_mb`,`information_schema`.`TABLES`.`INDEX_LENGTH` AS `index_length`,(`information_schema`.`TABLES`.`DATA_LENGTH` + `information_schema`.`TABLES`.`INDEX_LENGTH`) AS `length`,`information_schema`.`TABLES`.`DATA_FREE` AS `data_free` from `information_schema`.`TABLES` where ((`information_schema`.`TABLES`.`TABLE_SCHEMA` = 'pvlng') and (`information_schema`.`TABLES`.`TABLE_NAME` like 'pvlng_%') and (`information_schema`.`TABLES`.`ENGINE` is not null)) group by `information_schema`.`TABLES`.`TABLE_SCHEMA`,`information_schema`.`TABLES`.`TABLE_NAME` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `pvlng_tariff_view`
--

/*!50001 DROP TABLE IF EXISTS `pvlng_tariff_view`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE  */
/*!50013   */
/*!50001 VIEW `pvlng_tariff_view` AS select `t1`.`id` AS `id`,`t1`.`name` AS `name`,`t1`.`comment` AS `tariff_comment`,`t2`.`date` AS `date`,`t2`.`cost` AS `cost`,`t3`.`time` AS `time`,`t3`.`days` AS `days`,`t3`.`tariff` AS `tariff`,`t3`.`comment` AS `time_comment` from ((`pvlng_tariff` `t1` left join `pvlng_tariff_date` `t2` on((`t1`.`id` = `t2`.`id`))) left join `pvlng_tariff_time` `t3` on(((`t2`.`id` = `t3`.`id`) and (`t2`.`date` = `t3`.`date`)))) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `pvlng_tree_view`
--

/*!50001 DROP TABLE IF EXISTS `pvlng_tree_view`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_general_ci */;
/*!50001 CREATE  */
/*!50013   */
/*!50001 VIEW `pvlng_tree_view` AS select `n`.`id` AS `id`,`n`.`entity` AS `entity`,ifnull(`n`.`guid`,`c`.`guid`) AS `guid`,if(`co`.`id`,`co`.`name`,`c`.`name`) AS `name`,if(`co`.`id`,`co`.`serial`,`c`.`serial`) AS `serial`,`c`.`channel` AS `channel`,if(`co`.`id`,`co`.`description`,`c`.`description`) AS `description`,if(`co`.`id`,`co`.`resolution`,`c`.`resolution`) AS `resolution`,if(`co`.`id`,`co`.`cost`,`c`.`cost`) AS `cost`,if(`co`.`id`,`co`.`meter`,`c`.`meter`) AS `meter`,if(`co`.`id`,`co`.`numeric`,`c`.`numeric`) AS `numeric`,if(`co`.`id`,`co`.`offset`,`c`.`offset`) AS `offset`,if(`co`.`id`,`co`.`adjust`,`c`.`adjust`) AS `adjust`,if(`co`.`id`,`co`.`unit`,`c`.`unit`) AS `unit`,if(`co`.`id`,`co`.`decimals`,`c`.`decimals`) AS `decimals`,if(`co`.`id`,`co`.`threshold`,`c`.`threshold`) AS `threshold`,if(`co`.`id`,`co`.`valid_from`,`c`.`valid_from`) AS `valid_from`,if(`co`.`id`,`co`.`valid_to`,`c`.`valid_to`) AS `valid_to`,if(`co`.`id`,`co`.`public`,`c`.`public`) AS `public`,if(`co`.`id`,`co`.`tags`,`c`.`tags`) AS `tags`,if(`co`.`id`,`co`.`extra`,`c`.`extra`) AS `extra`,if(`co`.`id`,`co`.`comment`,`c`.`comment`) AS `comment`,`t`.`id` AS `type_id`,`t`.`name` AS `type`,`t`.`model` AS `model`,`t`.`childs` AS `childs`,`t`.`read` AS `read`,`t`.`write` AS `write`,`t`.`graph` AS `graph`,if(`co`.`id`,`co`.`icon`,`c`.`icon`) AS `icon`,`ca`.`id` AS `alias`,`ta`.`id` AS `alias_of`,`ta`.`entity` AS `entity_of`,(((count(0) - 1) + (`n`.`lft` > 1)) + 1) AS `level`,round((((`n`.`rgt` - `n`.`lft`) - 1) / 2),0) AS `haschilds`,((((min(`p`.`rgt`) - `n`.`rgt`) - (`n`.`lft` > 1)) / 2) > 0) AS `lower`,((`n`.`lft` - max(`p`.`lft`)) > 1) AS `upper` from ((((((`pvlng_tree` `n` join `pvlng_tree` `p`) join `pvlng_channel` `c` on((`n`.`entity` = `c`.`id`))) join `pvlng_type` `t` on((`c`.`type` = `t`.`id`))) left join `pvlng_channel` `ca` on(((if(`t`.`childs`,`n`.`guid`,`c`.`guid`) = `ca`.`channel`) and (`ca`.`type` = 0)))) left join `pvlng_tree` `ta` on((`c`.`channel` = `ta`.`guid`))) left join `pvlng_channel` `co` on(((`ta`.`entity` = `co`.`id`) and (`c`.`type` = 0)))) where ((`n`.`lft` between `p`.`lft` and `p`.`rgt`) and ((`p`.`id` <> `n`.`id`) or (`n`.`lft` = 1))) group by `n`.`id` order by `n`.`lft` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `pvlng_type_icons`
--

/*!50001 DROP TABLE IF EXISTS `pvlng_type_icons`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_general_ci */;
/*!50001 CREATE  */
/*!50013   */
/*!50001 VIEW `pvlng_type_icons` AS select `pvlng_type`.`icon` AS `icon`,group_concat(`pvlng_type`.`name` order by `pvlng_type`.`name` ASC separator ', ') AS `name` from `pvlng_type` where (`pvlng_type`.`id` <> 0) group by `pvlng_type`.`icon` order by group_concat(`pvlng_type`.`name` order by `pvlng_type`.`name` ASC separator ',') */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Dumping events for database 'pvlng'
--
/*!50106 SET @save_time_zone= @@TIME_ZONE */ ;
DELIMITER ;;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;;
/*!50003 SET character_set_client  = utf8mb4 */ ;;
/*!50003 SET character_set_results = utf8mb4 */ ;;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;;
/*!50003 SET sql_mode              = '' */ ;;
/*!50003 SET @saved_time_zone      = @@time_zone */ ;;
/*!50003 SET time_zone             = 'SYSTEM' */ ;;
/*!50106 CREATE*/ /*!50117 */ /*!50106 EVENT `pvlng_aggregate_performance` ON SCHEDULE EVERY 1 HOUR STARTS '2000-01-01 00:00:00' ON COMPLETION PRESERVE ENABLE DO CALL `aggregatePerformance`() */ ;;
/*!50003 SET time_zone             = @saved_time_zone */ ;;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;;
/*!50003 SET character_set_client  = @saved_cs_client */ ;;
/*!50003 SET character_set_results = @saved_cs_results */ ;;
/*!50003 SET collation_connection  = @saved_col_connection */ ;;
DELIMITER ;;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;;
/*!50003 SET character_set_client  = utf8mb4 */ ;;
/*!50003 SET character_set_results = utf8mb4 */ ;;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;;
/*!50003 SET sql_mode              = '' */ ;;
/*!50003 SET @saved_time_zone      = @@time_zone */ ;;
/*!50003 SET time_zone             = 'SYSTEM' */ ;;
/*!50106 CREATE*/ /*!50117 */ /*!50106 EVENT `pvlng_gc` ON SCHEDULE EVERY 1 DAY STARTS '2000-01-01 00:00:00' ON COMPLETION PRESERVE ENABLE DO BEGIN

    -- Remove outdated calculated rows
    DELETE FROM `pvlng_reading_tmp`
            -- Remove out-dated data older 1 day
     WHERE `created` BETWEEN 0 AND UNIX_TIMESTAMP()-86400
            -- Remove hanging calulations, 300 sec. must be enough...
        OR `created` < 0 AND -`created` < UNIX_TIMESTAMP()-300;

    -- Remove orphan calculated rows
    DELETE FROM `pvlng_reading_num_tmp` WHERE `id` NOT IN (SELECT `uid` FROM `pvlng_reading_tmp`);
    DELETE FROM `pvlng_reading_str_tmp` WHERE `id` NOT IN (SELECT `uid` FROM `pvlng_reading_tmp`);

    -- Optimze working tables
    OPTIMIZE TABLE `pvlng_reading_tmp`;
    OPTIMIZE TABLE `pvlng_reading_num_tmp`;
    OPTIMIZE TABLE `pvlng_reading_str_tmp`;
    OPTIMIZE TABLE `pvlng_reading_last`;

END */ ;;
/*!50003 SET time_zone             = @saved_time_zone */ ;;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;;
/*!50003 SET character_set_client  = @saved_cs_client */ ;;
/*!50003 SET character_set_results = @saved_cs_results */ ;;
/*!50003 SET collation_connection  = @saved_col_connection */ ;;
DELIMITER ;
/*!50106 SET TIME_ZONE= @save_time_zone */ ;

--
-- Dumping routines for database 'pvlng'
--
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
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
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
CREATE  FUNCTION `GUID`() RETURNS char(39) CHARSET utf8
BEGIN
    SET @GUID = MD5(UUID());
    -- Build 8 blocks 4 chars each, devided by a hyphen
    RETURN CONCAT_WS( '-',
        SUBSTRING(@GUID, 1,4), SUBSTRING(@GUID, 5,4), SUBSTRING(@GUID, 9,4),
        SUBSTRING(@GUID,13,4), SUBSTRING(@GUID,17,4), SUBSTRING(@GUID,21,4),
        SUBSTRING(@GUID,25,4), SUBSTRING(@GUID,29,4)
    );
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
CREATE  FUNCTION `pvlng_APIkey`() RETURNS varchar(36) CHARSET utf8
BEGIN
    SELECT `value` INTO @KEY FROM `pvlng_config` WHERE `key` = 'APIKey';
    IF @KEY IS NULL THEN
        SET @KEY = UUID();
        INSERT INTO `pvlng_config`
                    (`key`, `value`, `comment`)
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
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
CREATE  FUNCTION `pvlng_bool`(`in_val` char(5)) RETURNS tinyint(1) unsigned
    NO SQL
BEGIN
    -- Valid (not case-sensitive) values for TRUE (return as 1): 1,x,on,y,yes,true
    SET in_val = LOWER(in_val);
    IF in_val = '1' OR in_val = 'x' OR in_val = 'on' OR in_val = 'yes' OR in_val = 'y' OR in_val = 'true' THEN
        RETURN 1;
    ELSE
        RETURN 0;
    END IF;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
CREATE  FUNCTION `pvlng_guid`() RETURNS char(39) CHARSET utf8
BEGIN
    SET @GUID = MD5(UUID());
    -- Build 8 blocks 4 chars each, devided by a hyphen
    RETURN CONCAT_WS( '-',
        SUBSTRING(@GUID, 1,4), SUBSTRING(@GUID, 5,4), SUBSTRING(@GUID, 9,4),
        SUBSTRING(@GUID,13,4), SUBSTRING(@GUID,17,4), SUBSTRING(@GUID,21,4),
        SUBSTRING(@GUID,25,4), SUBSTRING(@GUID,29,4)
    );
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
CREATE  FUNCTION `pvlng_id`() RETURNS char(32) CHARSET utf8
BEGIN
  SELECT `value` INTO @ID FROM `pvlng_config` WHERE `key` = 'Installation';
  IF @ID IS NULL THEN
    -- Range of 100000 .. 999999
    SET @ID = MD5(RAND());
    INSERT INTO `pvlng_config` (`key`, `value`, `comment`)
       VALUES ('Installation', @ID, 'Unique PVLng installation Id');
  END IF;
  RETURN @ID;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
CREATE  FUNCTION `pvlng_reading_tmp_start`(`in_id` smallint unsigned, `in_start` int unsigned, `in_end` int unsigned, `in_lifetime` mediumint unsigned) RETURNS smallint(6)
BEGIN
    -- Handler for "Duplicate entry '%s' for key %d"
    DECLARE EXIT HANDLER FOR 1062
        -- Insert failed, so check existing data
        -- created < 0 - other process is just creating the data,
        --               return 0 as "have to wait" marker
        -- created > 0 - return uid to mark correct data
        RETURN (
            SELECT IF(`created` < 0, 0, `uid`)
              FROM `pvlng_reading_tmp`
             WHERE `id`    = in_id
               AND `start` = in_start
               AND `end`   = in_end
        );

    -- Remove outdated records, older than lifetime for this Id and time range
    DELETE FROM `pvlng_reading_tmp`
     WHERE `id`    = in_id
       AND `start` = in_start
       AND `end`   = in_end
       AND `created` BETWEEN 0 AND UNIX_TIMESTAMP()-`lifetime`-1;

    SET @UID = 1 + FLOOR(RAND()*32766);

    -- Try to insert initial row
    INSERT INTO `pvlng_reading_tmp`
         VALUES (in_id, in_start, in_end, in_lifetime, @UID, -UNIX_TIMESTAMP());

    -- Insert succeeded, return neg. uid as marker to create data
    RETURN -@UID;
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
CREATE  FUNCTION `pvlng_save_data`(`in_id` int unsigned, `in_timestamp` int unsigned, `in_data` char(100)) RETURNS tinyint(1)
    MODIFIES SQL DATA
BEGIN
    -- Return codes
    --  0 : Not inserted - double read
    -- -1 : Not inserted - outside valid range
    -- -2 : Not inserted - outside threshold
    --  1 : Data inserted

    -- Channel attributes
    SELECT `numeric`, `meter`, `offset`, `adjust`, IFNULL(`threshold`, 0), `valid_from`, `valid_to`
      INTO @numeric,  @meter,  @offset,  @adjust,  @threshold,             @valid_from,  @valid_to
      FROM `pvlng_channel`
     WHERE `id` = in_id;

    -- Double readings
    SELECT IFNULL(`value`,0) INTO @doubleRead
      FROM `pvlng_settings`
     WHERE `scope` = 'model' AND `key` = 'DoubleRead';

    IF in_timestamp = 0 THEN
        SET in_timestamp = UNIX_TIMESTAMP();
    END IF;

    -- Tests for numeric channels only
    IF @numeric THEN

        IF @doubleRead > 0 THEN
            SELECT COUNT(`id`) INTO @FOUND
              FROM `pvlng_reading_num`
             WHERE `id` = in_id
               AND `timestamp` BETWEEN in_timestamp - @doubleRead AND in_timestamp + @doubleRead;
            IF @FOUND THEN
                -- We got at least 1 row in time range, ignore
                RETURN 0;
            END IF;
        END IF;

        -- Make data numeric
        SET in_data = +in_data;

        -- Check valid range
        if (@valid_from IS NOT NULL AND in_data < @valid_from) OR
           (@valid_to   IS NOT NULL AND in_data > @valid_to) THEN
            -- Outside valid range
            RETURN -1;
        END IF;

        -- Check threshold range against average of last in_avg rows, at least 3!
        IF @meter = 0 AND @threshold > 0 THEN

            -- Use at 3 rows backwards
            SELECT AVG(`data`) INTO @avg
              FROM (SELECT `data`
                      FROM `pvlng_reading_num`
                     WHERE `id` = in_id
                     ORDER BY `timestamp` DESC
                     LIMIT 3) a;

            IF @avg IS NOT NULL AND
               ( @avg < in_data-@threshold OR @avg > in_data+@threshold ) THEN
                -- Outside threshold
                RETURN -2;
            END IF;
        END IF;

        -- Check meter channel adjustment
        IF @meter AND @offset AND @adjust THEN

            -- Get last reading before this timestamp
            SELECT `data` INTO @last
              FROM `pvlng_reading_num`
             WHERE `id` = in_id
               AND `timestamp` < in_timestamp
             ORDER BY `timestamp` DESC
             LIMIT 1;

            if @last IS NOT NULL AND @last < in_data THEN

                -- Get last offset before timestamp, if exists
                SELECT IFNULL(`old`, 0) INTO @offset_before
                  FROM `pvlng_changes`
                 WHERE `table` = 'channel'
                   AND `key` = in_id
                   AND `timestamp` < in_timestamp
                 ORDER BY `timestamp` DESC
                 LIMIT 1;

                -- Check, if this reading would adjust the offset
                IF @offset_before <> 0 THEN

                    SET @delta = @offset_before - @offset;

                    -- Reading must adjust the offset, update channel
                    UPDATE `pvlng_channel`
                       SET `offset` = `offset` + @delta
                     WHERE `id` = in_id;

                    -- Tramsform any further reading to reflect new offset
                    UPDATE `pvlng_reading_num`
                       SET `data` = `data` + @delta
                     WHERE `id` = in_id
                       AND `timestamp` > in_timestamp;

                    -- Adjust in_data before write
                    SET in_data = id_data + @delta;

                END IF;

            END IF;

        END IF;

        -- All fine, insert
        INSERT INTO `pvlng_reading_num` VALUES (in_id, in_timestamp, in_data);

    ELSE

        IF @doubleRead > 0 THEN
            SELECT COUNT(`id`) INTO @FOUND
              FROM `pvlng_reading_str`
             WHERE `id` = in_id
               AND `timestamp` BETWEEN in_timestamp - @doubleRead AND in_timestamp + @doubleRead;
            IF @FOUND THEN
                -- We got at least 1 row in time range, ignore
                RETURN 0;
            END IF;
        END IF;

        -- All fine, insert
        INSERT INTO `pvlng_reading_str` VALUES (in_id, in_timestamp, in_data);

    END IF;

    RETURN 1;

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
CREATE  FUNCTION `pvlng_save_num`(`in_id` int unsigned, `in_timestamp` int unsigned, `in_data` decimal(13,4)) RETURNS tinyint(1)
BEGIN

    -- Return codes
    --    0 : Not inserted - double read
    -- -1 : Not inserted - outside valid range
    -- -2 : Not inserted - outside threshold
    --    1 : Data inserted

    -- 1. Check for double readings
    IF in_timestamp = 0 THEN
        SET in_timestamp = UNIX_TIMESTAMP();
    END IF;

    SELECT IFNULL(`value`,0)
      INTO @range
      FROM `pvlng_settings`
     WHERE `scope` = 'model' AND `key` = 'DoubleRead';

    IF @range > 0 THEN
        SELECT COUNT(0)
          INTO @FOUND
          FROM `pvlng_reading_num`
         WHERE `id` = in_id
           AND `timestamp` BETWEEN in_timestamp - @range AND in_timestamp + @range;

        IF @FOUND THEN
            -- We got at least 1 row in time range, ignore
            RETURN 0;
        END IF;
    END IF;

    -- Channel attributes
    SELECT `meter`, `offset`, `adjust`, IFNULL(`threshold`, 0), `valid_from`, `valid_to`
      INTO @meter,  @offset,  @adjust,  @threshold,             @valid_from,  @valid_to
      FROM `pvlng_channel`
     WHERE `id` = in_id;

    -- Check valid range
    IF ( @valid_from IS NOT NULL AND in_data < @valid_from ) OR
         ( @valid_to IS NOT NULL AND in_data > @valid_to ) THEN
        RETURN -1;
    END IF;

    -- Check threshold range against average of last in_avg rows, at least 3!
    IF @meter = 0 AND @threshold > 0 THEN

        -- Use at 3 rows backwards
        SELECT AVG(`data`)
          INTO @avg
          FROM (
                  SELECT `data`
                    FROM `pvlng_reading_num`
                   WHERE `id` = in_id
                   ORDER BY `timestamp` DESC
                   LIMIT 3
          ) a;

        IF @avg IS NOT NULL AND
           ( @avg < in_data-@threshold OR @avg > in_data+@threshold ) THEN
            RETURN -2;
        END IF;
    END IF;

    -- Check meter channel adjustment
    IF @meter AND @offset AND @adjust THEN

        -- Get last reading before this timestamp
        SELECT `data`
          INTO @last
          FROM `pvlng_reading_num`
         WHERE `id` = in_id
           AND `timestamp` < in_timestamp
         ORDER BY `timestamp` DESC
         LIMIT 1;

        if @last IS NOT NULL AND @last < in_data THEN

            -- Get last offset before timestamp, if exists
            SELECT IFNULL(`old`, 0)
              INTO @offset_before
              FROM `pvlng_changes`
             WHERE `table` = 'channel'
               AND `key` = in_id
               AND `timestamp` < in_timestamp
             ORDER BY `timestamp` DESC
             LIMIT 1;

            -- Check, if this reading would adjust the offset
            IF @offset_before <> 0 THEN

                SET @delta = @offset_before - @offset;

                -- Reading must adjust the offset, update channel
                UPDATE `pvlng_channel`
                   SET `offset` = `offset` + @delta
                 WHERE `id` = in_id;

                -- Tramsform any further reading to reflect new offset
                UPDATE `pvlng_reading_num`
                   SET `data` = `data` + @delta
                 WHERE `id` = in_id
                   AND `timestamp` > in_timestamp;

                -- Adjust in_data before write
                SET in_data = id_data + @delta;

            END IF;

        END IF;

    END IF;

    -- All fine, insert
    INSERT INTO `pvlng_reading_num` VALUES (in_id, in_timestamp, in_data);

    RETURN 1;

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
CREATE  FUNCTION `pvlng_save_switch`(`in_id` int unsigned, `in_timestamp` int unsigned, `in_data` varchar(50)) RETURNS tinyint(1)
BEGIN

    SELECT `numeric` INTO @numeric FROM `pvlng_channel` WHERE `id` = in_id;

  IF @numeric THEN

    SELECT `data`
      INTO @last
      FROM `pvlng_reading_num`
     WHERE `id` = in_id AND `timestamp` < in_timestamp
     ORDER BY `timestamp` DESC
     LIMIT 1;

    IF @last = in_data THEN
      RETURN 0;
    END IF;

    INSERT INTO `pvlng_reading_num` VALUES (in_id, in_timestamp, in_data);

  ELSE

    SELECT `data`
      INTO @last
      FROM `pvlng_reading_str`
     WHERE `id` = in_id AND `timestamp` < in_timestamp
     ORDER BY `timestamp` DESC
     LIMIT 1;

    IF @last = in_data THEN
      RETURN 0;
    END IF;

    INSERT INTO `pvlng_reading_str` VALUES (in_id, in_timestamp, in_data);

  END IF;

  RETURN 1;

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
CREATE  FUNCTION `pvlng_slugify`(`in_str` varchar(200)) RETURNS varchar(200) CHARSET utf8
    NO SQL
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
CREATE  FUNCTION `pvlng_tariff`(`in_id` int unsigned, `in_date` date, `in_time` varchar(10)) RETURNS decimal(9,3)
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
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
CREATE  FUNCTION `pvlng_timestamp`(`in_ms` char(1)) RETURNS bigint(13)
    NO SQL
BEGIN
    IF pvlng_bool(in_ms) = 0 THEN
        -- Seconds
        RETURN UNIX_TIMESTAMP();
    END IF;

    -- Micro seconds, http://stackoverflow.com/a/25889615
    RETURN (
        SELECT CONV(
                   CONCAT(SUBSTRING(uid,16,3), SUBSTRING(uid,10,4), SUBSTRING(uid,1,8)),
                   16, 10
               ) DIV 10000 - 141427 * 24 * 60 * 60 * 1000
          FROM (SELECT UUID() uid) t
    );
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
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
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
CREATE  PROCEDURE `deleteForeacstFromTodayOnwards`()
delete from pvlng_reading_num
where id between 679 and 682 and
from_unixtime(timestamp) >= current_date ;;
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
CREATE  PROCEDURE `pvlng_changed`(IN `in_table` varchar(50), IN `in_key` varchar(50), IN `in_field` varchar(50), IN `in_timestamp` int unsigned, IN `in_old` varchar(255), IN `in_new` varchar(255))
IF in_old <> in_new THEN
  INSERT INTO `pvlng_changes`
  (`table`, `key`, `field`, `timestamp`, `old`, `new`)
  VALUES
  (in_table, in_key, in_field, in_timestamp, in_old, in_new);
END IF ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
CREATE  PROCEDURE `pvlng_model_averageline`(IN `in_uid` smallint unsigned, IN `in_child` smallint unsigned, IN `in_p` tinyint)
BEGIN
    -- Calulated with the Hölder mean fomulas
    -- http://en.wikipedia.org/wiki/H%C3%B6lder_mean
    -- in_p ==  1 - arithmetic
    -- in_p == -1 - harmonic

    DECLARE iStart INT;
    DECLARE iEnd INT;
    DECLARE iCount INT DEFAULT 0;
    DECLARE fSum DECIMAL(13,4) DEFAULT 0;
    DECLARE fData DECIMAL(13,4);
    DECLARE fAvg DECIMAL(13,4);
    DECLARE bDone INT DEFAULT 0;

    DECLARE curs CURSOR FOR
     SELECT data
       FROM pvlng_reading_num
      WHERE id = in_child
        AND timestamp BETWEEN iStart AND iEnd;

    DECLARE CONTINUE HANDLER FOR NOT FOUND SET bDone = 1;

    SELECT start, `end`
      INTO iStart, iEnd
      FROM pvlng_reading_tmp
     WHERE uid = in_uid;

    OPEN curs;

    REPEAT

        FETCH curs INTO fData;

        IF bDone = 0 THEN
            SET iCount = iCount + 1;
            SET fSum = fSum + POW(fData, in_p);
        END IF;

    UNTIL bDone END REPEAT;

    CLOSE curs;

    if iCount > 0 THEN

        SET fAvg = POW(fSum / iCount, 1 / in_p);

        -- Get real min. and max. timestamps in selection range
        SELECT MIN(timestamp), MAX(timestamp)
          INTO iStart, iEnd
          FROM pvlng_reading_num
         WHERE id = in_child
           AND timestamp BETWEEN iStart AND iEnd;

        INSERT INTO pvlng_reading_num_tmp
             VALUES (in_uid, iStart, fAvg), (in_uid, iEnd, fAvg);

    END IF;

END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
CREATE  PROCEDURE `pvlng_model_baseline`(IN `in_uid` smallint unsigned, IN `in_child` smallint unsigned)
BEGIN
  DECLARE iStart INT;
  DECLARE iEnd INT;
  DECLARE iTimestampMin INT;
  DECLARE iTimestampMax INT;
  DECLARE fData DECIMAL(13,4);

  SELECT start, `end`
    INTO iStart, iEnd
    FROM pvlng_reading_tmp
   WHERE uid = in_uid;

  SELECT MIN(`timestamp`), MAX(`timestamp`), MIN(data)
    INTO iTimestampMin,    iTimestampMax,     fData
    FROM pvlng_reading_num
   WHERE id = in_child AND timestamp BETWEEN iStart AND iEnd;

  IF NOT ISNULL(fData) THEN
    INSERT INTO pvlng_reading_num_tmp
         VALUES (in_uid, iTimestampMin, fData),
                (in_uid, iTimestampMax, fData);
  END IF;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
CREATE  PROCEDURE `pvlng_model_sensortometer`(IN `in_uid` smallint unsigned, IN `in_child` smallint unsigned)
BEGIN

    DECLARE iStart INT;
    DECLARE iEnd INT;
    DECLARE iTimestamp INT;
    DECLARE fData DECIMAL(13,4);
    DECLARE iLast INT DEFAULT 0;
    DECLARE fSum DECIMAL(13,4) DEFAULT 0;
    DECLARE bDone INT DEFAULT 0;

    DECLARE curs CURSOR FOR
    SELECT `timestamp`, `data`
     FROM `pvlng_reading_num`
    WHERE `id` = in_child
      AND `timestamp` BETWEEN iStart AND iEnd;

    DECLARE CONTINUE HANDLER FOR NOT FOUND SET bDone = 1;

    SELECT `start`, `end`
      INTO iStart, iEnd
      FROM `pvlng_reading_tmp`
     WHERE `uid` = in_uid;

    OPEN curs;

    REPEAT

        FETCH curs INTO iTimestamp, fData;

        IF bDone = 0 THEN

            IF iLast THEN
                SET fSum = fSum + (iTimestamp - iLast) / 3600 * fData;
            END IF;

            INSERT INTO `pvlng_reading_num_tmp` VALUES (in_uid, iTimestamp, fSum);

            SET iLast = iTimestamp;

        END IF;

    UNTIL bDone END REPEAT;

    CLOSE curs;

END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
CREATE  PROCEDURE `pvlng_model_topline`(IN `in_uid` smallint unsigned, IN `in_child` smallint unsigned)
BEGIN
  DECLARE iStart INT;
  DECLARE iEnd INT;
  DECLARE iTimestampMin INT;
  DECLARE iTimestampMax INT;
  DECLARE fData DECIMAL(13,4);

  SELECT start, `end`
    INTO iStart, iEnd
    FROM pvlng_reading_tmp
   WHERE uid = in_uid;

  SELECT MIN(`timestamp`), MAX(`timestamp`), MAX(data)
    INTO iTimestampMin,    iTimestampMax,     fData
    FROM pvlng_reading_num
   WHERE id = in_child AND timestamp BETWEEN iStart AND iEnd;

  IF NOT ISNULL(fData) THEN
    INSERT INTO pvlng_reading_num_tmp
         VALUES (in_uid, iTimestampMin, fData),
                (in_uid, iTimestampMax, fData);
  END IF;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
CREATE  PROCEDURE `pvlng_reading_num_stats`()
BEGIN
    SELECT DATABASE() INTO @db;

    SET @sql = CONCAT("
        SELECT PARTITION_ORDINAL_POSITION partition
             , TABLE_ROWS rows
             , round(TABLE_ROWS / 1000, 1) `tsd. rows`
             , round(TABLE_ROWS / 1000000, 1) `mio. rows`
          FROM information_schema.PARTITIONS
         WHERE TABLE_SCHEMA = '", @db, "' AND TABLE_NAME = 'pvlng_reading_num'
    ");

    PREPARE _sql FROM @sql;
    EXECUTE _sql;
    DEALLOCATE PREPARE _sql;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
CREATE  PROCEDURE `pvlng_reading_tmp_done`(IN `in_uid` smallint unsigned)
BEGIN

    -- Mark entry done for further reads
    UPDATE `pvlng_reading_tmp`
       SET `created` = ABS(`created`)
     WHERE `uid` = in_uid;

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
CREATE  PROCEDURE `pvlng_tariff_day`(IN `in_id` int unsigned, IN `in_date` date)
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
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed

-- ------------------------------------------------------
-- Translations and Channel types
-- ------------------------------------------------------

-- MySQL dump 10.13  Distrib 5.5.50, for debian-linux-gnu (i686)
--
-- Host: localhost    Database: pvlng
-- ------------------------------------------------------
-- Server version	5.5.50-0+deb8u1-log

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `pvlng_babelkit`
--

DROP TABLE IF EXISTS `pvlng_babelkit`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pvlng_babelkit` (
  `code_set` varchar(16) NOT NULL DEFAULT '',
  `code_lang` varchar(5) NOT NULL DEFAULT '',
  `code_code` varchar(50) NOT NULL DEFAULT '',
  `code_desc` text NOT NULL,
  `code_order` smallint(6) NOT NULL DEFAULT '0',
  `code_flag` char(1) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL DEFAULT '',
  `changed` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`code_set`,`code_lang`,`code_code`),
  KEY `code_set_code_code` (`code_set`,`code_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 PACK_KEYS=1 COMMENT='I18N';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pvlng_babelkit`
--

LOCK TABLES `pvlng_babelkit` WRITE;
/*!40000 ALTER TABLE `pvlng_babelkit` DISABLE KEYS */;
INSERT INTO `pvlng_babelkit` VALUES ('app','de','above','über',0,'','2014-07-02 13:31:51'),('app','de','AcceptChild','1:\"%2$s\" akzeptiert nur einen Sub-Kanal!||\r\nn:\"%2$s\" akzeptiert nur %1$d Sub-Kanäle!',0,'','2014-05-26 18:34:56'),('app','de','AcceptChildCount','Anzahl der erlaubten Sub-Kanäle',0,'','2014-09-26 11:11:39'),('app','de','Actions','Aktionen',0,'','0000-00-00 00:00:00'),('app','de','ActualState','Aktueller Datenstatus',0,'','0000-00-00 00:00:00'),('app','de','Add','Hinzufügen',0,'','0000-00-00 00:00:00'),('app','de','AddAnotherChild','Einen weiteren Kanal hinzufügen',0,'','0000-00-00 00:00:00'),('app','de','AddChannel','Einen Kanal zur Hierarchie hinzufügen',0,'','0000-00-00 00:00:00'),('app','de','AddChild','Sub-Kanal hinzufügen',0,'','0000-00-00 00:00:00'),('app','de','AddOneToManyChannels','Einen oder mehrere Kanäle zur Hierarchie hinzufügen',0,'','2014-02-08 19:58:43'),('app','de','AddTariffDate','Neuen Startdatumsbereich anlegen',0,'','2014-05-01 16:46:15'),('app','de','AdjustTemplate','Vorlage anpassen',0,'','2014-09-01 09:13:04'),('app','de','AdjustTemplateAfterwards','Korrigiere z.B. Dezimalstellen, Einheiten und Öffentlich-Kennzeichen im Nachgang.',0,'','2014-01-12 14:50:09'),('app','de','AdminAndPasswordRequired','Benutzername und Passwort sind erforderlich!',0,'','0000-00-00 00:00:00'),('app','de','Aggregation','Aggregation',0,'','0000-00-00 00:00:00'),('app','de','AliasCreated','Alias-Kanal erstellt',0,'','2014-07-19 19:00:41'),('app','de','AliasEntity','Alias-Kanal erstellen',0,'','0000-00-00 00:00:00'),('app','de','AliasesUpdated','Der Alias-Kanal wurden ebenfalls geändert.',0,'','0000-00-00 00:00:00'),('app','de','AliasStillExists','Es existiert bereits ein Alias-Kanal.',0,'','0000-00-00 00:00:00'),('app','de','AliasStillInTree','Dieser Kanal hat einen Alias-Kanal.\r\nDieser Alias-Kanal ist noch in der Hierarchie vorhanden, entferne ihn vorher!',0,'','2013-12-27 21:43:17'),('app','de','All','Alle',0,'','0000-00-00 00:00:00'),('app','de','AllDataWillBeRemoved','Alle Daten werden gelöscht, [color=red]alle[/color] Stamm- und [color=red]alle[/color] Betriebsdaten!',0,'','0000-00-00 00:00:00'),('app','de','Amount','Summe',0,'','0000-00-00 00:00:00'),('app','de','Analysis','Auswertungen',0,'','2014-09-24 15:23:53'),('app','de','APIkeyRegenerated','Dein API key wurde neu generiert.',0,'','0000-00-00 00:00:00'),('app','de','APIURL','API URL',0,'','0000-00-00 00:00:00'),('app','de','AreaSplineChart','Spline mit Bereich',0,'','2014-02-14 07:55:53'),('app','de','AreaSplineRangeChart','Spline mit min./max. Bereich',0,'','2014-02-14 07:54:17'),('app','de','AreYouSure','Bist Du sicher?!',0,'','2014-05-01 13:30:49'),('app','de','ArithmeticMean','Arithmetisches Mittel',0,'','0000-00-00 00:00:00'),('app','de','as','als',0,'','2014-03-13 11:29:10'),('app','de','AsChild','Als Kind-Kanal',0,'','2014-01-09 14:31:25'),('app','de','AsChildOf','Als Kind-Kanal von',0,'','2016-04-02 14:45:49'),('app','de','AssignEntity','Sub-Kanal zuordnen',0,'','0000-00-00 00:00:00'),('app','de','Author','Autor',0,'','0000-00-00 00:00:00'),('app','de','Average','Durchschnitt',0,'','0000-00-00 00:00:00'),('app','de','Axis','Achse',0,'','0000-00-00 00:00:00'),('app','de','Back','Zurück',0,'','0000-00-00 00:00:00'),('app','de','BackToTop','Zurück nach oben',0,'','0000-00-00 00:00:00'),('app','de','BarChart','Balken',0,'','2014-02-14 07:56:06'),('app','de','BasicDate','Basisdatum',0,'','0000-00-00 00:00:00'),('app','de','below','unter',0,'','2014-02-14 10:50:41'),('app','de','Bookmark','Lesezeichen',0,'','0000-00-00 00:00:00'),('app','de','Bytes','Bytes',0,'','0000-00-00 00:00:00'),('app','de','Cache','Cache',0,'','2014-07-08 08:08:32'),('app','de','CacheHits','Treffer',0,'','2014-07-08 08:31:41'),('app','de','CacheMisses','Fehlschläge',0,'','2014-07-08 08:33:22'),('app','de','Cancel','Abbrechen',0,'','0000-00-00 00:00:00'),('app','de','CantCopyGroups','Du kannst keine Gruppen kopieren!\r\nErstelle bitte einen Alias für diese und nutze ihn.',0,'','2014-04-26 14:48:52'),('app','de','Change','Ändern',0,'','2014-05-08 18:39:42'),('app','de','ChangeType','Kanaltyp',0,'','2014-05-09 07:08:00'),('app','de','ChangeTypeHint','Der Kanaltyp kann nur zu einem mit den gleichen Eigenschaften geändert werden (Anzahl Kind-Kanäle, lesen/schreiben)',0,'','2014-05-09 07:15:45'),('app','de','channel','Kanal',0,'','0000-00-00 00:00:00'),('app','de','Channel2Overview','Füge diesen neuen Kanal auch zur Übersicht hinzu',0,'','2014-01-09 14:29:43'),('app','de','ChannelAttributes','Kanal-Attribute',0,'','0000-00-00 00:00:00'),('app','de','ChannelDeleted','Der Kanal \'%s\' wurde gelöscht.',0,'','0000-00-00 00:00:00'),('app','de','ChannelHierarchy','Kanal-Hierarchie',0,'','0000-00-00 00:00:00'),('app','de','ChannelList','Kanalliste',0,'','2016-04-24 13:29:00'),('app','de','ChannelName','Kanalname',0,'','0000-00-00 00:00:00'),('app','de','Channels','Kanäle',0,'','0000-00-00 00:00:00'),('app','de','ChannelSaved','Die Kanaldaten wurden gesichert.',0,'','0000-00-00 00:00:00'),('app','de','ChannelsHint','Übersicht über alle definierten Kanäle',0,'','2014-01-31 20:26:59'),('app','de','ChannelsLoaded','Kanäle geladen',0,'','2014-05-26 18:50:28'),('app','de','ChannelsSaved','%d Kanäle gesichert',0,'','2013-12-30 17:57:14'),('app','de','ChannelStillInTree','Kanal \'%s\' wird noch in der Übersicht verwendet!\r\nBitte erst dort entfernen.',0,'','0000-00-00 00:00:00'),('app','de','ChannelType','Kanaltyp',0,'','0000-00-00 00:00:00'),('app','de','ChannelTypes','Kanaltypen',0,'','2014-06-04 14:22:01'),('app','de','Chart','Diagramm',0,'','0000-00-00 00:00:00'),('app','de','ChartAutoRefresh','Diagramm automatisch autualisieren',0,'','2015-03-07 18:22:29'),('app','de','ChartHint','Anzeigen der Kanal-Diagramme',0,'','2014-01-31 20:26:59'),('app','de','ChartPosition','Diagrammposition',0,'','2014-07-02 10:52:20'),('app','de','ChartPositionHint','Normalarweise werden die Kanäle in der Reihenfolge im Diagramm angezeigt, in der sie in der Kanalübersicht aufgelistet sind.\r\nHier können die Kanäle abweichend davon weiter nach hinten/vorn verschoben werden.',0,'','2014-07-02 11:05:41'),('app','de','ChartRefreshHint','Klick oder F6: Neu lesen aller Kanaldaten\r\nShift+Klick oder F7: Neuaufbau des gesamten Diagramms',0,'','2013-12-22 17:25:01'),('app','de','Charts','Diagramme',0,'','0000-00-00 00:00:00'),('app','de','ChartSettings','Diagrammeinstellungen',0,'','0000-00-00 00:00:00'),('app','de','ChartSettingsTip','Kanaleinstellungen, Achse, Stil, Farbe etc.',0,'','2015-12-28 17:56:04'),('app','de','ChartTodayHint','Setzt beide Datumsfelder auf heute und lädt das Diagramm neu',0,'','2014-01-31 20:12:33'),('app','de','ChartTypeHint','Linen-Diagramme sind etwas schneller als Splines, aber Splines sind gleichmäßiger',0,'','2014-02-14 07:59:22'),('app','de','Childs','Sub-Kanäle',0,'','0000-00-00 00:00:00'),('app','de','Clear','Leeren',0,'','0000-00-00 00:00:00'),('app','de','ClearSearch','Suchbegriff löschen',0,'','2014-04-26 14:48:52'),('app','de','ClickAndPressCtrlC','Klicke und drücke Strg+C zum kopieren',0,'','2014-03-27 21:02:53'),('app','de','ClickDragShiftPan','Klicken und ziehen zum Vergrößern, Shift-Taste drücken und Klicken zum Verschieben.',0,'','2014-07-09 12:01:09'),('app','de','ClickForGUID','Klicke hier um die GUID anzuzeigen',0,'','0000-00-00 00:00:00'),('app','de','ClickToDeleteRow','Zeile löschen',0,'','2014-05-02 12:02:44'),('app','de','CloneEntity','Kanal kopieren',0,'','0000-00-00 00:00:00'),('app','de','CloneTariff','Tarif kopieren',0,'','2014-05-01 16:47:49'),('app','de','CloneTariffDate','Zeiten für diese Startzeit kopieren',0,'','2014-05-01 16:52:34'),('app','de','Close','Schließen',0,'','0000-00-00 00:00:00'),('app','de','Clouds','Wolken',0,'','2014-08-21 06:25:02'),('app','de','Collapse','Zusammenklappen',0,'','0000-00-00 00:00:00'),('app','de','CollapseAll','Alles zusammenklappen',0,'','0000-00-00 00:00:00'),('app','de','Color','Farbe',0,'','0000-00-00 00:00:00'),('app','de','Comment','Kommentar',0,'','2014-04-30 10:18:09'),('app','de','Commissioning','Inbetriebnahme',0,'','0000-00-00 00:00:00'),('app','de','Confirm','Bestätigen',0,'','2014-05-01 13:32:35'),('app','de','ConfirmDeleteEntity','Löscht den Kanal und alle existierenden Messwerte.\r\n\r\nBist Du sicher?',0,'','0000-00-00 00:00:00'),('app','de','ConfirmDeleteTreeItems','Löscht den Kanal (und eventuelle Sub-Kanäle) aus dem Baum.\r\n\r\nBist Du sicher?',0,'','2014-07-19 14:28:39'),('app','de','ConfirmDeleteTreeNode','Löscht den Kanal aus dem Baum.\r\n\r\nBist Du sicher?',0,'','2014-07-19 14:35:09'),('app','de','Consumption','Verbrauch',0,'','0000-00-00 00:00:00'),('app','de','Copy','Kopieren',0,'','2014-04-30 05:00:07'),('app','de','CopyDates','Zeiten kopieren',0,'','2014-05-01 16:51:27'),('app','de','CopyOf','Kopie von',0,'','2014-05-01 13:42:14'),('app','de','copyTo','nach',0,'','2014-04-30 04:57:53'),('app','de','Cost','Kosten',0,'','0000-00-00 00:00:00'),('app','de','Create','Erstellen',0,'','0000-00-00 00:00:00'),('app','de','CreateChannel','Kanal erstellen',0,'','2014-05-08 10:31:45'),('app','de','CreateDashboardChannel','Dashboard-Kanal erstellen',0,'','2014-05-08 10:31:14'),('app','de','CreateFromTemplate','Aus Vorlage erstellen',0,'','2014-10-12 14:28:00'),('app','de','CreateTariff','Tarif erstellen',0,'','2014-05-08 10:31:45'),('app','de','CreateTreeWithoutReqest','Hier werden alle Kanäle und die gesamte Kanal-Hierarchie ohne weitere Nachfrage erstellt.',0,'','2014-01-17 11:00:35'),('app','de','Curve','Kurve',0,'','2014-05-25 18:08:14'),('app','de','DailyAverage','Tagesdurchschnitt',0,'','0000-00-00 00:00:00'),('app','de','DailyValue','Tageswerte',0,'','0000-00-00 00:00:00'),('app','de','Dashboard','Dashboard',0,'','0000-00-00 00:00:00'),('app','de','DashboardHint','Schnellübersichten mit Gauges',0,'','2014-05-08 10:43:26'),('app','de','DashboardIntro','Bitte wähle die Kanäle zur Anzeige aus.\r\n\r\nWenn die Tabelle unten leer ist, hast Du noch keine Kanäle vom Typ \"Dashboard channel\" definiert.',0,'','2014-05-08 10:34:05'),('app','de','Dashboards','Dashboards',0,'','2014-05-08 10:42:19'),('app','de','dashStyle','Linienart',0,'','0000-00-00 00:00:00'),('app','de','Data','Daten',0,'','0000-00-00 00:00:00'),('app','de','DataArea','Datenbereich',0,'','0000-00-00 00:00:00'),('app','de','Database','Datenbank',0,'','2014-06-07 12:48:29'),('app','de','DatabaseFree','Freier Bereich',0,'','2014-06-07 12:50:30'),('app','de','DatabaseSize','Datengrösse',0,'','2014-06-07 12:49:47'),('app','de','DatabaseTable','Datenbanktabelle',0,'','2014-11-20 16:07:06'),('app','de','DataExtraction','Datenabfragen',0,'','0000-00-00 00:00:00'),('app','de','DataLength','Datengröße',0,'','0000-00-00 00:00:00'),('app','de','DataSaved','Daten wurden gesichert',0,'','2014-10-03 20:47:25'),('app','de','DataState','Datenstatus',0,'','0000-00-00 00:00:00'),('app','de','DataStateHint','Einige Informationen zur Aktualität der Daten',0,'','2013-12-22 17:16:10'),('app','de','DataStorage','Datenspeicherung',0,'','0000-00-00 00:00:00'),('app','de','DataType','Datentyp',0,'','0000-00-00 00:00:00'),('app','de','Date','Datum',0,'','2014-05-01 12:39:22'),('app','de','DateTime','Datum / Zeit',0,'','2014-01-26 19:47:38'),('app','de','Day','Tag',0,'','0000-00-00 00:00:00'),('app','de','dbField','Bezeichnung',0,'','0000-00-00 00:00:00'),('app','de','dbValue','Wert',0,'','0000-00-00 00:00:00'),('app','de','Decimals','Dezimalstellen',0,'','2014-09-11 07:14:37'),('app','de','Decommissioning','Außerbetriebnahme',0,'','0000-00-00 00:00:00'),('app','de','Delete','Löschen',0,'','0000-00-00 00:00:00'),('app','de','DeleteBranch','Teilbaum löschen',0,'','0000-00-00 00:00:00'),('app','de','DeleteEntity','Kanal löschen',0,'','0000-00-00 00:00:00'),('app','de','DeleteEntityChilds','Kanal und Kind-Kanäle löschen',0,'','0000-00-00 00:00:00'),('app','de','DeleteEntityHint','Kanal löschen (nur möglich, wenn nicht in der Kanal-Hierarchie verwendet)',0,'','2014-09-10 10:00:28'),('app','de','DeleteReading','Messwert löschen (nur für Roh-Daten möglich)',0,'','2016-04-24 14:06:51'),('app','de','DeleteReadingConfirm','Willst Du diesen Messwert wirklich löschen?!',0,'','2014-02-14 13:55:20'),('app','de','DeleteTariff','Tarif löschen',0,'','2014-05-01 16:48:15'),('app','de','DeleteTariffDate','Daten für dieses Startdatum löschen',0,'','2014-05-01 16:53:35'),('app','de','DeleteViewFailed','Löschen des Diagramms \'%s\' ist fehlgeschlagen.',0,'','0000-00-00 00:00:00'),('app','de','Delta','Delta',0,'','0000-00-00 00:00:00'),('app','de','Description','Beschreibung',0,'','0000-00-00 00:00:00'),('app','de','DontForgetUpdateAPIKey','Vergiss nicht Deinen API-Key nach einer Neuerstellung in externen Scripten zu aktualisieren!',0,'','0000-00-00 00:00:00'),('app','de','DragBookmark','Ziehe den Link zu Deinen Lesezeichen',0,'','0000-00-00 00:00:00'),('app','de','DragDropHelp','- Ziehe eine Gruppe oder Kanal hierher für oberste Ebene\r\n- Benutze Strg-Klick um Kanäle zu kopieren\r\n- Gruppen können nicht kopiert werden, erstelle einen Alias und nutze diesen',0,'','2014-04-26 14:48:52'),('app','de','DragPermanent','Permanent Link mit Datum\r\nZiehe den Link zu Deinen Lesezeichen',0,'','0000-00-00 00:00:00'),('app','de','DragRowsToReorder','Ziehe die Zeilen um die Reihenfolge zu ändern',0,'','2014-05-07 16:27:53'),('app','de','DrawOutline','Schatten',0,'','2015-11-04 16:39:43'),('app','de','DrawOutlineHint','Zeichnet einen weißen Schatten hinter die Linie, um sie besser sichtbar zu machen.',0,'','2015-11-04 16:40:01'),('app','de','DSEP',',',0,'','0000-00-00 00:00:00'),('app','de','DuringDaylight','Nur zwischen Sonnenauf- und untergang',0,'','2014-03-26 14:05:31'),('app','de','Earning','Ertrag',0,'','0000-00-00 00:00:00'),('app','de','Edit','Bearbeiten',0,'','0000-00-00 00:00:00'),('app','de','EditChannel','Kanal bearbeiten',0,'','0000-00-00 00:00:00'),('app','de','EditEntity','Kanal bearbeiten',0,'','0000-00-00 00:00:00'),('app','de','EditSwitchAliasWithOriginal','Du kannst keinen Alias bearbeiten, deshalb Wechsel zum Original-Kanal!',0,'','2014-07-04 09:33:15'),('app','de','EditTariff','Tarif-Stammdaten ändern',0,'','2014-05-01 16:47:09'),('app','de','EditTariffDate','Tarif-Zeitscheibe ändern',0,'','2014-05-01 16:51:27'),('app','de','EndTime','Endezeit',0,'','2014-05-01 12:39:41'),('app','de','Energy','Energie',0,'','0000-00-00 00:00:00'),('app','de','EntityType','Kanaltyp',0,'','0000-00-00 00:00:00'),('app','de','Equipment','Geräte',0,'','0000-00-00 00:00:00'),('app','de','ExampleUnit','Beispiel-Einheit',0,'','2013-12-30 10:09:58'),('app','de','Expand','Erweitern',0,'','0000-00-00 00:00:00'),('app','de','ExpandAll','Alles erweitern',0,'','0000-00-00 00:00:00'),('app','de','FindYourLocation','Finde Deinen Standort',0,'','2014-10-13 11:29:40'),('app','de','FixCostDay','Feste Kosten pro Tag',0,'','2014-05-01 20:32:01'),('app','de','FixCostPerDay','Fixe Kosten pro Tag',0,'','2014-05-02 13:29:34'),('app','de','from','von',0,'','0000-00-00 00:00:00'),('app','de','GenerateAdminHash','Erstelle Administrations-Authorisierung',0,'','0000-00-00 00:00:00'),('app','de','HarmonicMean','Harmonisches Mittel',0,'','0000-00-00 00:00:00'),('app','de','HierarchyCreated','Kanal-Hierarchie wurde erstellt',0,'','2013-12-30 17:59:41'),('app','de','IndexLength','Indexgröße',0,'','0000-00-00 00:00:00'),('app','de','InfoHint','Hintergrundinformationen',0,'','2014-01-31 20:26:59'),('app','de','Information','Information',0,'','2014-04-26 14:48:52'),('app','de','InformationHint','Informationen die zur Konfiguration zum Speichern und Abfragen benötigt werden',0,'','0000-00-00 00:00:00'),('app','de','InstalledAdapters','Installierte Adapter',0,'','0000-00-00 00:00:00'),('app','de','Inverter','Wechselrichter',0,'','0000-00-00 00:00:00'),('app','de','InverterWithStrings','Wechselrichter mit Stringdaten',0,'','0000-00-00 00:00:00'),('app','de','Irradiation','Einstrahlung',0,'','0000-00-00 00:00:00'),('app','de','JustAMoment','Einen Moment bitte ...',0,'','0000-00-00 00:00:00'),('app','de','Key','Schlüssel',0,'','2014-07-08 08:07:37'),('app','de','Last','Letzte',0,'','0000-00-00 00:00:00'),('app','de','lastone','letzter',0,'','2014-01-13 13:58:35'),('app','de','LastReading','Letzter Wert',0,'','0000-00-00 00:00:00'),('app','de','LastTimestamp','Zeitpunkt der letzten\r\nDatenaufzeichnung',0,'','0000-00-00 00:00:00'),('app','de','LatestAPIVersion','Aktuelle API Version',0,'','2014-09-28 14:30:16'),('app','de','left','links',0,'','0000-00-00 00:00:00'),('app','de','Legend','Legende',0,'','2014-05-09 10:47:58'),('app','de','LineBold','dick',0,'','0000-00-00 00:00:00'),('app','de','LineChart','Linie',0,'','2014-02-14 07:52:21'),('app','de','LineDash','gestrichelt',0,'','2014-02-14 08:18:45'),('app','de','LineDashDot','Strich-Punkt',0,'','2014-02-14 08:19:54'),('app','de','LineDot','gepunktet',0,'','2014-02-14 08:19:26'),('app','de','LineLongDash','gestrichelt lang',0,'','2014-02-14 08:28:15'),('app','de','LineLongDashDot','Strich-Punkt lang',0,'','2014-02-14 08:20:46'),('app','de','LineLongDashDotDot','Strich-Punkt-Punkt',0,'','2014-02-14 08:41:11'),('app','de','LineNormal','normal',0,'','0000-00-00 00:00:00'),('app','de','LinesDashed','getrichelt',0,'','2014-02-14 08:40:47'),('app','de','LinesDashedDotted','Strich-Punkt',0,'','2014-02-14 08:36:03'),('app','de','LinesDashedDottedDotted','Strich-Punkt-Punkt',0,'','2014-02-14 08:36:21'),('app','de','LinesDotted','gepunktet',0,'','2014-02-14 08:40:47'),('app','de','LineShortDash','gestrichelt kurz',0,'','2014-02-14 08:21:50'),('app','de','LineShortDashDot','Strich-Punkt kurz',0,'','2014-02-14 08:22:49'),('app','de','LineShortDashDotDot','Strich-Punkt-Punkt kurz',0,'','2014-02-14 08:23:20'),('app','de','LineShortDot','gepunktet kurz',0,'','2014-02-14 08:22:18'),('app','de','LineSolid','durchgezogen',0,'','2014-02-14 08:17:52'),('app','de','LineWidth','Linienstärke',0,'','0000-00-00 00:00:00'),('app','de','List','Liste',0,'','2014-01-25 13:43:20'),('app','de','ListExportCSVHint','Export aller Werte als Komma-getrennte Datei',0,'','2014-01-26 20:19:51'),('app','de','ListExportTextHint','Export aller Werte als Leerzeichen-getrennte Datei',0,'','2014-01-26 20:19:51'),('app','de','ListExportTSVHint','Export aller Werte als Tab-getrennte Datei',0,'','2014-01-26 20:19:51'),('app','de','ListHint','Messwerte als Tabelle',0,'','2014-01-25 13:43:20'),('app','de','ListRefreshHint','Klick oder F6: Neu lesen der Kanaldaten',0,'','0000-00-00 00:00:00'),('app','de','Lists','Listen',0,'','2014-09-24 19:23:09'),('app','de','Load','Laden',0,'','0000-00-00 00:00:00'),('app','de','Log','Log',0,'','0000-00-00 00:00:00'),('app','de','LogHint','Log-Einträge',0,'','0000-00-00 00:00:00'),('app','de','Login','Anmelden',0,'','0000-00-00 00:00:00'),('app','de','LoginRequired','Diese Funktion steht nur eingeloggten Benutzern zur Verfügung!',0,'','2014-10-05 11:12:03'),('app','de','LoginToken','Permanentes Login-Token, nur für diese Computer-IP!',0,'','2014-05-13 06:40:47'),('app','de','Logout','Abmelden',0,'','0000-00-00 00:00:00'),('app','de','LogoutSuccessful','[b]%s[/b] wurde erfolgreich abgemeldet.',0,'','0000-00-00 00:00:00'),('app','de','Manufacturer','Hersteller',0,'','0000-00-00 00:00:00'),('app','de','MarkAll','alle',0,'','2014-02-13 13:39:26'),('app','de','MarkAllHint','Kann nur für Balken-Diagramme verwendet werden (und macht nur dort Sinn)',0,'','2014-02-13 14:42:50'),('app','de','MarkExtremes','Markiere Messwerte',0,'','2014-01-13 14:16:13'),('app','de','MarkLast','letzter',0,'','2014-02-13 13:39:16'),('app','de','MarkMax','max.',0,'','2014-02-13 13:38:56'),('app','de','MarkMin','min.',0,'','2014-02-13 13:38:42'),('app','de','MasterData','Stammdaten',0,'','2014-09-24 15:26:06'),('app','de','max','max',0,'','0000-00-00 00:00:00'),('app','de','Message','Nachricht',0,'','0000-00-00 00:00:00'),('app','de','min','min',0,'','0000-00-00 00:00:00'),('app','de','Minutes','Minuten',0,'','2014-10-19 18:43:23'),('app','de','MissingAPIkey','API key ist erforderlich!',0,'','0000-00-00 00:00:00'),('app','de','MobileChart','für Mobilgeräte',0,'','2014-03-13 11:31:57'),('app','de','MobileVariantHint','Wenn Du PVLng auf mobilen Geräten nutzen möchtest, definiere mindestens ein Diagramm [b]@mobile[/b] als Standard-Diagramm.\r\nNur Diagramme beginnend mit einem [b]@[/b] sind mobil verfügbar.\r\n(Mobile Diagramme sind immer öffentlich!)',0,'','0000-00-00 00:00:00'),('app','de','Model','Modell',0,'','0000-00-00 00:00:00'),('app','de','Month','Monat',0,'','0000-00-00 00:00:00'),('app','de','MonthlyAverage','Monatsdurchschnitt',0,'','0000-00-00 00:00:00'),('app','de','MoreIntoBackground','weiter nach hinten',0,'','2014-07-02 11:10:11'),('app','de','MoreIntoForeground','weiter nach vorn',0,'','2014-07-02 11:09:41'),('app','de','MoveChannel','Kanal verschieben',0,'','0000-00-00 00:00:00'),('app','de','MoveChannelHowMuchRows','Um wie viele Positionen (auf gleicher Ebene) soll der Kanal verschoben werden?',0,'','0000-00-00 00:00:00'),('app','de','MoveChannelStartEnd','an den Anfang / das Ende',0,'','0000-00-00 00:00:00'),('app','de','MoveEntityDown','Verschiebe Kanal nach unten',0,'','0000-00-00 00:00:00'),('app','de','MoveEntityLeft','Verschiebe Kanal eine Ebene höher',0,'','0000-00-00 00:00:00'),('app','de','MoveEntityRight','Verschiebe Kanal eine Ebene tiefer',0,'','0000-00-00 00:00:00'),('app','de','MoveEntityUp','Verschiebe Kanal nach oben',0,'','0000-00-00 00:00:00'),('app','de','MustHaveChilds','Diesem Kanaltyp müssen Sub-Kanäle für eine korrekte Funktion zugeordnet werden!',0,'','2013-12-30 08:24:10'),('app','de','Name','Name',0,'','0000-00-00 00:00:00'),('app','de','NameRequired','Der Name ist erforderlich.',0,'','0000-00-00 00:00:00'),('app','de','New','Neu',0,'','0000-00-00 00:00:00'),('app','de','NewStartDate','Neues Startdatum',0,'','2014-04-30 04:58:24'),('app','de','NextDay','Nächster Tag',0,'','0000-00-00 00:00:00'),('app','de','No','Nein',0,'','0000-00-00 00:00:00'),('app','de','NoChannelMatch','Kein Kanal enthält',0,'','2014-05-17 18:25:17'),('app','de','NoChannelsSelectedYet','Es wurden noch keine Kanäle oder ein Diagramm zur Anzeige ausgewählt.',0,'','0000-00-00 00:00:00'),('app','de','NoChartMatch','Kein Diagramm gefunden',0,'','2014-05-21 11:11:56'),('app','de','NoDataAvailable','Keine Daten verfügbar',0,'','0000-00-00 00:00:00'),('app','de','None','Keine',0,'','0000-00-00 00:00:00'),('app','de','NotAuthorized','Nicht autorisiert! Es wurde ein falscher API key übermittelt.',0,'','0000-00-00 00:00:00'),('app','de','NoViewSelectedYet','Es wurde noch kein Diagramm zur Anzeige ausgewählt.',0,'','0000-00-00 00:00:00'),('app','de','of','von',0,'','2014-03-14 19:45:15'),('app','de','Ok','Ok',0,'','0000-00-00 00:00:00'),('app','de','OnlyChannelsWithReadings','Nur Kanäle mit Messwerten',0,'','2014-07-07 19:56:55'),('app','de','or','oder',0,'','0000-00-00 00:00:00'),('app','de','Overview','Kanalhierarchie',0,'','2014-09-24 15:34:17'),('app','de','OverviewHint','Übersicht über Dein Equipment und desen Hierarchie',0,'','2014-09-24 15:35:38'),('app','de','Overwrite','Überschreiben',0,'','0000-00-00 00:00:00'),('app','de','Page','Seite',0,'','2014-03-14 19:45:04'),('app','de','Parameter','Parameter',0,'','0000-00-00 00:00:00'),('app','de','Password','Passwort',0,'','0000-00-00 00:00:00'),('app','de','PasswordSaved','Passwort wurde gesichert',0,'','2014-10-13 11:42:46'),('app','de','PasswordsNotEqual','Die Passworte sind nicht identisch.',0,'','0000-00-00 00:00:00'),('app','de','PerformanceRatio','Wirkungsgrad',0,'','0000-00-00 00:00:00'),('app','de','Period','Zeitraum',0,'','0000-00-00 00:00:00'),('app','de','PlantDescriptionHint','Beschreibung der Installation',0,'','2014-01-31 20:26:59'),('app','de','PleaseRelogin','Bitte neu einloggen!',0,'','2014-10-13 07:08:24'),('app','de','Positions','Position(en)',0,'','0000-00-00 00:00:00'),('app','de','Power','Leistung',0,'','0000-00-00 00:00:00'),('app','de','Presentation','Darstellung',0,'','0000-00-00 00:00:00'),('app','de','PrevDay','Vorheriger Tag',0,'','0000-00-00 00:00:00'),('app','de','private','privat',0,'','2014-03-14 09:23:46'),('app','de','PrivateChannel','Nicht-öffentlicher Kanal',0,'','0000-00-00 00:00:00'),('app','de','PrivateChart','nicht-öffentliches Diagramm',0,'','2014-03-13 11:29:58'),('app','de','proceed','weiter',0,'','2013-12-27 17:15:43'),('app','de','Production','Produktion',0,'','0000-00-00 00:00:00'),('app','de','public','öffentlich',0,'','0000-00-00 00:00:00'),('app','de','PublicChart','öffentliches Diagramm',0,'','2014-03-13 11:30:17'),('app','de','publicHint','- Öffentliche Diagramme sind von nicht eingeloggten Besuchern anzeigbar.\r\n- Diagramme für Mobilgeräte sind für nicht eingeloggte Besucher nur im Mobilmodus sichtbar, private Kanäle werden dabei nicht angezeigt.',0,'','2014-03-15 18:01:30'),('app','de','ReadableEntity','Lesbarer Kanal',0,'','0000-00-00 00:00:00'),('app','de','Reading','Messwert',0,'','2014-01-26 19:48:08'),('app','de','ReadingDeleted','Messwert wurde gelöscht',0,'','2014-02-14 12:57:45'),('app','de','Readings','Messwerte',0,'','0000-00-00 00:00:00'),('app','de','ReadWritableEntity','Schreib- und lesbarer Kanal',0,'','2014-05-29 12:31:24'),('app','de','RecordCount','Anzahl Datensätze',0,'','0000-00-00 00:00:00'),('app','de','Redisplay','Anzeigen',0,'','0000-00-00 00:00:00'),('app','de','Refresh','Aktualisieren',0,'','0000-00-00 00:00:00'),('app','de','Regenerate','Regenerieren',0,'','0000-00-00 00:00:00'),('app','de','RemoveTariffIfUsed','Wenn der Tarif in einem Kanal benutzt wird, wird er dort entfernt.',0,'','2014-05-01 18:32:56'),('app','de','RequestTypes','Anfragetypen',0,'','0000-00-00 00:00:00'),('app','de','Required','erforderlich',0,'','2014-05-11 10:14:56'),('app','de','resetZoom','Vergrößerung zurücksetzen',0,'','0000-00-00 00:00:00'),('app','de','resetZoomTitle','Setze Vergrößerung auf 1:1 zurück',0,'','0000-00-00 00:00:00'),('app','de','Resolution','Faktor',0,'','2014-07-08 10:17:24'),('app','de','right','rechts',0,'','0000-00-00 00:00:00'),('app','de','RowCount','Zeilenzahl',0,'','2014-01-26 19:48:48'),('app','de','RowCountHint','Anzahl der Zeilen über die verdichtet wurde',0,'','2014-01-26 19:49:57'),('app','de','Rows','Zeilen',0,'','2014-11-20 16:12:07'),('app','de','Save','Sichern',0,'','0000-00-00 00:00:00'),('app','de','ScanForMobileView','Mobile Ansicht',0,'','2015-02-27 13:31:06'),('app','de','ScatterCandidate','Dieser Kanal ist nicht numerisch oder hat keine Einheit, die Darstellung als \"Punkte\" könnte am geeignetsten sein',0,'','2014-10-12 11:54:58'),('app','de','ScatterChart','Punkte',0,'','2014-02-14 07:56:17'),('app','de','Scope','Bereich',0,'','0000-00-00 00:00:00'),('app','de','SeeAdapters','Siehe unten welche Adapter installiert sind.',0,'','0000-00-00 00:00:00'),('app','de','SeeAPIReference','Für mehr Informationen, siehe in die [url=http://pvlng.com/API]API-Referenz[/url].',0,'','2014-04-05 16:32:36'),('app','de','Select','Auswählen',0,'','0000-00-00 00:00:00'),('app','de','SelectChannel','Kanal auswählen',0,'','2014-01-26 21:22:38'),('app','de','SelectChart','Diagramm auswählen',0,'','2014-05-17 19:42:10'),('app','de','SelectEntity','Kanal auswählen',0,'','0000-00-00 00:00:00'),('app','de','SelectEntityTemplate','Auswahl Vorlage',0,'','2013-12-30 16:12:04'),('app','de','SelectEntityType','Auswahl Kanaltyp',0,'','0000-00-00 00:00:00'),('app','de','Selection','Auswahl',0,'','0000-00-00 00:00:00'),('app','de','SelectView','Diagramm auswählen',0,'','0000-00-00 00:00:00'),('app','de','Send','Absenden',0,'','0000-00-00 00:00:00'),('app','de','Serial','Seriennummer',0,'','0000-00-00 00:00:00'),('app','de','SerialRequired','Die Serialnummer ist erforderlich',0,'','0000-00-00 00:00:00'),('app','de','SerialStillExists','Die Serialnummer existiert bereits.',0,'','0000-00-00 00:00:00'),('app','de','SeriesType','Datenreihendarstellung',0,'','0000-00-00 00:00:00'),('app','de','SetAxisMinZero','Setze Y-Achsen-Minimum auf 0',0,'','0000-00-00 00:00:00'),('app','de','Settings','Einstellungen',0,'','2014-10-06 09:39:49'),('app','de','SettingsMenu','Einstellungen (nur in englisch)',0,'','2014-10-06 17:56:41'),('app','de','Show','Anzeigen',0,'','2014-04-30 04:59:54'),('app','de','ShowConsumption','Periodenwerte',0,'','0000-00-00 00:00:00'),('app','de','ShowConsumptionHint','Zeigt für Meter-Kanäle die Daten pro Periode und nicht den Gesamtwert über die Zeit',0,'','0000-00-00 00:00:00'),('app','de','ShowDescription','Beschreibung anzeigen',0,'','2014-06-04 13:54:03'),('app','de','ShowGUID','Kanal-GUID anzeigen',0,'','2014-05-29 12:34:48'),('app','de','Size','Größe',0,'','2014-11-20 16:07:28'),('app','de','SplineChart','Spline',0,'','2014-02-14 07:53:33'),('app','de','StartDate','Startdatum',0,'','2014-04-30 04:55:23'),('app','de','StartHidden','Anfangs ausgeblendet',0,'','2014-09-29 08:07:28'),('app','de','StartingTimes','Startzeitpunkte',0,'','2014-04-30 11:45:35'),('app','de','StartTime','Startzeit',0,'','2014-04-30 04:58:58'),('app','de','Statistics','Statistik',0,'','0000-00-00 00:00:00'),('app','de','StayLoggedIn','Angemeldet bleiben[br][small]für 1 Woche[/small]',0,'','2016-04-25 08:43:11'),('app','de','Stick','Anheften',0,'','0000-00-00 00:00:00'),('app','de','SuppressZero','Unterdrücke 0-Werte',0,'','0000-00-00 00:00:00'),('app','de','Sure','Sicher',0,'','0000-00-00 00:00:00'),('app','de','SystemInformation','Systeminformationen',0,'','0000-00-00 00:00:00'),('app','de','Tariff','Tarif',0,'','2014-04-30 04:58:41'),('app','de','TariffCreated','Tarif wurde angelegt',0,'','2014-05-01 10:39:39'),('app','de','TariffDatesCopied','Tarif-Zeitbereiche wurden kopiert',0,'','2014-05-01 10:40:13'),('app','de','Tariffs','Tarife',0,'','2014-05-01 08:58:14'),('app','de','TariffsHint','Tages- oder tageszeitabhängige Tarife',0,'','2014-05-29 12:33:08'),('app','de','TariffThisWeek','Tarife diese Woche',0,'','2014-05-01 12:40:26'),('app','de','Temperature','Temperatur',0,'','0000-00-00 00:00:00'),('app','de','TemperatureDifference','Temperaturdifferenz',0,'','0000-00-00 00:00:00'),('app','de','TemperatureModules','Modultemperatur',0,'','0000-00-00 00:00:00'),('app','de','TemperatureOutside','Außentemperatur',0,'','0000-00-00 00:00:00'),('app','de','Template','Vorlage',0,'','2014-09-07 12:46:52'),('app','de','ThinLine','dünn',0,'','0000-00-00 00:00:00'),('app','de','Threshold','Grenzwert',0,'','0000-00-00 00:00:00'),('app','de','TimeDaysTariffRequired','Nur Zeilen mit einer Startzeit, mindestens einem Wochentag und einem Tarif werden als gültig betrachtet.',0,'','2014-04-30 05:05:22'),('app','de','TimeRange','Zeitbereich',0,'','2014-03-12 13:25:34'),('app','de','TimeRangeHint','Wenn Du einen Kanal mit 24h-Daten in einem Diagramm hast, dass auch Kanäle enthält die nur während des Tageslichtes Daten haben, kannst Du die Ausgabe hier einschränken.',0,'','2014-09-17 07:29:29'),('app','de','Timestamp','Timestamp',0,'','0000-00-00 00:00:00'),('app','de','to','bis',0,'','0000-00-00 00:00:00'),('app','de','Today','Heute',0,'','0000-00-00 00:00:00'),('app','de','ToggleChannels','Kanäle ein-/ausklappen',0,'','0000-00-00 00:00:00'),('app','de','toggleGUIDs','Kanal-GUIDs anzeigen',0,'','0000-00-00 00:00:00'),('app','de','TopLevel','Auf oberster Ebene',0,'','2014-01-09 14:30:08'),('app','de','Total','Gesamt',0,'','0000-00-00 00:00:00'),('app','de','TotalRows','Datensatzanzahl',0,'','0000-00-00 00:00:00'),('app','de','TotalSize','Gesamtgröße',0,'','0000-00-00 00:00:00'),('app','de','TSEP','.',0,'','0000-00-00 00:00:00'),('app','de','Type','Typ',0,'','0000-00-00 00:00:00'),('app','de','Unit','Einheit',0,'','0000-00-00 00:00:00'),('app','de','UnknownUser','Falsches Passwort',0,'','2014-10-13 06:57:28'),('app','de','UnknownView','Unbekanntes Diagramm: \'%s\'',0,'','0000-00-00 00:00:00'),('app','de','unlimited','unendlich',0,'','2013-12-30 12:02:40'),('app','de','UnsavedChanges','Du hast ungesicherte Änderungen für Dein Diagramm',0,'','2014-02-26 09:40:32'),('app','de','UsableInCharts','Kann in Diagrammen angezeigt werden',0,'','2014-09-08 06:02:19'),('app','de','UseDifferentColor','Abweichende Farbe ab Grenzwert',0,'','2014-07-02 13:49:10'),('app','de','UseOwnConsolidation','Benutze einen eigenen Verdichtungzeitraum\r\n(Dieser wird aber nicht in den Varianten-Einstellungen gespeichert)',0,'','2014-01-13 12:54:16'),('app','de','Value','Wert',0,'','0000-00-00 00:00:00'),('app','de','ValueMustGEzero','Der Wert muss größer oder gleich 0 sein',0,'','2014-10-10 19:32:59'),('app','de','ValueMustGTzero','Der Wert muss größer als 0 sein',0,'','2014-10-10 19:34:29'),('app','de','Variant','Diagramm',0,'','0000-00-00 00:00:00'),('app','de','Variants','Diagramme',0,'','0000-00-00 00:00:00'),('app','de','VariantsPublic','Öffentliche Diagramme',0,'','0000-00-00 00:00:00'),('app','de','ViewDeleted','Diagramm \'%s\' gelöscht.',0,'','0000-00-00 00:00:00'),('app','de','Voltage','Spannung',0,'','0000-00-00 00:00:00'),('app','de','Weather','Wetter',0,'','2014-08-25 12:55:12'),('app','de','WeatherForecast','Wettervorhersage',0,'','2014-08-21 06:25:31'),('app','de','Weekdays','Wochentage',0,'','2014-04-30 04:59:19'),('app','de','WeeklyAverage','Wochendurchschnitt',0,'','0000-00-00 00:00:00'),('app','de','Welcome','Wilkommen %s!',0,'','0000-00-00 00:00:00'),('app','de','WelcomeToAdministration','Willkommen in Deinem PVLng Administrationsbereich.',0,'','0000-00-00 00:00:00'),('app','de','WritableEntity','Schreibbarer Kanal',0,'','0000-00-00 00:00:00'),('app','de','YearlyAverage','Jahresdurchschnitt',0,'','0000-00-00 00:00:00'),('app','de','Yes','Ja',0,'','0000-00-00 00:00:00'),('app','de','YourAPIcode','API-Schlüssel für den Daten-Update\r\n\r\n[i]Halte Deinen API-Schlüssel immer geheim![/i]',0,'','2014-10-12 15:14:05'),('app','en','above','above',0,'','2014-07-02 13:32:09'),('app','en','AcceptChild','1:\"%2$s\" accepts only one child at all!||\r\nn:\"%2$s\" accepts only %1$d childs at all!',0,'','2014-05-26 18:34:56'),('app','en','AcceptChildCount','Number of possible child channels',0,'','2014-09-26 11:10:46'),('app','en','Actions','Actions',0,'','0000-00-00 00:00:00'),('app','en','ActualState','Actual data state',0,'','0000-00-00 00:00:00'),('app','en','Add','Add',0,'','2014-02-05 14:56:24'),('app','en','AddAnotherChild','Add another channel',0,'','0000-00-00 00:00:00'),('app','en','AddChannel','Add a channel to the hierarchy',0,'','0000-00-00 00:00:00'),('app','en','AddChild','Add child channel',0,'','0000-00-00 00:00:00'),('app','en','AddOneToManyChannels','Add one ore more channels to hierarchy',0,'','2014-02-08 19:58:43'),('app','en','AddTariffDate','Add new start date data',0,'','2014-05-01 16:46:15'),('app','en','AdjustTemplate','Adjust template',0,'','2014-09-01 09:13:04'),('app','en','AdjustTemplateAfterwards','Adjust e.g. units, decimals and public settings afterwards.',0,'','2014-01-12 14:50:09'),('app','en','AdminAndPasswordRequired','User name and password required!',0,'','0000-00-00 00:00:00'),('app','en','Aggregation','Aggregation',0,'','0000-00-00 00:00:00'),('app','en','AliasCreated','Alias channel created',0,'','2014-07-19 19:00:41'),('app','en','AliasEntity','Create alias channel',0,'','0000-00-00 00:00:00'),('app','en','AliasesUpdated','The alias channel was also updated.',0,'','0000-00-00 00:00:00'),('app','en','AliasStillExists','An alias channel still exists.',0,'','0000-00-00 00:00:00'),('app','en','AliasStillInTree','This channel have an alias channel defined.\r\nThis alias channel is still in tree, remove the alias before!',0,'','2013-12-27 21:43:17'),('app','en','All','All',0,'','0000-00-00 00:00:00'),('app','en','AllDataWillBeRemoved','All data will be removed, all master data and [color=red]all[/color] operating data!',0,'','0000-00-00 00:00:00'),('app','en','Amount','Amount',0,'','0000-00-00 00:00:00'),('app','en','Analysis','Analysis',0,'','2014-09-24 15:23:53'),('app','en','APIkeyRegenerated','Your API key was regenerated.',0,'','0000-00-00 00:00:00'),('app','en','APIURL','API URL',0,'','0000-00-00 00:00:00'),('app','en','AreaSplineChart','Spline with area',0,'','2014-02-14 07:55:53'),('app','en','AreaSplineRangeChart','Spline with min./max. range',0,'','2014-02-14 07:54:17'),('app','en','AreYouSure','Are you sure?!',0,'','2014-05-01 13:30:49'),('app','en','ArithmeticMean','Arithmetic mean',0,'','0000-00-00 00:00:00'),('app','en','as','as',0,'','2014-03-13 11:29:10'),('app','en','AsChild','As sub channel',0,'','2014-01-09 14:31:25'),('app','en','AsChildOf','As child channel of',0,'','2016-04-02 14:45:49'),('app','en','AssignEntity','Assign sub channel',0,'','0000-00-00 00:00:00'),('app','en','Author','Author',0,'','0000-00-00 00:00:00'),('app','en','Average','Average',0,'','0000-00-00 00:00:00'),('app','en','Axis','Axis',0,'','0000-00-00 00:00:00'),('app','en','Back','Back',0,'','0000-00-00 00:00:00'),('app','en','BackToTop','Back to top',0,'','0000-00-00 00:00:00'),('app','en','BarChart','Bar',0,'','2014-02-14 07:56:05'),('app','en','BasicDate','Basic date',0,'','0000-00-00 00:00:00'),('app','en','below','below',0,'','2014-02-14 10:50:41'),('app','en','Bookmark','Bookmark',0,'','0000-00-00 00:00:00'),('app','en','Bytes','Bytes',0,'','0000-00-00 00:00:00'),('app','en','Cache','Cache',0,'','2014-07-08 08:08:32'),('app','en','CacheHits','Hits',0,'','2014-07-08 08:31:41'),('app','en','CacheMisses','Misses',0,'','2014-07-08 08:33:02'),('app','en','Cancel','Cancel',0,'','0000-00-00 00:00:00'),('app','en','CantCopyGroups','You can\'t copy groups!\r\nCreate an alias and use this instead.',0,'','2014-04-26 14:48:52'),('app','en','Change','Change',0,'','2014-05-08 18:39:41'),('app','en','ChangeType','Channel type',0,'','2014-05-09 07:03:07'),('app','en','ChangeTypeHint','The channel type can only be changed to one with the same attributes (sub channel count, read/write)',0,'','2014-05-09 07:15:45'),('app','en','channel','Channel',0,'','0000-00-00 00:00:00'),('app','en','Channel2Overview','Add this new channel also into overview',0,'','2014-01-09 14:29:43'),('app','en','ChannelAttributes','Channel attributes',0,'','0000-00-00 00:00:00'),('app','en','ChannelDeleted','Channel \'%s\' deleted.',0,'','0000-00-00 00:00:00'),('app','en','ChannelHierarchy','Channel hierarchy\r\n',0,'','0000-00-00 00:00:00'),('app','en','ChannelList','Channels list',0,'','2016-04-24 13:29:00'),('app','en','ChannelName','Channel name',0,'','0000-00-00 00:00:00'),('app','en','Channels','Channels',0,'','0000-00-00 00:00:00'),('app','en','ChannelSaved','Channel data saved.',0,'','0000-00-00 00:00:00'),('app','en','ChannelsHint','Overview of all defined channels',0,'','2014-01-31 20:26:59'),('app','en','ChannelsLoaded','channels loaded',0,'','2014-05-26 18:50:28'),('app','en','ChannelsSaved','%d channels saved',0,'','2013-12-30 17:57:14'),('app','en','ChannelStillInTree','Channel \'%s\' is still used in overview!\r\nPlease remove it there first.',0,'','0000-00-00 00:00:00'),('app','en','ChannelType','Channel type',0,'','0000-00-00 00:00:00'),('app','en','ChannelTypes','Channel types',0,'','2014-06-04 14:22:01'),('app','en','Chart','Chart',0,'','0000-00-00 00:00:00'),('app','en','ChartAutoRefresh','Automatic chart refresh',0,'','2015-03-07 18:22:29'),('app','en','ChartHint','Display channel charts',0,'','2014-01-31 20:26:59'),('app','en','ChartPosition','Chart position',0,'','2014-07-02 10:51:51'),('app','en','ChartPositionHint','The channels are displayed in the chart by default in the same order as in the channels overview list.\r\nHere you can move them more to back/front.\r\n',0,'','2014-07-02 11:07:33'),('app','en','ChartRefreshHint','Click or F6: Reread chart channel data\r\nShift+Click or F7: Rebuild the whole chart',0,'','2013-12-22 17:25:01'),('app','en','Charts','Charts',0,'','0000-00-00 00:00:00'),('app','en','ChartSettings','Chart settings',0,'','0000-00-00 00:00:00'),('app','en','ChartSettingsTip','Channel settings, axis, presentation style, color etc.',0,'','2015-12-28 17:56:04'),('app','en','ChartTodayHint','Set both date fields to today and reload chart',0,'','2014-01-31 20:12:33'),('app','en','ChartTypeHint','Line charts are a bit faster than a slines, but splines are smoother',0,'','2014-02-14 07:59:22'),('app','en','Childs','Childs',0,'','0000-00-00 00:00:00'),('app','en','Clear','Clear',0,'','0000-00-00 00:00:00'),('app','en','ClearSearch','Clear search term',0,'','2014-04-26 14:48:52'),('app','en','ClickAndPressCtrlC','Click and press Ctrl+C to copy',0,'','2014-03-27 21:02:53'),('app','en','ClickDragShiftPan','Click and drag to zoom in, hold down shift key and click to pan.',0,'','2014-07-09 12:01:09'),('app','en','ClickForGUID','Click here to show GUID',0,'','0000-00-00 00:00:00'),('app','en','ClickToDeleteRow','Delete row',0,'','2014-05-02 12:02:44'),('app','en','CloneEntity','Copy channel',0,'','0000-00-00 00:00:00'),('app','en','CloneTariff','Clone tariff',0,'','2014-05-01 16:47:49'),('app','en','CloneTariffDate','Clone data for this start date',0,'','2014-05-01 16:52:34'),('app','en','Close','Close',0,'','0000-00-00 00:00:00'),('app','en','Clouds','Clouds',0,'','2014-08-21 06:25:01'),('app','en','Collapse','Collapse',0,'','0000-00-00 00:00:00'),('app','en','CollapseAll','CollapseAll',0,'','0000-00-00 00:00:00'),('app','en','Color','Color',0,'','0000-00-00 00:00:00'),('app','en','Comment','Comment',0,'','2014-04-30 10:18:09'),('app','en','Commissioning','Commissioning',0,'','0000-00-00 00:00:00'),('app','en','Confirm','Confirm',0,'','2014-05-01 13:32:35'),('app','en','ConfirmDeleteEntity','Delete channel and all existing measuring data.\r\n\r\nAre you sure?',0,'','0000-00-00 00:00:00'),('app','en','ConfirmDeleteTreeItems','Delete channel (and may be all sub channels) from tree.\r\n\r\nAre you sure?',0,'','2014-07-19 14:28:39'),('app','en','ConfirmDeleteTreeNode','Delete channel from tree.\r\n\r\nAre you sure?',0,'','2014-07-19 14:35:09'),('app','en','Consumption','Consumption',0,'','0000-00-00 00:00:00'),('app','en','Copy','Copy',0,'','2014-04-30 05:00:07'),('app','en','CopyDates','Copy date records',0,'','2014-05-01 09:07:36'),('app','en','CopyOf','Copy of',0,'','2014-05-01 13:42:14'),('app','en','copyTo','to',0,'','2014-04-30 04:57:53'),('app','en','Cost','Cost',0,'','0000-00-00 00:00:00'),('app','en','Create','Create',0,'','0000-00-00 00:00:00'),('app','en','CreateChannel','Create channel',0,'','2014-05-08 10:31:45'),('app','en','CreateDashboardChannel','Create Dashboard channel',0,'','2014-05-08 10:31:14'),('app','en','CreateFromTemplate','Create from template',0,'','2014-10-12 14:28:00'),('app','en','CreateTariff','Create tariff',0,'','2014-05-08 10:31:45'),('app','en','CreateTreeWithoutReqest','This will create all channels and the whole channel hierarchy without further request.',0,'','2014-01-17 11:00:35'),('app','en','Curve','Curve',0,'','2014-05-25 18:08:14'),('app','en','DailyAverage','Daily average',0,'','0000-00-00 00:00:00'),('app','en','DailyValue','Daily values',0,'','0000-00-00 00:00:00'),('app','en','Dashboard','Dashboard',0,'','0000-00-00 00:00:00'),('app','en','DashboardHint','Quick overviews with gauges',0,'','2014-05-08 10:43:26'),('app','en','DashboardIntro','Please select your channels to display.\r\n\r\nIf the table below is empty, you have not defined channels of type \"Dashboard channel\" yet.',0,'','2014-05-08 10:34:05'),('app','en','Dashboards','Dashboards',0,'','2014-05-08 10:42:32'),('app','en','dashStyle','Dash style',0,'','0000-00-00 00:00:00'),('app','en','Data','Data',0,'','0000-00-00 00:00:00'),('app','en','DataArea','Data area',0,'','0000-00-00 00:00:00'),('app','en','Database','Database',0,'','2014-06-07 12:48:29'),('app','en','DatabaseFree','Data free',0,'','2014-06-07 12:50:30'),('app','en','DatabaseSize','Data Length',0,'','2014-06-07 12:49:47'),('app','en','DatabaseTable','Database table',0,'','2014-11-20 16:07:06'),('app','en','DataExtraction','Data extraction',0,'','0000-00-00 00:00:00'),('app','en','DataLength','Data size',0,'','0000-00-00 00:00:00'),('app','en','DataSaved','Data was saved',0,'','2014-10-03 20:47:25'),('app','en','DataState','Data state',0,'','0000-00-00 00:00:00'),('app','en','DataStateHint','Some information about the data health',0,'','2013-12-22 17:16:10'),('app','en','DataStorage','Data storage',0,'','0000-00-00 00:00:00'),('app','en','DataType','Data type',0,'','0000-00-00 00:00:00'),('app','en','Date','Date',0,'','2014-05-01 12:39:22'),('app','en','DateTime','Date / Time',0,'','2014-01-26 19:47:38'),('app','en','Day','Day',0,'','0000-00-00 00:00:00'),('app','en','dbField','Identifier',0,'','0000-00-00 00:00:00'),('app','en','dbValue','Value',0,'','0000-00-00 00:00:00'),('app','en','Decimals','Decimals',0,'','2014-09-07 13:39:13'),('app','en','Decommissioning','Decommissioning',0,'','0000-00-00 00:00:00'),('app','en','Delete','Delete',0,'','0000-00-00 00:00:00'),('app','en','DeleteBranch','Delete branch',0,'','0000-00-00 00:00:00'),('app','en','DeleteEntity','Delete channel',0,'','0000-00-00 00:00:00'),('app','en','DeleteEntityChilds','Delete channel with sub channels',0,'','0000-00-00 00:00:00'),('app','en','DeleteEntityHint','Delete channel (only possible if not assigned in channel hierarchy)',0,'','2014-09-10 10:00:28'),('app','en','DeleteReading','Delete reading value (only possible for raw data listing)',0,'','2016-04-24 14:06:51'),('app','en','DeleteReadingConfirm','Do you really want delete this reading value?!',0,'','2014-02-14 13:55:20'),('app','en','DeleteTariff','Delete tariff',0,'','2014-05-01 16:48:15'),('app','en','DeleteTariffDate','Delete data for this start date',0,'','2014-05-01 16:53:35'),('app','en','DeleteViewFailed','Delete chart \'%s\' failed.',0,'','0000-00-00 00:00:00'),('app','en','Delta','Delta',0,'','0000-00-00 00:00:00'),('app','en','Description','Description',0,'','0000-00-00 00:00:00'),('app','en','DontForgetUpdateAPIKey','Don\'t forget to update the API key in extranl scripts after recreation!',0,'','0000-00-00 00:00:00'),('app','en','DragBookmark','Drag the link to your bookmarks',0,'','0000-00-00 00:00:00'),('app','en','DragDropHelp','- Drag a group or channel here for append to top level\r\n- Use Ctrl+Click to start copy of channel\r\n- You can\'t copy groups, create an alias and use this instead',0,'','2014-04-26 14:48:52'),('app','en','DragPermanent','Permanent link with dates\r\nDrag the link to your bookmarks',0,'','0000-00-00 00:00:00'),('app','en','DragRowsToReorder','Drag rows to change channel order',0,'','2014-05-07 16:26:38'),('app','en','DrawOutline','Schadow',0,'','2015-11-04 16:39:43'),('app','en','DrawOutlineHint','Draw a white shadow behind the line to make the line better visible.',0,'','2015-11-04 16:40:01'),('app','en','DSEP','.',0,'','0000-00-00 00:00:00'),('app','en','DuringDaylight','Between sunrise and sunset only',0,'','2014-03-26 14:05:31'),('app','en','Earning','Earning',0,'','0000-00-00 00:00:00'),('app','en','Edit','Edit',0,'','0000-00-00 00:00:00'),('app','en','EditChannel','Edit channel',0,'','0000-00-00 00:00:00'),('app','en','EditEntity','Edit channel',0,'','0000-00-00 00:00:00'),('app','en','EditSwitchAliasWithOriginal','You can\'t edit an alias, therefor switch to original channel!',0,'','2014-07-04 09:35:23'),('app','en','EditTariff','Edit tariff master data',0,'','2014-05-01 16:47:09'),('app','en','EditTariffDate','Edit tariff date time set',0,'','2014-05-01 09:15:55'),('app','en','EndTime','End time',0,'','2014-05-01 12:39:41'),('app','en','Energy','Energy',0,'','0000-00-00 00:00:00'),('app','en','EntityType','Channel type',0,'','0000-00-00 00:00:00'),('app','en','Equipment','Equipment',0,'','0000-00-00 00:00:00'),('app','en','ExampleUnit','Unit example',0,'','2013-12-30 10:09:58'),('app','en','Expand','Expand',0,'','0000-00-00 00:00:00'),('app','en','ExpandAll','ExpandAll',0,'','0000-00-00 00:00:00'),('app','en','FindYourLocation','Find your location',0,'','2014-10-13 11:29:40'),('app','en','FixCostDay','Fixed cost per day',0,'','2014-05-01 20:32:01'),('app','en','FixCostPerDay','Fix cost per day',0,'','2014-05-02 13:29:34'),('app','en','from','from',0,'','0000-00-00 00:00:00'),('app','en','GenerateAdminHash','Create admininistration authorization',0,'','0000-00-00 00:00:00'),('app','en','HarmonicMean','Harmonic mean',0,'','0000-00-00 00:00:00'),('app','en','HierarchyCreated','Channel hierarchy created',0,'','2013-12-30 17:59:41'),('app','en','IndexLength','Index size',0,'','0000-00-00 00:00:00'),('app','en','InfoHint','Background information',0,'','2014-01-31 20:27:00'),('app','en','Information','Information',0,'','0000-00-00 00:00:00'),('app','en','InformationHint','Information required for configuring storage and extractions',0,'','0000-00-00 00:00:00'),('app','en','InstalledAdapters','Installed adapters',0,'','0000-00-00 00:00:00'),('app','en','Inverter','Inverter',0,'','0000-00-00 00:00:00'),('app','en','InverterWithStrings','Inverter with string data',0,'','0000-00-00 00:00:00'),('app','en','Irradiation','Irradiation',0,'','0000-00-00 00:00:00'),('app','en','JustAMoment','Just a moment please ...',0,'','0000-00-00 00:00:00'),('app','en','Key','Key',0,'','2014-07-08 08:07:37'),('app','en','Last','Last',0,'','0000-00-00 00:00:00'),('app','en','lastone','last',0,'','2014-01-13 13:58:35'),('app','en','LastReading','Last reading',0,'','0000-00-00 00:00:00'),('app','en','LastTimestamp','Time stamp of\r\nlast data recording',0,'','0000-00-00 00:00:00'),('app','en','LatestAPIVersion','Latest API version',0,'','2014-09-28 14:30:16'),('app','en','left','left',0,'','0000-00-00 00:00:00'),('app','en','Legend','Legend',0,'','2014-05-09 10:47:58'),('app','en','LineBold','thick',0,'','0000-00-00 00:00:00'),('app','en','LineChart','Line',0,'','2014-02-14 07:52:21'),('app','en','LineDash','dashed',0,'','2014-02-14 08:18:45'),('app','en','LineDashDot','dash-dot',0,'','2014-02-14 08:19:53'),('app','en','LineDot','dotted',0,'','2014-02-14 08:19:26'),('app','en','LineLongDash','dashed long',0,'','2014-02-14 08:28:15'),('app','en','LineLongDashDot','dash-dot long',0,'','2014-02-14 08:20:46'),('app','en','LineLongDashDotDot','dash-dot-dot',0,'','2014-02-14 08:41:11'),('app','en','LineNormal','normal',0,'','0000-00-00 00:00:00'),('app','en','LinesDashed','Dashed',0,'','2014-02-14 08:35:15'),('app','en','LinesDashedDotted','dash-dot',0,'','2014-02-14 08:36:03'),('app','en','LinesDashedDottedDotted','dash-dot-dot',0,'','2014-02-14 08:36:21'),('app','en','LinesDotted','Dotted',0,'','2014-02-14 08:35:27'),('app','en','LineShortDash','dashed short',0,'','2014-02-14 08:21:50'),('app','en','LineShortDashDot','dash-dot short',0,'','2014-02-14 08:22:49'),('app','en','LineShortDashDotDot','dash-dot-dot short',0,'','2014-02-14 08:23:20'),('app','en','LineShortDot','dotted short',0,'','2014-02-14 08:22:18'),('app','en','LineSolid','solid',0,'','2014-02-14 08:17:52'),('app','en','LineWidth','Line width',0,'','0000-00-00 00:00:00'),('app','en','List','List',0,'','2014-01-25 13:43:20'),('app','en','ListExportCSVHint','Export all data as Comma-Separated file',0,'','2014-01-26 20:19:51'),('app','en','ListExportTextHint','Export all data as Space-Separated file',0,'','2014-01-26 20:19:51'),('app','en','ListExportTSVHint','Export all data as Tab-Separated file',0,'','2014-01-26 20:19:51'),('app','en','ListHint','Measuring data as table',0,'','2014-01-25 13:43:20'),('app','en','ListRefreshHint','Click or F6: Reread channel data',0,'','0000-00-00 00:00:00'),('app','en','Lists','Lists',0,'','2014-09-24 19:23:08'),('app','en','Load','Load',0,'','0000-00-00 00:00:00'),('app','en','Log','Log',0,'','0000-00-00 00:00:00'),('app','en','LogHint','Log entries',0,'','0000-00-00 00:00:00'),('app','en','Login','Login',0,'','0000-00-00 00:00:00'),('app','en','LoginRequired','This function is only for logged in users available!',0,'','2014-10-05 11:12:03'),('app','en','LoginToken','Permanent login token, for this computer IP only!',0,'','2014-05-13 06:40:47'),('app','en','Logout','Logout',0,'','0000-00-00 00:00:00'),('app','en','LogoutSuccessful','[b]%s[/b] logged out successful.',0,'','0000-00-00 00:00:00'),('app','en','Manufacturer','Manufacturer',0,'','0000-00-00 00:00:00'),('app','en','MarkAll','all',0,'','2014-02-13 13:39:26'),('app','en','MarkAllHint','Can only be used (and makes only sense) for Bar charts',0,'','2014-02-13 14:42:50'),('app','en','MarkExtremes','Mark reading values',0,'','2014-01-13 14:16:13'),('app','en','MarkLast','last',0,'','2014-02-13 13:39:16'),('app','en','MarkMax','max.',0,'','2014-02-13 13:38:55'),('app','en','MarkMin','min.',0,'','2014-02-13 13:38:42'),('app','en','MasterData','Master data',0,'','2014-09-24 15:26:05'),('app','en','max','max',0,'','0000-00-00 00:00:00'),('app','en','Message','Message',0,'','0000-00-00 00:00:00'),('app','en','min','min',0,'','0000-00-00 00:00:00'),('app','en','Minutes','Minutes',0,'','2014-10-19 18:43:22'),('app','en','MissingAPIkey','Missing API key!',0,'','0000-00-00 00:00:00'),('app','en','MobileChart','chart for mobiles',0,'','2014-03-13 11:31:57'),('app','en','MobileVariantHint','If you plan to use PVLng on mobile devices, define at least a chart [b]@mobile[/b] as default chart.\r\nOnly charts starting with a [b]@[/b] will be available mobile.\r\n(Mobile charts are public by default!) ',0,'','0000-00-00 00:00:00'),('app','en','Model','Model',0,'','0000-00-00 00:00:00'),('app','en','Month','Month',0,'','0000-00-00 00:00:00'),('app','en','MonthlyAverage','Monthly average',0,'','0000-00-00 00:00:00'),('app','en','MoreIntoBackground','more to back',0,'','2014-07-02 11:10:11'),('app','en','MoreIntoForeground','more to front',0,'','2014-07-02 11:10:11'),('app','en','MoveChannel','Move channel',0,'','0000-00-00 00:00:00'),('app','en','MoveChannelHowMuchRows','Move how many positions (on same level)?',0,'','0000-00-00 00:00:00'),('app','en','MoveChannelStartEnd','to the start / the end',0,'','0000-00-00 00:00:00'),('app','en','MoveEntityDown','Move channel down',0,'','0000-00-00 00:00:00'),('app','en','MoveEntityLeft','Move channel one level up',0,'','0000-00-00 00:00:00'),('app','en','MoveEntityRight','Move channel one level down',0,'','0000-00-00 00:00:00'),('app','en','MoveEntityUp','Move channel up',0,'','0000-00-00 00:00:00'),('app','en','MustHaveChilds','This channel type must have childs provided for correct working!',0,'','2013-12-30 08:24:10'),('app','en','Name','Name',0,'','0000-00-00 00:00:00'),('app','en','NameRequired','The name is required.',0,'','0000-00-00 00:00:00'),('app','en','New','New',0,'','0000-00-00 00:00:00'),('app','en','NewStartDate','New start date',0,'','2014-04-30 04:58:24'),('app','en','NextDay','Next day',0,'','0000-00-00 00:00:00'),('app','en','No','No',0,'','0000-00-00 00:00:00'),('app','en','NoChannelMatch','No channel match',0,'','2014-05-17 18:25:17'),('app','en','NoChannelsSelectedYet','There are no channels or a chart selected yet to view.',0,'','0000-00-00 00:00:00'),('app','en','NoChartMatch','No chart match',0,'','2014-05-17 18:43:32'),('app','en','NoDataAvailable','No data available',0,'','0000-00-00 00:00:00'),('app','en','None','None',0,'','0000-00-00 00:00:00'),('app','en','NotAuthorized','Not authorized! A wrong API key was submitted.',0,'','0000-00-00 00:00:00'),('app','en','NoViewSelectedYet','There is no chart selected yet to view.',0,'','0000-00-00 00:00:00'),('app','en','of','of',0,'','2014-03-14 19:45:15'),('app','en','Ok','Ok',0,'','0000-00-00 00:00:00'),('app','en','OnlyChannelsWithReadings','Only channels with readings',0,'','2014-07-07 19:56:55'),('app','en','or','or',0,'','0000-00-00 00:00:00'),('app','en','Overview','Channel hierarchy',0,'','2014-09-24 15:34:17'),('app','en','OverviewHint','Overview of your equipments and its hierarchy',0,'','2014-09-24 15:35:38'),('app','en','Overwrite','Overwrite',0,'','0000-00-00 00:00:00'),('app','en','Page','Page',0,'','2014-03-14 19:45:04'),('app','en','Parameter','Parameter',0,'','0000-00-00 00:00:00'),('app','en','Password','Password',0,'','0000-00-00 00:00:00'),('app','en','PasswordSaved','Password was saved',0,'','2014-10-13 11:42:46'),('app','en','PasswordsNotEqual','The passwords are not equal.',0,'','0000-00-00 00:00:00'),('app','en','PerformanceRatio','Performance ratio',0,'','0000-00-00 00:00:00'),('app','en','Period','Period',0,'','0000-00-00 00:00:00'),('app','en','PlantDescriptionHint','Description of installation',0,'','2014-01-31 20:27:00'),('app','en','PleaseRelogin','Please re-login!',0,'','2014-10-13 07:08:24'),('app','en','Positions','Position(s)',0,'','0000-00-00 00:00:00'),('app','en','Power','Power',0,'','0000-00-00 00:00:00'),('app','en','Presentation','Presentation',0,'','0000-00-00 00:00:00'),('app','en','PrevDay','Previous day',0,'','0000-00-00 00:00:00'),('app','en','private','private',0,'','2014-03-14 09:23:46'),('app','en','PrivateChannel','No public channel',0,'','0000-00-00 00:00:00'),('app','en','PrivateChart','private chart',0,'','2014-03-13 11:29:58'),('app','en','proceed','proceed',0,'','2013-12-27 17:15:42'),('app','en','Production','Production',0,'','0000-00-00 00:00:00'),('app','en','public','public',0,'','0000-00-00 00:00:00'),('app','en','PublicChart','public chart',0,'','2014-03-13 11:30:17'),('app','en','publicHint','- Public charts are accessible by not logged in visitors.\r\n- Mobile charts are only visible for not logged in users in mobile mode, private channels will be suppressed.',0,'','2014-03-15 17:59:59'),('app','en','ReadableEntity','Readable channel',0,'','0000-00-00 00:00:00'),('app','en','Reading','Reading value',0,'','2014-01-26 19:48:08'),('app','en','ReadingDeleted','Reading data deleted',0,'','2014-02-14 12:57:45'),('app','en','Readings','Readings',0,'','0000-00-00 00:00:00'),('app','en','ReadWritableEntity','Writable and readable channel',0,'','2014-05-29 12:31:24'),('app','en','RecordCount','Record count',0,'','0000-00-00 00:00:00'),('app','en','Redisplay','Display',0,'','0000-00-00 00:00:00'),('app','en','Refresh','Refresh',0,'','0000-00-00 00:00:00'),('app','en','Regenerate','Regenerate',0,'','0000-00-00 00:00:00'),('app','en','RemoveTariffIfUsed','If the tariff is used in a channel, it will be removed there.',0,'','2014-05-01 18:32:57'),('app','en','RequestTypes','Request types',0,'','0000-00-00 00:00:00'),('app','en','Required','required',0,'','2014-05-11 10:14:56'),('app','en','resetZoom','Reset zoom',0,'','0000-00-00 00:00:00'),('app','en','resetZoomTitle','Reset zoom to 1:1',0,'','0000-00-00 00:00:00'),('app','en','Resolution','Factor',0,'','2014-07-08 10:17:39'),('app','en','right','right',0,'','0000-00-00 00:00:00'),('app','en','RowCount','Row count',0,'','2014-01-26 19:48:48'),('app','en','RowCountHint','Number of rows which was consolidated',0,'','2014-01-26 19:49:57'),('app','en','Rows','Rows',0,'','2014-11-20 16:12:07'),('app','en','Save','Save',0,'','0000-00-00 00:00:00'),('app','en','ScanForMobileView','Mobile view',0,'','2015-02-27 13:31:06'),('app','en','ScatterCandidate','This channel is non-numeric or have no unit, may be \"Scatter\" could be a good presentation',0,'','2014-10-12 11:54:58'),('app','en','ScatterChart','Scatter',0,'','2014-02-14 07:56:17'),('app','en','Scope','Scope',0,'','0000-00-00 00:00:00'),('app','en','SeeAdapters','See below which adapters are installed.',0,'','0000-00-00 00:00:00'),('app','en','SeeAPIReference','For more information take a look into the [url=http://pvlng.com/API]API reference[/url].',0,'','2014-04-05 16:32:36'),('app','en','Select','Select',0,'','0000-00-00 00:00:00'),('app','en','SelectChannel','Select channel',0,'','2014-01-26 21:22:38'),('app','en','SelectChart','Select chart',0,'','2014-05-17 19:42:10'),('app','en','SelectEntity','Select channel',0,'','0000-00-00 00:00:00'),('app','en','SelectEntityTemplate','Select template',0,'','2013-12-30 16:12:03'),('app','en','SelectEntityType','Select channel type',0,'','0000-00-00 00:00:00'),('app','en','Selection','Selection',0,'','0000-00-00 00:00:00'),('app','en','SelectView','Select chart',0,'','0000-00-00 00:00:00'),('app','en','Send','Send',0,'','0000-00-00 00:00:00'),('app','en','Serial','Serial number',0,'','0000-00-00 00:00:00'),('app','en','SerialRequired','Serial number is required',0,'','0000-00-00 00:00:00'),('app','en','SerialStillExists','This serial number still exists.',0,'','0000-00-00 00:00:00'),('app','en','SeriesType','Series display type',0,'','0000-00-00 00:00:00'),('app','en','SetAxisMinZero','Set Y axis min. to 0',0,'','0000-00-00 00:00:00'),('app','en','Settings','Settings',0,'','2014-10-06 09:39:49'),('app','en','SettingsMenu','Settings',0,'','2014-10-06 17:56:41'),('app','en','Show','Show',0,'','2014-04-30 04:59:54'),('app','en','ShowConsumption','Period values',0,'','0000-00-00 00:00:00'),('app','en','ShowConsumptionHint','Shows for meter channels the data per selected aggregation period and not the total over time',0,'','0000-00-00 00:00:00'),('app','en','ShowDescription','Show description',0,'','2014-06-04 13:54:03'),('app','en','ShowGUID','Show channel GUID',0,'','2014-05-29 12:34:48'),('app','en','Size','Size',0,'','2014-11-20 16:07:28'),('app','en','SplineChart','Spline',0,'','2014-02-14 07:53:33'),('app','en','StartDate','Start date',0,'','2014-04-30 04:55:23'),('app','en','StartHidden','Start hidden',0,'','2014-09-29 08:07:28'),('app','en','StartingTimes','Starting times',0,'','2014-04-30 11:45:35'),('app','en','StartTime','Start time',0,'','2014-04-30 04:58:58'),('app','en','Statistics','Statistics',0,'','0000-00-00 00:00:00'),('app','en','StayLoggedIn','Remember me[br][small]for 1 week[/small]',0,'','2016-04-25 08:43:11'),('app','en','Stick','Stick',0,'','0000-00-00 00:00:00'),('app','en','SuppressZero','Suppress zero values',0,'','0000-00-00 00:00:00'),('app','en','Sure','Sure',0,'','0000-00-00 00:00:00'),('app','en','SystemInformation','System information',0,'','0000-00-00 00:00:00'),('app','en','Tariff','Tariff',0,'','2014-04-30 04:58:41'),('app','en','TariffCreated','Tariff was created',0,'','2014-05-01 10:39:39'),('app','en','TariffDatesCopied','Tariff dates was copied',0,'','2014-05-01 10:40:13'),('app','en','Tariffs','Tariffs',0,'','2014-05-01 08:58:14'),('app','en','TariffsHint','Day or day time based tariffs',0,'','2014-05-29 12:33:08'),('app','en','TariffThisWeek','Tariffs this week',0,'','2014-05-01 12:40:26'),('app','en','Temperature','Temperature',0,'','0000-00-00 00:00:00'),('app','en','TemperatureDifference','Temperature difference',0,'','0000-00-00 00:00:00'),('app','en','TemperatureModules','Temperature modules',0,'','0000-00-00 00:00:00'),('app','en','TemperatureOutside','Temperature outside',0,'','0000-00-00 00:00:00'),('app','en','Template','Template',0,'','2014-09-07 12:46:52'),('app','en','ThinLine','thin',0,'','0000-00-00 00:00:00'),('app','en','Threshold','Threshold',0,'','0000-00-00 00:00:00'),('app','en','TimeDaysTariffRequired','Only rows with a start time, at least one weekday and a tariff will be valid.',0,'','2014-04-30 05:05:21'),('app','en','TimeRange','Time range',0,'','2014-03-12 13:25:32'),('app','en','TimeRangeHint','If you have a channel with 24hr data at the same chart with channels which have only data during daylight times, you can limit the displayed time.',0,'','2014-09-17 07:29:28'),('app','en','Timestamp','Timestamp',0,'','0000-00-00 00:00:00'),('app','en','to','to',0,'','0000-00-00 00:00:00'),('app','en','Today','Today',0,'','0000-00-00 00:00:00'),('app','en','ToggleChannels','Expand/collapse channels',0,'','0000-00-00 00:00:00'),('app','en','toggleGUIDs','Show channel GUIDs',0,'','0000-00-00 00:00:00'),('app','en','TopLevel','On top level',0,'','2014-01-09 14:30:08'),('app','en','Total','Total',0,'','0000-00-00 00:00:00'),('app','en','TotalRows','Total rows',0,'','0000-00-00 00:00:00'),('app','en','TotalSize','Total size',0,'','0000-00-00 00:00:00'),('app','en','TSEP',',',0,'','0000-00-00 00:00:00'),('app','en','Type','Type',0,'','0000-00-00 00:00:00'),('app','en','Unit','Unit',0,'','0000-00-00 00:00:00'),('app','en','UnknownUser','Wrong password',0,'','2014-10-13 06:57:28'),('app','en','UnknownView','Unknown chart: \'%s\'',0,'','0000-00-00 00:00:00'),('app','en','unlimited','unlimited',0,'','2013-12-30 12:02:40'),('app','en','UnsavedChanges','You have unsaved changes for your chart',0,'','2014-02-25 14:33:59'),('app','en','UsableInCharts','Usable in charts',0,'','2014-09-08 06:02:19'),('app','en','UseDifferentColor','Different color from threshold',0,'','2014-07-02 13:49:10'),('app','en','UseOwnConsolidation','Use your own consolidation period\r\n(But this will not saved in variant settings)',0,'','2014-01-13 12:54:16'),('app','en','Value','Value',0,'','0000-00-00 00:00:00'),('app','en','ValueMustGEzero','Value must be greater or equal 0',0,'','2014-10-10 19:33:29'),('app','en','ValueMustGTzero','Value must be greater than 0',0,'','2014-10-10 19:34:29'),('app','en','Variant','Chart',0,'','0000-00-00 00:00:00'),('app','en','Variants','Charts',0,'','0000-00-00 00:00:00'),('app','en','VariantsPublic','Public charts',0,'','0000-00-00 00:00:00'),('app','en','ViewDeleted','Chart \'%s\' deleted.',0,'','0000-00-00 00:00:00'),('app','en','Voltage','Voltage',0,'','0000-00-00 00:00:00'),('app','en','Weather','Weather',0,'','2014-08-25 12:55:12'),('app','en','WeatherForecast','Weather forecast',0,'','2014-08-21 06:25:31'),('app','en','Weekdays','Weekdays',0,'','2014-04-30 04:59:19'),('app','en','WeeklyAverage','Weekly average',0,'','0000-00-00 00:00:00'),('app','en','Welcome','Welcome %s!',0,'','0000-00-00 00:00:00'),('app','en','WelcomeToAdministration','Welcome in your PVLng administration area.',0,'','0000-00-00 00:00:00'),('app','en','WritableEntity','Writable channel',0,'','0000-00-00 00:00:00'),('app','en','YearlyAverage','Yearly average',0,'','0000-00-00 00:00:00'),('app','en','Yes','Yes',0,'','0000-00-00 00:00:00'),('app','en','YourAPIcode','API key for updating your data\r\n\r\n[i]Always keep your API key secret![/i]',0,'','2014-10-12 15:14:05'),('channel','de','adjust','Offset anpassen',0,'','0000-00-00 00:00:00'),('channel','de','adjustHint','Passt den Kanal-Offset automatisch an, wenn der aktuelle Messwert kleiner als der letzte gespeicherte Messwert ist aber <> 0.\r\nWird nur bei Meter-Kanälen benutzt.\r\nSetze das Kennzeichen, wenn Dein Mess-Equipment manchmal seinen Stand verliert/zurücksetzt.',0,'','0000-00-00 00:00:00'),('channel','de','channel','Kanal',0,'','0000-00-00 00:00:00'),('channel','de','channelHint','Kanalname bei Multi-Sensoren',0,'','0000-00-00 00:00:00'),('channel','de','comment','Kommentar',0,'','0000-00-00 00:00:00'),('channel','de','commentHint','interner Kommentar',0,'','0000-00-00 00:00:00'),('channel','de','cost','Kosten',0,'','0000-00-00 00:00:00'),('channel','de','costHint','Kosten pro Einheit, nur bei Meter-Kanälen',0,'','0000-00-00 00:00:00'),('channel','de','decimals','Dezimalstellen',0,'','0000-00-00 00:00:00'),('channel','de','decimalsHint','Für die Wert-Ausgabe',0,'','0000-00-00 00:00:00'),('channel','de','description','Beschreibung',0,'','0000-00-00 00:00:00'),('channel','de','descriptionHint','Langtext',0,'','0000-00-00 00:00:00'),('channel','de','Help','Hinweis',0,'','0000-00-00 00:00:00'),('channel','de','icon','Symbol',0,'','2014-10-03 08:58:41'),('channel','de','iconHint','Kanal-Symbol',0,'','2014-10-03 08:59:17'),('channel','de','latitude','Breitengrad',0,'','2014-01-19 10:03:29'),('channel','de','latitudeHint','Breitengrad des Standortes\r\nStandard ist Norden, gib einen negativen Werte für Süden ein\r\n(Finde Deine Koordinaten auf [url=http://de.mygeoposition.com/]MyGeoPosition.com[/url])',0,'','2014-06-03 10:48:28'),('channel','de','legend','Legende',0,'','2015-11-13 18:44:48'),('channel','de','legendHint','Alternative Beschriftung in Diagramm-Legenden',0,'','2015-11-13 18:50:40'),('channel','de','longitude','Längengrad',0,'','2014-01-19 10:03:29'),('channel','de','longitudeHint','Längengrad des Standortes\r\nStandard ist Osten, gib einen negativen Werte für Westen ein',0,'','2014-06-03 10:48:19'),('channel','de','meter','Meter',0,'','0000-00-00 00:00:00'),('channel','de','meterHint','Meter-Kanäle speichern nur aufsteigende Werte',0,'','0000-00-00 00:00:00'),('channel','de','Name','Name',0,'','0000-00-00 00:00:00'),('channel','de','nameHint','Eindeutiger Kanalname',0,'','0000-00-00 00:00:00'),('channel','de','NoChannelForGUID','Es existiert kein Kanal mit dieser GUID',0,'','2014-05-18 13:47:12'),('channel','de','NoValidGUID','Kein gültiges GUID-Format',0,'','2014-05-18 13:48:23'),('channel','de','numeric','Numerische Werte',0,'','0000-00-00 00:00:00'),('channel','de','numericHint','Der Kanal hat numerische oder Alphanumerische Daten?',0,'','0000-00-00 00:00:00'),('channel','de','offset','Offset',0,'','0000-00-00 00:00:00'),('channel','de','offsetHint','Mittels dieses Offsets werden die realen Messwerte während des Auslesens korrigiert.',0,'','0000-00-00 00:00:00'),('channel','de','Param','Parameter',0,'','0000-00-00 00:00:00'),('channel','de','ParamIsRequired','Wert erforderlich',0,'','2014-01-25 12:08:49'),('channel','de','ParamMustInteger','Der Wert muss ganzzahlig sein',0,'','2014-01-25 12:10:38'),('channel','de','ParamMustNumeric','Wert muss numerisch sein',0,'','2014-01-25 12:09:56'),('channel','de','public','Öffentlich',0,'','0000-00-00 00:00:00'),('channel','de','publicHint','Nicht-öffentliche Kanäle sind für nicht eingeloggte Besucher oder ohne API key nicht ansprechbar.',0,'','0000-00-00 00:00:00'),('channel','de','resolution','Faktor',0,'','2013-12-29 14:19:15'),('channel','de','resolutionHint','Beim Auslesen wird der gespeicherte/berechnete Messwert mit diesem Faktor multipliziert.',0,'','2014-02-12 09:29:47'),('channel','de','Serial','Seriennummer',0,'','0000-00-00 00:00:00'),('channel','de','serialHint','Eindeutige Sensor-Serialnummer',0,'','0000-00-00 00:00:00'),('channel','de','tags','Kanal-Tags',0,'','2015-04-08 12:48:02'),('channel','de','tagsHint','Manche Funktionalitäten benötigen den Kanälen zugeordenete Tags die in den speziellen Hilfeseiten beschrieben werden.',0,'','2015-04-08 12:02:41'),('channel','de','tariff','Tarif',0,'','2014-05-01 16:12:38'),('channel','de','tariffHint','Wenn Du verschiedene Tarife über den Tag/Woche hast, ordne hier einen [url=/tariff]entsprechenden Tarif[/url] zu. (Für konstante Beträge benutze das Kosten-Attribut für bessere Performanz)\r\nWenn ein Tarif zugeordnet ist wird das Kosten-Attribut übersteuert!',0,'','2014-05-01 16:27:26'),('channel','de','threshold','Schwellwert',0,'','0000-00-00 00:00:00'),('channel','de','thresholdHint','Ein Messwert ist nur gültig, wenn er sich um +- Schwellwert vom letzten gespeicherten Messwert unterscheidet.',0,'','0000-00-00 00:00:00'),('channel','de','unit','Einheit',0,'','0000-00-00 00:00:00'),('channel','de','unitHint','Einheit des Kanals',0,'','0000-00-00 00:00:00'),('channel','de','valid_from','Unterer Grenzwert',0,'','0000-00-00 00:00:00'),('channel','de','valid_fromHint','Werte sind nur gültig, wenn sie größer oder gleich dieses Wertes sind.\r\nBei beschreibbaren Kanälen werden werden ungültige Werte bereits beim Speichern, bei berechneten Kanälen beim Auslesen verworfen.',0,'','2014-01-20 14:04:51'),('channel','de','valid_to','Oberer Grenzwert',0,'','0000-00-00 00:00:00'),('channel','de','valid_toHint','Werte sind nur gültig, wenn sie kleiner oder gleich dieses Wertes sind.\r\nBei beschreibbaren Kanälen werden werden ungültige Werte bereits beim Speichern, bei berechneten Kanälen beim Auslesen verworfen.',0,'','2014-01-20 14:04:51'),('channel','de','Value','Parameterwert',0,'','0000-00-00 00:00:00'),('channel','en','adjust','Adjust offset',0,'','0000-00-00 00:00:00'),('channel','en','adjustHint','Adjust channel offset automatic, if the actual reading value is lower than last reading but <> 0.\r\nUsed only for meter channels.\r\nUse this, if your measuring equipment sometimes looses/resets its counter.',0,'','0000-00-00 00:00:00'),('channel','en','channel','Channel',0,'','0000-00-00 00:00:00'),('channel','en','channelHint','Channel name for multi sensors',0,'','0000-00-00 00:00:00'),('channel','en','comment','Comment',0,'','0000-00-00 00:00:00'),('channel','en','commentHint','Internal comment',0,'','0000-00-00 00:00:00'),('channel','en','cost','Cost',0,'','0000-00-00 00:00:00'),('channel','en','costHint','Cost per unit, for meter channels only',0,'','0000-00-00 00:00:00'),('channel','en','decimals','Decimals',0,'','0000-00-00 00:00:00'),('channel','en','decimalsHint','Decimals for value output',0,'','0000-00-00 00:00:00'),('channel','en','description','Description',0,'','0000-00-00 00:00:00'),('channel','en','descriptionHint','Long description',0,'','0000-00-00 00:00:00'),('channel','en','Help','Hint',0,'','0000-00-00 00:00:00'),('channel','en','icon','Icon',0,'','2014-10-03 08:58:41'),('channel','en','iconHint','Channel icon',0,'','2014-10-03 08:59:17'),('channel','en','latitude','Latitude',0,'','2014-01-19 10:03:29'),('channel','en','latitudeHint','Latitude of location\r\nDefaults to North, use negative value for South\r\n(Find your coordinates on [url=http://en.mygeoposition.com/]MyGeoPosition.com[/url])',0,'','2014-06-03 10:48:10'),('channel','en','legend','Legend',0,'','2015-11-13 18:44:48'),('channel','en','legendHint','Alternate label in chart legend',0,'','2015-11-13 18:46:07'),('channel','en','longitude','Longitude',0,'','2014-01-19 10:03:29'),('channel','en','longitudeHint','Longitude of location\r\ndefaults to East, use negative value for West',0,'','2014-06-03 10:48:00'),('channel','en','meter','Meter',0,'','0000-00-00 00:00:00'),('channel','en','meterHint','Meter channels stores raising values',0,'','0000-00-00 00:00:00'),('channel','en','Name','Name',0,'','0000-00-00 00:00:00'),('channel','en','nameHint','Unique channel name',0,'','0000-00-00 00:00:00'),('channel','en','NoChannelForGUID','No channel exists with this GUID',0,'','2014-05-18 13:47:12'),('channel','en','NoValidGUID','No valid GUID format',0,'','2014-05-18 13:48:23'),('channel','en','numeric','Numeric values',0,'','0000-00-00 00:00:00'),('channel','en','numericHint','Channels have numeric or alphanumeric data?',0,'','0000-00-00 00:00:00'),('channel','en','offset','Offset',0,'','0000-00-00 00:00:00'),('channel','en','offsetHint','Apply this value during readout to the reading values to correct them.',0,'','0000-00-00 00:00:00'),('channel','en','Param','Parameter',0,'','0000-00-00 00:00:00'),('channel','en','ParamIsRequired','Value required',0,'','2014-01-25 12:08:48'),('channel','en','ParamMustInteger','Value must be an integer',0,'','2014-01-25 12:10:38'),('channel','en','ParamMustNumeric','Value must be numeric',0,'','2014-01-25 12:09:55'),('channel','en','public','Public',0,'','0000-00-00 00:00:00'),('channel','en','publicHint','Non public channels are not accessible for not logged in visitors or without API key.',0,'','0000-00-00 00:00:00'),('channel','en','resolution','Factor',0,'','2013-12-29 14:19:15'),('channel','en','resolutionHint','On data readout the stored/calculated reading will multiplied with this factor',0,'','2014-02-12 09:29:47'),('channel','en','Serial','Serial number',0,'','0000-00-00 00:00:00'),('channel','en','serialHint','Unique sensor serial number',0,'','0000-00-00 00:00:00'),('channel','en','tags','Channel tags',0,'','2015-04-08 11:59:41'),('channel','en','tagsHint','Some features needs tags attached to channel, described in the specific help pages.',0,'','2015-04-08 12:02:41'),('channel','en','tariff','Tariff',0,'','2014-05-01 16:12:38'),('channel','en','tariffHint','If you have different costs over day/week for this channel, assign an [url=/tariff]appropriate tariff[/url] here. (For constant costs use the cost attribute for better performance)\r\nIf a tariff is assigned, it will overrule a cost value!',0,'','2014-05-01 16:26:52'),('channel','en','threshold','Threshold',0,'','0000-00-00 00:00:00'),('channel','en','thresholdHint','A reading is only accepted, if the value is +- threshold from last reading.',0,'','0000-00-00 00:00:00'),('channel','en','unit','Unit',0,'','0000-00-00 00:00:00'),('channel','en','unitHint','Channel unit',0,'','0000-00-00 00:00:00'),('channel','en','valid_from','Valid from',0,'','0000-00-00 00:00:00'),('channel','en','valid_fromHint','Readings are only valid if they are greater or equal this limit.\r\nFor writable channels invalid values skipped on saving, for calculated channels they will be skipped on readout.',0,'','2014-01-20 14:04:51'),('channel','en','valid_to','Valid to',0,'','0000-00-00 00:00:00'),('channel','en','valid_toHint','Readings are only valid if they are lower or equal this limit.\r\nFor writable channels invalid values skipped on saving, for calculated channels they will be skipped on readout.',0,'','2014-01-20 14:04:51'),('channel','en','Value','Parameter value',0,'','0000-00-00 00:00:00'),('code_admin','en','app','multi=1',0,'','0000-00-00 00:00:00'),('code_admin','en','channel','multi=1',0,'','0000-00-00 00:00:00'),('code_admin','en','code_admin','param=1 slave=1',0,'','0000-00-00 00:00:00'),('code_admin','en','EquiVars','slave=1',0,'','0000-00-00 00:00:00'),('code_admin','en','inverter','multi=1',0,'','0000-00-00 00:00:00'),('code_admin','en','model','multi=1',0,'','0000-00-00 00:00:00'),('code_admin','en','plant','multi=1',0,'','0000-00-00 00:00:00'),('code_admin','en','sensor','multi=1',0,'','0000-00-00 00:00:00'),('code_admin','en','var','multi=1',0,'','0000-00-00 00:00:00'),('code_lang','de','de','Deutsch',0,'','0000-00-00 00:00:00'),('code_lang','de','en','Englisch',-1,'','0000-00-00 00:00:00'),('code_lang','en','de','german',0,'','0000-00-00 00:00:00'),('code_lang','en','en','english',-1,'','0000-00-00 00:00:00'),('code_set','de','app','Anwendung',0,'','0000-00-00 00:00:00'),('code_set','de','channel','Kanal',0,'','0000-00-00 00:00:00'),('code_set','de','code_admin','Code admin',-1,'','0000-00-00 00:00:00'),('code_set','de','code_lang','Sprache',-2,'','0000-00-00 00:00:00'),('code_set','de','code_set','Code set',-3,'','0000-00-00 00:00:00'),('code_set','de','day','Tag',0,'','0000-00-00 00:00:00'),('code_set','de','day1','Tag (1)',0,'','0000-00-00 00:00:00'),('code_set','de','day2','Tag (2)',0,'','0000-00-00 00:00:00'),('code_set','de','day3','Tag (3)',0,'','0000-00-00 00:00:00'),('code_set','de','locale','Lokalisierung',0,'','0000-00-00 00:00:00'),('code_set','de','model','Model',0,'','0000-00-00 00:00:00'),('code_set','de','month','Monat',0,'','0000-00-00 00:00:00'),('code_set','de','month3','Monat (3)',0,'','0000-00-00 00:00:00'),('code_set','de','period','Periode',0,'','0000-00-00 00:00:00'),('code_set','de','preset','Verdichtung',0,'','2014-01-31 21:24:02'),('code_set','en','app','Application',100,'','0000-00-00 00:00:00'),('code_set','en','channel','Channel',101,'','0000-00-00 00:00:00'),('code_set','en','code_admin','code admin',-1,'','0000-00-00 00:00:00'),('code_set','en','code_lang','language',-2,'','0000-00-00 00:00:00'),('code_set','en','code_set','code set',-3,'','0000-00-00 00:00:00'),('code_set','en','day','day',0,'','0000-00-00 00:00:00'),('code_set','en','day1','day (1)',0,'','0000-00-00 00:00:00'),('code_set','en','day2','day (2)',0,'','0000-00-00 00:00:00'),('code_set','en','day3','day (3)',0,'','0000-00-00 00:00:00'),('code_set','en','locale','Locales',0,'','0000-00-00 00:00:00'),('code_set','en','model','Model',102,'','0000-00-00 00:00:00'),('code_set','en','month','month',0,'','0000-00-00 00:00:00'),('code_set','en','month3','month (3)',0,'','0000-00-00 00:00:00'),('code_set','en','period','Period',0,'','0000-00-00 00:00:00'),('code_set','en','preset','Aggregation',0,'','2014-10-11 14:10:33'),('day','de','0','Sonntag',0,'','0000-00-00 00:00:00'),('day','de','1','Montag',0,'','0000-00-00 00:00:00'),('day','de','2','Dienstag',0,'','0000-00-00 00:00:00'),('day','de','3','Mittwoch',0,'','0000-00-00 00:00:00'),('day','de','4','Donnerstag',0,'','0000-00-00 00:00:00'),('day','de','5','Freitag',0,'','0000-00-00 00:00:00'),('day','de','6','Samstag',0,'','0000-00-00 00:00:00'),('day','en','0','Sunday',0,'','0000-00-00 00:00:00'),('day','en','1','Monday',1,'','0000-00-00 00:00:00'),('day','en','2','Tuesday',2,'','0000-00-00 00:00:00'),('day','en','3','Wednesday',3,'','0000-00-00 00:00:00'),('day','en','4','Thursday',4,'','0000-00-00 00:00:00'),('day','en','5','Friday',5,'','0000-00-00 00:00:00'),('day','en','6','Saturday',6,'','0000-00-00 00:00:00'),('day1','de','0','S',0,'','0000-00-00 00:00:00'),('day1','de','1','M',0,'','0000-00-00 00:00:00'),('day1','de','2','D',0,'','0000-00-00 00:00:00'),('day1','de','3','M',0,'','0000-00-00 00:00:00'),('day1','de','4','D',0,'','0000-00-00 00:00:00'),('day1','de','5','F',0,'','0000-00-00 00:00:00'),('day1','de','6','S',0,'','0000-00-00 00:00:00'),('day1','en','0','S',0,'','0000-00-00 00:00:00'),('day1','en','1','M',1,'','0000-00-00 00:00:00'),('day1','en','2','T',2,'','0000-00-00 00:00:00'),('day1','en','3','W',3,'','0000-00-00 00:00:00'),('day1','en','4','T',4,'','0000-00-00 00:00:00'),('day1','en','5','F',5,'','0000-00-00 00:00:00'),('day1','en','6','S',6,'','0000-00-00 00:00:00'),('day2','de','0','So',0,'','0000-00-00 00:00:00'),('day2','de','1','Mo',0,'','0000-00-00 00:00:00'),('day2','de','2','Di',0,'','0000-00-00 00:00:00'),('day2','de','3','Mi',0,'','0000-00-00 00:00:00'),('day2','de','4','Do',0,'','0000-00-00 00:00:00'),('day2','de','5','Fr',0,'','0000-00-00 00:00:00'),('day2','de','6','Sa',0,'','0000-00-00 00:00:00'),('day2','en','0','Su',0,'','0000-00-00 00:00:00'),('day2','en','1','Mo',1,'','0000-00-00 00:00:00'),('day2','en','2','Tu',2,'','0000-00-00 00:00:00'),('day2','en','3','We',3,'','0000-00-00 00:00:00'),('day2','en','4','Th',4,'','0000-00-00 00:00:00'),('day2','en','5','Fr',5,'','0000-00-00 00:00:00'),('day2','en','6','Sa',6,'','0000-00-00 00:00:00'),('day3','de','0','Son',0,'','0000-00-00 00:00:00'),('day3','de','1','Mon',0,'','0000-00-00 00:00:00'),('day3','de','2','Die',0,'','0000-00-00 00:00:00'),('day3','de','3','Mit',0,'','0000-00-00 00:00:00'),('day3','de','4','Don',0,'','0000-00-00 00:00:00'),('day3','de','5','Fre',0,'','0000-00-00 00:00:00'),('day3','de','6','Sam',0,'','0000-00-00 00:00:00'),('day3','en','0','Sun',0,'','0000-00-00 00:00:00'),('day3','en','1','Mon',1,'','0000-00-00 00:00:00'),('day3','en','2','Tue',2,'','0000-00-00 00:00:00'),('day3','en','3','Wed',3,'','0000-00-00 00:00:00'),('day3','en','4','Thu',4,'','0000-00-00 00:00:00'),('day3','en','5','Fri',5,'','0000-00-00 00:00:00'),('day3','en','6','Sat',6,'','0000-00-00 00:00:00'),('locale','de','Date','d.m.Y',0,'','0000-00-00 00:00:00'),('locale','de','DateDefault','d.m.Y',0,'','0000-00-00 00:00:00'),('locale','de','DateFull','l, j. F Y',0,'','0000-00-00 00:00:00'),('locale','de','DateLong','j. F Y',0,'','0000-00-00 00:00:00'),('locale','de','DateMedium','j. M Y',0,'','0000-00-00 00:00:00'),('locale','de','DateShort','j.n.y',0,'','0000-00-00 00:00:00'),('locale','de','DateTime','d.m.Y H:i:s',0,'','0000-00-00 00:00:00'),('locale','de','DateTimeDefault','d.m.Y / H:i:s',0,'','0000-00-00 00:00:00'),('locale','de','DateTimeFull','l, j. F Y, H:i \\U\\h\\r T O',0,'','0000-00-00 00:00:00'),('locale','de','DateTimeLong','j. F Y, H:i:s T O',0,'','0000-00-00 00:00:00'),('locale','de','DateTimeMedium','j. M Y / H:i:s',0,'','0000-00-00 00:00:00'),('locale','de','DateTimeShort','j.n.y / G:i',0,'','0000-00-00 00:00:00'),('locale','de','DecimalPoint',',',0,'','0000-00-00 00:00:00'),('locale','de','locales','de_DE@euro,de_DE,de,ge',0,'','0000-00-00 00:00:00'),('locale','de','MonthDefault','m.Y',0,'','0000-00-00 00:00:00'),('locale','de','MonthLong','F Y',0,'','0000-00-00 00:00:00'),('locale','de','MonthShort','m.y',0,'','0000-00-00 00:00:00'),('locale','de','ThousandSeparator','.',0,'','0000-00-00 00:00:00'),('locale','de','Time','H:i:s',0,'','0000-00-00 00:00:00'),('locale','de','TimeDefault','H:i:s',0,'','0000-00-00 00:00:00'),('locale','de','TimeFull','H:i \\U\\h\\r T O',0,'','0000-00-00 00:00:00'),('locale','de','TimeLong','H:i:s T O',0,'','0000-00-00 00:00:00'),('locale','de','TimeMedium','H:i:s',0,'','0000-00-00 00:00:00'),('locale','de','TimeShort','H:i',0,'','0000-00-00 00:00:00'),('locale','de','YearDefault','Y',0,'','0000-00-00 00:00:00'),('locale','de','YearShort','y',0,'','0000-00-00 00:00:00'),('locale','en','Date','d/M/Y',0,'','2014-05-29 16:25:26'),('locale','en','DateDefault','d/M/Y',0,'','2014-05-29 16:25:26'),('locale','en','DateFull','l, d F Y',0,'','0000-00-00 00:00:00'),('locale','en','DateLong','d F Y',0,'','0000-00-00 00:00:00'),('locale','en','DateMedium','d-M-Y',0,'','0000-00-00 00:00:00'),('locale','en','DateShort','d/m/y',0,'','0000-00-00 00:00:00'),('locale','en','DateTime','d-M-Y H:i:s',0,'','0000-00-00 00:00:00'),('locale','en','DateTimeDefault','d-M-Y H:i:s',0,'','0000-00-00 00:00:00'),('locale','en','DateTimeFull','l, d F Y, H:i \\o\\\'\\c\\l\\o\\c\\k T O',0,'','0000-00-00 00:00:00'),('locale','en','DateTimeLong','d F Y, H:i:s T O',0,'','0000-00-00 00:00:00'),('locale','en','DateTimeMedium','d-M-Y H:i:s',0,'','0000-00-00 00:00:00'),('locale','en','DateTimeShort','d/m/y G:i',0,'','0000-00-00 00:00:00'),('locale','en','DecimalPoint','.',0,'','0000-00-00 00:00:00'),('locale','en','locales','en_EN,en',0,'','0000-00-00 00:00:00'),('locale','en','MonthDefault','m.Y',0,'','0000-00-00 00:00:00'),('locale','en','MonthLong','F Y',0,'','0000-00-00 00:00:00'),('locale','en','MonthShort','m.y',0,'','0000-00-00 00:00:00'),('locale','en','ThousandSeparator',',',0,'','0000-00-00 00:00:00'),('locale','en','Time','H:i:s',0,'','0000-00-00 00:00:00'),('locale','en','TimeDefault','H:i:s',0,'','0000-00-00 00:00:00'),('locale','en','TimeFull','H:i \\o\\\'\\c\\l\\o\\c\\k T O',0,'','0000-00-00 00:00:00'),('locale','en','TimeLong','H:i:s T O',0,'','0000-00-00 00:00:00'),('locale','en','TimeMedium','H:i:s',0,'','0000-00-00 00:00:00'),('locale','en','TimeShort','H:i',0,'','0000-00-00 00:00:00'),('locale','en','YearDefault','Y',0,'','0000-00-00 00:00:00'),('locale','en','YearShort','y',0,'','0000-00-00 00:00:00'),('model','de','Accumulator','Summiert die Messwerte aller Sub-Kanäle für den gleichen Zeitpunkt und ignoriert alle Datensätze, wo mindestens ein Wert pro Zeitpunkt fehlt.',0,'','2014-04-19 13:57:54'),('model','de','AccumulatorFull','Summiert die Messwerte aller Sub-Kanäle für den gleichen Zeitpunkt, summiert die Werte auch, wenn ein Wert für einen Zeitpunkt fehlt.',0,'','2014-04-19 13:57:54'),('model','de','Accumulator_extra','Strikter Modus',0,'','2014-06-10 19:38:46'),('model','de','Accumulator_extraHint','Erst wenn alle Sub-Kanäle Werte haben, wird zusammengefasst',0,'','2014-06-10 19:38:46'),('model','de','AliasHelp','Ein Alias verhält sich genau so wie seine originale Gruppe',0,'','2014-04-28 19:23:26'),('model','de','Alias_channel','GUID',0,'','0000-00-00 00:00:00'),('model','de','Alias_channelHint','GUID des Orignalkanals aus der Übersicht',0,'','0000-00-00 00:00:00'),('model','de','Average','Berechnet den Durchschnitt der Messwerte aller Sub-Kanäle für den gleichen Zeitpunkt',0,'','2013-12-30 11:18:21'),('model','de','Averageline','Berechnet den Mittelwert der Werte des Kind-Kanals',0,'','2016-08-06 12:35:38'),('model','de','Averageline_extra','Berechnungsmodus',0,'','2014-07-02 10:44:07'),('model','de','Averageline_extraHint','Das Harmonische Mittel glättet Spitzen, z.B. für Stromverbrauchs-Kanäle',0,'','2014-07-02 10:44:07'),('model','de','Baseline','Erzeugt eine Basislinie für Sensoren für den kleinsten Wert im Zeitbereich',0,'','2013-12-30 09:18:01'),('model','de','Building','Repräsentiert eine Gruppe diverser anderer Dinge',0,'','2013-12-30 11:18:40'),('model','de','Calculator','Nutzt den Faktor um die Daten eines Sub-Kanales zu transformieren',0,'','2013-12-30 11:19:11'),('model','de','CurrentSensor','Speichert aktuelle Stromwerte',0,'','2013-12-30 11:19:20'),('model','de','Dashboard','Proxy-Kanal für konkrete Kanäle zur Anzeige im Dashboard',0,'','2013-12-30 11:19:42'),('model','de','Dashboard_extra','Farbbänder',0,'','2014-05-17 16:32:12'),('model','de','Dashboard_extraHint','Definiere hier die Farbbänder für die Achse. ([url=http://pvlng.com/Dashboard_module#Channel_definition]Anleitung[/url])',0,'','2014-05-17 16:32:12'),('model','de','Dashboard_thresholdHint','Wenn angegeben, werden Messwerte (Zahlen) unterhalb in rot und oberhalb in grün ausgegeben.',0,'','2014-07-04 10:27:54'),('model','de','Dashboard_valid_from','Achsen-Start',0,'','2013-12-29 14:17:27'),('model','de','Dashboard_valid_fromHint','Niedrigster Wert für die Achse',0,'','2013-12-30 13:21:55'),('model','de','Dashboard_valid_to','Achsen-Ende',0,'','2013-12-29 14:17:49'),('model','de','Dashboard_valid_toHint','Höchster Wert für die Achse',0,'','2013-12-30 13:21:12'),('model','de','DatabaseUsage_extra','Messwerte-Typ',0,'','2014-06-04 14:44:17'),('model','de','DatabaseUsage_extraHint','Funktioniert für die numerischen und die alphanumerischen Messwerte',0,'','2014-06-04 14:45:42'),('model','de','Daylight','Zeigt entweder Marker für Sonnauf- und untergang oder eine Kurve zwischen Sonnauf- und untergang (erfordert einen Einstahlungssensor-Kanal)',0,'','2014-06-04 14:26:35'),('model','de','Daylight_extra','Einstrahlungssensor',0,'','2014-05-25 18:13:32'),('model','de','Daylight_extraHint','Wenn eine Kurve gezeichnet werden soll, muss hier ein Einstrahlungssensors angegeben werden.\r\nDie Kurve wird dann anhand des Durchschnittes der Eintrahlungs-Maximalwerte der letzen 5 Tage errechnet.',0,'','2014-05-25 18:12:28'),('model','de','Daylight_IrradiationIsRequired','Für die Darstellung als Kurve ist ein Einstrahlungssensor-Kanal erforderlich',0,'','2014-05-25 18:12:05'),('model','de','Daylight_resolution','Anzeige',0,'','2014-02-02 17:02:36'),('model','de','Daylight_resolutionHint','Anzeige als Sonnenaufgangs/-untergangs-Marker oder als Kurve über die Zeit.\r\nUm die Zeiten anzuzeigen, aktiviere \"Markiere Messwerte: alle\" in den Kanaleinstellungen des Diagramms.',0,'','2016-04-02 20:44:30'),('model','de','Daylight_seeAbove','siehe oben',0,'','2014-09-20 20:44:19'),('model','de','Daylight_times','Zeige Uhrzeit',0,'','2014-06-03 10:30:34'),('model','de','Daylight_timesHint','Zeige auch die Uhrzeit für Sonnenaufgang und -untergang\r\n(Nur für Anzeige als [b]Marker[/b])',0,'','2014-06-03 10:31:28'),('model','de','Differentiator','Subtrahiert den 2. und weitere Sub-Kanäle vom 1. Sub-Kanal, aber nur wenn für einen Zeitpunkt alle Sub-Kanäle Messwerte enthalten',0,'','2013-12-30 11:20:52'),('model','de','DifferentiatorFull','Subtrahiert den 2. und weitere Sub-Kanäle vom 1. Sub-Kanal, auch wenn für einen Zeitpunkt nicht alle Sub-Kanäle Messwerte enthalten (kann zu negativen Werten führen)',0,'','2013-12-30 11:21:56'),('model','de','EnergyMeter','Speichert Produktion oder Verbrauch über die Zeit',0,'','2013-12-30 11:29:24'),('model','de','Estimate','Zeigt den täglichen Erwartungswert der solaren Tagesproduktion basierend auf montlichen oder täglichen Werten',0,'','2013-12-30 09:40:01'),('model','de','EstimateHelp','Sollte als \"Scatter\" (Zielmarke) im Diagramm angezeigt werden',0,'','2014-02-01 22:22:17'),('model','de','Estimate_extra','Erwartungswerte',0,'','2014-05-27 11:23:35'),('model','de','Estimate_extraHint','Definiere die Erwartungswerte in [b]kWh[/b] auf Monats- oder Tagesbasis.\r\n\r\nWenn nur Monatswerte zur Verfügung stehen (z.B. von [url=http://re.jrc.ec.europa.eu/pvgis/apps4/pvest.php]PVGIS[/url]) werden diese als Werte des 15. des Monats verwendet und die anderen Tageswerte linear interpoliert.\r\n[list][*]Monat: [font=courier]Monat:Wert[/font]\r\n[*]Tag: [font=courier]Monat-Tag:Wert[/font][/list]\r\nBeispiel für einen Januar, 4,5kWh pro Tag\r\n[list][*]Monat: [font=courier]1:4.5[/font]\r\n[*]Tag (1. Januar): [font=courier]01-01:4.5[/font][/list]',0,'','2014-05-27 11:23:35'),('model','de','Fix','Zeigt eine horizontale Linie basierend auf dem Faktor',0,'','2013-12-30 10:07:13'),('model','de','Fix_resolution','Festwert',0,'','2013-12-29 21:05:57'),('model','de','Fix_resolutionHint','Erzeugt 2 Datenpunkte, einer am Anfang und einer am Ende des gewählten Zeitbereiches.',0,'','2013-12-29 21:07:39'),('model','de','FrequencySensor','Speichert aktuelle Frequenzwerte',0,'','2013-12-30 11:30:05'),('model','de','FroniusSolarNet','Akzeptiert JSON-Daten für einen [url=http://www.fronius.com/cps/rde/xchg/SID-E3D1267B-7210CC3C/fronius_international/hs.xsl/83_318_ENG_HTML.htm]Fronius Wechselrichter[/url] von einer Abfrage von\r\n[tt]GetInverterRealtimeData.cgi[/tt] mit [tt]Scope = Device[/tt] und [tt]DataCollection = CommonInverterData[/tt] oder\r\n[tt]GetSensorRealtimeData.cgi[/tt] mit [tt]Scope = Device[/tt] und [tt]DataCollection = NowSensorData[/tt]',0,'','2014-01-15 09:36:47'),('model','de','FroniusSolarNet_channel','Typ',0,'','2014-01-15 09:39:10'),('model','de','FroniusSolarNet_channelHint','Equipment-Typ, definiert die unterstützten Kanal-Arten',0,'','2014-01-15 11:09:31'),('model','de','FroniusSolarNet_serial','Device Id',0,'','2014-01-15 09:39:10'),('model','de','FroniusSolarNet_serialHint','Wechselrichter- oder SensorCard-Id im Fronius Solar Net',0,'','2014-01-15 09:38:50'),('model','de','GasMeter','Speichert Verbrauch oder Produktion über die Zeit',0,'','2013-12-30 11:30:21'),('model','de','GasSensor','Speichert aktuellen Verbrauch oder Produktion',0,'','2013-12-30 11:30:34'),('model','de','Group','Eine generische Gruppe',0,'','2013-12-30 10:18:25'),('model','de','HeatSensor','Speichert aktuellen Verbrauch oder Produktion',0,'','2013-12-30 11:30:48'),('model','de','History','Zeigt historische Daten, die letzten x Tage oder die gleichen Tage der letzten Jahre',0,'','2013-12-30 10:22:45'),('model','de','History_valid_from','Tage zurück',0,'','0000-00-00 00:00:00'),('model','de','History_valid_fromHint','Um diese Tage werden die Daten rückwärts gelesen.',0,'','2013-12-29 18:13:09'),('model','de','History_valid_to','Tage vorwärts',0,'','0000-00-00 00:00:00'),('model','de','History_valid_toHint','Um diese Tage werden die Daten vorwärts gelesen.\r\n(0 = bis heute)\r\nEin Wert größer 0 bedeutet, dass die letzten 10 Jahre * (rückwärts + vorwärts Tage) gelesen werden!',0,'','2013-12-29 21:18:34'),('model','de','Humidity','Speichert die aktuelle Luftfeuchtigkeit',0,'','2013-12-30 11:32:06'),('model','de','ImportExport','Errechnet Import oder Export von Verbrauch oder Produktion',0,'','2013-12-30 10:28:06'),('model','de','Inverter','Ein (Solar-) Wechselrichter gruppiert meist Energie-, Spannungs- und Stromkanäle',0,'','2013-12-30 11:32:43'),('model','de','Irradiation','Speichert aktuelle Einstrahlungswerte',0,'','2013-12-30 11:32:53'),('model','de','KacoInverter','Akzeptiert JSON-Daten für einen Kaco Wechselrichter',0,'','2016-03-31 15:12:40'),('model','de','KostalPiko','Verarbeitet die Daten eines Kostal Piko Wechselrichters',0,'','2016-08-06 12:38:45'),('model','de','Luminosity','Speichert die aktuelle Helligkeit/Lichtstärke',0,'','2013-12-30 11:33:06'),('model','de','Meter','Generischer Meter-Kanal zur beliebigen Verwendung',0,'','2014-10-12 10:35:02'),('model','de','MeterToSensor','Berechnet Sensor-Daten aus einem Meter-Kanal in Abhängigkeit der Zeitdifferenz zwischen den Messwerten',0,'','2014-06-04 14:28:43'),('model','de','Multiplier','Multipliziert die Werte aller Kind-Kanäle',0,'','2016-08-06 12:33:16'),('model','de','MultiSensor','Ein Sensor mit mehreren Kanälen',0,'','2013-12-30 10:32:31'),('model','de','OpenWeatherMap','Multi sensor für die [url=http://openweathermap.org/]OpenWeatherMap API[/url]',0,'','2014-06-04 14:31:15'),('model','de','Percentage','Berechnet das Prozent-Verhältnis seiner Kind-Kanäle',0,'','2016-08-06 12:42:51'),('model','de','PowerCounter','Speichert aktuellen Verbrauch oder Produktion basierend auf Impulsen pro Faktor',0,'','2013-12-30 11:33:39'),('model','de','PowerPlant','Ein (Solar-) Power plant gruppiert z.B. Wechselrichter und Sensoren',0,'','2013-12-30 10:34:13'),('model','de','PowerSensor','Speichert aktuellen Verbrauch oder Produktion',0,'','2013-12-30 11:33:55'),('model','de','Pressure','Speichert aktuelle Druckwerte',0,'','2013-12-30 11:34:08'),('model','de','PVLogInverter','Liest Wechselrichter-Werte für PV-Log JSON-Import',0,'','2013-12-30 10:38:58'),('model','de','PVLogInverter11','Liest Wechselrichter-Werte für PV-Log JSON-Import (v1.1)',0,'','2016-08-06 12:45:04'),('model','de','PVLogPlant','Liest Anlagen-Werte für PV-Log JSON-Import',0,'','2013-12-30 10:40:31'),('model','de','PVLogPlant11','Liest Anlagen-Werte für PV-Log JSON-Import (v1.1)',0,'','2016-08-06 12:45:04'),('model','de','RadiationMeter','Speichert Strahlungswerte über die Zeit',0,'','2013-12-30 11:37:03'),('model','de','RadiationSensor','Speichert aktuelle Strahlungswerte',0,'','2013-12-30 11:37:44'),('model','de','RainfallMeter','Speichert die Regenmenge über die Zeit',0,'','2013-12-30 18:00:41'),('model','de','RainfallSensor','Speichert die aktuelle Regenmenge',0,'','2013-12-30 18:01:23'),('model','de','Random','Zeigt zufällige Messwerte im Bereich \"Unterer Grenzwert\" ... \"Oberer Grenzwert\" mit Änderung ±\"Schwellwert\" je Zeitpunkt',0,'','2013-12-30 11:57:41'),('model','de','Ratio','Berechnet das Verhältnis von Sub-Kanälen',0,'','2013-12-30 12:47:33'),('model','de','Selector','Gibt Werte in Abhängigkeit des ersten Kind-Kanals aus',0,'','2014-04-28 18:56:05'),('model','de','SelectorHelp','Der erste Kind-Kanal ist der selektierende Kanal, Werte unterhalb des Grenzwertes setzen den Output auf 0, Werte darüber geben den Wert des zweiten Kind-Kanals aus. Der zweite Kind-Kanal ist der Datenkanal, seine Werte Werte werden in Abhängigkeit des ersten Kind-Kanals ausgegeben oder nicht.',0,'','2014-04-28 18:59:29'),('model','de','Selector_thresholdHint','Nur Werte oberhalb des Schwellwertes bewirken die Ausgabe der Werte des zweiten Kind-Kanals',0,'','2014-04-28 19:00:34'),('model','de','Sensor','Generischer Sensor-Kanal zur beliebigen Verwendung',0,'','2014-10-12 10:35:54'),('model','de','SensorToMeter','Transformiert Sensor Messwerte in einen Meter-Kanal',0,'','2013-12-30 12:52:08'),('model','de','SMAInverter','Akzeptiert JSON-Daten für einen Wechselrichter von einer [url=http://www.sma.de/produkte/monitoring-systems/sunny-webbox.html]SMA Webbox[/url]',0,'','2013-12-30 12:52:56'),('model','de','SMASensorbox','Akzeptiert JSON-Daten für eine Sensorbox von einer [url=http://www.sma.de/produkte/monitoring-systems/sunny-webbox.html]SMA Webbox[/url]',0,'','2013-12-30 12:55:51'),('model','de','SMAWebbox','Akzeptiert JSON-Daten von einer [url=http://www.sma.de/produkte/monitoring-systems/sunny-webbox.html]SMA Webbox[/url]',0,'','2013-12-30 12:56:19'),('model','de','SMAWebbox_channel','Typ',0,'','0000-00-00 00:00:00'),('model','de','SMAWebbox_channelHint','Equipment-Typ, definiert die unterstützten Kanal-Arten',0,'','2014-01-15 11:09:31'),('model','de','SMAWebbox_resolution','Installierte Leistung',0,'','0000-00-00 00:00:00'),('model','de','SMAWebbox_resolutionHint','in kWp',0,'','2014-01-15 11:09:31'),('model','de','SolarEdgeInverter','Verarbeitet die Daten eines Solar Edge Wechselrichters',0,'','2016-08-06 12:39:30'),('model','de','SolarEdgeOptimizer','Verarbeitet die Daten eines Solar Edge Optimizers',0,'','2016-08-06 12:39:30'),('model','de','SolarEstimate','Berechnet den erwarteten Ertrag mit Hilfe der Daten der letzten Tage',0,'','2014-10-13 08:15:07'),('model','de','SolarEstimate_extra','Tage',0,'','2014-07-03 09:29:59'),('model','de','SolarEstimate_extraHint','Anzahl der Tage in der Vergangenheit, die zur Berechnung herangezogen werden sollen.',0,'','2014-07-03 09:33:00'),('model','de','SonnenertragJSON','Liefert Anlagen-/Wechselrichterdaten für den Sonnenertrag JSON import',0,'','2013-12-30 12:57:24'),('model','de','Switch','Speichert nur Status-Änderungen',0,'','2013-12-30 12:58:04'),('model','de','Temperature','Speichert aktuelle Temperaturen',0,'','2013-12-30 13:07:38'),('model','de','Timer','Speichert zeitbasierte Messwerte über die Zeit, z.B. Laufzeiten',0,'','2013-12-30 13:00:05'),('model','de','Topline','Erzeugt eine Oberlinie für Sensoren für den größten Wert im Zeitbereich',0,'','2014-01-12 12:38:41'),('model','de','Valve','Speichert aktuelle Ventilstellungen',0,'','2013-12-30 13:00:51'),('model','de','Voltage','Speichert aktuelle Spannungswerte',0,'','2013-12-30 13:01:35'),('model','de','WaterMeter','Speichert Wasserverbrauch oder -erzeugung über die Zeit',0,'','2013-12-30 13:02:27'),('model','de','WaterSensor','Speichert aktuellen Wasserverbrauch oder -erzeugung',0,'','2013-12-30 13:03:18'),('model','de','WindDirection','Speichert aktuelle Windrichtung',0,'','2013-12-30 13:09:55'),('model','de','Windspeed','Speichert die aktuelle Windgeschwindigkeit',0,'','2013-12-30 13:04:09'),('model','de','Wunderground','Multi sensor für die [url=http://www.wunderground.com]Weather Underground API[/url]',0,'','2014-06-04 14:32:34'),('model','en','Accumulator','Build the sum of readings of all child channels for same timestamp and ignores data sets, where at least one for a timestamp ist missing.',0,'','2014-04-19 13:57:54'),('model','en','AccumulatorFull','Build the sum of readings of all child channels for same timestamp, works for all timestamps, also if one data set is missing.',0,'','2014-04-19 13:57:54'),('model','en','Accumulator_extra','Strict mode',0,'','2014-06-10 19:38:46'),('model','en','Accumulator_extraHint','Only if all child channels have value the consolidation starts',0,'','2014-06-10 19:38:47'),('model','en','AliasHelp','An alias act in the same way as its original channel group',0,'','2014-04-28 19:23:26'),('model','en','Alias_channel','GUID',0,'','0000-00-00 00:00:00'),('model','en','Alias_channelHint','GUID of original channel from overview',0,'','0000-00-00 00:00:00'),('model','en','Average','Calculates the average of readings of all child channels for same timestamp',0,'','2013-12-30 11:18:20'),('model','en','Averageline','Calculates the average of the values of its child channel',0,'','2016-08-06 12:35:38'),('model','en','Averageline_extra','Calculation mode',0,'','2014-07-02 10:44:08'),('model','en','Averageline_extraHint','The harmonic mean smooth peaks, e.g. for power consumption channels',0,'','2014-07-02 10:44:07'),('model','en','Baseline','Generates a baseline for sensors for the lowest value in time range',0,'','2013-12-30 09:18:00'),('model','en','Building','Acts as a group for several other things',0,'','2013-12-30 11:18:40'),('model','en','Calculator','Uses the factor to transform readings of a child channel',0,'','2013-12-30 11:19:10'),('model','en','CurrentSensor','Tracks actual current values',0,'','2013-12-30 11:19:20'),('model','en','Dashboard','Acts as proxy channel for concrete channels for dashboard display',0,'','2013-12-30 11:19:42'),('model','en','Dashboard_extra','Color bands',0,'','2014-05-17 16:32:12'),('model','en','Dashboard_extraHint','Define here the color bands for the axis. ([url=http://pvlng.com/Dashboard_module#Channel_definition]Instructions[/url])',0,'','2014-05-17 16:32:12'),('model','en','Dashboard_thresholdHint','If defined, reading values (numbers) below this will be colored red, above green.',0,'','2014-07-04 10:27:54'),('model','en','Dashboard_valid_from','Axis start',0,'','2013-12-29 14:17:27'),('model','en','Dashboard_valid_fromHint','Lowest value for axis',0,'','2013-12-30 13:21:55'),('model','en','Dashboard_valid_to','Axis end',0,'','2013-12-29 14:17:49'),('model','en','Dashboard_valid_toHint','Highest value for axis',0,'','2013-12-30 13:21:12'),('model','en','DatabaseUsage_extra','Readings type',0,'','2014-06-04 14:44:17'),('model','en','DatabaseUsage_extraHint','Works for the numeric and the non-numeric readings',0,'','2014-06-04 14:45:42'),('model','en','Daylight','Show either markers for sunrise / sunset or a curve between sunrise and sunset (requires a irradiation sensor channel)',0,'','2014-06-04 14:26:35'),('model','en','Daylight_extra','Irradiation sensor',0,'','2014-05-25 18:13:32'),('model','en','Daylight_extraHint','If a curve should displayed, an irradiation sensor must here be provided.\r\nThe curve will then calulated by the average of the max. irradiation values of the last 5 days.',0,'','2014-05-25 18:04:26'),('model','en','Daylight_IrradiationIsRequired','To display a curve, a irradiation sensor channel is required',0,'','2014-05-25 18:12:05'),('model','en','Daylight_resolution','Display',0,'','2014-02-02 17:02:46'),('model','en','Daylight_resolutionHint','Show as sunrise/sunset markers or as curve over time.\r\nTo show the times also, check the \"Mark reading values: all\" in chart channel settings.',0,'','2016-04-02 20:44:41'),('model','en','Daylight_seeAbove','see above',0,'','2014-09-20 20:44:42'),('model','en','Daylight_times','Show time',0,'','2014-06-03 10:30:34'),('model','en','Daylight_timesHint','Show also the sunrise/sunset time\r\n(Only for display as [b]Marker[/b])',0,'','2014-06-03 10:29:17'),('model','en','Differentiator','Subtract 2nd and following sub channels from 1st sub channel, but only if all reading values for a timestamp exist',0,'','2013-12-30 11:20:52'),('model','en','DifferentiatorFull','Subtract 2nd and following sub channels from 1st sub channel, also if not all readings values for a timestamp exist (can result in negative values)',0,'','2013-12-30 11:21:56'),('model','en','EnergyMeter','Tracks production or consumption over time',0,'','2013-12-30 11:29:24'),('model','en','Estimate','Show the the daily estimate of solar production based on monthly or daily values',0,'','2013-12-30 09:40:00'),('model','en','EstimateHelp','Should be shown as \"Scatter\" (target marker) in chart',0,'','2014-02-01 22:22:17'),('model','en','Estimate_extra','Estimates',0,'','2014-05-27 11:23:35'),('model','en','Estimate_extraHint','Define your estimates in [b]kilo watt hours[/b] on monthly or daily base.\r\n\r\nIf only monthly data exists (e.g from [url=http://re.jrc.ec.europa.eu/pvgis/apps4/pvest.php]PVGIS[/url]) the values will be used as of the 15th of the month and the other values will be linear interpolated to get daily values.\r\n[list][*]Monthly: [font=courier]month:value[/font]\r\n[*]Daily: [font=courier]month-day:value[/font][/list]\r\nExample for a january, 4.5kWh each day\r\n[list][*]Monthly: [font=courier]1:4.5[/font]\r\n[*]Daily (1st of january): [font=courier]01-01:4.5[/font][/list]',0,'','2014-05-27 11:23:35'),('model','en','Fix','Shows a horizontal line based on the factor',0,'','2013-12-30 10:07:13'),('model','en','Fix_resolution','Fix value',0,'','2013-12-29 21:05:57'),('model','en','Fix_resolutionHint','Creates 2 data points, at start and at of selected time range.',0,'','2014-01-20 13:47:58'),('model','en','FrequencySensor','Tracks actual frequencies',0,'','2013-12-30 11:30:05'),('model','en','FroniusSolarNet','Accept JSON data for a [url=http://www.fronius.com/cps/rde/xchg/SID-E3D1267B-7210CC3C/fronius_international/hs.xsl/83_318_ENG_HTML.htm]Fronius inverter[/url], either from a request of [tt]GetInverterRealtimeData.cgi[/tt] with [tt]Scope = Device[/tt] and [tt]DataCollection = CommonInverterData[/tt] or\r\n[tt]GetSensorRealtimeData.cgi[/tt] with [tt]Scope = Device[/tt] and [tt]DataCollection = NowSensorData[/tt]',0,'','2016-08-06 12:37:30'),('model','en','FroniusSolarNet_channel','Type',0,'','2014-01-15 09:39:10'),('model','en','FroniusSolarNet_channelHint','Equipment type, defines the supported channels',0,'','2014-01-15 11:08:18'),('model','en','FroniusSolarNet_serial','Device Id',0,'','2014-01-15 09:39:10'),('model','en','FroniusSolarNet_serialHint','Inverter or SensorCard Id in Fronius Solar Net',0,'','2014-01-15 09:38:57'),('model','en','GasMeter','Tracks consumption or production over time',0,'','2013-12-30 11:30:21'),('model','en','GasSensor','Tracks actual consumption or production',0,'','2013-12-30 11:30:34'),('model','en','Group','A generic group',0,'','2013-12-30 10:18:25'),('model','en','HeatSensor','Tracks actual consumption or production',0,'','2013-12-30 11:30:47'),('model','en','History','Shows historic data, last x days or same days last years',0,'','2013-12-30 11:31:18'),('model','en','History_valid_from','Days backwards',0,'','0000-00-00 00:00:00'),('model','en','History_valid_fromHint','These are number of days to fetch backwards.',0,'','2013-12-29 18:13:09'),('model','en','History_valid_to','Days foreward',0,'','0000-00-00 00:00:00'),('model','en','History_valid_toHint','These are number of days to fetch foreward.\r\n(0 = until today)\r\nA value greater 0 means reading last 10 years * (backward + foreward days)!',0,'','2013-12-29 21:18:34'),('model','en','Humidity','Tracks actual humitiy',0,'','2013-12-30 11:32:06'),('model','en','ImportExport','Calculates import or export by consumption and production',0,'','2013-12-30 10:28:05'),('model','en','Inverter','A (solar) Inverter groups mostly energy, voltage and current channels',0,'','2013-12-30 11:32:43'),('model','en','Irradiation','Tracks actual irradiation',0,'','2013-12-30 11:32:53'),('model','en','KacoInverter','Accept JSON data for a Kaco inverter',0,'','2016-03-31 15:12:40'),('model','en','KostalPiko','Accept data from Kostal Piko inverters',0,'','2016-08-06 12:38:45'),('model','en','Luminosity','Tracks actual luminosity',0,'','2013-12-30 11:33:06'),('model','en','Meter','Generic meter channel for general use',0,'','2014-10-12 10:35:35'),('model','en','MeterToSensor','Calculates sensor data from a meter channel depending of the time difference between the readings',0,'','2014-06-04 14:28:43'),('model','en','Multiplier','Multiply the values of all sub channels',0,'','2016-08-06 12:33:16'),('model','en','MultiSensor','A sensor with multiple channels',0,'','2013-12-30 10:32:31'),('model','en','OpenWeatherMap','Multi sensor for [url=http://openweathermap.org/]OpenWeatherMap API[/url]',0,'','2014-06-04 14:31:15'),('model','en','Percentage','Calculates the percentage ration of its child channels',0,'','2016-08-06 12:42:51'),('model','en','PowerCounter','Tracks actual consumption or production based on impulses per factor',0,'','2013-12-30 11:33:39'),('model','en','PowerPlant','A (solar) Power plant groups e.g. inverters and sensors',0,'','2013-12-30 10:34:13'),('model','en','PowerSensor','Tracks actual consumption or production',0,'','2013-12-30 11:33:55'),('model','en','Pressure','Tracks actual pressure values',0,'','2013-12-30 11:34:08'),('model','en','PVLogInverter','Readout inverter data for PV-Log JSON import',0,'','2013-12-30 10:38:58'),('model','en','PVLogInverter11','Readout inverter data for PV-Log JSON import (v1.1)',0,'','2016-08-06 12:45:04'),('model','en','PVLogPlant','Readout plant data for PV-Log JSON import',0,'','2013-12-30 10:40:31'),('model','en','PVLogPlant11','Readout plant data for PV-Log JSON import (v1.1)',0,'','2016-08-06 12:45:04'),('model','en','RadiationMeter','Tracks radiation over time',0,'','2013-12-30 11:36:04'),('model','en','RadiationSensor','Tracks actual radiation',0,'','2013-12-30 11:36:41'),('model','en','RainfallMeter','Tracks rainfall over time',0,'','2013-12-30 18:00:41'),('model','en','RainfallSensor','Tracks actual rainfall',0,'','2013-12-30 18:01:23'),('model','en','Random','Shows data \"Valid from\" ... \"Valid to\" with variance ±\"Threshold\" per timestamp',0,'','2013-12-30 11:57:41'),('model','en','Ratio','Calculates the ratio between child channels',0,'','2013-12-30 12:47:33'),('model','en','Selector','Calculates the output in dependence of first sub channel',0,'','2014-04-28 18:56:05'),('model','en','SelectorHelp','The first sub channel is the selective channel, values below threshold set the output to 0, values above do just pass the value of the second sub channel through. Second sub channel is the data channel, its values are passed through or not based on the first sub channel.',0,'','2014-04-28 18:59:29'),('model','en','Selector_thresholdHint','Only values above the threshold trigger the output of the second sub channel',0,'','2014-04-28 19:00:34'),('model','en','Sensor','Generic sensor channel for general use',0,'','2014-10-12 10:35:54'),('model','en','SensorToMeter','Transform data of a sensor to meter data',0,'','2013-12-30 12:52:08'),('model','en','SMAInverter','Accept JSON data for an inverter from a [url=http://www.sma.de/produkte/monitoring-systems/sunny-webbox.html]SMA Webbox[/url]',0,'','2013-12-30 12:52:56'),('model','en','SMASensorbox','Accept JSON data for an sensor box from a [url=http://www.sma.de/produkte/monitoring-systems/sunny-webbox.html]SMA Webbox[/url]',0,'','2013-12-30 12:55:51'),('model','en','SMAWebbox','Accept JSON data from a [url=http://www.sma.de/produkte/monitoring-systems/sunny-webbox.html]SMA Webbox[/url]',0,'','2013-12-30 12:56:19'),('model','en','SMAWebbox_channel','Type',0,'','2014-01-15 09:39:10'),('model','en','SMAWebbox_channelHint','Equipment type, defines the supported channels',0,'','2014-01-15 11:08:18'),('model','en','SMAWebbox_resolution','Installed power',0,'','2014-01-15 09:39:10'),('model','en','SMAWebbox_resolutionHint','in kilo watt peak',0,'','2014-01-15 11:08:18'),('model','en','SolarEdgeInverter','Accept data of a Solar Edge inverter',0,'','2016-08-06 12:39:30'),('model','en','SolarEdgeOptimizer','Accept data of a Solar Edge Optimizers',0,'','2016-08-06 12:39:30'),('model','en','SolarEstimate','Calculates the energy estimate by data from last days',0,'','2014-10-13 08:15:07'),('model','en','SolarEstimate_extra','Days',0,'','2014-07-03 09:30:21'),('model','en','SolarEstimate_extraHint','Count of days in the past to be used for calulation.',0,'','2014-07-03 09:32:54'),('model','en','SonnenertragJSON','Readout plant/inverter data for Sonnenertrag JSON import',0,'','2013-12-30 12:57:24'),('model','en','Switch','Tracks only state changes',0,'','2013-12-30 12:58:03'),('model','en','Temperature','Tracks actual temperature',0,'','2013-12-30 13:07:38'),('model','en','Timer','Tracks time based reading values over time, e.g working hours',0,'','2013-12-30 13:00:05'),('model','en','Topline','Generates a top line for sensors for the highest value in time range',0,'','2014-01-12 12:38:41'),('model','en','Valve','Tracks actual valve positions',0,'','2013-12-30 13:00:51'),('model','en','Voltage','Tracks actual voltage',0,'','2013-12-30 13:01:35'),('model','en','WaterMeter','Tracks water consumption or production over time',0,'','2013-12-30 13:02:27'),('model','en','WaterSensor','Tracks actual water consumption or production',0,'','2013-12-30 13:03:18'),('model','en','WindDirection','Tracks actual wind direction',0,'','2013-12-30 13:09:55'),('model','en','Windspeed','Tracks actual windspeed',0,'','2013-12-30 13:04:09'),('model','en','Wunderground','Multi sensor for [url=http://www.wunderground.com]Weather Underground API[/url]',0,'','2014-06-04 14:32:34'),('month','de','1','Januar',0,'','0000-00-00 00:00:00'),('month','de','10','Oktober',0,'','0000-00-00 00:00:00'),('month','de','11','November',0,'','0000-00-00 00:00:00'),('month','de','12','Dezember',0,'','0000-00-00 00:00:00'),('month','de','2','Februar',0,'','0000-00-00 00:00:00'),('month','de','3','März',0,'','0000-00-00 00:00:00'),('month','de','4','April',0,'','0000-00-00 00:00:00'),('month','de','5','Mai',0,'','0000-00-00 00:00:00'),('month','de','6','Juni',0,'','0000-00-00 00:00:00'),('month','de','7','Juli',0,'','0000-00-00 00:00:00'),('month','de','8','August',0,'','0000-00-00 00:00:00'),('month','de','9','September',0,'','0000-00-00 00:00:00'),('month','en','1','January',1,'','0000-00-00 00:00:00'),('month','en','10','October',10,'','0000-00-00 00:00:00'),('month','en','11','November',11,'','0000-00-00 00:00:00'),('month','en','12','December',12,'','0000-00-00 00:00:00'),('month','en','2','February',2,'','0000-00-00 00:00:00'),('month','en','3','March',3,'','0000-00-00 00:00:00'),('month','en','4','April',4,'','0000-00-00 00:00:00'),('month','en','5','May',5,'','0000-00-00 00:00:00'),('month','en','6','June',6,'','0000-00-00 00:00:00'),('month','en','7','July',7,'','0000-00-00 00:00:00'),('month','en','8','August',8,'','0000-00-00 00:00:00'),('month','en','9','September',9,'','0000-00-00 00:00:00'),('month3','de','1','Jan',0,'','0000-00-00 00:00:00'),('month3','de','10','Okt',0,'','0000-00-00 00:00:00'),('month3','de','11','Nov',0,'','0000-00-00 00:00:00'),('month3','de','12','Dez',0,'','0000-00-00 00:00:00'),('month3','de','2','Feb',0,'','0000-00-00 00:00:00'),('month3','de','3','Mär',0,'','0000-00-00 00:00:00'),('month3','de','4','Apr',0,'','0000-00-00 00:00:00'),('month3','de','5','Mai',0,'','0000-00-00 00:00:00'),('month3','de','6','Jun',0,'','0000-00-00 00:00:00'),('month3','de','7','Jul',0,'','0000-00-00 00:00:00'),('month3','de','8','Aug',0,'','0000-00-00 00:00:00'),('month3','de','9','Sep',0,'','0000-00-00 00:00:00'),('month3','en','1','Jan',1,'','0000-00-00 00:00:00'),('month3','en','10','Oct',10,'','0000-00-00 00:00:00'),('month3','en','11','Nov',11,'','0000-00-00 00:00:00'),('month3','en','12','Dec',12,'','0000-00-00 00:00:00'),('month3','en','2','Feb',2,'','0000-00-00 00:00:00'),('month3','en','3','Mar',3,'','0000-00-00 00:00:00'),('month3','en','4','Apr',4,'','0000-00-00 00:00:00'),('month3','en','5','May',5,'','0000-00-00 00:00:00'),('month3','en','6','Jun',6,'','0000-00-00 00:00:00'),('month3','en','7','Jul',7,'','0000-00-00 00:00:00'),('month3','en','8','Aug',8,'','0000-00-00 00:00:00'),('month3','en','9','Sep',9,'','0000-00-00 00:00:00'),('period','de','d','Tag',0,'','0000-00-00 00:00:00'),('period','de','h','Stunde',0,'','0000-00-00 00:00:00'),('period','de','i','Minute',0,'','0000-00-00 00:00:00'),('period','de','m','Monat',0,'','0000-00-00 00:00:00'),('period','de','q','Quartal',0,'','0000-00-00 00:00:00'),('period','de','w','Woche',0,'','0000-00-00 00:00:00'),('period','de','y','Jahr',0,'','0000-00-00 00:00:00'),('period','en','d','Day',2,'','0000-00-00 00:00:00'),('period','en','h','Hour',1,'','0000-00-00 00:00:00'),('period','en','i','Minute',0,'','0000-00-00 00:00:00'),('period','en','m','Month',4,'','0000-00-00 00:00:00'),('period','en','q','Quarter',5,'','0000-00-00 00:00:00'),('period','en','w','Week',3,'','0000-00-00 00:00:00'),('period','en','y','Year',6,'','0000-00-00 00:00:00'),('preset','de','--','--- keine ---',0,'','2014-01-31 19:57:39'),('preset','de','10i','10 Minuten',0,'','2014-02-05 12:31:45'),('preset','de','10y','Dekade',0,'','2014-01-12 21:08:51'),('preset','de','12h','12 Stunden',0,'','2014-02-05 12:35:05'),('preset','de','14d','14 Tage',0,'','2014-02-05 12:37:52'),('preset','de','15i','15 Minuten',0,'','2015-05-24 17:05:37'),('preset','de','1d','1 Tag',0,'','2014-02-05 12:37:52'),('preset','de','1h','1 Stunde',0,'','2014-02-05 12:35:05'),('preset','de','1i','1 Minute',0,'','2014-03-03 12:13:46'),('preset','de','1m','1 Monat',0,'','2014-02-05 12:36:00'),('preset','de','1q','1 Quartal',0,'','2014-02-05 12:36:49'),('preset','de','1w','1 Woche',0,'','2014-02-05 12:35:33'),('preset','de','1y','1 Jahr',0,'','2014-02-05 12:36:18'),('preset','de','20i','20 Minuten',0,'','2014-02-05 12:32:58'),('preset','de','2h','2 Stunden',0,'','2014-02-05 12:35:05'),('preset','de','2i','2 Minuten',0,'','2014-02-05 12:32:58'),('preset','de','2m','2 Monate',0,'','2014-02-05 12:36:00'),('preset','de','2q','2 Quartale',0,'','2014-02-05 12:36:49'),('preset','de','2w','2 Wochen',0,'','2014-02-05 12:35:33'),('preset','de','30i','30 Minuten',0,'','2014-02-05 12:32:58'),('preset','de','4h','4 Stunden',0,'','2014-02-05 12:35:06'),('preset','de','4m','4 Monate',0,'','2014-02-05 12:39:20'),('preset','de','5i','5 Minuten',0,'','2014-02-05 12:32:58'),('preset','de','6h','6 Stunden',0,'','2014-02-05 12:35:06'),('preset','de','7d','7 Tage',0,'','2014-02-05 12:37:52'),('preset','de','8h','8 Stunden',0,'','2014-02-05 12:35:06'),('preset','de','d','::Tage::',0,'','2014-02-05 12:26:34'),('preset','de','h','::Stunden::',0,'','2014-02-05 12:26:34'),('preset','de','i','::Minuten::',0,'','2014-02-05 12:26:34'),('preset','de','m','::Monate::',0,'','2014-02-05 12:16:27'),('preset','de','q','::Quartale::',0,'','2014-02-05 12:26:34'),('preset','de','w','::Wochen::',0,'','2014-02-05 12:26:34'),('preset','de','y','::Jahre::',0,'','2014-02-05 12:16:59'),('preset','en','--','--- none ---',50,'','2014-10-11 14:09:33'),('preset','en','10i','10 Minutes',110,'','2014-02-05 12:32:58'),('preset','en','10y','Decade',710,'','2014-02-05 11:59:14'),('preset','en','12h','12 Hours',212,'','2014-02-05 12:35:06'),('preset','en','14d','14 Days',314,'','2014-02-05 12:37:52'),('preset','en','15i','15 Minutes',115,'','2015-05-24 17:05:36'),('preset','en','1d','1 Day',301,'','2014-02-05 12:37:52'),('preset','en','1h','1 Hour',201,'','2014-02-05 12:35:06'),('preset','en','1i','1 Minute',101,'','2014-03-03 12:13:46'),('preset','en','1m','1 Month',501,'','2014-02-05 12:36:00'),('preset','en','1q','1 Quarter',601,'','2014-02-05 12:36:49'),('preset','en','1w','1 Week',401,'','2014-02-05 12:35:33'),('preset','en','1y','1 Year',701,'','2014-02-05 12:36:18'),('preset','en','20i','20 Minutes',120,'','2014-02-05 12:32:58'),('preset','en','2h','2 Hours',202,'','2014-02-05 12:35:06'),('preset','en','2i','2 Minutes',102,'','2014-02-05 12:32:58'),('preset','en','2m','2 Months',502,'','2014-02-05 12:36:00'),('preset','en','2q','2 Quarters',602,'','2014-02-05 12:36:49'),('preset','en','2w','2 Weeks',402,'','2014-02-05 12:35:33'),('preset','en','30i','30 Minutes',130,'','2014-02-05 12:32:58'),('preset','en','4h','4 Hours',204,'','2014-02-05 12:35:06'),('preset','en','4m','4 Month',504,'','2014-02-05 12:39:20'),('preset','en','5i','5 Minutes',105,'','2014-02-05 12:32:58'),('preset','en','6h','6 Hours',206,'','2014-02-05 12:35:06'),('preset','en','7d','7 Days',307,'','2014-02-05 12:37:53'),('preset','en','8h','8 Hours',208,'','2014-02-05 12:35:06'),('preset','en','d','::Days::',300,'','2014-02-05 12:26:34'),('preset','en','h','::Hours::',200,'','2014-02-05 12:26:34'),('preset','en','i','::Minutes::',100,'','2014-02-09 17:14:57'),('preset','en','m','::Months::',500,'','2014-02-05 12:26:35'),('preset','en','q','::Quarters::',600,'','2014-02-05 12:26:35'),('preset','en','w','::Weeks::',400,'','2014-02-05 12:26:34'),('preset','en','y','::Years::',700,'','2014-02-05 12:26:35');
/*!40000 ALTER TABLE `pvlng_babelkit` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pvlng_type`
--

DROP TABLE IF EXISTS `pvlng_type`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pvlng_type` (
  `id` smallint(5) unsigned NOT NULL DEFAULT '0',
  `name` varchar(60) NOT NULL DEFAULT '',
  `description` varchar(255) NOT NULL DEFAULT '',
  `model` varchar(30) NOT NULL DEFAULT 'Group',
  `unit` varchar(10) NOT NULL DEFAULT '',
  `type` enum('group','general','numeric','sensor','meter') NOT NULL DEFAULT 'group',
  `childs` tinyint(1) NOT NULL DEFAULT '0',
  `read` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `write` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `graph` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `icon` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`),
  KEY `childs` (`childs`),
  KEY `read` (`read`),
  KEY `write` (`write`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 PACK_KEYS=1 COMMENT='Channel types';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pvlng_type`
--

LOCK TABLES `pvlng_type` WRITE;
/*!40000 ALTER TABLE `pvlng_type` DISABLE KEYS */;
INSERT INTO `pvlng_type` VALUES (0,'Alias','model::Alias','Channel','','general',0,0,0,1,''),(1,'Power plant','model::PowerPlant','Channel','','group',-1,0,0,0,'/images/ico/building.png'),(2,'Inverter','model::Inverter','Channel','','group',-1,0,0,0,'/images/ico/exclamation_frame.png'),(3,'Building','model::Building','Channel','','group',-1,0,0,0,'/images/ico/home.png'),(4,'Multi-Sensor','model::MultiSensor','Channel','','group',-1,0,0,0,'/images/ico/wooden_box.png'),(5,'Group','model::Group','Channel','','group',-1,0,0,0,'/images/ico/folders_stack.png'),(10,'Random','model::Random','Random','','numeric',0,1,0,1,'/images/ico/ghost.png'),(11,'Fixed value','model::Fix','Fix','','sensor',0,1,0,1,'/images/ico/chart_arrow.png'),(12,'Estimate','model::Estimate','Estimate','Wh','sensor',0,1,0,1,'/images/ico/plug.png'),(13,'Daylight','model::Daylight','Daylight','','sensor',0,1,0,1,'/images/ico/picture-sunset.png'),(15,'Ratio calculator','model::Ratio','Ratio','','sensor',2,1,0,1,'/images/ico/calculator_scientific.png'),(16,'Accumulator','model::Accumulator','Accumulator','','numeric',-1,1,0,1,'/images/ico/calculator_scientific.png'),(17,'Differentiator','model::Differentiator','Differentiator','','numeric',-1,1,0,1,'/images/ico/calculator_scientific.png'),(18,'Full Differentiator','model::DifferentiatorFull','DifferentiatorFull','','numeric',-1,1,0,1,'/images/ico/calculator_scientific.png'),(19,'Sensor to meter','model::SensorToMeter','SensorToMeter','Wh','meter',1,1,0,1,'/images/ico/calculator_scientific.png'),(20,'Import / Export','model::ImportExport','InternalConsumption','','meter',2,1,0,1,'/images/ico/calculator_scientific.png'),(21,'Average','model::Average','Average','','numeric',-1,1,0,1,'/images/ico/calculator_scientific.png'),(22,'Calculator','model::Calculator','Calculator','','numeric',1,1,0,1,'/images/ico/calculator_scientific.png'),(23,'History','model::History','History','','numeric',1,1,0,1,'/images/ico/calculator_scientific.png'),(24,'Baseline','model::Baseline','Baseline','','sensor',1,1,0,1,'/images/ico/calculator_scientific.png'),(25,'Topline','model::Topline','Topline','','sensor',1,1,0,1,'/images/ico/calculator_scientific.png'),(26,'Meter to sensor','model::MeterToSensor','MeterToSensor','','sensor',1,1,0,1,'/images/ico/calculator_scientific.png'),(27,'Full Accumulator','model::AccumulatorFull','AccumulatorFull','','numeric',-1,1,0,1,'/images/ico/calculator_scientific.png'),(28,'Selector','model::Selector','Selector','','numeric',2,1,0,1,'/images/ico/ui_check_boxes.png'),(29,'Multiplier','model::Multiplier','Multiplier','','numeric',-1,1,0,1,'/images/ico/calculator_scientific.png'),(30,'Dashboard channel','model::Dashboard','Dashboard','','numeric',1,1,0,1,'/images/ico/dashboard.png'),(31,'Solar Estimate','model::SolarEstimate','SolarEstimate','Wh','sensor',1,1,0,1,'/images/ico/plug.png'),(32,'Averageline','model::Averageline','Averageline','','sensor',1,1,0,1,'/images/ico/calculator_scientific.png'),(33,'Percentage calculator','model::Ratio','Ratio','%','sensor',2,1,0,1,'/images/ico/edit_percent.png'),(40,'Kaco Inverter','model::KacoInverter','Kaco\\RS485','','group',-1,0,1,0,'/images/ico/kaco.png'),(41,'SMA Inverter','model::SMAInverter','SMA\\Webbox','','group',-1,0,1,0,'/images/ico/sma_inverter.png'),(42,'SMA Sensorbox','model::SMASensorbox','SMA\\Webbox','','group',-1,0,1,0,'/images/ico/sma_sensorbox.png'),(43,'Fronius Inverter','model::FroniusSolarNet','Fronius\\SolarNet','','group',-1,0,1,0,'/images/ico/fronius.png'),(44,'Fronius Sensorbox','model::FroniusSolarNet','Fronius\\SolarNet','','group',-1,0,1,0,'/images/ico/fronius.png'),(45,'OpenWeatherMap','model::OpenWeatherMap','JSON','','group',-1,0,1,0,'/images/ico/OpenWeatherMap.png'),(46,'Wunderground','model::Wunderground','JSON','','group',-1,0,1,0,'/images/ico/Wunderground.png'),(47,'Kostal Piko Inverter','model::KostalPiko','Kostal\\Piko','','group',-1,0,1,0,'/images/ico/kostal_inverter.png'),(48,'Solar Edge Inverter','model::SolarEdgeInverter','SE\\Inverter','','group',-1,0,1,0,'/images/ico/solar_edge.png'),(49,'Solar Edge Optimizer','model::SolarEdgeOptimizer','SE\\Optimizer','','group',-1,0,1,0,'/images/ico/solar_edge.png'),(50,'Energy meter absolute','model::EnergyMeter','Channel','Wh','meter',0,1,1,1,'/images/ico/plug.png'),(51,'Power sensor','model::PowerSensor','Channel','W','sensor',0,1,1,1,'/images/ico/plug.png'),(52,'Voltage sensor','model::Voltage','Channel','V','sensor',0,1,1,1,'/images/ico/dashboard.png'),(53,'Current sensor','model::CurrentSensor','Channel','A','sensor',0,1,1,1,'/images/ico/lightning.png'),(54,'Gas sensor','model::GasSensor','Channel','m³/h','sensor',0,1,1,1,'/images/ico/fire.png'),(55,'Heat sensor','model::HeatSensor','Channel','W','sensor',0,1,1,1,'/images/ico/fire_big.png'),(56,'Humidity sensor','model::Humidity','Channel','%','sensor',0,1,1,1,'/images/ico/weather_cloud.png'),(57,'Luminosity sensor','model::Luminosity','Channel','lm','sensor',0,1,1,1,'/images/ico/light_bulb.png'),(58,'Pressure sensor','model::Pressure','Channel','hPa','sensor',0,1,1,1,'/images/ico/umbrella.png'),(59,'Radiation sensor','model::RadiationSensor','Channel','µSV','sensor',0,1,1,1,'/images/ico/radioactivity.png'),(60,'Temperature sensor','model::Temperature','Channel','°C','sensor',0,1,1,1,'/images/ico/thermometer.png'),(61,'Valve sensor','model::Valve','Channel','°','sensor',0,1,1,1,'/images/ico/wheel.png'),(62,'Water sensor','model::WaterSensor','Channel','m³/h','sensor',0,1,1,1,'/images/ico/water.png'),(63,'Windspeed sensor','model::Windspeed','Channel','m/s','sensor',0,1,1,1,'/images/ico/paper_plane.png'),(64,'Irradiation sensor','model::Irradiation','Channel','W/m²','sensor',0,1,1,1,'/images/ico/brightness.png'),(65,'Timer','model::Timer','Channel','h','meter',0,1,1,1,'/images/ico/clock.png'),(66,'Frequency sensor','model::FrequencySensor','Channel','Hz','sensor',0,1,1,1,'/images/ico/dashboard.png'),(67,'Winddirection sensor','model::Winddirection','Channel','°','sensor',0,1,1,1,'/images/ico/wheel.png'),(68,'Rainfall sensor','model::RainfallSensor','Channel','mm/h','sensor',0,1,1,1,'/images/ico/umbrella.png'),(69,'Sensor','model::Sensor','Channel','','sensor',0,1,1,1,'/images/ico/system-monitor.png'),(70,'Gas meter','model::GasMeter','Channel','m³','meter',0,1,1,1,'/images/ico/fire.png'),(71,'Radiation meter','model::RadiationMeter','Channel','µSV/h','meter',0,1,1,1,'/images/ico/radioactivity.png'),(72,'Water meter','model::WaterMeter','Channel','m³','meter',0,1,1,1,'/images/ico/water.png'),(73,'Rainfall meter','model::RainfallMeter','Channel','mm','meter',0,1,1,1,'/images/ico/umbrella.png'),(74,'Meter','model::Meter','Channel','','meter',0,1,1,1,'/images/ico/chart-up.png'),(80,'Percentage','model::Percentage','Channel','%','sensor',0,1,1,1,'/images/ico/edit-percent.png'),(90,'Power sensor counter','model::PowerCounter','Counter','W','sensor',0,1,1,1,'/images/ico/plug.png'),(91,'Switch','model::Switch','Switcher','','general',0,1,1,1,'/images/ico/ui_check_boxes.png'),(99,'Database usage','Database usage','DatabaseUsage','rows','sensor',0,1,0,1,'/images/ico/database.png'),(100,'PV-Log Plant','model::PVLogPlant','PVLog\\Plant','','group',-1,1,0,0,'/images/ico/pv_log_sum.png'),(101,'PV-Log Inverter','model::PVLogInverter','PVLog\\Inverter','','group',-1,1,0,0,'/images/ico/pv_log.png'),(102,'PV-Log JSON 1.1 Plant','model::PVLogPlant11','PVLog\\Plant11','','group',-1,1,0,0,'/images/ico/pv-log-p.png'),(103,'PV-Log JSON 1.1 Inverter','model::PVLogInverter11','PVLog\\Inverter11','','group',-1,0,0,0,'/images/ico/pv-log-i.png'),(110,'Sonnenertrag JSON','model::SonnenertragJSON','Sonnenertrag\\JSON','','group',-1,1,0,0,'/images/ico/sonnenertrag.png');
/*!40000 ALTER TABLE `pvlng_type` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed
-- ------------------------------------------------------
-- Initial channel data, demo views and demo dashboard
-- ------------------------------------------------------

/*!40101 SET NAMES utf8 */;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;

INSERT INTO `pvlng_channel`
(`id`, `name`, `description`, `type`, `resolution`, `unit`, `decimals`, `meter`, `cost`, `threshold`, `valid_from`, `valid_to`, `extra`) VALUES
(1, 'DO NOT TOUCH', 'Dummy for tree root', 0, 0, '', 2, 0, 0, NULL, NULL, NULL, ''),
(2, 'RANDOM Temperature sensor', '15 ... 25, &plusmn;0.1', 10, 1, '°C', 1, 0, 0, 0.1, 15, 25, ''),
(3, 'RANDOM Energy meter', '0 ... &infin;, +0.05', 10, 1000, 'Wh', 0, 1, 0.0002, 0.05, 0, 10000000000, ''),
(4, 'Dashboard', 'Dashboard group', 5, 1, '', 2, 0, 0, NULL, NULL, NULL, ''),
(5, 'Temperature sensor', 'RANDOM Temperature sensor for Dashboard', 30, 1, '°C', 1, 0, 0, NULL, 0, 40, '\"> 10 : #BFB\\n10 > 20 : #FFB\\n20 > : #FBB\"'),
(6, 'Calculations', 'Group for separation of real channels from calculations', 5, 1, '', 2, 0, 0, NULL, NULL, NULL, '');

-- Update icon column from type
UPDATE `pvlng_channel` c
   SET `icon` = (SELECT `icon` from `pvlng_type` WHERE `id` = c.`type`);

-- Update Dashboard channel icon from temperature sensor channel type
-- In the Web front end this will be done by adding the 1st child channel
UPDATE `pvlng_channel`
   SET `icon` = (SELECT `icon` from `pvlng_type` WHERE `id` = 10)
 WHERE `id` = 5;

INSERT INTO `pvlng_settings` (`scope`, `name`, `key`, `value`, `order`, `description`, `type`, `data`) VALUES
('core', '', 'Language', 'en', 10, 'Default language', 'option', 'en:English;de:Deutsch'),
('core', '', 'Title', 'PhotoVoltaic Logger new generation', 20, 'Your personal title (HTML allowed)', '', ''),
('core', '', 'SendStats', 1, 30, 'Send anonymous statistics', 'bool', ''),
('core', '', 'Latitude', '', 50, 'Location latitude (<a href="/location" target="_blank">or search</a>)<br /><small>Your geographic coordinate that specifies the north-south position (-90..90)</small>', 'num', ''),
('core', '', 'Longitude', '', 60, 'Location longitude (<a href="/location" target="_blank">or search</a>)<br /><small>Your geographic coordinate that specifies the east-west position (-180..180)</small>', 'num', ''),
('core', 'Currency', 'ISO', 'EUR', 80, 'ISO Code', 'str', ''),
('core', 'Currency', 'Symbol', '€', 81, 'Symbol', 'str', ''),
('core', 'Currency', 'Decimals', 2, 82, 'Decimals', 'num', ''),
('core', '', 'EmptyDatabaseAllowed', 0, 100, 'Enable function for deletion of all measuring data from database.<br>Channels and channel hierarchy will <strong>not</strong> be deleted!<br><strong style="color:red">Only if this is allowed, the deletion is possible!</strong>', 'bool', ''),
('core', 'Currency', 'Format', '{} €', 83, 'Output format, <strong><tt>{}</tt></strong> will be replaced with value', 'str', ''),
('controller', 'Index', 'ChartHeight', 528, 10, 'Default chart height', 'num', ''),
('controller', 'Index', 'NotifyAll', 1, 30, 'Notify overall loading time for all channels', 'bool', ''),
('controller', 'Index', 'NotifyEach', 0, 40, 'Notify loading time for each channel', 'bool', ''),
('controller', 'Index', 'Refresh', 300, 20, 'Auto refresh chart each ? seconds, set 0 to disable', 'num', ''),
('controller', 'Mobile', 'ChartHeight', 320, 0, 'Default chart height', 'num', ''),
('controller', 'Tariff', 'TimesLines', 10, 0, 'Initial times lines for each taiff', 'num', ''),
('controller', 'Weather', 'APIkey', '', 0, 'Wunderground API key', '', ''),
('model', '', 'DoubleRead', 5, 0, 'Detect double readings by timestamp &plusmn;seconds<br /><small>(set 0 to disable)</small>', 'num', '0:geometric mean;1:arithmetic mean'),
('model', 'Daylight', 'Average', 0, 10, 'Calculation method for irradiation average', 'option', '0:geometric mean;1:arithmetic mean'),
('model', 'Daylight', 'CurveDays', 5, 20, 'Build average over the last ? days', 'num', ''),
('model', 'Daylight', 'SunriseIcon', '/images/sunrise.png', 30, 'Sunrise marker image', '', ''),
('model', 'Daylight', 'SunsetIcon', '/images/sunset.png', 40, 'Sunset marker image', '', ''),
('model', 'Daylight', 'ZenitIcon', '/images/zenit.png', 50, 'Sun zenit marker image', '', ''),
('model', 'Estimate', 'Marker', '/images/energy.png', 0, 'Marker image', '', ''),
('model', 'History', 'AverageDays', '5', 0, 'Build average over the last ? days', 'num', ''),
('model', 'InternalCalc', 'LifeTime', '60', 0, 'Buffer lifetime of calculated data in seconds<br /><small>(e.g. if your store most data each 5 minutes, set to 300 and so on)</small>', 'num', '');

INSERT INTO `pvlng_tree` (`id`, `lft`, `rgt`, `entity`) VALUES
(1, 1, 12, 1), (2, 2, 3, 2), (3, 4, 5, 3), (4, 6, 11, 4), (5, 7, 10, 5), (6, 8, 9, 2);

INSERT INTO `pvlng_view` (`name`, `public`, `data`, `slug`) VALUES
('Demo - Simpel Sensor and Meter', 1, '{\"2\":\"{\\\"v\\\":2,\\\"axis\\\":2,\\\"type\\\":\\\"areaspline\\\",\\\"style\\\":\\\"Solid\\\",\\\"width\\\":2,\\\"color\\\":\\\"#4572a7\\\",\\\"colorusediff\\\":0,\\\"colordiff\\\":\\\"#404040\\\",\\\"consumption\\\":false,\\\"threshold\\\":0,\\\"min\\\":false,\\\"max\\\":false,\\\"last\\\":true,\\\"all\\\":false,\\\"time1\\\":\\\"00:00\\\",\\\"time2\\\":\\\"24:00\\\",\\\"daylight\\\":false,\\\"daylight_grace\\\":0,\\\"legend\\\":true,\\\"position\\\":0,\\\"hidden\\\":false,\\\"outline\\\":false}\",\"3\":\"{\\\"v\\\":2,\\\"axis\\\":1,\\\"type\\\":\\\"spline\\\",\\\"style\\\":\\\"Solid\\\",\\\"width\\\":2,\\\"color\\\":\\\"#89a54e\\\",\\\"colorusediff\\\":-1,\\\"colordiff\\\":\\\"#db843d\\\",\\\"consumption\\\":false,\\\"threshold\\\":20,\\\"min\\\":true,\\\"max\\\":true,\\\"last\\\":false,\\\"all\\\":false,\\\"time1\\\":\\\"00:00\\\",\\\"time2\\\":\\\"24:00\\\",\\\"daylight\\\":false,\\\"daylight_grace\\\":0,\\\"legend\\\":true,\\\"position\\\":1,\\\"hidden\\\":false,\\\"outline\\\":true}\",\"p\":\"5i\"}', 'demo-1-simply-sensor-and-meter'),
('Demo - Sensor with min/max', 1, '{\"2\":\"{\\\"v\\\":2,\\\"axis\\\":2,\\\"type\\\":\\\"areasplinerange\\\",\\\"style\\\":\\\"Solid\\\",\\\"width\\\":2,\\\"color\\\":\\\"#4572a7\\\",\\\"colorusediff\\\":0,\\\"colordiff\\\":\\\"#404040\\\",\\\"consumption\\\":false,\\\"threshold\\\":0,\\\"min\\\":false,\\\"max\\\":false,\\\"last\\\":true,\\\"all\\\":false,\\\"time1\\\":\\\"00:00\\\",\\\"time2\\\":\\\"24:00\\\",\\\"daylight\\\":false,\\\"daylight_grace\\\":0,\\\"legend\\\":true,\\\"position\\\":0,\\\"hidden\\\":false,\\\"outline\\\":false}\",\"p\":\"20i\"}', 'demo-2-sensor-with-min-max'),
('Demo - Daily values of Meter', 1, '{\"3\":\"{\\\"v\\\":2,\\\"axis\\\":1,\\\"type\\\":\\\"bar\\\",\\\"style\\\":\\\"Solid\\\",\\\"width\\\":2,\\\"color\\\":\\\"#89a54e\\\",\\\"colorusediff\\\":0,\\\"colordiff\\\":\\\"#000\\\",\\\"consumption\\\":true,\\\"threshold\\\":20,\\\"min\\\":false,\\\"max\\\":true,\\\"last\\\":false,\\\"all\\\":false,\\\"time1\\\":\\\"00:00\\\",\\\"time2\\\":\\\"24:00\\\",\\\"daylight\\\":false,\\\"daylight_grace\\\":0,\\\"legend\\\":true,\\\"position\\\":1,\\\"hidden\\\":false,\\\"outline\\\":true}\",\"p\":\"1d\"}', 'demo-daily-values-of-meter');

INSERT INTO `pvlng_dashboard` (`name`, `public`, `data`) VALUES ('Temperatur', 1, '[5]');

-- Fianally generate and show API key
SELECT `getAPIkey`() AS `Your PVLng API key:`;
