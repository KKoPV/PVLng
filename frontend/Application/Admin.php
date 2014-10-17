<?php
/**
 * Routes
 *
 * @author     Knut Kohl <github@knutkohl.de>
 * @copyright  2012-2014 Knut Kohl
 * @license    MIT License (MIT) http://opensource.org/licenses/MIT
 * @version    1.0.0
 */

/**
 * Routes
 */
$app->map('/login', function() use ($app) {
    $app->process('Admin', 'Login');
})->via('GET', 'POST');

$app->get('/login/:token', function($token) use ($app) {
    $app->process('Admin', 'Login', array('token' => $token));
});

$app->any('/logout', function() use ($app) {
    $app->process('Admin', 'Logout');
});

$app->map('/adminpass', function() use ($app) {
    $app->process('Admin', 'AdminPassword');
})->via('GET', 'POST')->Language = 'en';

$app->map('/location', $checkAuth, function() use ($app) {
    $app->process('Admin', 'Location');
})->via('GET', 'POST')->Language = 'en';

$app->map('/cc', $checkAuth, function() use ($app) {
    $app->process('Admin', 'Clearcache');
})->via('GET', 'POST')->Language = 'en';

$app->get('/bk', function() {
    Header('Location: /public/bk/index.php');
    exit;
});
