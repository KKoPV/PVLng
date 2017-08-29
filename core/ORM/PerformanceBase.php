<?php
/**
 * Abstract base class for table "pvlng_performance"
 *
 *****************************************************************************
 *                       NEVER EVER EDIT THIS FILE!
 *****************************************************************************
 * To extend functionallity edit "Performance.php"
 * If you make changes here, they will be lost on next upgrade!
 *
 * @author     Knut Kohl <github@knutkohl.de>
 * @copyright  2017 Knut Kohl
 * @license    MIT License (MIT) http://opensource.org/licenses/MIT
 *
 * @author     ORM class builder
 * @version    2.0.0 / 2017-08-17
 */
namespace ORM;

/**
 *
 */
use Core\ORM;

/**
 *
 */
abstract class PerformanceBase extends ORM
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
    }

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
    }

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
    }

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
    }

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
    }

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
    }

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
    }

    /**
     * Basic getter for field "action"
     *
     * @return mixed Action value
     */
    public function getAction()
    {
        return $this->fields['action'];
    }

    /**
     * Basic getter for field "time"
     *
     * @return mixed Time value
     */
    public function getTime()
    {
        return $this->fields['time'];
    }

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
        return $this->filter('timestamp', $timestamp);
    }

    /**
     * Filter for field "action"
     *
     * @param  mixed    $action Filter value
     * @return Instance For fluid interface
     */
    public function filterByAction($action)
    {
        return $this->filter('action', $action);
    }

    /**
     * Filter for field "time"
     *
     * @param  mixed    $time Filter value
     * @return Instance For fluid interface
     */
    public function filterByTime($time)
    {
        return $this->filter('time', $time);
    }

    // -----------------------------------------------------------------------
    // PROTECTED
    // -----------------------------------------------------------------------

    /**
     * Call create table sql on class creation and set to false
     */
    protected static $memory = true;

    /**
     * SQL for creation
     *
     * @var string $createSQL
     */
    // @codingStandardsIgnoreStart
    protected static $createSQL = '
        CREATE TABLE IF NOT EXISTS `pvlng_performance` (
          `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
          `action` enum(\'read\',\'write\') NOT NULL DEFAULT \'read\',
          `time` int(10) unsigned NOT NULL DEFAULT \'0\' COMMENT \'ms\',
          KEY `timestamp` (`timestamp`)
        ) ENGINE=MEMORY DEFAULT CHARSET=utf8 COMMENT=\'Gather system performance\'
    ';
    // @codingStandardsIgnoreEnd

    /**
     * Table name
     *
     * @var string $table Table name
     */
    protected $table = 'pvlng_performance';

    /**
     *
     */
    protected $fields = [
        'timestamp' => '',
        'action'    => '',
        'time'      => ''
    ];

    /**
     *
     */
    protected $nullable = [
        'timestamp' => false,
        'action'    => false,
        'time'      => false
    ];

    /**
     *
     */
    protected $primary = [];

    /**
     *
     */
    protected $autoinc = '';
}
