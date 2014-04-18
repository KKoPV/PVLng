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
PVLng::Menu(array(
    'position' => 50,
    'label'    => I18N::translate('Channel'),
    'hint'     => I18N::translate('ChannelsHint') . ' (Shift+F5)',
    'route'    => '/channel'
));

/**
 * Routes
 */
$app->get('/channel', $checkAuth, function() use ($app) {
    $app->process('Channel');
});

$app->map('/channel/add(/:clone)', $checkAuth, function( $clone=0 ) use ($app) {
    $app->params->set('clone', $clone);
    $app->process('Channel', 'Add');
})->via('GET', 'POST');

$app->map('/channel/template', $checkAuth, function() use ($app) {
    $app->process('Channel', 'Template');
})->via('POST');

$app->get('/channel/edit/:id', $checkAuth, function( $id ) use ($app) {
    $app->params->set('id', $id);
    $app->process('Channel', 'Edit');
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
