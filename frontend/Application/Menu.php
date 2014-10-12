<?php
/**
 *
 * @author     Knut Kohl <github@knutkohl.de>
 * @copyright  2012-2014 Knut Kohl
 * @license    MIT License (MIT) http://opensource.org/licenses/MIT
 * @version    1.0.0
 */

/**
 * Basic menu structure
 */
$app->hook('slim.before', function () use ($app) {
    $app->menu->add('10', '#', 'MasterData');
    $app->menu->add('20', '#', 'Analysis');
});
