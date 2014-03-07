<?php
/**
 * Routes
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
