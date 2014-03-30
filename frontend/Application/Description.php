<?php
/**
 *
 * @author     Knut Kohl <github@knutkohl.de>
 * @copyright  2012-2014 Knut Kohl
 * @license    MIT License (MIT) http://opensource.org/licenses/MIT
 * @version    1.0.0
 */

/**
 * Add menu and route only if description file exists
 */
if (!file_exists(ROOT_DIR . DS . 'description.md')) return;

PVLng::Menu(array(
    'position' => 70,
    'label'    => I18N::translate('Description'),
    'hint'     => I18N::translate('PlantDescriptionHint') . ' (Shift+F7)',
    'route'    => '/description',
));

/**
 * Route
 */
$app->get('/description', function() use ($app) {
    $app->process('Description');
});
