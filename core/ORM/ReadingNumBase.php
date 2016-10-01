<?php
/**
 * Abstract base class for table "pvlng_reading_num"
 *
 * *** NEVER EVER EDIT THIS FILE! ***
 *
 * To extend the functionallity, edit "ReadingNum.php"!
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
abstract class ReadingNumBase extends \slimMVC\ORM
{

    // -----------------------------------------------------------------------
    // PUBLIC
    // -----------------------------------------------------------------------

    // -----------------------------------------------------------------------
    // Setter methods
    // -----------------------------------------------------------------------

    /**
     * Basic setter for field "id"
     *
     * @param  mixed    $id Id value
     * @return Instance For fluid interface
     */
    public function setId($id)
    {
        $this->fields['id'] = $id;
        return $this;
    }   // setId()

    /**
     * Raw setter for field "id", for INSERT, REPLACE and UPDATE
     *
     * @param  mixed    $id Id value
     * @return Instance For fluid interface
     */
    public function setIdRaw($id)
    {
        $this->raw['id'] = $id;
        return $this;
    }   // setIdRaw()

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
     * Basic getter for field "id"
     *
     * @return mixed Id value
     */
    public function getId()
    {
        return $this->fields['id'];
    }   // getId()

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
     * Filter for unique fields "id', 'timestamp"
     *
     * @param  mixed    $id, $timestamp Filter values
     * @return Instance For fluid interface
     */
    public function filterByIdTimestamp($id, $timestamp)
    {

        $this->filter[] = $this->field('id').' = '.$this->quote($id).'';
        $this->filter[] = $this->field('timestamp').' = '.$this->quote($timestamp).'';
        return $this;
    }   // filterByIdTimestamp()

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
     * Filter for field "id"
     *
     * @param  mixed    $id Filter value
     * @return Instance For fluid interface
     */
    public function filterById($id)
    {
        $this->filter[] = $this->field('id').' = '.$this->quote($id);
        return $this;
    }   // filterById()

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
    }   // filterByData()

    // -----------------------------------------------------------------------
    // PROTECTED
    // -----------------------------------------------------------------------

    /**
     * Update fields on insert on duplicate key
     */
    protected function onDuplicateKey()
    {
        return '`data` = '.$this->quote($this->fields['data']).'';
    }   // onDuplicateKey()

    /**
     * Table name
     *
     * @var string $table Table name
     */
    protected $table = 'pvlng_reading_num';

    /**
     * SQL for creation
     *
     * @var string $createSQL
     */
    protected $createSQL = '
        CREATE TABLE `pvlng_reading_num` (
          `id` smallint(5) unsigned NOT NULL DEFAULT \'0\',
          `timestamp` int(10) unsigned NOT NULL DEFAULT \'0\',
          `data` decimal(13,4) NOT NULL DEFAULT \'0.0000\',
          PRIMARY KEY (`id`,`timestamp`),
          KEY `timestamp` (`timestamp`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT=\'Numeric readings\'
        /*!50100 PARTITION BY LINEAR KEY (id)
        PARTITIONS 50 */
    ';

    /**
     *
     */
    protected $fields = array(
        'id'        => '',
        'timestamp' => '',
        'data'      => ''
    );

    /**
     *
     */
    protected $nullable = array(
        'id'        => false,
        'timestamp' => false,
        'data'      => false
    );

    /**
     *
     */
    protected $primary = array(
        'id',
        'timestamp'
    );

    /**
     *
     */
    protected $autoinc = '';

}
