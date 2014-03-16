<?php
/**
 * Routes
 */
$app->get('/widget.inc.js', function() use ($app) {
    $app->showStats = FALSE;
    $app->process('Widget', 'Inc');
});

$app->get('/widget.js', function() use ($app) {
    $app->showStats = FALSE;
    $app->process('Widget', 'Chart');
});
