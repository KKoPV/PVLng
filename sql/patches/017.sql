-- Handle obsolete channel types
ALTER TABLE `pvlng_type` ADD `obsolete` tinyint(1) unsigned NOT NULL DEFAULT 0;
-- Old PV-Log JSON 1.0 channels
UPDATE `pvlng_type` SET `obsolete` = 1 WHERE `id` = 100 OR `id` = 101;

-- Typo
UPDATE `pvlng_type` SET `name` = 'LaCrosse WS-2300' WHERE `id` = 95;
