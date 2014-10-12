<?php
/**
 *
 * @author     Knut Kohl <github@knutkohl.de>
 * @copyright  2012-2014 Knut Kohl
 * @license    MIT License (MIT) http://opensource.org/licenses/MIT
 * @version    1.0.0
 */

$app->hook('slim.before', function() use ($app) {
    $app->menu->add('20.20', '/dashboard', 'Dashboards', TRUE, 'Shift+F2');

    $tblDashboard = new ORM\Dashboard;
    foreach ($tblDashboard->order('name')->find() as $dashboard) {
        if ($app->user || $dashboard->getPublic()) {
            $app->menu->add('20.20.', '/dashboard/'.$dashboard->getSlug(), ':'.$dashboard->getName());
        }
    }
});

/**
 * Routes
 */
$app->get('/dashboard(/:slug)', function( $slug=NULL ) use ($app) {
    $app->process('Dashboard', 'Index', array('slug' => $slug));
});

$app->post('/dashboard', $checkAuth, function() use ($app) {
    $app->process('Dashboard');
});

/**
 * Embedded mode
 */
$app->get('/dashboard/embed/:slug', function( $slug ) use ($app) {
    $app->process('Dashboard', 'IndexEmbedded', array('slug' => $slug));
})->Module = 'Dashboard';

// Just a shorter alias for /dashboard/embed
$app->get('/ed/:slug', function( $slug ) use ($app) {
    $app->redirect('/dashboard/embed/'.$slug, 302);
})->Module = 'Dashboard';
