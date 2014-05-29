<?php
/**
 * Base access class for "pvlng_tariff_time"
 *
 * NEVER EVER EDIT THIS FILE
 *
 * To extend the functionallity, edit "TariffTime.php"
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
abstract class TariffTimeBase extends \slimMVC\ORM {

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
     * Basic setter for field 'time'
     *
     * @param  mixed    $time Time value
     * @return Instance For fluid interface
     */
    public function setTime( $time ) {
        $this->fields['time'] = $time;
        return $this;
    } // setTime()

    /**
     * Basic setter for field 'days'
     *
     * @param  mixed    $days Days value
     * @return Instance For fluid interface
     */
    public function setDays( $days ) {
        $this->fields['days'] = $days;
        return $this;
    } // setDays()

    /**
     * Basic setter for field 'tariff'
     *
     * @param  mixed    $tariff Tariff value
     * @return Instance For fluid interface
     */
    public function setTariff( $tariff ) {
        $this->fields['tariff'] = $tariff;
        return $this;
    } // setTariff()

    /**
     * Basic setter for field 'comment'
     *
     * @param  mixed    $comment Comment value
     * @return Instance For fluid interface
     */
    public function setComment( $comment ) {
        $this->fields['comment'] = $comment;
        return $this;
    } // setComment()

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
     * Basic getter for field 'comment'
     *
     * @return mixed Comment value
     */
    public function getComment() {
        return $this->fields['comment'];
    } // getComment()

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
     * Filter for field comment
     *
     * @param mixed Field value
     */
    public function filterByComment( $comment ) {
        return $this->filter('comment', $comment);
    } // filterByComment()

    // -----------------------------------------------------------------------
    // PROTECTED
    // -----------------------------------------------------------------------

    /**
     * Table name
     *
     * @var string $table Table name
     */
    protected $table = 'pvlng_tariff_time';

}
