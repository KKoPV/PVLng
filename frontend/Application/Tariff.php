<?php
/**
 *
 * @author     Knut Kohl <github@knutkohl.de>
 * @copyright  2012-2014 Knut Kohl
 * @license    MIT License (MIT) http://opensource.org/licenses/MIT
 * @version    1.0.0
 */

$app->hook('slim.before', function() use ($app) {
    $app->menu->add('10.40', '/tariff', 'Tariffs', !!$app->user);
});

/**
 * Routes
 */
$app->get('/tariff', $checkAuth, function() use ($app) {
    $app->process('Tariff');
});

$app->get('/tariff/:id', $checkAuth, function( $id ) use ($app) {
    $app->process('Tariff', 'Show', array('id' => $id));
});

/**
 * Tariff master data
 */
$app->map('/tariff/add(/:id)', $checkAuth, function( $id=NULL ) use ($app) {
    $app->process('Tariff', 'Add', array('id' => $id));
})->via('GET', 'POST');

$app->map('/tariff/edit/:id', $checkAuth, function( $id ) use ($app) {
    $app->process('Tariff', 'Edit', array('id' => $id));
})->via('GET', 'POST');

// Delete data for given tariff & date
$app->post('/tariff/delete', $checkAuth, function() use ($app) {
    $app->process('Tariff', 'Delete');
});

/**
 * Date records
 */
$app->map('/tariff/date/add/:id(/:date)', $checkAuth, function( $id, $date=NULL ) use ($app) {
    // Add/clone date set
    $app->process('Tariff', 'AddDate', array('id' => $id, 'date' => $date));
})->via('GET', 'POST');

$app->map('/tariff/date/edit/:id/:date', $checkAuth, function( $id, $date=NULL ) use ($app) {
    // Edit data of one date of a tariff
    $app->process('Tariff', 'EditDate', array('id' => $id, 'date' => $date));
})->via('GET', 'POST');

// Delete data for given tariff & date
$app->post('/tariff/date/delete', $checkAuth, function() use ($app) {
        $app->process('Tariff', 'DeleteDate');
});
