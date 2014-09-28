<?php
/**
 *
 * @author     Knut Kohl <github@knutkohl.de>
 * @copyright  2012-2014 Knut Kohl
 * @license    MIT License (MIT) http://opensource.org/licenses/MIT
 * @version    1.0.0
 */
if (!Session::get('User')) return;

/**
 *
 */
PVLng::Menu('10.10', '/overview', __('Overview'), 'Shift+F4');

/**
 * Routes
 */
$app->get('/overview', $checkAuth, function() use ($app) {
    $app->process('Overview');
});

$app->post('/overview/:action', $checkAuth, function( $action ) use ($app) {
    // Tree manipulation requests
    $app->process('Overview', $action);
});
