<?php
/**
 * PVLng - PhotoVoltaic Logger new generation
 *
 * @link       https://github.com/KKoPV/PVLng
 * @link       https://pvlng.com/
 * @author     Knut Kohl <github@knutkohl.de>
 * @copyright  2012 Knut Kohl
 * @license    MIT License (MIT) http://opensource.org/licenses/MIT
 */
$app->hook('slim.before', function () use ($app) {
    $app->menu->add('20.90', '#', '---');
    $app->menu->add('20.91', '/infoframe', 'Infoframe');
});

/**
 * Routes
 */
$app->get(
    '/infoframe',
    function () use ($app) {
        $app->process('Infoframe', 'Index', array('frame' => 'default'));
    }
);

/**
 * Custom frames
 */
$app->get(
    '/infoframe/:frame',
    function ($frame) use ($app) {
        $app->process('Infoframe', 'Index', array('frame' => Core\PVLng::path('custom', $frame)));
    }
);
