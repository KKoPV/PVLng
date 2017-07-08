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
 * BabelKit wrapper for plain MySQLi
 */

#
# Interface to a Universal Multilingual Code Table.
#
# Copyright (C) 2003 John Gorman <jgorman@webbysoft.com>
# http://www.webbysoft.com/babelkit
#
### Public methods:
#
# $bk = new BabelKit($param=[]);
#   'db'    => $InstanceOfMySQLi
#   'table' => 'bk_code'
#
### Get code descriptions safe for HTML display:
#
# $str = $bk->desc(   $code_set, $code_lang, $code_code);
# $str = $bk->ucfirst($code_set, $code_lang, $code_code);
# $str = $bk->ucwords($code_set, $code_lang, $code_code);
# list($code_desc, $code_order, $code_flag) = $bk->get($code_set, $code_lang, $code_code);
#
### Get code descriptions not safe for HTML display:
#
# $str = $bk->render($code_set, $code_lang, $code_code);
# $str = $bk->data(  $code_set, $code_lang, $code_code);
# $str = $bk->param( $code_set, $code_code)
#
### HTML select common options:
#  'var_name'      => 'start_day'
#  'value'         => $start_day
#  'default'       => 1
#  'subset'        => array( 1, 2, 3, 4, 5 )
#  'options'       => 'onchange="submit()"'
#
### HTML select single value methods:
#
# $str = $bk->select($code_set, $code_lang, $param=[]);
#  'select_prompt' => "Code set description?"
#  'blank_prompt'  => "None"
#
# $str = $bk->radio($code_set, $code_lang, $param=[]);
#  'blank_prompt'  => "None"
#  'sep'           => "<br>\n"
#
### HTML select multiple value methods:
#
# $str = $bk->multiple($code_set, $code_lang, $param=[]);
#  'size'          => 10
#
# $str = $bk->checkbox($code_set, $code_lang, $param=[]);
#  'sep'           => "<br>\n"
#
### Code sets:
#
# $rows = $bk->languageSet($code_set, $code_lang);
# $rows = $bk->fullSet($code_set, $code_lang);
# $rows = $bk->fullSetAssoc($code_set, $code_lang);
#
### Code table updates:
#
# $bk->slave($code_set, $code_code, $code_desc);
#
# $bk->remove($code_set, $code_code);
#
# $bk->put($code_set, $code_lang, $code_code, $code_desc, $code_order = 0, $code_flag = '');
#

/**
 *
 */
namespace Core;

/**
 *
 */
use Exception;

/**
 *
 */
class BabelKit
{
    /**
     *
     */
    public $native;

    /**
     *
     */
    public function __construct(array $params)
    {
        if (empty($params['db'])) {
            throw new Exception('BabelKit: Missing database parameter.');
        }

        $this->db     = $params['db'];
        $this->table  = isset($params['table']) ? $params['table'] : 'bk_code';
        $this->cache  = isset($params['cache']) ? $params['cache'] : false;

        foreach ($this->query as &$query) {
            // Replace {TABLE} placeholder}
            $query = str_replace('{TABLE}', $this->table, $query);
        }

        $this->native = $this->findNativeLanguage();

        if (!$this->native && $this->cache) {
            // Uncleared cache?
            $this->cache->flush();
            // Check again
            $this->native = $this->findNativeLanguage();
        }

        if (!$this->native) {
            throw new Exception(
                "BabelKit: Unable to determine native language. " .
                "Check table '$this->table' for code_admin/code_admin record."
            );
        }
    }

    /**
     * Get a code description safe for html display.
     * Fill missing translations with the native desc or code.
     */
    public function desc($code_set, $code_lang, $code_code)
    {
        return htmlspecialchars(
            $this->render($code_set, $code_lang, $code_code)
        );
    }

    /**
     * Get a code description with the First letter capitalized.
     */
    public function ucfirst($code_set, $code_lang, $code_code)
    {
        return ucfirst($this->desc($code_set, $code_lang, $code_code));
    }

    /**
     * Get a code description with Each Word Capitalized.
     */
    public function ucwords($code_set, $code_lang, $code_code)
    {
        return ucwords($this->desc($code_set, $code_lang, $code_code));
    }

    /**
     * Get a raw code description *not* safe for html display.
     * Fill missing translations with the native desc or code.
     */
    public function render($code_set, $code_lang, $code_code)
    {
        $code_desc = $this->data($code_set, $code_lang, $code_code);

        if ($code_desc == '') {
            $code_desc = $this->data($code_set, $this->native, $code_code);
            if ($code_desc == '') {
                $code_desc = $code_code;
            }
        }

        return $code_desc;
    }

