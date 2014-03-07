<?php
/**
 *
 */
PVLng::Menu(array(
    'position' => 30,
    'label'    => I18N::translate('List'),
    'hint'     => I18N::translate('ListHint') . ' (Shift+F3)',
    'route'    => '/list',
    'login'    => TRUE
));

/**
 * Routes
 */
$app->get('/list(/:id)', $checkAuth, function( $id=NULL ) use ($app) {
    $app->params->set('id', $id);
    $app->process('Lists');
});
