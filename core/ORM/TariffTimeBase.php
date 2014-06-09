<?php
/**
 * Abstract base class for table 'pvlng_tariff_time'
 *
 * *** NEVER EVER EDIT THIS FILE! ***
 *
 * To extend the functionallity, edit "TariffTime.php"
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
abstract class TariffTimeBase extends \slimMVC\ORM {

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
     * Basic setter for field 'time'
     *
     * @param  mixed    $time Time value
     * @return Instance For fluid interface
     */
    public function setTime( $time ) {
        $this->fields['time'] = $time;
        return $this;
    }   // setTime()

    /**
     * Basic setter for field 'days'
     *
     * @param  mixed    $days Days value
     * @return Instance For fluid interface
     */
    public function setDays( $days ) {
        $this->fields['days'] = $days;
        return $this;
    }   // setDays()

    /**
     * Basic setter for field 'tariff'
     *
     * @param  mixed    $tariff Tariff value
     * @return Instance For fluid interface
     */
    public function setTariff( $tariff ) {
        $this->fields['tariff'] = $tariff;
        return $this;
    }   // setTariff()

    /**
     * Basic setter for field 'comment'
     *
     * @param  mixed    $comment Comment value
     * @return Instance For fluid interface
     */
    public function setComment( $comment ) {
        $this->fields['comment'] = $comment;
        return $this;
    }   // setComment()

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
     * Basic getter for field 'time'
     *
     * @return mixed Time value
     */
    public function getTime() {
        return $this->fields['time'];
    }   // getTime()

    /**
     * Basic getter for field 'days'
     *
     * @return mixed Days value
     */
    public function getDays() {
        return $this->fields['days'];
    }   // getDays()

    /**
     * Basic getter for field 'tariff'
     *
     * @return mixed Tariff value
     */
    public function getTariff() {
        return $this->fields['tariff'];
    }   // getTariff()

    /**
     * Basic getter for field 'comment'
     *
     * @return mixed Comment value
     */
    public function getComment() {
        return $this->fields['comment'];
    }   // getComment()

    // -----------------------------------------------------------------------
    // Filter methods
    // -----------------------------------------------------------------------

    /**
     * Filter for unique fields 'id', 'date', 'time', 'days'
     *
     * @param  mixed    $id, $date, $time, $days Filter values
     * @return Instance For fluid interface
     */
    public function filterByIdDateTimeDays( $id, $date, $time, $days ) {
        $this->filter[] = '`id` = "'.$this->quote($id).'"';
        $this->filter[] = '`date` = "'.$this->quote($date).'"';
        $this->filter[] = '`time` = "'.$this->quote($time).'"';
        $this->filter[] = '`days` = "'.$this->quote($days).'"';
        return $this;
    }   // filterByIdDateTimeDays()

    /**
     * Filter for field 'days'
     *
     * @param  mixed    $days Filter value
     * @return Instance For fluid interface
     */
    public function filterByDays( $days ) {
        $this->filter[] = '`days` = "'.$this->quote($days).'"';
        return $this;
    }   // filterByDays()

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
     * Filter for field 'time'
     *
     * @param  mixed    $time Filter value
     * @return Instance For fluid interface
     */
    public function filterByTime( $time ) {
        $this->filter[] = '`time` = "'.$this->quote($time).'"';
        return $this;
    }   // filterByTime()

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
     * Filter for field 'tariff'
     *
     * @param  mixed    $tariff Filter value
     * @return Instance For fluid interface
     */
    public function filterByTariff( $tariff ) {
        $this->filter[] = '`tariff` = "'.$this->quote($tariff).'"';
        return $this;
    }   // filterByTariff()

    /**
     * Filter for field 'comment'
     *
     * @param  mixed    $comment Filter value
     * @return Instance For fluid interface
     */
    public function filterByComment( $comment ) {
        $this->filter[] = '`comment` = "'.$this->quote($comment).'"';
        return $this;
    }   // filterByComment()

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
