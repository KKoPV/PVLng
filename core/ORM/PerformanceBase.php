<?php
/**
 * Base access class for "pvlng_performance"
 *
 * NEVER EVER EDIT THIS FILE
 *
 * To extend the functionallity, edit "Performance.php"
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
abstract class PerformanceBase extends \slimMVC\ORM {

    // -----------------------------------------------------------------------
    // PUBLIC
    // -----------------------------------------------------------------------

    // -----------------------------------------------------------------------
    // Setter methods
    // -----------------------------------------------------------------------

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
     * Basic setter for field 'action'
     *
     * @param  mixed    $action Action value
     * @return Instance For fluid interface
     */
    public function setAction( $action ) {
        $this->fields['action'] = $action;
        return $this;
    } // setAction()

    /**
     * Basic setter for field 'time'
     *
     * @param  mixed    $time Time value
     * @return Instance For fluid interface
     */
    public function setTime( $time ) {
        $this->fields['time'] = $time;
        return $this;
    } // setTime()

    // -----------------------------------------------------------------------
    // Getter methods
    // -----------------------------------------------------------------------

    /**
     * Basic getter for field 'timestamp'
     *
     * @return mixed Timestamp value
     */
    public function getTimestamp() {
        return $this->fields['timestamp'];
    } // getTimestamp()

    /**
     * Basic getter for field 'action'
     *
     * @return mixed Action value
     */
    public function getAction() {
        return $this->fields['action'];
    } // getAction()

    /**
     * Basic getter for field 'time'
     *
     * @return mixed Time value
     */
    public function getTime() {
        return $this->fields['time'];
    } // getTime()

    // -----------------------------------------------------------------------
    // Filter methods
    // -----------------------------------------------------------------------

    /**
     * Filter for field timestamp
     *
     * @param mixed Field value
     */
    public function filterByTimestamp( $timestamp ) {
        return $this->filter('timestamp', $timestamp);
    } // filterByTimestamp()

    /**
     * Filter for field action
     *
     * @param mixed Field value
     */
    public function filterByAction( $action ) {
        return $this->filter('action', $action);
    } // filterByAction()

    /**
     * Filter for field time
     *
     * @param mixed Field value
     */
    public function filterByTime( $time ) {
        return $this->filter('time', $time);
    } // filterByTime()

    // -----------------------------------------------------------------------
    // PROTECTED
    // -----------------------------------------------------------------------

    /**
     * Table name
     *
     * @var string $table Table name
     */
    protected $table = 'pvlng_performance';

}
