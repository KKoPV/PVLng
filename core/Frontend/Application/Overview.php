<?php
/**
 *
 * @author     Knut Kohl <github@knutkohl.de>
 * @copyright  2012-2014 Knut Kohl
 * @license    MIT License (MIT) http://opensource.org/licenses/MIT
 * @version    1.0.0
 */

$app->hook('slim.before', function () use ($app) {
    $app->menu->add('10.10', '/overview', 'Overview', !!$app->user, 'Shift+F4');
});

/**
 * Routes
 */
$app->get('/overview', $checkAuth, function () use ($app) {
    $app->process('Overview');
});

$app->post('/overview/:action', $checkAuth, function ($action) use ($app) {
    // Tree manipulation requests
    $app->process('Overview', $action);
});
