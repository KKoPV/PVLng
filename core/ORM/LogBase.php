<?php
/**
 * Base access class for "pvlng_log"
 *
 * NEVER EVER EDIT THIS FILE
 *
 * To extend the functionallity, edit "Log.php"
 *
 * If you make changes here, they will be lost on next upgrade PVLng!
 *
 * @author     PVLng ORM class builder
 * @version    1.0.0
 */
namespace ORM;

/**
 *
 */
abstract class LogBase extends \slimMVC\ORM {

    // -----------------------------------------------------------------------
    // PUBLIC
    // -----------------------------------------------------------------------

    /**
     * Forge method for key field(s)
     *
     * @param mixed Field value
     */
    public static function forge( $id ) {
        return new static(array($id));
    } // forge()

    // -----------------------------------------------------------------------
    // Setter methods
    // -----------------------------------------------------------------------

    /**
     * "id" is AutoInc, no setter
     */

    /**
     * Basic setter for field 'timestamp'
     *
     * @param  mixed    $timestamp Timestamp value
     * @return Instance For fluid interface
     */
    public function setTimestamp( $timestamp ) {
        $this->fields['timestamp'] = $timestamp;
        return $this;
    } // setTimestamp()

    /**
     * Basic setter for field 'scope'
     *
     * @param  mixed    $scope Scope value
     * @return Instance For fluid interface
     */
    public function setScope( $scope ) {
        $this->fields['scope'] = $scope;
        return $this;
    } // setScope()

    /**
     * Basic setter for field 'data'
     *
     * @param  mixed    $data Data value
     * @return Instance For fluid interface
     */
    public function setData( $data ) {
        $this->fields['data'] = $data;
        return $this;
    } // setData()

    // -----------------------------------------------------------------------
    // Getter methods
    // -----------------------------------------------------------------------

    /**
     * Basic getter for field 'id'
     *
     * @return mixed Id value
     */
    public function getId() {
        return $this->fields['id'];
    } // getId()

    /**
     * Basic getter for field 'timestamp'
     *
     * @return mixed Timestamp value
     */
    public function getTimestamp() {
        return $this->fields['timestamp'];
    } // getTimestamp()

    /**
     * Basic getter for field 'scope'
     *
     * @return mixed Scope value
     */
    public function getScope() {
        return $this->fields['scope'];
    } // getScope()

    /**
     * Basic getter for field 'data'
     *
     * @return mixed Data value
     */
    public function getData() {
        return $this->fields['data'];
    } // getData()

    // -----------------------------------------------------------------------
    // Filter methods
    // -----------------------------------------------------------------------

    /**
     * Filter for field id
     *
     * @param mixed Field value
     */
    public function filterById( $id ) {
        return $this->filter('id', $id);
    } // filterById()

    /**
     * Filter for field timestamp
     *
     * @param mixed Field value
     */
    public function filterByTimestamp( $timestamp ) {
        return $this->filter('timestamp', $timestamp);
    } // filterByTimestamp()

    /**
     * Filter for field scope
     *
     * @param mixed Field value
     */
    public function filterByScope( $scope ) {
        return $this->filter('scope', $scope);
    } // filterByScope()

    /**
     * Filter for field data
     *
     * @param mixed Field value
     */
    public function filterByData( $data ) {
        return $this->filter('data', $data);
    } // filterByData()

    // -----------------------------------------------------------------------
    // PROTECTED
    // -----------------------------------------------------------------------

    /**
     * Table name
     *
     * @var string $table Table name
     */
    protected $table = 'pvlng_log';

}
