--
-- v1.4.0
--

CREATE TABLE `pvlng_performance` (
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `action` enum('read','write') NOT NULL,
  `time` int(10) unsigned NOT NULL COMMENT 'ms'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Gather system performance';

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
) ENGINE=InnoDB;

ALTER TABLE `pvlng_log` CHANGE `scope` `scope` varchar(40) NOT NULL AFTER `timestamp`;

ALTER TABLE `pvlng_view` ADD `public` tinyint(1) NOT NULL COMMENT 'Public view';
ALTER TABLE `pvlng_view` ADD `slug` varchar(50) NOT NULL COMMENT 'URL-save slug';

ALTER TABLE `pvlng_type` CHANGE `model` `model` varchar(30) NOT NULL DEFAULT 'NoModel' AFTER `description`;
UPDATE `pvlng_type` SET `model` = 'NoModel' WHERE `model` = '';
ALTER TABLE `pvlng_type` CHANGE `icon` `icon` varchar(255) NOT NULL AFTER `graph`;

ALTER TABLE `pvlng_channel` ADD `comment` text NOT NULL;
ALTER TABLE `pvlng_channel` CHANGE `channel` `channel` varchar(255) NOT NULL AFTER `serial`;
ALTER TABLE `pvlng_channel` CHANGE `threshold` `threshold` double unsigned NULL AFTER `cost`;
UPDATE `pvlng_channel` SET `threshold` = NULL WHERE `threshold` = 0;
ALTER TABLE `pvlng_channel` ADD `offset` double NOT NULL AFTER `numeric`;

DROP INDEX `Name-Description`;
ALTER TABLE `pvlng_channel` ADD UNIQUE `Name-Description-Type` (`name`, `description`, `type`),

ALTER TABLE `pvlng_tree` CHANGE `entity` `entity` int(10) unsigned NOT NULL COMMENT 'pvlng_channel -> id' AFTER `moved`

INSERT INTO `pvlng_config` VALUES
('Currency', 'EUR', 'Costs currency', 'str'),
('CurrencyDecimals', '2', 'Costs currency decimals', 'str'),
('TimeStep', '60', 'Reading time step in seconds', 'num');


DELIMITER ;;

DROP TRIGGER `pvlng_channel_bd`;;
CREATE TRIGGER `pvlng_channel_bd` BEFORE DELETE ON `pvlng_channel` FOR EACH ROW
BEGIN
  SELECT COUNT(*) INTO @COUNT FROM `pvlng_tree` WHERE `entity` = old.`id`;
  IF @COUNT > 0 THEN
    SIGNAL SQLSTATE '99999' SET MESSAGE_TEXT = 'ChannelStillInTree';
  END IF;
END;;

DROP TRIGGER `pvlng_tree_bi`;;
CREATE TRIGGER `pvlng_tree_bi` BEFORE INSERT ON `pvlng_tree` FOR EACH ROW
BEGIN
  SELECT `t`.`childs`, `t`.`read`+`t`.`write`
    INTO @CHILDS, @RW
    FROM `pvlng_channel` `e`
    JOIN `pvlng_type` `t` ON `e`.`type` = `t`.`id`
   WHERE `e`.`id` = new.`entity`;
   IF @CHILDS != 0 AND @RW > 0 THEN
     SET new.`guid` = GUID();
   END IF;
END;;

DROP PROCEDURE `getTimestamp`;;
CREATE PROCEDURE `getTimestamp`(INOUT `timestamp` int unsigned)
BEGIN
  IF `timestamp` = 0 THEN
    SET `timestamp` = UNIX_TIMESTAMP();
  END IF;
  SELECT `value` FROM `pvlng_config` WHERE `key` = "TimeStep" INTO @SECONDS;
  SET `timestamp` = `timestamp` DIV @SECONDS * @SECONDS;
END;;

CREATE PROCEDURE `aggregatePerformance`()
BEGIN
    -- Build average of hours over raw data
    REPLACE INTO `pvlng_performance_avg`
    SELECT 'hour',`action`
          ,YEAR(`timestamp`),MONTH(`timestamp`),DAY(`timestamp`),HOUR(`timestamp`)
          ,AVG(`time`),COUNT(*)
      FROM `pvlng_performance`
     GROUP BY `action`,YEAR(`timestamp`),DAYOFYEAR(`timestamp`),HOUR(`timestamp`);
    -- Delete raw data
    TRUNCATE `pvlng_performance`;
    -- Delete hourly data older 1 month
    DELETE FROM `pvlng_performance_avg`
     WHERE `aggregation` = "hour"
       AND FROM_UNIXTIME(UNIX_TIMESTAMP(CONCAT(`year`,'-',`month`,'-',`day`))) <
           NOW() - INTERVAL 1 MONTH;
    -- Build average of days over hours data
    REPLACE INTO `pvlng_performance_avg`
    SELECT 'day',`action`
          ,`year`,`month`,`day`,0
          ,AVG(`average`),SUM(`count`)
      FROM `pvlng_performance_avg`
     WHERE `aggregation` = "hour"
     GROUP BY `action`,`year`,`month`,`day`;
    -- Delete daily data older 1 year
    DELETE FROM `pvlng_performance_avg`
     WHERE `aggregation` = "day"
       AND FROM_UNIXTIME(UNIX_TIMESTAMP(CONCAT(`year`,'-',`month`,'-',`day`))) <
           NOW() - INTERVAL 1 YEAR;
    -- Build average of month over days data
    REPLACE INTO `pvlng_performance_avg`
    SELECT 'month',`action`
          ,`year`,`month`,0,0
          ,AVG(`average`),SUM(`count`)
      FROM `pvlng_performance_avg`
     WHERE `aggregation` = "day"
     GROUP BY `action`,`year`,`month`;
    -- Build average of years over months data
    REPLACE INTO `pvlng_performance_avg`
    SELECT 'year',`action`
          ,`year`,0,0,0
          ,AVG(`average`),SUM(`count`)
      FROM `pvlng_performance_avg`
     WHERE `aggregation` = "month"
     GROUP BY `action`,`year`;
    -- Build overall average over year data
    REPLACE INTO `pvlng_performance_avg`
    SELECT 'overall',`action`
          ,0,0,0,0
          ,AVG(`average`),SUM(`count`)
      FROM `pvlng_performance_avg`
     WHERE `aggregation` = "year"
     GROUP BY `action`;
END;;

CREATE EVENT `aggregatePerformance` ON SCHEDULE EVERY 1 HOUR
STARTS '2000-01-01 00:00:00' ON COMPLETION PRESERVE ENABLE
DO CALL `aggregatePerformance`();;

DELIMITER ;
