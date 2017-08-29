<?php
/**
 * Abstract base class for table "pvlng_tariff_view"
 *
 *****************************************************************************
 *                       NEVER EVER EDIT THIS FILE!
 *****************************************************************************
 * To extend functionallity edit "TariffView.php"
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
abstract class TariffViewBase extends ORM
{

    // -----------------------------------------------------------------------
    // PUBLIC
    // -----------------------------------------------------------------------

    // -----------------------------------------------------------------------
    // Setter methods
    // -----------------------------------------------------------------------

    /**
     * "pvlng_tariff_view" is a view, no setters
     */

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
     * Basic getter for field "name"
     *
     * @return mixed Name value
     */
    public function getName()
    {
        return $this->fields['name'];
    }

    /**
     * Basic getter for field "tariff_comment"
     *
     * @return mixed TariffComment value
     */
    public function getTariffComment()
    {
        return $this->fields['tariff_comment'];
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
     * Basic getter for field "cost"
     *
     * @return mixed Cost value
     */
    public function getCost()
    {
        return $this->fields['cost'];
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
     * Basic getter for field "time_comment"
     *
     * @return mixed TimeComment value
     */
    public function getTimeComment()
    {
        return $this->fields['time_comment'];
    }

    // -----------------------------------------------------------------------
    // Filter methods
    // -----------------------------------------------------------------------

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
     * Filter for field "name"
     *
     * @param  mixed    $name Filter value
     * @return Instance For fluid interface
     */
    public function filterByName($name)
    {
        return $this->filter('name', $name);
    }

    /**
     * Filter for field "tariff_comment"
     *
     * @param  mixed    $tariff_comment Filter value
     * @return Instance For fluid interface
     */
    public function filterByTariffComment($tariff_comment)
    {
        return $this->filter('tariff_comment', $tariff_comment);
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
     * Filter for field "cost"
     *
     * @param  mixed    $cost Filter value
     * @return Instance For fluid interface
     */
    public function filterByCost($cost)
    {
        return $this->filter('cost', $cost);
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
     * Filter for field "time_comment"
     *
     * @param  mixed    $time_comment Filter value
     * @return Instance For fluid interface
     */
    public function filterByTimeComment($time_comment)
    {
        return $this->filter('time_comment', $time_comment);
    }

    // -----------------------------------------------------------------------
    // PROTECTED
    // -----------------------------------------------------------------------

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
        CREATE VIEW `pvlng_tariff_view` AS select `t1`.`id` AS `id`,`t1`.`name` AS `name`,`t1`.`comment` AS `tariff_comment`,`t2`.`date` AS `date`,`t2`.`cost` AS `cost`,`t3`.`time` AS `time`,`t3`.`days` AS `days`,`t3`.`tariff` AS `tariff`,`t3`.`comment` AS `time_comment` from ((`pvlng_tariff` `t1` left join `pvlng_tariff_date` `t2` on((`t1`.`id` = `t2`.`id`))) left join `pvlng_tariff_time` `t3` on(((`t2`.`id` = `t3`.`id`) and (`t2`.`date` = `t3`.`date`))))
    ';
    // @codingStandardsIgnoreEnd

    /**
     * Table name
     *
     * @var string $table Table name
     */
    protected $table = 'pvlng_tariff_view';

    /**
     *
     */
    protected $fields = [
        'id'             => '',
        'name'           => '',
        'tariff_comment' => '',
        'date'           => '',
        'cost'           => '',
        'time'           => '',
        'days'           => '',
        'tariff'         => '',
        'time_comment'   => ''
    ];

    /**
     *
     */
    protected $nullable = [

    ];

    /**
     *
     */
    protected $primary = [];

    /**
     *
     */
    protected $autoinc = '';
}
