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
$api->get(
    '/translation',
    function () use ($api) {
        $texts = array(array(
            'Code'    => 'Code (don\'t touch!)',
            'Content' => 'Translate this'
        ));

        $q = new DBQuery('pvlng_babelkit');

        $q->get($q->CONCAT('code_set', '"/"', 'code_code'), 'Code')
          ->get('code_desc', 'Content')
          ->whereEQ('code_lang', 'en')
          // Exclude administrative code sets
          ->whereNotLIKE('code_set', 'code%');

        $api->db->setBuffered();

        if ($res = $api->db->query($q)) {
            while ($row = $res->fetch_assoc()) {
                $texts[] = $row;
            }
            $res->close();
        }

        $api->render($texts);
    }
)
->name('GET /translation')
->help = array(
    'since'       => 'r3',
    'description' => 'Extract english texts for translation'
);

/**
 *
 */
$api->get(
    '/translate/:language(/:set)',
    function ($language, $set = null) use ($api) {
        $q = new DBQuery('pvlng_babelkit');

        if ($set) {
            $q->get('code_code', 'code')
              ->whereEQ('code_set', $set);
        } else {
            $q->get($q->CONCAT('code_set', '"/"', 'code_code'), 'code')
            // Exclude administrative code sets
              ->whereNotLIKE('code_set', 'code%');
        }

        $q->get('code_desc')->whereEQ('code_lang', $language);

        $api->db->setBuffered();

        $texts = array();

        if ($res = $api->db->query($q)) {
            while ($row = $res->fetch_object()) {
                $texts[$row->code] = $row->code_desc;
            }
            $res->close();
        }

        $api->render($texts);
    }
)
->name('GET /translate/:language(/:set)')
->help = array(
    'since'       => 'r6',
    'description' => 'Extract translations'
);

/**
 *
 */
$api->get(
    '/languages',
    function () use ($api) {
        $q = new DBQuery('pvlng_babelkit');

        $q->get('DISTINCT code_lang');

        $languages = array();

        if ($res = $api->db->query($q)) {
            while ($row = $res->fetch_object()) {
                $languages[] = $row->code_lang;
            }
        }

        $api->render($languages);
    }
)
->name('GET languages')
->help = array(
    'since'       => 'r6',
    'description' => 'Get available languages'
);
