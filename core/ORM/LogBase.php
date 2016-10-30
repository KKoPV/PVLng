<?php
/**
 * Abstract base class for table "pvlng_log"
 *
 * *** NEVER EVER EDIT THIS FILE! ***
 *
 * To extend the functionallity, edit "Log.php"!
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
abstract class LogBase extends \slimMVC\ORM
{

    // -----------------------------------------------------------------------
    // PUBLIC
    // -----------------------------------------------------------------------

    // -----------------------------------------------------------------------
    // Setter methods
    // -----------------------------------------------------------------------

    /**
     * "id" is AutoInc, no setter
     */

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
     * Basic getter for field "id"
     *
     * @return mixed Id value
     */
    public function getId()
    {
        return $this->fields['id'];
    }

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
     * Basic getter for field "scope"
     *
     * @return mixed Scope value
     */
    public function getScope()
    {
        return $this->fields['scope'];
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
     * Filter for field "id"
     *
     * @param  mixed    $id Filter value
     * @return Instance For fluid interface
     */
    public function filterById($id)
    {
        $this->filter[] = $this->field('id').' = '.$this->quote($id);
        return $this;
    }

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
    }

    /**
     * Filter for field "scope"
     *
     * @param  mixed    $scope Filter value
     * @return Instance For fluid interface
     */
    public function filterByScope($scope)
    {
        $this->filter[] = $this->field('scope').' = '.$this->quote($scope);
        return $this;
    }

    /**
     * Filter for field "data"
     *
     * @param  mixed    $data Filter value
     * @return Instance For fluid interface
     */
    public function filterByData($data)
    {
        $this->filter[] = $this->field('data').' = '.$this->quote($data);
        return $this;
    }

    // -----------------------------------------------------------------------
    // PROTECTED
    // -----------------------------------------------------------------------

    /**
     * Table name
     *
     * @var string $table Table name
     */
    protected $table = 'pvlng_log';

    /**
     * SQL for creation
     *
     * @var string $createSQL
     */
    protected $createSQL = '
        CREATE TABLE `pvlng_log` (
          `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
          `timestamp` datetime NOT NULL DEFAULT \'2000-01-01 00:00:00\',
          `scope` varchar(40) NOT NULL DEFAULT \'\',
          `data` text,
          PRIMARY KEY (`id`),
          KEY `timestamp` (`timestamp`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT=\'Logging messages\'
    ';

    /**
     *
     */
    protected $fields = array(
        'id'        => '',
        'timestamp' => '',
        'scope'     => '',
        'data'      => ''
    );

    /**
     *
     */
    protected $nullable = array(
        'id'        => false,
        'timestamp' => false,
        'scope'     => false,
        'data'      => true
    );

    /**
     *
     */
    protected $primary = array(
        'id'
    );

    /**
     *
     */
    protected $autoinc = 'id';

}
