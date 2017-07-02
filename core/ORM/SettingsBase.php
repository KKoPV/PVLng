<?php
/**
 * Abstract base class for table "pvlng_settings"
 *
 * *** NEVER EVER EDIT THIS FILE! ***
 *
 * To extend the functionallity, edit "Settings.php"!
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
abstract class SettingsBase extends ORM
{

    // -----------------------------------------------------------------------
    // PUBLIC
    // -----------------------------------------------------------------------

    // -----------------------------------------------------------------------
    // Setter methods
    // -----------------------------------------------------------------------

    /**
     * Basic setter for field "scope"
     *
     * @param  mixed    $scope Scope value
     * @return Instance For fluid interface
     */
    public function setScope($scope)
    {
        $this->fields['scope'] = $scope;
        return $this;
    }

    /**
     * Raw setter for field "scope", for INSERT, REPLACE and UPDATE
     *
     * @param  mixed    $scope Scope value
     * @return Instance For fluid interface
     */
    public function setScopeRaw($scope)
    {
        $this->raw['scope'] = $scope;
        return $this;
    }

    /**
     * Basic setter for field "name"
     *
     * @param  mixed    $name Name value
     * @return Instance For fluid interface
     */
    public function setName($name)
    {
        $this->fields['name'] = $name;
        return $this;
    }

    /**
     * Raw setter for field "name", for INSERT, REPLACE and UPDATE
     *
     * @param  mixed    $name Name value
     * @return Instance For fluid interface
     */
    public function setNameRaw($name)
    {
        $this->raw['name'] = $name;
        return $this;
    }

    /**
     * Basic setter for field "key"
     *
     * @param  mixed    $key Key value
     * @return Instance For fluid interface
     */
    public function setKey($key)
    {
        $this->fields['key'] = $key;
        return $this;
    }

    /**
     * Raw setter for field "key", for INSERT, REPLACE and UPDATE
     *
     * @param  mixed    $key Key value
     * @return Instance For fluid interface
     */
    public function setKeyRaw($key)
    {
        $this->raw['key'] = $key;
        return $this;
    }

    /**
     * Basic setter for field "value"
     *
     * @param  mixed    $value Value value
     * @return Instance For fluid interface
     */
    public function setValue($value)
    {
        $this->fields['value'] = $value;
        return $this;
    }

    /**
     * Raw setter for field "value", for INSERT, REPLACE and UPDATE
     *
     * @param  mixed    $value Value value
     * @return Instance For fluid interface
     */
    public function setValueRaw($value)
    {
        $this->raw['value'] = $value;
        return $this;
    }

    /**
     * Basic setter for field "order"
     *
     * @param  mixed    $order Order value
     * @return Instance For fluid interface
     */
    public function setOrder($order)
    {
        $this->fields['order'] = $order;
        return $this;
    }

    /**
     * Raw setter for field "order", for INSERT, REPLACE and UPDATE
     *
     * @param  mixed    $order Order value
     * @return Instance For fluid interface
     */
    public function setOrderRaw($order)
    {
        $this->raw['order'] = $order;
        return $this;
    }

    /**
     * Basic setter for field "type"
     *
     * @param  mixed    $type Type value
     * @return Instance For fluid interface
     */
    public function setType($type)
    {
        $this->fields['type'] = $type;
        return $this;
    }

    /**
     * Raw setter for field "type", for INSERT, REPLACE and UPDATE
     *
     * @param  mixed    $type Type value
     * @return Instance For fluid interface
     */
    public function setTypeRaw($type)
    {
        $this->raw['type'] = $type;
        return $this;
    }

    /**
     * Basic setter for field "data"
     *
     * @param  mixed    $data Data value
     * @return Instance For fluid interface
     */
    public function setData($data)
    {
        $this->fields['data'] = $data;
        return $this;
    }

    /**
     * Raw setter for field "data", for INSERT, REPLACE and UPDATE
     *
     * @param  mixed    $data Data value
     * @return Instance For fluid interface
     */
    public function setDataRaw($data)
    {
        $this->raw['data'] = $data;
        return $this;
    }

    // -----------------------------------------------------------------------
    // Getter methods
    // -----------------------------------------------------------------------

    /**
     * Basic getter for field "scope"
     *
     * @return mixed Scope value
     */
    public function getScope()
    {
        return $this->fields['scope'];
    }

    /**
     * Basic getter for field "name"
     *
     * @return mixed Name value
     */
    public function getName()
    {
        return $this->fields['name'];
    }

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

    /**
     * Basic getter for field "order"
     *
     * @return mixed Order value
     */
    public function getOrder()
    {
        return $this->fields['order'];
    }

    /**
     * Basic getter for field "type"
     *
     * @return mixed Type value
     */
    public function getType()
    {
        return $this->fields['type'];
    }

    /**
     * Basic getter for field "data"
     *
     * @return mixed Data value
     */
    public function getData()
    {
        return $this->fields['data'];
    }

    // -----------------------------------------------------------------------
    // Filter methods
    // -----------------------------------------------------------------------

    /**
     * Filter for unique fields "scope', 'name', 'key"
     *
     * @param  mixed    $scope, $name, $key Filter values
     * @return Instance For fluid interface
     */
    public function filterByScopeNameKey($scope, $name, $key)
    {
        $this->filter('scope', $scope);
        $this->filter('name', $name);
        $this->filter('key', $key);
        return $this;
    }

    /**
     * Filter for field "scope"
     *
     * @param  mixed    $scope Filter value
     * @return Instance For fluid interface
     */
    public function filterByScope($scope)
    {
        return $this->filter('scope', $scope);
    }

    /**
     * Filter for field "name"
     *
     * @param  mixed    $name Filter value
     * @return Instance For fluid interface
     */
    public function filterByName($name)
    {
        return $this->filter('name', $name);
    }

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

    /**
     * Filter for field "order"
     *
     * @param  mixed    $order Filter value
     * @return Instance For fluid interface
     */
    public function filterByOrder($order)
    {
        return $this->filter('order', $order);
    }

    /**
     * Filter for field "type"
     *
     * @param  mixed    $type Filter value
     * @return Instance For fluid interface
     */
    public function filterByType($type)
    {
        return $this->filter('type', $type);
    }

    /**
     * Filter for field "data"
     *
     * @param  mixed    $data Filter value
     * @return Instance For fluid interface
     */
    public function filterByData($data)
    {
        return $this->filter('data', $data);
    }

    // -----------------------------------------------------------------------
    // PROTECTED
    // -----------------------------------------------------------------------

    /**
     * Update fields on insert on duplicate key
     */
    protected function onDuplicateKey()
    {
        return '`value` = VALUES(`value`)
              , `order` = VALUES(`order`)
              , `type` = VALUES(`type`)
              , `data` = VALUES(`data`)';
    }

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
        CREATE TABLE IF NOT EXISTS `pvlng_settings` (
          `scope` enum(\'core\',\'controller\',\'model\') NOT NULL DEFAULT \'core\',
          `name` char(100) NOT NULL DEFAULT \'\',
          `key` char(100) NOT NULL DEFAULT \'\',
          `value` varchar(100) NOT NULL DEFAULT \'\',
          `order` tinyint(3) unsigned NOT NULL DEFAULT \'0\',
          `type` enum(\'str\',\'short\',\'num\',\'bool\',\'option\') NOT NULL DEFAULT \'str\',
          `data` varchar(255) NOT NULL DEFAULT \'\',
          PRIMARY KEY (`scope`,`name`,`key`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 PACK_KEYS=1 COMMENT=\'Application settings\'
    ';
    // @codingStandardsIgnoreEnd

    /**
     * Table name
     *
     * @var string $table Table name
     */
    protected $table = 'pvlng_settings';

    /**
     *
     */
    protected $fields = array(
        'scope' => '',
        'name'  => '',
        'key'   => '',
        'value' => '',
        'order' => '',
        'type'  => '',
        'data'  => ''
    );

    /**
     *
     */
    protected $nullable = array(
        'scope' => false,
        'name'  => false,
        'key'   => false,
        'value' => false,
        'order' => false,
        'type'  => false,
        'data'  => false
    );

    /**
     *
     */
    protected $primary = array(
        'scope',
        'name',
        'key'
    );

    /**
     *
     */
    protected $autoinc = '';
}
