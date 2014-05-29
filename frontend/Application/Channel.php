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
PVLng::Menu( 'channel', 50, '#', __('Channels') );

/**
 *
 */
PVLng::SubMenu(
    'channel', 10, '/channel', __('Channels'), __('ChannelsHint') . ' (Shift+F4)'
);

PVLng::SubMenu(
    'channel', 20, '/overview', __('Overview'), __('OverviewHint')
);

PVLng::SubMenu(
    'channel', 30, '/tariff', __('Tariffs'), __('TariffsHint')
);

/**
 * Routes
 */
$app->get('/channel', $checkAuth, function() use ($app) {
    $app->process('Channel');
});

$app->get('/channel/new/:type', $checkAuth, function( $type ) use ($app) {
    $app->process('Channel', 'New', array('type' => $type));
});

$app->map('/channel/add(/:clone)', $checkAuth, function( $clone=0 ) use ($app) {
    $app->process('Channel', 'Add', array('clone' => $clone));
})->via('GET', 'POST');

$app->map('/channel/template', $checkAuth, function() use ($app) {
    $app->process('Channel', 'Template');
})->via('POST');

$app->get('/channel/edit/:id', $checkAuth, function( $id ) use ($app) {
    $app->process('Channel', 'Edit', array('id' => $id));
});

$app->post('/channel/alias', $checkAuth, function() use ($app) {
    $app->process('Channel', 'Alias');
});

$app->post('/channel/edit', $checkAuth, function() use ($app) {
    $app->process('Channel', 'Edit');
});

$app->post('/channel/delete', $checkAuth, function() use ($app) {
    $app->process('Channel', 'Delete');
});
