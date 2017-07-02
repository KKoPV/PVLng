<?php
/**
 * Abstract base class for table "pvlng_settings_keys"
 *
 * *** NEVER EVER EDIT THIS FILE! ***
 *
 * To extend the functionallity, edit "SettingsKeys.php"!
 *
 * If you make changes here, they will be lost on next upgrade PVLng!
 *
 * @author     Knut Kohl <github@knutkohl.de>
 * @copyright  2017 Knut Kohl
 * @license    MIT License (MIT) http://opensource.org/licenses/MIT
 *
 * @author     PVLng ORM class builder
 * @version    1.4.0 / 2016-07-18
 */
namespace ORM;

/**
 *
 */
use Core\ORM;

/**
 *
 */
abstract class SettingsKeysBase extends ORM
{

    // -----------------------------------------------------------------------
    // PUBLIC
    // -----------------------------------------------------------------------

    // -----------------------------------------------------------------------
    // Setter methods
    // -----------------------------------------------------------------------

    /**
     * "pvlng_settings_keys" is a view, no setters
     */

    // -----------------------------------------------------------------------
    // Getter methods
    // -----------------------------------------------------------------------

    /**
     * Basic getter for field "key"
     *
     * @return mixed Key value
     */
    public function getKey()
    {
        return $this->fields['key'];
    }

    /**
     * Basic getter for field "value"
     *
     * @return mixed Value value
     */
    public function getValue()
    {
        return $this->fields['value'];
    }

    // -----------------------------------------------------------------------
    // Filter methods
    // -----------------------------------------------------------------------

    /**
     * Filter for field "key"
     *
     * @param  mixed    $key Filter value
     * @return Instance For fluid interface
     */
    public function filterByKey($key)
    {
        return $this->filter('key', $key);
    }

    /**
     * Filter for field "value"
     *
     * @param  mixed    $value Filter value
     * @return Instance For fluid interface
     */
    public function filterByValue($value)
    {
        return $this->filter('value', $value);
    }

    // -----------------------------------------------------------------------
    // PROTECTED
    // -----------------------------------------------------------------------

    /**
     * Call create table sql on class creation and set to false
     */
    protected static $memory = false;

    /**
     * SQL for creation
     *
     * @var string $createSQL
     */
    // @codingStandardsIgnoreStart
    protected static $createSQL = '
        CREATE ALGORITHM=UNDEFINED DEFINER=`pvlng`@`localhost` SQL SECURITY DEFINER VIEW `pvlng_settings_keys` AS select concat(`pvlng_settings`.`scope`,if((`pvlng_settings`.`name` <> \'\'),concat(\'.\',`pvlng_settings`.`name`),\'\'),\'.\',`pvlng_settings`.`key`) AS `key`,`pvlng_settings`.`value` AS `value` from `pvlng_settings`
    ';
    // @codingStandardsIgnoreEnd

    /**
     * Table name
     *
     * @var string $table Table name
     */
    protected $table = 'pvlng_settings_keys';

    /**
     *
     */
    protected $fields = array(
        'key'   => '',
        'value' => ''
    );

    /**
     *
     */
    protected $nullable = array(

    );

    /**
     *
     */
    protected $primary = array();

    /**
     *
     */
    protected $autoinc = '';
}
