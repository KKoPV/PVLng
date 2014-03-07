<?php
/**
 *
 */
PVLng::Menu(array(
    'position' => 60,
    'label'    => I18N::translate('Information'),
    'hint'     => I18N::translate('InfoHint') . ' (Shift+F6)',
    'route'    => '/info',
    'login'    => TRUE
));

/**
 * Routes
 */
$app->map('/info', $checkAuth, function() use ($app) {
    $app->process('Info');
})->via('GET', 'POST');
