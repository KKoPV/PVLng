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
 * @copyright  2016 Knut Kohl
 * @license    MIT License (MIT) http://opensource.org/licenses/MIT
 *
 * @author     PVLng ORM class builder
 * @version    1.4.0 / 2016-07-18
 */
namespace ORM;

/**
 *
 */
abstract class SettingsBase extends \slimMVC\ORM
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
    }   // setScope()

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
    }   // setScopeRaw()

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
    }   // setName()

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
    }   // setNameRaw()

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
    }   // setKey()

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
    }   // setKeyRaw()

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
    }   // setValue()

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
    }   // setValueRaw()

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
    }   // setOrder()

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
    }   // setOrderRaw()

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
    }   // setType()

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
    }   // setTypeRaw()

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
    }   // setData()

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
    }   // setDataRaw()

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
    }   // getScope()

    /**
     * Basic getter for field "name"
     *
     * @return mixed Name value
     */
    public function getName()
    {
        return $this->fields['name'];
    }   // getName()

    /**
     * Basic getter for field "key"
     *
     * @return mixed Key value
     */
    public function getKey()
    {
        return $this->fields['key'];
    }   // getKey()

    /**
     * Basic getter for field "value"
     *
     * @return mixed Value value
     */
    public function getValue()
    {
        return $this->fields['value'];
    }   // getValue()

    /**
     * Basic getter for field "order"
     *
     * @return mixed Order value
     */
    public function getOrder()
    {
        return $this->fields['order'];
    }   // getOrder()

    /**
     * Basic getter for field "type"
     *
     * @return mixed Type value
     */
    public function getType()
    {
        return $this->fields['type'];
    }   // getType()

    /**
     * Basic getter for field "data"
     *
     * @return mixed Data value
     */
    public function getData()
    {
        return $this->fields['data'];
    }   // getData()

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

        $this->filter[] = '`scope` = '.$this->quote($scope).'';
        $this->filter[] = '`name` = '.$this->quote($name).'';
        $this->filter[] = '`key` = '.$this->quote($key).'';
        return $this;
    }   // filterByScopeNameKey()

    /**
     * Filter for field "scope"
     *
     * @param  mixed    $scope Filter value
     * @return Instance For fluid interface
     */
    public function filterByScope($scope)
    {
        $this->filter[] = '`scope` = '.$this->quote($scope);
        return $this;
    }   // filterByScope()

    /**
     * Filter for field "name"
     *
     * @param  mixed    $name Filter value
     * @return Instance For fluid interface
     */
    public function filterByName($name)
    {
        $this->filter[] = '`name` = '.$this->quote($name);
        return $this;
    }   // filterByName()

    /**
     * Filter for field "key"
     *
     * @param  mixed    $key Filter value
     * @return Instance For fluid interface
     */
    public function filterByKey($key)
    {
        $this->filter[] = '`key` = '.$this->quote($key);
        return $this;
    }   // filterByKey()

    /**
     * Filter for field "value"
     *
     * @param  mixed    $value Filter value
     * @return Instance For fluid interface
     */
    public function filterByValue($value)
    {
        $this->filter[] = '`value` = '.$this->quote($value);
        return $this;
    }   // filterByValue()

    /**
     * Filter for field "order"
     *
     * @param  mixed    $order Filter value
     * @return Instance For fluid interface
     */
    public function filterByOrder($order)
    {
        $this->filter[] = '`order` = '.$this->quote($order);
        return $this;
    }   // filterByOrder()

    /**
     * Filter for field "type"
     *
     * @param  mixed    $type Filter value
     * @return Instance For fluid interface
     */
    public function filterByType($type)
    {
        $this->filter[] = '`type` = '.$this->quote($type);
        return $this;
    }   // filterByType()

    /**
     * Filter for field "data"
     *
     * @param  mixed    $data Filter value
     * @return Instance For fluid interface
     */
    public function filterByData($data)
    {
        $this->filter[] = '`data` = '.$this->quote($data);
        return $this;
    }   // filterByData()

    // -----------------------------------------------------------------------
    // PROTECTED
    // -----------------------------------------------------------------------

    /**
     * Update fields on insert on duplicate key
     */
    protected function onDuplicateKey()
    {
        return '`value` = '.$this->quote($this->fields['value']).'
              , `order` = '.$this->quote($this->fields['order']).'
              , `type` = '.$this->quote($this->fields['type']).'
              , `data` = '.$this->quote($this->fields['data']).'';
    }   // onDuplicateKey()

    /**
     * Table name
     *
     * @var string $table Table name
     */
    protected $table = 'pvlng_settings';

    /**
     * SQL for creation
     *
     * @var string $createSQL
     */
    protected $createSQL = '
        CREATE TABLE `pvlng_settings` (
          `scope` enum(\'core\',\'controller\',\'model\') NOT NULL DEFAULT \'core\',
          `name` varchar(100) NOT NULL DEFAULT \'\',
          `key` varchar(100) NOT NULL DEFAULT \'\',
          `value` varchar(100) NOT NULL DEFAULT \'\',
          `order` tinyint(3) unsigned NOT NULL DEFAULT \'0\',
          `type` enum(\'str\',\'num\',\'bool\',\'option\') NOT NULL DEFAULT \'str\',
          `data` varchar(255) NOT NULL DEFAULT \'\',
          PRIMARY KEY (`scope`,`name`,`key`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 PACK_KEYS=1 COMMENT=\'Application settings\'
    ';

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
