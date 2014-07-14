<?php

/**
 * Remove a node from channel tree
 */
$api->delete('/tree/:id', $APIkeyRequired, function($id) use ($api) {
    if (Channel::ById($id)->removeFromTree()) {
        $api->halt(204);
    } else {
        $api->stopAPI('Unable to delete node '.$node);
    }
})->name('Delete channel from channel tree')->help = array(
    'since'       => 'r4',
    'description' => 'Delete channel from channel tree',
    'apikey'      => TRUE
);
