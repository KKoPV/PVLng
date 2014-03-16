<?php
/**
 *
 */
PVLng::Menu(array(
    'position' => 70,
    'label'    => I18N::translate('Description'),
    'hint'     => I18N::translate('PlantDescriptionHint') . ' (Shift+F7)',
    'route'    => '/description',
));

/**
 * Routes
 */
$app->get('/description', function() use ($app) {
    $app->process('Description');
});
