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
PVLng::Menu(array(
    'position' => 60,
    'label'    => I18N::translate('Information'),
    'hint'     => I18N::translate('InfoHint') . ' (Shift+F6)',
    'route'    => '/info'
));

/**
 * Routes
 */
$app->map('/info', $checkAuth, function() use ($app) {
    $app->process('Info');
})->via('GET', 'POST');
