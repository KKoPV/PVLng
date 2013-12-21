--
-- v2.0.0
--

ALTER TABLE `pvlng_channel` ADD `adjust` tinyint(1) unsigned NOT NULL COMMENT 'allow auto adjustment of offset' AFTER `offset`;

CREATE OR REPLACE VIEW `pvlng_tree_view` AS
select `tree`.`id` AS `id`,`tree`.`entity` AS `entity`,if((`t`.`childs` <> 0),`tree`.`guid`,`c`.`guid`) AS `guid`,`c`.`name` AS `name`,`c`.`serial` AS `serial`,`c`.`channel` AS `channel`,`c`.`description` AS `description`,`c`.`resolution` AS `resolution`,`c`.`cost` AS `cost`,`c`.`meter` AS `meter`,`c`.`numeric` AS `numeric`,`c`.`offset` AS `offset`,`c`.`adjust` AS `adjust`,`c`.`unit` AS `unit`,`c`.`decimals` AS `decimals`,`c`.`threshold` AS `threshold`,`c`.`valid_from` AS `valid_from`,`c`.`valid_to` AS `valid_to`,`c`.`public` AS `public`,`c`.`comment` AS `comment`,`t`.`name` AS `type`,`t`.`model` AS `model`,`t`.`childs` AS `childs`,`t`.`read` AS `read`,`t`.`write` AS `write`,`t`.`graph` AS `graph`,`t`.`icon` AS `icon` from ((`pvlng_tree` `tree` join `pvlng_channel` `c` on((`tree`.`entity` = `c`.`id`))) join `pvlng_type` `t` on((`c`.`type` = `t`.`id`)));

CREATE OR REPLACE VIEW `pvlng_channel_view` AS
select `c`.`id` AS `id`,`c`.`guid` AS `guid`,`c`.`name` AS `name`,`c`.`serial` AS `serial`,`c`.`channel` AS `channel`,`c`.`description` AS `description`,`c`.`resolution` AS `resolution`,`c`.`cost` AS `cost`,`c`.`numeric` AS `numeric`,`c`.`offset` AS `offset`,`c`.`adjust` AS `adjust`,`c`.`unit` AS `unit`,`c`.`decimals` AS `decimals`,`c`.`meter` AS `meter`,`c`.`threshold` AS `threshold`,`c`.`valid_from` AS `valid_from`,`c`.`valid_to` AS `valid_to`,`t`.`id` AS `type_id`,`t`.`name` AS `type`,`t`.`model` AS `model`,`t`.`childs` AS `childs`,`t`.`read` AS `read`,`t`.`write` AS `write`,`t`.`graph` AS `graph`,`t`.`icon` AS `icon` from (`pvlng_channel` `c` join `pvlng_type` `t` on((`c`.`type` = `t`.`id`))) where (`c`.`id` <> 1);
