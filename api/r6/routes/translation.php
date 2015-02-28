<?php
/**
 * Extract english texts for translation
 *
 * @author     Knut Kohl <github@knutkohl.de>
 * @copyright  2012-2014 Knut Kohl
 * @license    MIT License (MIT) http://opensource.org/licenses/MIT
 * @version    1.0.0
 */

/**
 *
 */
$api->get('/translation', function() use ($api) {

    $texts = array(array(
        'Code'    => 'Code (don\'t touch!)',
        'Content' => 'Translate this'
    ));

    $q = new DBQuery('pvlng_babelkit');
    $q->get($q->CONCAT('code_set', '"/"', 'code_code'), 'Code')
      ->get('code_desc', 'Content')
      // Native language
      ->whereEQ('code_lang', 'en')
      // Exclude administrative code sets
      ->whereNotLIKE('code_set', 'code%')
      ->order('code_set')
      ->order('code_code');

    $api->db->setBuffered();

    if ($res = $api->db->query($q)) {
        while ($row = $res->fetch_assoc()) $texts[] = $row;
        $res->close();
    }

    $api->render($texts);
})->name('get translation')->help = array(
    'since'       => 'r3',
    'description' => 'Extract english texts for translation'
);
