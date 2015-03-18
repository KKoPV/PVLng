<?php
/**
 * Real access class for 'pvlng_config'
 *
 * To extend the functionallity, edit here
 *
 * @author     Knut Kohl <github@knutkohl.de>
 * @copyright  2014 Knut Kohl
 * @license    MIT License (MIT) http://opensource.org/licenses/MIT
 * @version    1.0.0
 *
 * 1.0.0
 * - Initial creation
 */
namespace ORM;

/**
 *
 */
class Config extends ConfigBase {

    /**
     *
     */
    public function getAPIkey() {
        return self::$db->queryOne('SELECT `getAPIkey`()');
    }

    /**
     *
     */
    public function getInstallation() {
        return self::$db->queryOne('SELECT `pvlng_id`()');
    }

    /**
     *
     */
    public function resetAPIkey() {
        self::$db->query(
            'UPDATE `'.$this->table.'` SET `value` = UUID() WHERE `key` = "APIKey" LIMIT 1'
        );
    }

}
