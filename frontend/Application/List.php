<?php
/**
 *
 * @author     Knut Kohl <github@knutkohl.de>
 * @copyright  2012-2014 Knut Kohl
 * @license    MIT License (MIT) http://opensource.org/licenses/MIT
 * @version    1.0.0
 */

$app->hook('slim.before', function() use ($app) {
    $app->menu->add('20.30','/list', 'Lists', !!$app->user, 'Shift+F5');
});

/**
 * Routes
 */
$app->get('/list(/:id)', $checkAuth, function( $id=NULL ) use ($app) {
    $app->process('Lists', 'Index', array('id' => $id));
});

$app->get('/list/:guid', $checkAuth, function( $guid ) use ($app) {
    $app->process('Lists', 'Index', array('guid' => $guid));
});
