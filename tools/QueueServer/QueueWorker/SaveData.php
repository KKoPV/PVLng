<?php
/**
 * Worker daemon
 *
 * @author     Knut Kohl <github@knutkohl.de>
 * @copyright  2012-2015 Knut Kohl
 * @license    MIT License (MIT) http://opensource.org/licenses/MIT
 * @version    1.0.0
 */
namespace QueueWorker;

/**
 *
 */
class SaveData implements WorkerInterface {

    /**
     *
     */
    public function __construct() {

        $config = (new \slimMVC\Config)
                  ->load(ROOT_DIR . DS . 'config' . DS . 'config.default.php')
                  ->load(ROOT_DIR . DS . 'config' . DS . 'config.php');

        extract($config->get('Database'), EXTR_REFS);
        $db = new \slimMVC\MySQLi($host, $username, $password, $database, $port, $socket);
        $db->setSettingsTable('pvlng_config');
        \slimMVC\ORM::setDatabase($db);

        /**
         * Nested set for channel tree
         */
        include_once LIB_DIR . DS . 'contrib' . DS . 'class.nestedset.php';

        \NestedSet::Init(array(
            'db'       => $db,
            'debug'    => TRUE,
            'lang'     => 'en',
            'path'     => LIB_DIR.DS.'contrib'.DS.'messages',
            'db_table' => array (
                'tbl' => 'pvlng_tree',
                'nid' => 'id',
                'l'   => 'lft',
                'r'   => 'rgt',
                'mov' => 'moved',
                'pay' => 'entity'
            )
        ));

        $cache = \Cache::factory(
            array('Directory' => TEMP_DIR, 'TTL' => 86400),
            $config->get('Cache', 'MemCache,APC,File')
        );
        \slimMVC\ORM::setCache($cache);
        \Channel::setCache($cache);
    }

    /**
     *
     */
    public function process( $data ) {
        // Extract parameters by position
        $guid = &$data[0];
        $data = &$data[1];

        $data = json_decode($data, TRUE);

        if (json_last_error() == JSON_ERROR_NONE) {
            if (!isset($this->channels[$guid])) {
                // Buffer channel instance
                $this->channels[$guid] = \Channel::byGUID($guid);
            }
            $this->channels[$guid]->write($data);
        }
    }

    // -----------------------------------------------------------------------
    // PROTECTED
    // -----------------------------------------------------------------------

    /**
     * Channels object buffer
     *
     * @var array
     */
    protected $channels = array();

}
