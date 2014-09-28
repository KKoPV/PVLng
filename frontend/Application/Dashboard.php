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
PVLng::Menu('20.20', '/dashboard', __('Dashboards'), 'Shift+F2');

/**
 *
 */
$tblDashboard = new ORM\Dashboard;
$user = Session::get('user');
foreach ($tblDashboard->order('name')->find() as $dashboard) {
    if ($user || $dashboard->getPublic()) {
        PVLng::Menu('20.20.', '/dashboard/'.$dashboard->getSlug(), $dashboard->getName());
    }
}

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
