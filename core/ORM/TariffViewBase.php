<?php
/**
 * Base access class for "pvlng_tariff_view"
 *
 * NEVER EVER EDIT THIS FILE
 *
 * To extend the functionallity, edit "TariffView.php"
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
abstract class TariffViewBase extends \slimMVC\ORM {

    // -----------------------------------------------------------------------
    // PUBLIC
    // -----------------------------------------------------------------------

    /**
     * Forge method for key field(s)
     *
     * @param mixed Field value
     */
    public static function forge( $id, $date, $time, $days ) {
        return new static(array($id, $date, $time, $days));
    } // forge()

    // -----------------------------------------------------------------------
    // Setter methods
    // -----------------------------------------------------------------------

    /**
     * 'pvlng_tariff_view' is a view, no setters
     */

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
     * Basic getter for field 'name'
     *
     * @return mixed Name value
     */
    public function getName() {
        return $this->fields['name'];
    } // getName()

    /**
     * Basic getter for field 'tariff_comment'
     *
     * @return mixed TariffComment value
     */
    public function getTariffComment() {
        return $this->fields['tariff_comment'];
    } // getTariffComment()

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

    /**
     * Basic getter for field 'time'
     *
     * @return mixed Time value
     */
    public function getTime() {
        return $this->fields['time'];
    } // getTime()

    /**
     * Basic getter for field 'days'
     *
     * @return mixed Days value
     */
    public function getDays() {
        return $this->fields['days'];
    } // getDays()

    /**
     * Basic getter for field 'tariff'
     *
     * @return mixed Tariff value
     */
    public function getTariff() {
        return $this->fields['tariff'];
    } // getTariff()

    /**
     * Basic getter for field 'time_comment'
     *
     * @return mixed TimeComment value
     */
    public function getTimeComment() {
        return $this->fields['time_comment'];
    } // getTimeComment()

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
     * Filter for field name
     *
     * @param mixed Field value
     */
    public function filterByName( $name ) {
        return $this->filter('name', $name);
    } // filterByName()

    /**
     * Filter for field tariff_comment
     *
     * @param mixed Field value
     */
    public function filterByTariffComment( $tariff_comment ) {
        return $this->filter('tariff_comment', $tariff_comment);
    } // filterByTariffComment()

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

    /**
     * Filter for field time
     *
     * @param mixed Field value
     */
    public function filterByTime( $time ) {
        return $this->filter('time', $time);
    } // filterByTime()

    /**
     * Filter for field days
     *
     * @param mixed Field value
     */
    public function filterByDays( $days ) {
        return $this->filter('days', $days);
    } // filterByDays()

    /**
     * Filter for field tariff
     *
     * @param mixed Field value
     */
    public function filterByTariff( $tariff ) {
        return $this->filter('tariff', $tariff);
    } // filterByTariff()

    /**
     * Filter for field time_comment
     *
     * @param mixed Field value
     */
    public function filterByTimeComment( $time_comment ) {
        return $this->filter('time_comment', $time_comment);
    } // filterByTimeComment()

    // -----------------------------------------------------------------------
    // PROTECTED
    // -----------------------------------------------------------------------

    /**
     * Table name
     *
     * @var string $table Table name
     */
    protected $table = 'pvlng_tariff_view';

}
