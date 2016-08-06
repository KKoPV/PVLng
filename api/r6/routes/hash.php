<?php
/**
 * Hash function
 *
 * @author     Knut Kohl <github@knutkohl.de>
 * @copyright  2012-2014 Knut Kohl
 * @license    MIT License (MIT) http://opensource.org/licenses/MIT
 * @version    1.0.0
 */

/**
 *
 */
$api->map('/hash', function() use ($api) {

    $text = $api->request->params('text');
    $slug = Slug::encode($text);

    $api->render(array(
        'text' => $text,
        'hash' => substr(md5($text), 0, 8),
        'md5'  => md5($text),
        'sha1' => sha1($text),
        'slug' => $slug,
        'unslug' => Slug::decode($slug)
    ));

})->via('GET', 'POST')->name('hash')->help = array(
    'since'       => 'r3',
    'description' => 'Create MD5 and SHA1 hashes and a slug for the given text',
    'payload'  => array(
        'text' => 'Text to make hashes for'
    )
);
