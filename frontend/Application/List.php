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
    'position' => 30,
    'label'    => I18N::translate('List'),
    'hint'     => I18N::translate('ListHint') . ' (Shift+F3)',
    'route'    => '/list',
    'login'    => TRUE
));

/**
 * Routes
 */
$app->get('/list(/:id)', $checkAuth, function( $id=NULL ) use ($app) {
    $app->params->set('id', $id);
    $app->process('Lists');
})->conditions(array(
    'id' => '\d+'
));

$app->get('/list(/:guid)', $checkAuth, function( $guid=NULL ) use ($app) {
    $app->params->set('guid', $guid);
    $app->process('Lists');
})->conditions(array(
    'guid' => '(\w{4}-){7}\w{4}'
));