    /**
     * Get a raw code_desc, *not* safe for html display
     *
     * Buffer request results
     */
    public function data($code_set, $code_lang, $code_code)
    {
        if ($code_code == '') {
            return '';
        }

        $result = $this->fetch(
            $this->cacheKey($code_set, $code_lang, $code_code),
            $this->sql($this->query['data'], $code_set, $code_lang, $code_code)
        );

        return isset($result[0][0]) ? $result[0][0] : '';
    }

    /**
     * Get a raw config parameter (native).
     */
    public function param($code_set, $code_code)
    {
        return $this->data($code_set, $this->native, $code_code);
    }

    /**
     * Create an html form selection dropdown from a code set.
     */
    public function select($code_set, $code_lang, $param = [])
    {
        $param = array_merge(
            [
                'var_name'      => null,
                'id'            => null,
                'value'         => null,
                'default'       => null,
                'subset'        => null,
                'options'       => null,
                'select_prompt' => null,
                'blank_prompt'  => null
            ],
            $param
        );

        extract($param);

        // Variable name.
        if (!$var_name) {
            $var_name = $code_set;
        }
        if (!$id) {
            $id = $code_set;
        }

        if (!isset($value)) {
            $value = $default;
        }
        $Subset = [];
        if (is_array($subset)) {
            foreach ($subset as $val) {
                $Subset[$val] = 1;
            }
        }
        if ($options) {
            $options = " $options";
        }

        // Drop down box.
        $select = "<select id='$id' name='$var_name'$options>";

        // Blank options.
        $selected = '';
        if ($value == '') {
            if ($select_prompt == '') {
                $select_prompt =
                    $this->ucwords('code_set', $code_lang, $code_set).'?';
            }
            $select .= "<option value='' selected>$select_prompt</option>";
            $selected = 1;
        } elseif ($blank_prompt <> '') {
            $select .= "<option value=''>$blank_prompt</option>";
        }

        // Show code set options.
        $optgroup = false;
        $set_list = $this->fullSet($code_set, $code_lang);

        foreach ($set_list as $row) {
            list($code_code, $code_desc) = $row;
            if (!empty($Subset) && !isset($Subset[$code_code]) && $code_code <> $value) {
                continue;
            }
            $code_desc = htmlspecialchars(ucfirst($code_desc));

            if (preg_match('~^::(.*?)::$~', $code_desc, $args)) {
                if ($optgroup) {
                    $select .= "</optgroup>";
                }
                $select .= '<optgroup label="'.$args[1].'">';
                $optgroup = true;
            } else {
                if ($code_code == $value) {
                    $selected = 1;
                    $select .= "<option value='$code_code' selected>$code_desc</option>";
                } elseif ($row[3] <> 'd') {
                    $select .= "<option value='$code_code'>$code_desc</option>";
                }
            }
        }
        if ($optgroup) {
            $select .= "</optgroup>";
        }

        // Show a missing value.
        if (!$selected) {
            $select .= "<option value='$value' selected>$value</option>";
        }

        $select .= "</select>";

        return $select;
    }

    /**
     * Create an html form radio box from a code set.
     */
    public function radio($code_set, $code_lang, $param = [])
    {
        $param = array_merge(
            [
                'var_name'      => null,
                'value'         => null,
                'default'       => null,
                'subset'        => null,
                'options'       => null,
                'blank_prompt'  => null,
                'sep'           => "<br>\n"
            ],
            $param
        );

        extract($param);

        // Variable name.
        if (!$var_name) {
            $var_name = $code_set;
        }

        if (!isset($value)) {
            $value = $default;
        }
        if (is_array($subset)) {
            $Subset = [];
            foreach ($subset as $val) {
                $Subset[$val] = 1;
            }
        }
        if ($options) {
            $options = " $options";
        }

        // Blank options.
        if ($value == '') {
            $selected = 1;
            if ($blank_prompt <> '') {
                $select .= "<input type='radio' name='$var_name'$options";
                $select .= " value='' checked>$blank_prompt";
            }
        } else {
            if ($blank_prompt <> '') {
                $select .= "<input type='radio' name='$var_name'$options";
                $select .= " value=''>$blank_prompt";
            }
        }

        // Show code set options.
        $set_list = $this->fullSet($code_set, $code_lang);

        foreach ($set_list as $row) {
            list($code_code, $code_desc) = $row;
            if ($Subset && !$Subset[$code_code] && $code_code <> $value) {
                continue;
            }
            $code_desc = htmlspecialchars(ucfirst($code_desc));
            if ($code_code == $value) {
                if ($select) {
                    $select .= $sep;
                }
                $selected = 1;
                $select .= "<input type='radio' name='$var_name'$options";
                $select .= " value='$code_code' checked>$code_desc";
            } elseif ($row[3] <> 'd') {
                if ($select) {
                    $select .= $sep;
                }
                $select .= "<input type='radio' name='$var_name'$options";
                $select .= " value='$code_code'>$code_desc";
            }
        }

        // Show missing values.
        if (!$selected) {
            if ($select) {
                $select .= $sep;
            }
            $select .= "<input type='radio' name='$var_name'$options";
            $select .= " value='$value' checked>$value";
        }

        return $select;
    }

