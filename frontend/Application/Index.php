<?php
/**
 *
 * @author     Knut Kohl <github@knutkohl.de>
 * @copyright  2012-2014 Knut Kohl
 * @license    MIT License (MIT) http://opensource.org/licenses/MIT
 * @version    1.0.0
 */

$app->hook('slim.before', function() use ($app) {
    $app->menu->add('20.10', '/', 'Charts', TRUE, 'Shift+F1');
});

// Add direct links to charts only if not chart controller is the active one
$app->hook('slim.before.dispatch', function () use ($app) {
    $tblView = new ORM\View;

    if ($app->user) {
        // Private charts
        $app->menu->add('20.10.10', '#', 'private');
        foreach ($tblView->filterByPublic(0)->find() as $view) {
            $app->menu->add('20.10.10.', '/chart/'.$view->getSlug(), ':'.$view->getName());
        }

        // Public charts
        $app->menu->add('20.10.20', '#', 'public');
        foreach ($tblView->reset()->filterByPublic(1)->find() as $view) {
            $app->menu->add('20.10.20.', '/chart/'.$view->getSlug(), ':'.$view->getName());
        }
    } else {
        // Public charts only
        foreach ($tblView->filterByPublic(1)->find() as $view) {
            $app->menu->add('20.10.', '/chart/'.$view->getSlug(), ':'.$view->getName());
        }
    }
});

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
