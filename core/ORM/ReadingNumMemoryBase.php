<?php
/**
 * Abstract base class for table 'pvlng_reading_num_tmp'
 *
 * *** NEVER EVER EDIT THIS FILE! ***
 *
 * To extend the functionallity, edit "ReadingNumMemory.php"
 *
 * If you make changes here, they will be lost on next upgrade PVLng!
 *
 * @author     Knut Kohl <github@knutkohl.de>
 * @copyright  2015 Knut Kohl
 * @license    MIT License (MIT) http://opensource.org/licenses/MIT
 *
 * @author     PVLng ORM class builder
 * @version    1.2.0 / 2015-03-18
 */
namespace ORM;

/**
 *
 */
abstract class ReadingNumMemoryBase extends \slimMVC\ORM {

    // -----------------------------------------------------------------------
    // PUBLIC
    // -----------------------------------------------------------------------

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
    }   // setId()

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
     * Basic getter for field 'id'
     *
     * @return mixed Id value
     */
    public function getId() {
        return $this->fields['id'];
    }   // getId()

    /**
     * Basic getter for field 'timestamp'
     *
     * @return mixed Timestamp value
     */
    public function getTimestamp() {
        return $this->fields['timestamp'];
    }   // getTimestamp()

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
     * Filter for unique fields 'id', 'timestamp'
     *
     * @param  mixed    $id, $timestamp Filter values
     * @return Instance For fluid interface
     */
    public function filterByIdTimestamp( $id, $timestamp ) {
        $this->filter[] = '`id` = "'.$this->quote($id).'"';
        $this->filter[] = '`timestamp` = "'.$this->quote($timestamp).'"';
        return $this;
    }   // filterByIdTimestamp()

    /**
     * Filter for field 'id'
     *
     * @param  mixed    $id Filter value
     * @return Instance For fluid interface
     */
    public function filterById( $id ) {
        $this->filter[] = '`id` = "'.$this->quote($id).'"';
        return $this;
    }   // filterById()

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
    protected $table = 'pvlng_reading_num_tmp';

    /**
     * SQL for creation
     *
     * @var string $createSQL
     */
    protected $createSQL = '
        CREATE TABLE `pvlng_reading_num_tmp` (
          `id` smallint(5) unsigned NOT NULL,
          `timestamp` int(11) NOT NULL,
          `data` decimal(13,4) NOT NULL,
          PRIMARY KEY (`id`,`timestamp`)
        ) ENGINE=MEMORY DEFAULT CHARSET=utf8
        /*!50100 PARTITION BY LINEAR KEY (id)
        PARTITIONS 10 */
    ';

}