    /**
     * Create an html form multiple select box from a code set.
     */
    public function multiple($code_set, $code_lang, $param = [])
    {
        $param = array_merge(
            [
                'var_name'      => $code_set,
                'id'            => $code_set,
                'value'         => null,
                'default'       => null,
                'subset'        => null,
                'options'       => null,
                'size'          => null
            ],
            $param
        );

        extract($param);

        if (!isset($value)) {
            $value = $default;
        }

        $values = [];
        if (is_array($value)) {
            foreach ($value as $val) {
                $values[$val] = 1;
            }
        } elseif ($value <> '') {
            $values[$value] = 1;
        }

        if (is_array($subset)) {
            $Subset = [];
            foreach ($subset as $val) {
                $Subset[$val] = 1;
            }
        }

        // Select multiple box.
        $select = "<select id='$id' name='$var_name"."[]'";
        if ($size) {
            $select .= " size='$size'";
        }
        $select .= "multiple $options>";

        // Show code set options.
        $set_list = $this->fullSet($code_set, $code_lang);
        foreach ($set_list as $row) {
            list($code_code, $code_desc) = $row;
            if ($Subset && !$Subset[$code_code] && !$values[$code_code]) {
                continue;
            }
            $code_desc = htmlspecialchars(ucfirst($code_desc));
            if ($values[$code_code]) {
                $select .= "<option value='$code_code' selected>$code_desc";
                unset($values[$code_code]);
            } elseif ($row[3] <> 'd') {
                $select .= "<option value='$code_code'>$code_desc";
            }
        }

        // Show missing values.
        foreach ($values as $code_code => $true) {
            $select .= "<option value='$code_code' selected>$code_code";
        }

        $select .= "</select>";

        return $select;
    }

    /**
     * Create an html form checkbox from a code set.
     */
    public function checkbox($code_set, $code_lang, $param = [])
    {
        $param = array_merge(
            [
                'var_name'      => $code_set,
                'value'         => null,
                'default'       => null,
                'subset'        => null,
                'options'       => null,
                'sep'           => "<br>\n"
            ],
            $param
        );

        extract($param);

        if (!isset($value)) {
            $value = $default;
        }
        $values = [];
        if (is_array($value)) {
            foreach ($value as $val) {
                $values[$val] = 1;
            }
        } elseif ($value <> '') {
            $values[$value] = 1;
        }

        if (is_array($subset)) {
            $Subset = [];
            foreach ($subset as $val) {
                $Subset[$val] = 1;
            }
        }

        if ($options) {
            $options = " $options";
        }

        // Show code set options.
        $set_list = $this->fullSet($code_set, $code_lang);

        foreach ($set_list as $row) {
            list($code_code, $code_desc) = $row;
            if ($Subset && !$Subset[$code_code] && !$values[$code_code]) {
                continue;
            }
            $code_desc = htmlspecialchars(ucfirst($code_desc));
            if ($values[$code_code]) {
                if ($select) {
                    $select .= $sep;
                }
                $select .= "<input type='checkbox' name='$var_name"."[]'";
                $select .= "$options value='$code_code' checked>$code_desc";
                unset($values[$code_code]);
            } elseif ($row[3] <> 'd') {
                if ($select) {
                    $select .= $sep;
                }
                $select .= "<input type='checkbox' name='$var_name"."[]'";
                $select .= "$options value='$code_code'>$code_desc";
            }
        }

        // Show missing values.
        foreach ($values as $code_code => $true) {
            if ($select) {
                $select .= $sep;
            }
            $select .= "<input type='checkbox' name='$var_name"."[]'";
            $select .= "$options value='$code_code' checked>$code_code";
        }

        return $select;
    }

