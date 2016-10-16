<?php
/**
 * Abstract base class for table "pvlng_tariff_time"
 *
 * *** NEVER EVER EDIT THIS FILE! ***
 *
 * To extend the functionallity, edit "TariffTime.php"!
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
abstract class TariffTimeBase extends \slimMVC\ORM
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
     * Basic setter for field "time"
     *
     * @param  mixed    $time Time value
     * @return Instance For fluid interface
     */
    public function setTime($time)
    {
        $this->fields['time'] = $time;
        return $this;
    }   // setTime()

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
    }   // setTimeRaw()

    /**
     * Basic setter for field "days"
     *
     * @param  mixed    $days Days value
     * @return Instance For fluid interface
     */
    public function setDays($days)
    {
        $this->fields['days'] = $days;
        return $this;
    }   // setDays()

    /**
     * Raw setter for field "days", for INSERT, REPLACE and UPDATE
     *
     * @param  mixed    $days Days value
     * @return Instance For fluid interface
     */
    public function setDaysRaw($days)
    {
        $this->raw['days'] = $days;
        return $this;
    }   // setDaysRaw()

    /**
     * Basic setter for field "tariff"
     *
     * @param  mixed    $tariff Tariff value
     * @return Instance For fluid interface
     */
    public function setTariff($tariff)
    {
        $this->fields['tariff'] = $tariff;
        return $this;
    }   // setTariff()

    /**
     * Raw setter for field "tariff", for INSERT, REPLACE and UPDATE
     *
     * @param  mixed    $tariff Tariff value
     * @return Instance For fluid interface
     */
    public function setTariffRaw($tariff)
    {
        $this->raw['tariff'] = $tariff;
        return $this;
    }   // setTariffRaw()

    /**
     * Basic setter for field "comment"
     *
     * @param  mixed    $comment Comment value
     * @return Instance For fluid interface
     */
    public function setComment($comment)
    {
        $this->fields['comment'] = $comment;
        return $this;
    }   // setComment()

    /**
     * Raw setter for field "comment", for INSERT, REPLACE and UPDATE
     *
     * @param  mixed    $comment Comment value
     * @return Instance For fluid interface
     */
    public function setCommentRaw($comment)
    {
        $this->raw['comment'] = $comment;
        return $this;
    }   // setCommentRaw()

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
     * Basic getter for field "time"
     *
     * @return mixed Time value
     */
    public function getTime()
    {
        return $this->fields['time'];
    }   // getTime()

    /**
     * Basic getter for field "days"
     *
     * @return mixed Days value
     */
    public function getDays()
    {
        return $this->fields['days'];
    }   // getDays()

    /**
     * Basic getter for field "tariff"
     *
     * @return mixed Tariff value
     */
    public function getTariff()
    {
        return $this->fields['tariff'];
    }   // getTariff()

    /**
     * Basic getter for field "comment"
     *
     * @return mixed Comment value
     */
    public function getComment()
    {
        return $this->fields['comment'];
    }   // getComment()

    // -----------------------------------------------------------------------
    // Filter methods
    // -----------------------------------------------------------------------

    /**
     * Filter for unique fields "id', 'date', 'time', 'days"
     *
     * @param  mixed    $id, $date, $time, $days Filter values
     * @return Instance For fluid interface
     */
    public function filterByIdDateTimeDays($id, $date, $time, $days)
    {

        $this->filter[] = $this->field('id').' = '.$this->quote($id).'';
        $this->filter[] = $this->field('date').' = '.$this->quote($date).'';
        $this->filter[] = $this->field('time').' = '.$this->quote($time).'';
        $this->filter[] = $this->field('days').' = '.$this->quote($days).'';
        return $this;
    }   // filterByIdDateTimeDays()

    /**
     * Filter for field "days"
     *
     * @param  mixed    $days Filter value
     * @return Instance For fluid interface
     */
    public function filterByDays($days)
    {
        $this->filter[] = $this->field('days').' = '.$this->quote($days);
        return $this;
    }   // filterByDays()

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
     * Filter for field "time"
     *
     * @param  mixed    $time Filter value
     * @return Instance For fluid interface
     */
    public function filterByTime($time)
    {
        $this->filter[] = $this->field('time').' = '.$this->quote($time);
        return $this;
    }   // filterByTime()

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
     * Filter for field "tariff"
     *
     * @param  mixed    $tariff Filter value
     * @return Instance For fluid interface
     */
    public function filterByTariff($tariff)
    {
        $this->filter[] = $this->field('tariff').' = '.$this->quote($tariff);
        return $this;
    }   // filterByTariff()

    /**
     * Filter for field "comment"
     *
     * @param  mixed    $comment Filter value
     * @return Instance For fluid interface
     */
    public function filterByComment($comment)
    {
        $this->filter[] = $this->field('comment').' = '.$this->quote($comment);
        return $this;
    }   // filterByComment()

    // -----------------------------------------------------------------------
    // PROTECTED
    // -----------------------------------------------------------------------

    /**
     * Update fields on insert on duplicate key
     */
    protected function onDuplicateKey()
    {
        return '`tariff` = '.$this->quote($this->fields['tariff']).'
              , `comment` = '.$this->quote($this->fields['comment']).'';
    }   // onDuplicateKey()

    /**
     * Table name
     *
     * @var string $table Table name
     */
    protected $table = 'pvlng_tariff_time';

    /**
     * SQL for creation
     *
     * @var string $createSQL
     */
    protected $createSQL = '
        CREATE TABLE `pvlng_tariff_time` (
          `id` int(10) unsigned NOT NULL DEFAULT \'0\' COMMENT \'pvlng_tariff_date -> id\',
          `date` date NOT NULL DEFAULT \'2000-01-01\' COMMENT \'pvlng_tariff_date -> date\',
          `time` time NOT NULL DEFAULT \'00:00:00\' COMMENT \'Starting time (incl.)\',
          `days` set(\'1\',\'2\',\'3\',\'4\',\'5\',\'6\',\'7\') NOT NULL DEFAULT \'1\' COMMENT \'1 Mo .. 7 Su\',
          `tariff` float DEFAULT NULL COMMENT \'e.g. EUR / kWh\',
          `comment` varchar(250) NOT NULL DEFAULT \'\',
          PRIMARY KEY (`id`,`date`,`time`,`days`),
          KEY `days` (`days`),
          KEY `date` (`date`),
          KEY `time` (`time`),
          CONSTRAINT `pvlng_tariff_time_ibfk_1` FOREIGN KEY (`id`, `date`) REFERENCES `pvlng_tariff_date` (`id`, `date`) ON DELETE CASCADE ON UPDATE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8
    ';

    /**
     *
     */
    protected $fields = array(
        'id'      => '',
        'date'    => '',
        'time'    => '',
        'days'    => '',
        'tariff'  => '',
        'comment' => ''
    );

    /**
     *
     */
    protected $nullable = array(
        'id'      => false,
        'date'    => false,
        'time'    => false,
        'days'    => false,
        'tariff'  => true,
        'comment' => false
    );

    /**
     *
     */
    protected $primary = array(
        'id',
        'date',
        'time',
        'days'
    );

    /**
     *
     */
    protected $autoinc = '';

}
