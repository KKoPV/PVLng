<?php
/**
 *
 *
 * @author     Knut Kohl <github@knutkohl.de>
 * @copyright  2012-2014 Knut Kohl
 * @license    MIT License (MIT) http://opensource.org/licenses/MIT
 * @version    1.0.0
 */

/**
 * Helper function
 */
$checkTariffId = function(Slim\Route $route) use ($api) {
    $id = $route->getParam('id');
    if (!(new ORM\Tariff($id))->getId()) {
        $api->stopAPI('Unknown tariff Id: '.$id);
    }
};

/**
 *
 */
$api->get('/tariff', $APIkeyRequired, function() use ($api) {
    $result = array();

    foreach (new ORM\Tariff as $row) {
        $result[$row->getId()] = array(
            'name'    => $row->getName(),
            'comment' => $row->getComment()
        );
    }

    $api->render($result);
})->name('get tariffs')->help = array(
    'since'       => 'r4',
    'description' => 'Extract all tariffs',
    'apikey'      => TRUE,
);

/**
 *
 */
$api->get('/tariff/:id', $APIkeyRequired, $checkTariffId, function( $id ) use ($api) {
    $result = array();

    $tbl = new ORM\TariffView;
    foreach ($tbl->findMany('id', $id) as $row) {
        $result[] = array(
            'name'           => $row->getName(),
            'comment'        => $row->getTariffComment(),
            'date'           => $row->getDate(),
            'cost'           => $row->getCost(),
            'time'           => $row->getTime(),
            'days'           => $row->getDays(),
            'tariff'         => $row->getTariff(),
            'tarrif_comment' => $row->getTariffComment()
        );
    }

    $api->render($result);
})->name('get tariff')->help = array(
    'since'       => 'r4',
    'description' => 'Extract a tariff',
    'apikey'      => TRUE,
);

/**
 *
 */
$api->get('/tariff/:id/:date', $APIkeyRequired, $checkTariffId, function( $id, $date ) use ($api) {
    $api->render((new ORM\Tariff($id))->getTariffDay(strtotime($date), ORM\Tariff::DAY));
})->conditions(array(
    'date' => '[0-9]{4}-[0-9]{2}-[0-9]{2}'
))->name('get tariffs for a day')->help = array(
    'since'       => 'r4',
    'description' => 'Extract tariff for a day',
    'apikey'      => TRUE,
);

/**
 *
 */
$api->get('/tariff/:id/time/:date(/:to)', $APIkeyRequired, $checkTariffId, function( $id, $date, $to=NULL ) use ($api) {
    $api->render((new ORM\Tariff($id))->getTariffTimes(strtotime($date), $to ? strtotime($to) : $to));
})->conditions(array(
    'date' => '[0-9]{4}-[0-9]{2}-[0-9]{2}',
    'to'   => '[0-9]{4}-[0-9]{2}-[0-9]{2}'
))->name('get tariffs for a range of days')->help = array(
    'since'       => 'r4',
    'description' => 'Extract tariff for a day',
    'apikey'      => TRUE,
);