    /**
     * Get a language set array
     *
     * Buffer request results
     */
    public function languageSet($code_set, $code_lang)
    {
        return $this->fetch(
            $this->cacheKey($code_set, $code_lang),
            $this->sql($this->query['languageSet'], $code_set, $code_lang)
        );
    }

    /**
     * Get a full language set with missing translations in native.
     */
    public function fullSet($code_set, $code_lang)
    {
        $data = $this->languageSet($code_set, $this->native);

        if ($code_lang != $this->native) {
            $other  = $this->languageSet($code_set, $code_lang);
            $lookup = [];

            foreach ($other as $row) {
                $lookup[$row[0]] = $row[1];
            }

            foreach ($data as $ord => $row) {
                if (isset($lookup[$row[0]])) {
                    $data[$ord][1] = $lookup[$row[0]];
                }
            }
        }

        return $data;
    }

    /**
     * Get a language set array
     *
     * Buffer request results
     */
    public function fullSetAssoc($code_set, $code_lang)
    {
        $data = [];

        if ($this->cache) {
            while ($this->cache->save(
                $this->cacheKey($code_set, $code_lang, 'assoc'),
                $data
            )) {
                foreach ($this->fullSet($code_set, $code_lang) as $row) {
                    $name = array_shift($row);
                    $data[$name] = $row;
                }
            }
        } else {
            foreach ($this->fullSet($code_set, $code_lang) as $row) {
                $name = array_shift($row);
                $data[$name] = $row;
            }
        }

        return $data;
    }

    /**
     * Add or update a slave code native description.
     */
    public function slave($code_set, $code_code, $code_desc)
    {
        $old = $this->get($code_set, $this->native, $code_code);

        if ($old) {
            list($old_desc, $old_order, $old_flag ) = $old;
            if ($code_desc <> $old_desc) {
                $this->put($code_set, $this->native, $code_code, $code_desc, $old_order, $old_flag);
            }
        } else {
            $this->put($code_set, $this->native, $code_code, $code_desc);
        }
    }

    /**
     * Remove a code completely.
     */
    public function remove($code_set, $code_code)
    {
        $this->doQuery($this->query['remove_1'], $code_set, $code_code);

        // Delete whole code_set?
        if ($code_set == 'code_set') {
            // Remove code_admin entry
            $this->doQuery($this->query['remove_2'], $code_code);
            // Remove remaining codes
            $this->doQuery($this->query['remove_3'], $code_code);
        }
    }

    /**
     * Get code desc, order, and flag
     *
     * Buffer request results
     */
    public function get($code_set, $code_lang, $code_code)
    {
        if ($code_code == '') {
            return '';
        }

        $result = $this->fetch(
            $this->cacheKey('full', $code_set, $code_lang, $code_code),
            $this->sql($this->query['get'], $code_set, $code_lang, $code_code)
        );

        return isset($result[0]) ? $result[0] : '';
    }

    /**
     * Put a code.  Insert, update or delete as appropriate.
     */
    public function put($code_set, $code_lang, $code_code, $code_desc, $code_order = 0, $code_flag = '')
    {
        // Get the existing code info, if any.
        $old = $this->get($code_set, $code_lang, $code_code);

        // Field work.
        if ($code_lang == $this->native) {
            if (!$old and is_numeric($code_code) and
                ( is_null($code_order) or $code_order === '' ) ) {
                $code_order = $code_code;
            }
            $code_order = (int)$code_order;
        } else {
            $code_order = 0;
            $code_flag = '';
        }

        // Make it so: add, update, or delete.
        if ($old) {
            list($old_desc, $old_order, $old_flag) = $old;
            if ($code_desc <> '') {
                if ($code_desc <> $old_desc || $code_order <> $old_order || $code_flag <> $old_flag) {
                    $this->doQuery(
                        $this->query['update'],
                        $code_desc,
                        $code_order,
                        $code_flag,
                        $code_set,
                        $code_lang,
                        $code_code
                    );
                }
            } else {
                if ($code_lang == $this->native) {
                    $this->remove($code_set, $code_code);
                } else {
                    $this->doQuery(
                        $this->query['delete'],
                        $code_set,
                        $code_lang,
                        $code_code
                    );
                }
            }
        } elseif ($code_desc <> '') {
            $this->doQuery(
                $this->query['insert'],
                $code_set,
                $code_lang,
                $code_code,
                $code_desc,
                $code_order,
                $code_flag
            );
        }
    }

