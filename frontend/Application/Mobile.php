<?php
/**
 * Routes
 */
$app->get('/m', function() use ($app) {
    $app->process('Mobile');
});
