<?php
/**
 *
 * @author     Knut Kohl <github@knutkohl.de>
 * @copyright  2012-2014 Knut Kohl
 * @license    MIT License (MIT) http://opensource.org/licenses/MIT
 * @version    1.0.0
 */

/**
 *
 */
PVLng::Menu('20.90', '/infoframe', __('Infoframe'));

/**
 * Routes
 */
$app->get('/infoframe', function() use ($app) {
    $app->process('Infoframe', 'Index', array('frame' => 'default'));
});

/**
 * Custom frames
 */
$app->get('/infoframe/:frame', function($frame) use ($app) {
    $app->process('Infoframe', 'Index', array('frame' => 'custom'.DS.$frame));
});
