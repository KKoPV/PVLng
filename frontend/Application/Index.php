<?php
/**
 *
 * @author     Knut Kohl <github@knutkohl.de>
 * @copyright  2012-2014 Knut Kohl
 * @license    MIT License (MIT) http://opensource.org/licenses/MIT
 * @version    1.0.0
 */

/**
 *
 */
PVLng::Menu('20.10', '/', __('Charts'), 'Shift+F1');

// Add direct links to charts only if not chart controller is the active one

$RequestPath = $app->request()->getPathInfo();

if ($RequestPath != '/' AND !strstr($RequestPath, '/index')) {

    $tblView = new ORM\View;

    if (Session::get('user')) {
        PVLng::Menu('20.10.10', '#', __('private'));
        foreach ($tblView->filterByPublic(0)->find() as $view) {
            PVLng::Menu('20.10.10.', '/chart/'.$view->getSlug(), $view->getName());
        }
        $tblView->reset();
    }

    PVLng::Menu('20.10.20', '#', __('public'));

    foreach ($tblView->filterByPublic(1)->find() as $view) {
        PVLng::Menu('20.10.20.', '/chart/'.$view->getSlug(), $view->getName());
    }
}

unset($RequestPath);

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
