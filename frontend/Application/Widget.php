<?php
/**
 * Routes
 *
 * @author     Knut Kohl <github@knutkohl.de>
 * @copyright  2012-2014 Knut Kohl
 * @license    MIT License (MIT) http://opensource.org/licenses/MIT
 * @version    1.0.0
 */

$app->get('/widget.inc.js', function() use ($app) {
    $app->showStats = FALSE;
    $app->process('Widget', 'Inc');
});

$app->get('/widget.js', function() use ($app) {
    $app->showStats = FALSE;
    $app->process('Widget', 'Chart');
});
