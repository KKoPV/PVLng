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

PVLng::Menu(
    'description', 70, '/description',
    I18N::translate('Description'),
    I18N::translate('PlantDescriptionHint') . ' (Shift+F6)'
);

/**
 * Route
 */
$app->get('/description', function() use ($app) {
    $app->process('Description');
});
