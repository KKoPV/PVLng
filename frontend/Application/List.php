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
PVLng::Menu(
    'list', 30, '/list',
    I18N::translate('List'),
    I18N::translate('ListHint') . ' (Shift+F3)'
);

/**
 * Routes
 */
$app->get('/list(/:id)', $checkAuth, function( $id=NULL ) use ($app) {
    $app->process('Lists', 'Index', array('id' => $id));
})->conditions(array(
    'id' => '\d+'
));

$app->get('/list(/:guid)', $checkAuth, function( $guid=NULL ) use ($app) {
    $app->process('Lists', 'Index', array('guid' => $guid));
})->conditions(array(
    'guid' => '(\w{4}-){7}\w{4}'
));
