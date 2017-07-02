<?php
/**
 *
 * @author     Knut Kohl <github@knutkohl.de>
 * @copyright  2012-2014 Knut Kohl
 * @license    MIT License (MIT) http://opensource.org/licenses/MIT
 * @version    1.0.0
 */

if (file_exists(Core\PVLng::pathRoot('config', 'description.md'))) {
    // Add route only if description file exists
    $app->hook('slim.before', function () use ($app) {
        $app->menu->add(80, '/description', 'Description', true, 'Shift+F7');
    });

    $app->get('/description', function () use ($app) {
        $app->process('Description');
    });
} else {
    $app->hook('slim.before', function () use ($app) {
        $app->menu->add(
            80, '#', 'Description', true,
            'Please create "config/description.md" first,<br />see "config/description.md.dist" for reference'
        );
    });
}
