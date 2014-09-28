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
PVLng::Menu(80, '/weather', __('Weather'), 'Shift+F8');

/**
 * Routes
 */
$app->get('/weather', function() use ($app) {
    $app->process('Weather');
});
