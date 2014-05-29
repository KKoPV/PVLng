<?php
/**
 * Base access class for "pvlng_reading_num"
 *
 * NEVER EVER EDIT THIS FILE
 *
 * To extend the functionallity, edit "Reading.php"
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
abstract class ReadingBase extends \slimMVC\ORM {

    // -----------------------------------------------------------------------
    // PUBLIC
    // -----------------------------------------------------------------------

    /**
     * Forge method for key field(s) id, $timestamp
     *
     * @param mixed Field value
     */
    public static function forge( $id, $timestamp ) {
        return new static(array($id, $timestamp));
    }

    // -----------------------------------------------------------------------
    // Setter methods
    // -----------------------------------------------------------------------

    /**
     * Basic setter for field 'id'
     *
     * @param  mixed    $id Id value
     * @return Instance For fluid interface
     */
    public function setId( $id ) {
        $this->fields['id'] = $id;
        return $this;
    }

    /**
     * Basic setter for field 'timestamp'
     *
     * @param  mixed    $timestamp Timestamp value
     * @return Instance For fluid interface
     */
    public function setTimestamp( $timestamp ) {
        $this->fields['timestamp'] = $timestamp;
        return $this;
    }

    /**
     * Basic setter for field 'data'
     *
     * @param  mixed    $data Data value
     * @return Instance For fluid interface
     */
    public function setData( $data ) {
        $this->fields['data'] = $data;
        return $this;
    }

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
    }

    /**
     * Basic getter for field 'timestamp'
     *
     * @return mixed Timestamp value
     */
    public function getTimestamp() {
        return $this->fields['timestamp'];
    }

    /**
     * Basic getter for field 'data'
     *
     * @return mixed Data value
     */
    public function getData() {
        return $this->fields['data'];
    }

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
    }

    /**
     * Filter for field timestamp
     *
     * @param mixed Field value
     */
    public function filterByTimestamp( $timestamp ) {
        return $this->filter('timestamp', $timestamp);
    }

    /**
     * Filter for field data
     *
     * @param mixed Field value
     */
    public function filterByData( $data ) {
        return $this->filter('data', $data);
    }

}
