<?php
/**
 * Extract english texts for translation
 *
 * @author       Knut Kohl <github@knutkohl.de>
 * @copyright    2012-2014 Knut Kohl
 * @license      GNU General Public License http://www.gnu.org/licenses/gpl.txt
 * @version      1.0.0
 */

/**
 *
 */
$api->get('/translation', function() use ($api) {

    $texts = array(array(
        'Code'    => 'Code (don\'t touch)',
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

    $api->db->Buffered = TRUE;

    if ($res = $api->db->query($q)) {
        while ($row = $res->fetch_assoc()) $texts[] = $row;
        $res->close();
    }

    $api->render($texts);
})->name('get translation')->help = array(
    'since'       => 'v3',
    'description' => 'Extract english texts for translation'
);
