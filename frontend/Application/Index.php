<?php
/**
 *
 * @author     Knut Kohl <github@knutkohl.de>
 * @copyright  2012-2014 Knut Kohl
 * @license    MIT License (MIT) http://opensource.org/licenses/MIT
 * @version    1.0.0
 */

PVLng::Menu(
    'index', 10, '/',
    I18N::translate('Charts'),
    I18N::translate('ChartHint') . ' (Shift+F1)'
);

/**
 * Routes
 */
// User check is done inside controller, only save and delete needs login!
$app->map('/', function() use ($app) {
    $app->process();
})->via('GET', 'POST');

$app->map('/index', function() use ($app) {
    $app->process();
})->via('GET', 'POST');

$app->get('/chart/:slug', function( $slug ) use ($app) {
    // Merge chart and GET parameters
    $app->redirect('/?' . http_build_query(array_merge(
        array('chart' => $slug),
        $app->request->get()
    )));
});
