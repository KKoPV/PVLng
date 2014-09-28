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
PVLng::Menu('10.30', '/type', __('ChannelTypes'));

/**
 * Routes
 */
$app->get('/type', $checkAuth, function() use ($app) {
    $app->process('Type');
});
