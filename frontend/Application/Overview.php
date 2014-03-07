<?php
/**
 *
 */
PVLng::Menu(array(
    'position' => 40,
    'label'    => I18N::translate('Overview'),
    'hint'     => I18N::translate('OverviewHint') . ' (Shift+F4)',
    'route'    => '/overview',
    'login'    => TRUE
));

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
