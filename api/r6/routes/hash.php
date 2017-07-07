<?php
/**
 * PVLng - PhotoVoltaic Logger new generation
 *
 * @link       https://github.com/KKoPV/PVLng
 * @link       https://pvlng.com/
 * @author     Knut Kohl <github@knutkohl.de>
 * @copyright  2012 Knut Kohl
 * @license    MIT License (MIT) http://opensource.org/licenses/MIT
 */

/**
 *
 */
$api->map(
    '/hash',
    function () use ($api) {
        $text = $api->request->params('text');
        $md5  = md5($text);
        $slug = Core\Slug::encode($text);

        $api->render(array(
        'text'   => $text,
        'hash'   => substr($md5, 0, 8),
        'md5'    => $md5,
        'sha1'   => sha1($text),
        'slug'   => $slug,
        'unslug' => Core\Slug::decode($slug)
        ));
    }
)
->via('GET', 'POST')
->name('hash')
->help = array(
    'since'       => 'r3',
    'description' => 'Create MD5 and SHA1 hashes and a slug for the given text',
    'payload'  => array(
        'text' => 'Text to make hashes for'
    )
);
