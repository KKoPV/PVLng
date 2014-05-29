<?php

$api->hook('slim.before.dispatch', function() use ($api) {
    if (!extension_loaded ('newrelic')) return;
    if (preg_match('~^/([^/]+)/?~', $api->request->getResourceUri(), $args)) {
        newrelic_name_transaction($api->request->getMethod() . '/' . $args[1]);
    } else {
        newrelic_name_transaction($api->request->getMethod());
    }
});

$api->get('/ping', function() use ($api) {
    $api->contentType('text/plain');
    $api->halt(200, 'pong');
})->name('ping')->help = array(
    'since'       => 'r4',
    'description' => 'For new relic pinger',
);
