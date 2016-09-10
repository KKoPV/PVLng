<?php
/**
 * Abstract base class for table "pvlng_performance"
 *
 * *** NEVER EVER EDIT THIS FILE! ***
 *
 * To extend the functionallity, edit "Performance.php"!
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
abstract class PerformanceBase extends \slimMVC\ORM
{

    // -----------------------------------------------------------------------
    // PUBLIC
    // -----------------------------------------------------------------------

    // -----------------------------------------------------------------------
    // Setter methods
    // -----------------------------------------------------------------------

    /**
     * Basic setter for field "timestamp"
     *
     * @param  mixed    $timestamp Timestamp value
     * @return Instance For fluid interface
     */
    public function setTimestamp($timestamp)
    {
        $this->fields['timestamp'] = $timestamp;
        return $this;
    }   // setTimestamp()

    /**
     * Raw setter for field "timestamp", for INSERT, REPLACE and UPDATE
     *
     * @param  mixed    $timestamp Timestamp value
     * @return Instance For fluid interface
     */
    public function setTimestampRaw($timestamp)
    {
        $this->raw['timestamp'] = $timestamp;
        return $this;
    }   // setTimestampRaw()

    /**
     * Basic setter for field "action"
     *
     * @param  mixed    $action Action value
     * @return Instance For fluid interface
     */
    public function setAction($action)
    {
        $this->fields['action'] = $action;
        return $this;
    }   // setAction()

    /**
     * Raw setter for field "action", for INSERT, REPLACE and UPDATE
     *
     * @param  mixed    $action Action value
     * @return Instance For fluid interface
     */
    public function setActionRaw($action)
    {
        $this->raw['action'] = $action;
        return $this;
    }   // setActionRaw()

    /**
     * Basic setter for field "time"
     *
     * @param  mixed    $time Time value
     * @return Instance For fluid interface
     */
    public function setTime($time)
    {
        $this->fields['time'] = $time;
        return $this;
    }   // setTime()

    /**
     * Raw setter for field "time", for INSERT, REPLACE and UPDATE
     *
     * @param  mixed    $time Time value
     * @return Instance For fluid interface
     */
    public function setTimeRaw($time)
    {
        $this->raw['time'] = $time;
        return $this;
    }   // setTimeRaw()

    // -----------------------------------------------------------------------
    // Getter methods
    // -----------------------------------------------------------------------

    /**
     * Basic getter for field "timestamp"
     *
     * @return mixed Timestamp value
     */
    public function getTimestamp()
    {
        return $this->fields['timestamp'];
    }   // getTimestamp()

    /**
     * Basic getter for field "action"
     *
     * @return mixed Action value
     */
    public function getAction()
    {
        return $this->fields['action'];
    }   // getAction()

    /**
     * Basic getter for field "time"
     *
     * @return mixed Time value
     */
    public function getTime()
    {
        return $this->fields['time'];
    }   // getTime()

    // -----------------------------------------------------------------------
    // Filter methods
    // -----------------------------------------------------------------------

    /**
     * Filter for field "timestamp"
     *
     * @param  mixed    $timestamp Filter value
     * @return Instance For fluid interface
     */
    public function filterByTimestamp($timestamp)
    {
        $this->filter[] = $this->field('timestamp').' = '.$this->quote($timestamp);
        return $this;
    }   // filterByTimestamp()

    /**
     * Filter for field "action"
     *
     * @param  mixed    $action Filter value
     * @return Instance For fluid interface
     */
    public function filterByAction($action)
    {
        $this->filter[] = $this->field('action').' = '.$this->quote($action);
        return $this;
    }   // filterByAction()

    /**
     * Filter for field "time"
     *
     * @param  mixed    $time Filter value
     * @return Instance For fluid interface
     */
    public function filterByTime($time)
    {
        $this->filter[] = $this->field('time').' = '.$this->quote($time);
        return $this;
    }   // filterByTime()

    // -----------------------------------------------------------------------
    // PROTECTED
    // -----------------------------------------------------------------------

    /**
     * Update fields on insert on duplicate key
     */
    protected function onDuplicateKey()
    {
        return '`timestamp` = '.$this->quote($this->fields['timestamp']).'
              , `action` = '.$this->quote($this->fields['action']).'
              , `time` = '.$this->quote($this->fields['time']).'';
    }   // onDuplicateKey()

    /**
     * Table name
     *
     * @var string $table Table name
     */
    protected $table = 'pvlng_performance';

    /**
     * SQL for creation
     *
     * @var string $createSQL
     */
    protected $createSQL = '
        CREATE TABLE `pvlng_performance` (
          `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
          `action` enum(\'read\',\'write\') NOT NULL DEFAULT \'read\',
          `time` int(10) unsigned NOT NULL DEFAULT \'0\' COMMENT \'ms\',
          KEY `timestamp` (`timestamp`)
        ) ENGINE=MEMORY DEFAULT CHARSET=utf8 COMMENT=\'Gather system performance\'
    ';

    /**
     *
     */
    protected $fields = array(
        'timestamp' => '',
        'action'    => '',
        'time'      => ''
    );

    /**
     *
     */
    protected $nullable = array(
        'timestamp' => false,
        'action'    => false,
        'time'      => false
    );

    /**
     *
     */
    protected $primary = array(

    );

    /**
     *
     */
    protected $autoinc = '';

}
