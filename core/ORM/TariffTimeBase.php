<?php
/**
 * Abstract base class for table "pvlng_tariff_time"
 *
 *****************************************************************************
 *                       NEVER EVER EDIT THIS FILE!
 *****************************************************************************
 * To extend functionallity edit "TariffTime.php"
 * If you make changes here, they will be lost on next upgrade!
 *
 * @author     Knut Kohl <github@knutkohl.de>
 * @copyright  2017 Knut Kohl
 * @license    MIT License (MIT) http://opensource.org/licenses/MIT
 *
 * @author     ORM class builder
 * @version    2.0.0 / 2017-08-17
 */
namespace ORM;

/**
 *
 */
use Core\ORM;

/**
 *
 */
abstract class TariffTimeBase extends ORM
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
    }

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
    }

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
    }

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
    }

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
    }

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
    }

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
    }

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
    }

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
    }

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
    }

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
    }

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
     * Basic getter for field "date"
     *
     * @return mixed Date value
     */
    public function getDate()
    {
        return $this->fields['date'];
    }

    /**
     * Basic getter for field "time"
     *
     * @return mixed Time value
     */
    public function getTime()
    {
        return $this->fields['time'];
    }

    /**
     * Basic getter for field "days"
     *
     * @return mixed Days value
     */
    public function getDays()
    {
        return $this->fields['days'];
    }

    /**
     * Basic getter for field "tariff"
     *
     * @return mixed Tariff value
     */
    public function getTariff()
    {
        return $this->fields['tariff'];
    }

    /**
     * Basic getter for field "comment"
     *
     * @return mixed Comment value
     */
    public function getComment()
    {
        return $this->fields['comment'];
    }

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
        $this->filter('id', $id);
        $this->filter('date', $date);
        $this->filter('time', $time);
        $this->filter('days', $days);
        return $this;
    }

    /**
     * Filter for field "days"
     *
     * @param  mixed    $days Filter value
     * @return Instance For fluid interface
     */
    public function filterByDays($days)
    {
        return $this->filter('days', $days);
    }

    /**
     * Filter for field "date"
     *
     * @param  mixed    $date Filter value
     * @return Instance For fluid interface
     */
    public function filterByDate($date)
    {
        return $this->filter('date', $date);
    }

    /**
     * Filter for field "time"
     *
     * @param  mixed    $time Filter value
     * @return Instance For fluid interface
     */
    public function filterByTime($time)
    {
        return $this->filter('time', $time);
    }

    /**
     * Filter for field "id"
     *
     * @param  mixed    $id Filter value
     * @return Instance For fluid interface
     */
    public function filterById($id)
    {
        return $this->filter('id', $id);
    }

    /**
     * Filter for field "tariff"
     *
     * @param  mixed    $tariff Filter value
     * @return Instance For fluid interface
     */
    public function filterByTariff($tariff)
    {
        return $this->filter('tariff', $tariff);
    }

    /**
     * Filter for field "comment"
     *
     * @param  mixed    $comment Filter value
     * @return Instance For fluid interface
     */
    public function filterByComment($comment)
    {
        return $this->filter('comment', $comment);
    }

    // -----------------------------------------------------------------------
    // PROTECTED
    // -----------------------------------------------------------------------

    /**
     * Update fields on insert on duplicate key
     */
    protected function onDuplicateKey()
    {
        return '`tariff` = VALUES(`tariff`)
              , `comment` = VALUES(`comment`)';
    }

    /**
     * Call create table sql on class creation and set to false
     */
    protected static $memory = false;

    /**
     * SQL for creation
     *
     * @var string $createSQL
     */
    // @codingStandardsIgnoreStart
    protected static $createSQL = '
        CREATE TABLE IF NOT EXISTS `pvlng_tariff_time` (
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
    // @codingStandardsIgnoreEnd

    /**
     * Table name
     *
     * @var string $table Table name
     */
    protected $table = 'pvlng_tariff_time';

    /**
     *
     */
    protected $fields = [
        'id'      => '',
        'date'    => '',
        'time'    => '',
        'days'    => '',
        'tariff'  => '',
        'comment' => ''
    ];

    /**
     *
     */
    protected $nullable = [
        'id'      => false,
        'date'    => false,
        'time'    => false,
        'days'    => false,
        'tariff'  => true,
        'comment' => false
    ];

    /**
     *
     */
    protected $primary = ['id', 'date', 'time', 'days'];

    /**
     *
     */
    protected $autoinc = '';
}
