<?php
/**
 *
 * @author     Knut Kohl <github@knutkohl.de>
 * @copyright  2012-2014 Knut Kohl
 * @license    MIT License (MIT) http://opensource.org/licenses/MIT
 * @version    1.0.0
 */

$app->hook('slim.before', function () use ($app) {
    $app->menu->add('10.20', '#', 'Channels', !!$app->user);
    $app->menu->add('10.20.10', '/channels', 'ChannelList', !!$app->user, 'Shift+F3');
    $app->menu->add('10.20.20', '/channels/add', 'CreateChannel', !!$app->user);
    $app->menu->add('10.20.30', '/channels/template', 'CreateFromTemplate', !!$app->user);
});

/**
 * Routes
 */
$app->get('/channels', $checkAuth, function () use ($app) {
    $app->process('Channels');
});

$app->get('/channels/new/:type', $checkAuth, function ($type) use ($app) {
    $app->process('Channels', 'New', array('type' => $type));
});

$app->map('/channels/add(/:clone)', $checkAuth, function ($clone = 0) use ($app) {
    $app->process('Channels', 'Add', array('clone' => $clone));
})->via('GET', 'POST');

$app->get('/channels/template', $checkAuth, function () use ($app) {
    $app->redirect('/channels/add#template');
});

$app->post('/channels/template', $checkAuth, function () use ($app) {
    $app->process('Channels', 'Template');
});

$app->get('/channels/edit/:id', $checkAuth, function ($id) use ($app) {
    $app->process('Channels', 'Edit', array('id' => $id));
});

$app->get('/channels/edit/:guid', $checkAuth, function ($guid) use ($app) {
    $app->process('Channels', 'Edit', array('guid' => $guid));
});

$app->post('/channels/alias', $checkAuth, function () use ($app) {
    $app->process('Channels', 'Alias');
});

$app->post('/channels/edit', $checkAuth, function () use ($app) {
    $app->process('Channels', 'Edit');
});

$app->post('/channels/delete', $checkAuth, function () use ($app) {
    $app->process('Channels', 'Delete');
});
