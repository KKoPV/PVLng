<?php
/**
 *
 */
PVLng::Menu(array(
    'position' => 20,
    'label'    => I18N::translate('Dashboard'),
    'hint'     => I18N::translate('DashboardHint') . ' (Shift+F2)',
    'route'    => '/dashboard',
    'login'    => TRUE
));

/**
 * Routes
 */
$app->map('/dashboard', $checkAuth, function() use ($app) {
    $app->process('Dashboard');
})->via('GET', 'POST');

/**
 * Embedded mode
 */
$app->get('/dashboard/embed', function() use ($app) {
    $app->process('Dashboard', 'IndexEmbedded');
});

// Just a shorter alias for /dashboard/embed
$app->get('/ed', function() use ($app) {
    $app->redirect('/dashboard/embed', 302);
});
