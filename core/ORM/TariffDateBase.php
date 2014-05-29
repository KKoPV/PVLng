<?php
/**
 * Base access class for "pvlng_tariff_date"
 *
 * NEVER EVER EDIT THIS FILE
 *
 * To extend the functionallity, edit "TariffDate.php"
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
abstract class TariffDateBase extends \slimMVC\ORM {

    // -----------------------------------------------------------------------
    // PUBLIC
    // -----------------------------------------------------------------------

    /**
     * Forge method for key field(s)
     *
     * @param mixed Field value
     */
    public static function forge( $id, $date ) {
        return new static(array($id, $date));
    } // forge()

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
    } // setId()

    /**
     * Basic setter for field 'date'
     *
     * @param  mixed    $date Date value
     * @return Instance For fluid interface
     */
    public function setDate( $date ) {
        $this->fields['date'] = $date;
        return $this;
    } // setDate()

    /**
     * Basic setter for field 'cost'
     *
     * @param  mixed    $cost Cost value
     * @return Instance For fluid interface
     */
    public function setCost( $cost ) {
        $this->fields['cost'] = $cost;
        return $this;
    } // setCost()

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
    } // getId()

    /**
     * Basic getter for field 'date'
     *
     * @return mixed Date value
     */
    public function getDate() {
        return $this->fields['date'];
    } // getDate()

    /**
     * Basic getter for field 'cost'
     *
     * @return mixed Cost value
     */
    public function getCost() {
        return $this->fields['cost'];
    } // getCost()

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
    } // filterById()

    /**
     * Filter for field date
     *
     * @param mixed Field value
     */
    public function filterByDate( $date ) {
        return $this->filter('date', $date);
    } // filterByDate()

    /**
     * Filter for field cost
     *
     * @param mixed Field value
     */
    public function filterByCost( $cost ) {
        return $this->filter('cost', $cost);
    } // filterByCost()

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