    /**
     * Get the code counts for all language sets.
     */
    public function count()
    {
        $code_counts = [];
        foreach ($this->doQuery($this->query['count']) as $row) {
            $code_counts[$row[0]][$row[1]] = $row[2];
        }
        return $code_counts;
    }

    // -------------------------------------------------------------------------
    // PROTECTED
    // -------------------------------------------------------------------------

    /**
     *
     */
    protected $db;

    /**
     *
     */
    protected $cache;

    /**
     *
     */
    protected $query = [
        'native' =>
            'SELECT `code_lang`
               FROM `{TABLE}`
              WHERE `code_set` = "code_admin" AND `code_code` = "code_admin"',

        'count' =>
            'SELECT `code_set`, `code_lang`, COUNT(1) AS `code_count`
               FROM `{TABLE}`
              GROUP BY `code_set`, `code_lang`',

        'data' =>
            'SELECT `code_desc`
               FROM `{TABLE}`
              WHERE `code_set` = "%s" AND `code_lang` = "%s" AND `code_code` = "%s"
              LIMIT 1',

        'get' =>
            'SELECT `code_desc`, `code_order`, `code_flag`
               FROM `{TABLE}`
              WHERE `code_set` = "%s" AND `code_lang` = "%s" AND `code_code` = "%s"
              LIMIT 1',

        'languageSet' =>
            'SELECT `code_code`, `code_desc`, `code_order`, `code_flag`
               FROM `{TABLE}`
              WHERE `code_set` = "%s" AND `code_lang` = "%s"
              ORDER BY `code_order`, `code_code`',

        'insert' =>
            'INSERT INTO `{TABLE}`
                    (`code_set`, `code_lang`, `code_code`, `code_desc`, `code_order`, `code_flag`)
             VALUES ("%s", "%s", "%s", "%s", "%s", "%s")',

        'update' =>
            'UPDATE `{TABLE}`
                SET `code_desc` = "%s", `code_order` = "%s", `code_flag` = "%s"
              WHERE `code_set` = "%s" AND `code_lang`  = "%s" AND `code_code` = "%s"',

        'delete' =>
            'DELETE FROM `{TABLE}`
              WHERE `code_set` = "%s" AND `code_lang` = "%s" AND `code_code` = "%s"',

        'remove_1' =>
            'DELETE FROM `{TABLE}` WHERE `code_set` = "%s" AND `code_code` = "%s"',

        'remove_2' =>
            'DELETE FROM `{TABLE}` WHERE `code_set` = "code_admin" AND `code_code` = "%s"',

        'remove_3' =>
            'DELETE FROM `{TABLE}` WHERE `code_set` = "%s"',
    ];

    /**
     * Build cache key, prepended with "BabelKit"
     */
    protected function cacheKey()
    {
        $args = func_get_args();
        array_unshift($args, 'BabelKit');
        return implode('.', $args);
    }

    /**
     * Find the native language
     *
     * Buffer request results
     */
    public function findNativeLanguage()
    {
        $result = $this->fetch($this->cacheKey('native'), $this->query['native']);

        return isset($result[0][0]) ? $result[0][0] : '';
    }

    /**
     * Fetch sql result, use cache if defined
     */
    protected function fetch($cacheKey, $sql)
    {
        if ($this->cache) {
            while ($this->cache->save($cacheKey, $data)) {
                $data = $this->doQuery($sql);
            }
        } else {
            $data = $this->doQuery($sql);
        }

        return $data;
    }

    /**
     *
     */
    protected function sql($query)
    {
        $args  = func_get_args();
        // Get query
        $query = array_shift($args);
        // Replace remaining arguments

        return vsprintf($query, $args);
    }

    /**
     *
     */
    protected function doQuery($query)
    {
        $args = func_get_args();
        // Get query
        $query = array_shift($args);
        // Replace remaining arguments
        $query = vsprintf($query, $args);

        if ($result = $this->db->query($query)) {
            if (is_scalar($result)) {
                return $result;
            } else {
                $data = [];
                while ($row = $result->fetch_array(MYSQLI_NUM)) {
                    $data[] = $row;
                }
                $result->free();
                return $data;
            }
        }
    }
}
