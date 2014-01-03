<?php

file_exists('.disabled') AND die('Adminer was disabled on this system.');

function adminer_object() {
    foreach (glob('plugins/*.php') as $filename) include_once $filename;

    $plugins = array(
        new AdminerJsonColumn,
    );

    return new AdminerPlugin($plugins);
}

include 'adminer.php';