CREATE VIEW `pvlng_reading_stats` AS
SELECT c.`guid`, c.`name`, c.`description`, c.`numeric`, c.`decimals`,
       t.*, IFNULL(n.`data`, s.`data`) AS `data`
  FROM `pvlng_reading_count` AS t
  LEFT JOIN `pvlng_channel_view` AS c USING(`id`)
  LEFT JOIN `pvlng_reading_num` AS n USING(`id`, `timestamp`)
  LEFT JOIN `pvlng_reading_str` AS s USING(`id`, `timestamp`);
