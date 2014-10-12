<?php
/**
 * Abstract base class for table 'pvlng_settings'
 *
 * *** NEVER EVER EDIT THIS FILE! ***
 *
 * To extend the functionallity, edit "Settings.php"
 *
 * If you make changes here, they will be lost on next upgrade PVLng!
 *
 * @author     Knut Kohl <github@knutkohl.de>
 * @copyright  2014 Knut Kohl
 * @license    MIT License (MIT) http://opensource.org/licenses/MIT
 *
 * @author     PVLng ORM class builder
 * @version    1.1.0 / 2014-06-04
 */
namespace ORM;

/**
 *
 */
abstract class SettingsBase extends \slimMVC\ORM {

    // -----------------------------------------------------------------------
    // PUBLIC
    // -----------------------------------------------------------------------

    // -----------------------------------------------------------------------
    // Setter methods
    // -----------------------------------------------------------------------

    /**
     * Basic setter for field 'scope'
     *
     * @param  mixed    $scope Scope value
     * @return Instance For fluid interface
     */
    public function setScope( $scope ) {
        $this->fields['scope'] = $scope;
        return $this;
    }   // setScope()

    /**
     * Basic setter for field 'name'
     *
     * @param  mixed    $name Name value
     * @return Instance For fluid interface
     */
    public function setName( $name ) {
        $this->fields['name'] = $name;
        return $this;
    }   // setName()

    /**
     * Basic setter for field 'key'
     *
     * @param  mixed    $key Key value
     * @return Instance For fluid interface
     */
    public function setKey( $key ) {
        $this->fields['key'] = $key;
        return $this;
    }   // setKey()

    /**
     * Basic setter for field 'value'
     *
     * @param  mixed    $value Value value
     * @return Instance For fluid interface
     */
    public function setValue( $value ) {
        $this->fields['value'] = $value;
        return $this;
    }   // setValue()

    /**
     * Basic setter for field 'order'
     *
     * @param  mixed    $order Order value
     * @return Instance For fluid interface
     */
    public function setOrder( $order ) {
        $this->fields['order'] = $order;
        return $this;
    }   // setOrder()

    /**
     * Basic setter for field 'description'
     *
     * @param  mixed    $description Description value
     * @return Instance For fluid interface
     */
    public function setDescription( $description ) {
        $this->fields['description'] = $description;
        return $this;
    }   // setDescription()

    /**
     * Basic setter for field 'type'
     *
     * @param  mixed    $type Type value
     * @return Instance For fluid interface
     */
    public function setType( $type ) {
        $this->fields['type'] = $type;
        return $this;
    }   // setType()

    /**
     * Basic setter for field 'data'
     *
     * @param  mixed    $data Data value
     * @return Instance For fluid interface
     */
    public function setData( $data ) {
        $this->fields['data'] = $data;
        return $this;
    }   // setData()

    // -----------------------------------------------------------------------
    // Getter methods
    // -----------------------------------------------------------------------

    /**
     * Basic getter for field 'scope'
     *
     * @return mixed Scope value
     */
    public function getScope() {
        return $this->fields['scope'];
    }   // getScope()

    /**
     * Basic getter for field 'name'
     *
     * @return mixed Name value
     */
    public function getName() {
        return $this->fields['name'];
    }   // getName()

    /**
     * Basic getter for field 'key'
     *
     * @return mixed Key value
     */
    public function getKey() {
        return $this->fields['key'];
    }   // getKey()

    /**
     * Basic getter for field 'value'
     *
     * @return mixed Value value
     */
    public function getValue() {
        return $this->fields['value'];
    }   // getValue()

    /**
     * Basic getter for field 'order'
     *
     * @return mixed Order value
     */
    public function getOrder() {
        return $this->fields['order'];
    }   // getOrder()

    /**
     * Basic getter for field 'description'
     *
     * @return mixed Description value
     */
    public function getDescription() {
        return $this->fields['description'];
    }   // getDescription()

    /**
     * Basic getter for field 'type'
     *
     * @return mixed Type value
     */
    public function getType() {
        return $this->fields['type'];
    }   // getType()

    /**
     * Basic getter for field 'data'
     *
     * @return mixed Data value
     */
    public function getData() {
        return $this->fields['data'];
    }   // getData()

    // -----------------------------------------------------------------------
    // Filter methods
    // -----------------------------------------------------------------------

    /**
     * Filter for unique fields 'scope', 'name', 'key'
     *
     * @param  mixed    $scope, $name, $key Filter values
     * @return Instance For fluid interface
     */
    public function filterByScopeNameKey( $scope, $name, $key ) {
        $this->filter[] = '`scope` = "'.$this->quote($scope).'"';
        $this->filter[] = '`name` = "'.$this->quote($name).'"';
        $this->filter[] = '`key` = "'.$this->quote($key).'"';
        return $this;
    }   // filterByScopeNameKey()

    /**
     * Filter for field 'scope'
     *
     * @param  mixed    $scope Filter value
     * @return Instance For fluid interface
     */
    public function filterByScope( $scope ) {
        $this->filter[] = '`scope` = "'.$this->quote($scope).'"';
        return $this;
    }   // filterByScope()

    /**
     * Filter for field 'name'
     *
     * @param  mixed    $name Filter value
     * @return Instance For fluid interface
     */
    public function filterByName( $name ) {
        $this->filter[] = '`name` = "'.$this->quote($name).'"';
        return $this;
    }   // filterByName()

    /**
     * Filter for field 'key'
     *
     * @param  mixed    $key Filter value
     * @return Instance For fluid interface
     */
    public function filterByKey( $key ) {
        $this->filter[] = '`key` = "'.$this->quote($key).'"';
        return $this;
    }   // filterByKey()

    /**
     * Filter for field 'value'
     *
     * @param  mixed    $value Filter value
     * @return Instance For fluid interface
     */
    public function filterByValue( $value ) {
        $this->filter[] = '`value` = "'.$this->quote($value).'"';
        return $this;
    }   // filterByValue()

    /**
     * Filter for field 'order'
     *
     * @param  mixed    $order Filter value
     * @return Instance For fluid interface
     */
    public function filterByOrder( $order ) {
        $this->filter[] = '`order` = "'.$this->quote($order).'"';
        return $this;
    }   // filterByOrder()

    /**
     * Filter for field 'description'
     *
     * @param  mixed    $description Filter value
     * @return Instance For fluid interface
     */
    public function filterByDescription( $description ) {
        $this->filter[] = '`description` = "'.$this->quote($description).'"';
        return $this;
    }   // filterByDescription()

    /**
     * Filter for field 'type'
     *
     * @param  mixed    $type Filter value
     * @return Instance For fluid interface
     */
    public function filterByType( $type ) {
        $this->filter[] = '`type` = "'.$this->quote($type).'"';
        return $this;
    }   // filterByType()

    /**
     * Filter for field 'data'
     *
     * @param  mixed    $data Filter value
     * @return Instance For fluid interface
     */
    public function filterByData( $data ) {
        $this->filter[] = '`data` = "'.$this->quote($data).'"';
        return $this;
    }   // filterByData()

    // -----------------------------------------------------------------------
    // PROTECTED
    // -----------------------------------------------------------------------

    /**
     * Table name
     *
     * @var string $table Table name
     */
    protected $table = 'pvlng_settings';

}
