-- --------------------------------------------------------------------------
-- @author      Knut Kohl <github@knutkohl.de>
-- @copyright   2012-2013 Knut Kohl
-- @license     GNU General Public License http://www.gnu.org/licenses/gpl.txt
-- @version     1.0.0
-- --------------------------------------------------------------------------

INSERT INTO `pvlng_channel` (`id`, `name`, `description`, `type`, `resolution`, `unit`, `decimals`, `meter`, `cost`, `threshold`, `valid_from`, `valid_to`) VALUES
(1,	'DO NOT TOUCH',	'Dummy for tree root',	0,	0,	'',	2,	0,	0,	NULL,	NULL,	NULL),
(2,	'RANDOM Temperature sensor',	'15 ... 25, &plusmn;0.5',	10,	1,	'°C',	1,	0,	0,	0.5,	15,	25),
(3,	'RANDOM Energy meter',	'0 ... &infin;, +0.05',	10,	1000,	'Wh',	0,	1,	0.0002,	0.05,	0,	10000000000);

INSERT INTO `pvlng_config` (`key`, `value`, `comment`, `type`) VALUES
('Currency',	'EUR',	'Costs currency',	'str'),
('CurrencyDecimals',	2,	'Costs currency decimals',	'num'),
('LogInvalid',	0,	'Log invalid values',	'str'),
('TimeStep',	60,	'Reading time step in seconds',	'num');

INSERT INTO `pvlng_tree` (`id`, `lft`, `rgt`, `entity`) VALUES
(1,  1,  6,  1), (2,  2,  3,  2), (3,  4,  5,  3);

SELECT `getAPIkey`() AS `PVLng API key`;
