INSERT INTO `pvlng_settings`
(`scope`, `name`, `key`, `value`, `order`, `description`, `type`, `data`)
VALUES
('core', '', 'EmptyDatabaseAllowed', '0', 100, 'Enable function for deletion of all measuring data from database.<br>Channels and channel hierarchy will <strong>not</strong> be deleted!<br><strong style=\"color:red\">Only if this is allowed, the deletion is possible!</strong>', 'bool', '');

UPDATE `pvlng_type` SET `unit` = '', `icon` = '/images/ico/calculator_scientific.png' WHERE `id` = 15;

INSERT INTO `pvlng_type`
(`id`, `name`, `description`, `model`, `unit`, `type`, `childs`, `read`, `write`, `graph`, `icon`)
VALUES
(33, 'Percentage calculator', 'model::Ratio', 'Ratio', '%', 'sensor', 2, 1, 0, 1, '/images/ico/edit_percent.png');
