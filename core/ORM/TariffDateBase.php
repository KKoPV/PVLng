<?php
/**
 * Abstract base class for table 'pvlng_tariff_date'
 *
 * *** NEVER EVER EDIT THIS FILE! ***
 *
 * To extend the functionallity, edit "TariffDate.php"
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
abstract class TariffDateBase extends \slimMVC\ORM {

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
     * Basic setter for field 'date'
     *
     * @param  mixed    $date Date value
     * @return Instance For fluid interface
     */
    public function setDate( $date ) {
        $this->fields['date'] = $date;
        return $this;
    }   // setDate()

    /**
     * Basic setter for field 'cost'
     *
     * @param  mixed    $cost Cost value
     * @return Instance For fluid interface
     */
    public function setCost( $cost ) {
        $this->fields['cost'] = $cost;
        return $this;
    }   // setCost()

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
     * Basic getter for field 'date'
     *
     * @return mixed Date value
     */
    public function getDate() {
        return $this->fields['date'];
    }   // getDate()

    /**
     * Basic getter for field 'cost'
     *
     * @return mixed Cost value
     */
    public function getCost() {
        return $this->fields['cost'];
    }   // getCost()

    // -----------------------------------------------------------------------
    // Filter methods
    // -----------------------------------------------------------------------

    /**
     * Filter for unique fields 'id', 'date'
     *
     * @param  mixed    $id, $date Filter values
     * @return Instance For fluid interface
     */
    public function filterByIdDate( $id, $date ) {
        $this->filter[] = '`id` = "'.$this->quote($id).'"';
        $this->filter[] = '`date` = "'.$this->quote($date).'"';
        return $this;
    }   // filterByIdDate()

    /**
     * Filter for field 'date'
     *
     * @param  mixed    $date Filter value
     * @return Instance For fluid interface
     */
    public function filterByDate( $date ) {
        $this->filter[] = '`date` = "'.$this->quote($date).'"';
        return $this;
    }   // filterByDate()

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
     * Filter for field 'cost'
     *
     * @param  mixed    $cost Filter value
     * @return Instance For fluid interface
     */
    public function filterByCost( $cost ) {
        $this->filter[] = '`cost` = "'.$this->quote($cost).'"';
        return $this;
    }   // filterByCost()

    // -----------------------------------------------------------------------
    // PROTECTED
    // -----------------------------------------------------------------------

    /**
     * Table name
     *
     * @var string $table Table name
     */
    protected $table = 'pvlng_tariff_date';

}
