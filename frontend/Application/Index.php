<?php
/**
 *
 * @author     Knut Kohl <github@knutkohl.de>
 * @copyright  2012-2014 Knut Kohl
 * @license    MIT License (MIT) http://opensource.org/licenses/MIT
 * @version    1.0.0
 */

PVLng::Menu(array(
    'position' => 10,
    'label'    => I18N::translate('Charts'),
    'hint'     => I18N::translate('ChartHint') . ' (Shift+F1)',
    'route'    => '/'
));

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

$app->get('/index(/:view)', function( $view='' ) use ($app) {
    // Put chart name at the begin
    $params = array_merge(
        array('chart' => $view),
        $app->request->get()
    );
    $app->redirect('/?' . http_build_query($params));
});

$app->get('/chart/:view', function( $view ) use ($app) {
    // Put chart name at the begin
    $params = array_merge(
        array('chart' => $view),
        $app->request->get()
    );
    $app->redirect('/?' . http_build_query($params));
});
