<?php
/**
 *
 */
require_once dirname(__FILE__) . DS . 'contrib' . DS . 'BabelKit.php';

/**
 *
 * @author      Knut Kohl <github@knutkohl.de>
 * @copyright   2012-2013 Knut Kohl
 * @license     GNU General Public License http://www.gnu.org/licenses/gpl.txt
 * @version     1.0.0
 */
class BabelKitMySQLi extends BabelKit {

    /**
     *
     */
    public static function setDB( MySQLi $db ) {
        self::$db = $db;
    }

    /**
     *
     */
    public static function setParams( $params ) {
        self::$params = $params;
    }

    /**
     *
     */
    public static function setCache( $cache ) {
        self::$cache = $cache;
    }

    /**
     *
     */
    public static function getInstance() {
        if (!self::$Instance) {
            self::$Instance = new BabelKitMySQLi(self::$db, self::$params);
        }
        return self::$Instance;
    }

    /**
     * DON't call parent::__construct() to overcome the "new" database type!
     *
     */
    public function __construct( $dbh, $param=array()) {

        $this->dbh = $dbh;

        $this->table = isset($param['table']) ? $param['table'] : 'bk_code';

        $this->native = $this->_find_native();
        if (!$this->native)
            throw new Exception("BabelKitMySQLi(): unable to determine native language. "
                               ."Check table '$this->table' for code_admin/code_admin record.");
    }

    /**
     * Get a raw code_desc, *not* safe for html display
     *
     * Buffer request results
     */
    public function data($code_set, $code_lang, $code_code) {
        $key = 'BabelKit.' . $code_set . '.' . $code_lang . '.' . $code_code;
        while (self::$cache->save($key, $data)) {
            $result = $this->_query("
                select  code_desc
                from    $this->table
                where   code_set  = '$code_set'
                and     code_lang = '$code_lang'
                and     code_code = '$code_code'
                limit 1
            ");
            $data = isset($result[0][0]) ? $result[0][0] : '';
        }
        return $data;
    }

    /**
     * Get code desc, order, and flag
     *
     * Buffer request results
     */
    public function get($code_set, $code_lang, $code_code) {
        $key = 'BabelKit.full.' . $code_set . '.' . $code_lang . '.' . $code_code;
        while (self::$cache->save($key, $data)) {
            $result = $this->_query("
                select  code_desc,
                        code_order,
                        code_flag
                from    $this->table
                where   code_set  = '$code_set'
                and     code_lang = '$code_lang'
                and     code_code = '$code_code'
                limit 1
            ");
            $data = isset($result[0]) ? $result[0] : '';
        }
        return $data;
    }

    /**
     * Get a language set array
     *
     * Buffer request results
     */
    public function lang_set($code_set, $code_lang) {
        $key = 'BabelKit.' . $code_set . '.' . $code_lang;
        while (self::$cache->save($key, $data)) {
            $data = $this->_query("
                select  code_code,
                        code_desc,
                        code_order,
                        code_flag
                from    $this->table
                where   code_set = '$code_set'
                and     code_lang = '$code_lang'
                order by code_order, code_code
            ");
        }
        return $data;
    }

    /**
     * Find the native language
     *
     * Buffer request results
     */
    public function _find_native() {
        $key = 'BabelKit.native';
        while (self::$cache->save($key, $data)) {
            $result = $this->_query("
                select  code_lang
                from    $this->table
                where   code_set  = 'code_admin'
                and     code_code = 'code_admin'
            ");
            $data = isset($result[0][0]) ? $result[0][0] : '';
        }
        return $data;
    }

    /**
     * Implement only MySQLi query
     */
    public function _query($query) {
        $result = array();

        $dbh = $this->dbh;
        $dbq = $dbh->query($query);
        if (is_object($dbq)) {
            while ($row = $dbq->fetch_array(MYSQLI_NUM)) {
                $result[] = $row;
            }
            $dbq->free();
        }

        return $result;
    }

    // -------------------------------------------------------------------------
    // PROTECTED
    // -------------------------------------------------------------------------

    /**
     *
     */
    protected static $Instance;

    /**
     *
     */
    protected static $db;

    /**
     *
     */
    protected static $params;

    /**
     *
     */
    protected static $cache;

}
