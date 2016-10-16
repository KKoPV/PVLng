<?php
/**
 * Abstract base class for table "pvlng_tariff_date"
 *
 * *** NEVER EVER EDIT THIS FILE! ***
 *
 * To extend the functionallity, edit "TariffDate.php"!
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
abstract class TariffDateBase extends \slimMVC\ORM
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
     * Basic setter for field "date"
     *
     * @param  mixed    $date Date value
     * @return Instance For fluid interface
     */
    public function setDate($date)
    {
        $this->fields['date'] = $date;
        return $this;
    }   // setDate()

    /**
     * Raw setter for field "date", for INSERT, REPLACE and UPDATE
     *
     * @param  mixed    $date Date value
     * @return Instance For fluid interface
     */
    public function setDateRaw($date)
    {
        $this->raw['date'] = $date;
        return $this;
    }   // setDateRaw()

    /**
     * Basic setter for field "cost"
     *
     * @param  mixed    $cost Cost value
     * @return Instance For fluid interface
     */
    public function setCost($cost)
    {
        $this->fields['cost'] = $cost;
        return $this;
    }   // setCost()

    /**
     * Raw setter for field "cost", for INSERT, REPLACE and UPDATE
     *
     * @param  mixed    $cost Cost value
     * @return Instance For fluid interface
     */
    public function setCostRaw($cost)
    {
        $this->raw['cost'] = $cost;
        return $this;
    }   // setCostRaw()

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
     * Basic getter for field "date"
     *
     * @return mixed Date value
     */
    public function getDate()
    {
        return $this->fields['date'];
    }   // getDate()

    /**
     * Basic getter for field "cost"
     *
     * @return mixed Cost value
     */
    public function getCost()
    {
        return $this->fields['cost'];
    }   // getCost()

    // -----------------------------------------------------------------------
    // Filter methods
    // -----------------------------------------------------------------------

    /**
     * Filter for unique fields "id', 'date"
     *
     * @param  mixed    $id, $date Filter values
     * @return Instance For fluid interface
     */
    public function filterByIdDate($id, $date)
    {

        $this->filter[] = $this->field('id').' = '.$this->quote($id).'';
        $this->filter[] = $this->field('date').' = '.$this->quote($date).'';
        return $this;
    }   // filterByIdDate()

    /**
     * Filter for field "date"
     *
     * @param  mixed    $date Filter value
     * @return Instance For fluid interface
     */
    public function filterByDate($date)
    {
        $this->filter[] = $this->field('date').' = '.$this->quote($date);
        return $this;
    }   // filterByDate()

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
     * Filter for field "cost"
     *
     * @param  mixed    $cost Filter value
     * @return Instance For fluid interface
     */
    public function filterByCost($cost)
    {
        $this->filter[] = $this->field('cost').' = '.$this->quote($cost);
        return $this;
    }   // filterByCost()

    // -----------------------------------------------------------------------
    // PROTECTED
    // -----------------------------------------------------------------------

    /**
     * Update fields on insert on duplicate key
     */
    protected function onDuplicateKey()
    {
        return '`cost` = '.$this->quote($this->fields['cost']).'';
    }   // onDuplicateKey()

    /**
     * Table name
     *
     * @var string $table Table name
     */
    protected $table = 'pvlng_tariff_date';

    /**
     * SQL for creation
     *
     * @var string $createSQL
     */
    protected $createSQL = '
        CREATE TABLE `pvlng_tariff_date` (
          `id` int(10) unsigned NOT NULL DEFAULT \'0\' COMMENT \'pvlng_tariff -> id\',
          `date` date NOT NULL DEFAULT \'2000-01-01\' COMMENT \'Start date for this tariff (incl.) \',
          `cost` float DEFAULT \'0\' COMMENT \'Fix costs per day, e.g. EUR / kWh\',
          PRIMARY KEY (`id`,`date`),
          KEY `date` (`date`),
          CONSTRAINT `pvlng_tariff_date_ibfk_2` FOREIGN KEY (`id`) REFERENCES `pvlng_tariff` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8
    ';

    /**
     *
     */
    protected $fields = array(
        'id'   => '',
        'date' => '',
        'cost' => ''
    );

    /**
     *
     */
    protected $nullable = array(
        'id'   => false,
        'date' => false,
        'cost' => true
    );

    /**
     *
     */
    protected $primary = array(
        'id',
        'date'
    );

    /**
     *
     */
    protected $autoinc = '';

}
