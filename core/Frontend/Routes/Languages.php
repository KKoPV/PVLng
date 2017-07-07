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
    $app->languages->add(0, 'en', 'English', 'gb');
    $app->languages->add(10, 'de', 'Deutsch');
});
