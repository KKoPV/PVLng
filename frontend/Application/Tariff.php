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
 * Routes
 */
$app->get('/tariff', $checkAuth, function() use ($app) {
    $app->process('Tariff');
});

$app->get('/tariff/:id', $checkAuth, function( $id ) use ($app) {
    $app->process('Tariff', 'Show', array('id' => $id));
})->conditions(array(
    'id' => '\d+'
));

/**
 * Tariff master data
 */
$app->map('/tariff/add(/:id)', $checkAuth, function( $id=NULL ) use ($app) {
    $app->process('Tariff', 'Add', array('id' => $id));
})->conditions(array(
    'id' => '\d+'
))->via('GET', 'POST');

$app->map('/tariff/edit/:id', $checkAuth, function( $id ) use ($app) {
    $app->process('Tariff', 'Edit', array('id' => $id));
})->conditions(array(
    'id' => '\d+'
))->via('GET', 'POST');

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
})->conditions(array(
    'id' => '\d+'
))->via('GET', 'POST');

$app->map('/tariff/date/edit/:id/:date', $checkAuth, function( $id, $date=NULL ) use ($app) {
    // Edit data of one date of a tariff
    $app->process('Tariff', 'EditDate', array('id' => $id, 'date' => $date));
})->conditions(array(
    'id' => '\d+'
))->via('GET', 'POST');

// Delete data for given tariff & date
$app->post('/tariff/date/delete', $checkAuth, function() use ($app) {
        $app->process('Tariff', 'DeleteDate');
});
