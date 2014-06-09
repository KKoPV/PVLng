<?php
/**
 * Abstract base class for table 'pvlng_performance'
 *
 * *** NEVER EVER EDIT THIS FILE! ***
 *
 * To extend the functionallity, edit "Performance.php"
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
    }   // setTimestamp()

    /**
     * Basic setter for field 'action'
     *
     * @param  mixed    $action Action value
     * @return Instance For fluid interface
     */
    public function setAction( $action ) {
        $this->fields['action'] = $action;
        return $this;
    }   // setAction()

    /**
     * Basic setter for field 'time'
     *
     * @param  mixed    $time Time value
     * @return Instance For fluid interface
     */
    public function setTime( $time ) {
        $this->fields['time'] = $time;
        return $this;
    }   // setTime()

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
    }   // getTimestamp()

    /**
     * Basic getter for field 'action'
     *
     * @return mixed Action value
     */
    public function getAction() {
        return $this->fields['action'];
    }   // getAction()

    /**
     * Basic getter for field 'time'
     *
     * @return mixed Time value
     */
    public function getTime() {
        return $this->fields['time'];
    }   // getTime()

    // -----------------------------------------------------------------------
    // Filter methods
    // -----------------------------------------------------------------------

    /**
     * Filter for field 'timestamp'
     *
     * @param  mixed    $timestamp Filter value
     * @return Instance For fluid interface
     */
    public function filterByTimestamp( $timestamp ) {
        $this->filter[] = '`timestamp` = "'.$this->quote($timestamp).'"';
        return $this;
    }   // filterByTimestamp()

    /**
     * Filter for field 'action'
     *
     * @param  mixed    $action Filter value
     * @return Instance For fluid interface
     */
    public function filterByAction( $action ) {
        $this->filter[] = '`action` = "'.$this->quote($action).'"';
        return $this;
    }   // filterByAction()

    /**
     * Filter for field 'time'
     *
     * @param  mixed    $time Filter value
     * @return Instance For fluid interface
     */
    public function filterByTime( $time ) {
        $this->filter[] = '`time` = "'.$this->quote($time).'"';
        return $this;
    }   // filterByTime()

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
