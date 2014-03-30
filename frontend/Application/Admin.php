<?php
/**
 * Routes
 *
 * @author     Knut Kohl <github@knutkohl.de>
 * @copyright  2012-2014 Knut Kohl
 * @license    MIT License (MIT) http://opensource.org/licenses/MIT
 * @version    1.0.0
 */
$app->map('/login', function() use ($app) {
    $app->process('Admin', 'Login');
})->via('GET', 'POST');

$app->any('/logout', function() use ($app) {
    $app->process('Admin', 'Logout');
});

$app->map('/adminpass', function() use ($app) {
    $app->process('Admin', 'AdminPassword');
})->via('GET', 'POST');

$app->map('/_config', $checkAuth, function() use ($app) {
    $app->process('Admin', 'Config');
})->via('GET', 'POST');

$app->get('/clearcache', $checkAuth, function() use ($app) {
    $app->process('Admin', 'Clearcache');
});

$app->get('/bk', function() {
    Header('Location: /public/bk/index.php');
    exit;
});
