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
 *
 */
PVLng::Menu(
    'weather', 80, '/weather',
    I18N::translate('Weather'),
    I18N::translate('WeatherForecast')
);

/**
 * Routes
 */
$app->get('/weather', function() use ($app) {
    $app->process('Weather');
});
