<?php
/**
 * Abstract base class for table "pvlng_tariff_view"
 *
 * *** NEVER EVER EDIT THIS FILE! ***
 *
 * To extend the functionallity, edit "TariffView.php"!
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
abstract class TariffViewBase extends \slimMVC\ORM
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
    }   // getId()

    /**
     * Basic getter for field "name"
     *
     * @return mixed Name value
     */
    public function getName()
    {
        return $this->fields['name'];
    }   // getName()

    /**
     * Basic getter for field "tariff_comment"
     *
     * @return mixed TariffComment value
     */
    public function getTariffComment()
    {
        return $this->fields['tariff_comment'];
    }   // getTariffComment()

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
     * Basic getter for field "time_comment"
     *
     * @return mixed TimeComment value
     */
    public function getTimeComment()
    {
        return $this->fields['time_comment'];
    }   // getTimeComment()

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
        $this->filter[] = '`id` = '.$this->quote($id);
        return $this;
    }   // filterById()

    /**
     * Filter for field "name"
     *
     * @param  mixed    $name Filter value
     * @return Instance For fluid interface
     */
    public function filterByName($name)
    {
        $this->filter[] = '`name` = '.$this->quote($name);
        return $this;
    }   // filterByName()

    /**
     * Filter for field "tariff_comment"
     *
     * @param  mixed    $tariff_comment Filter value
     * @return Instance For fluid interface
     */
    public function filterByTariffComment($tariff_comment)
    {
        $this->filter[] = '`tariff_comment` = '.$this->quote($tariff_comment);
        return $this;
    }   // filterByTariffComment()

    /**
     * Filter for field "date"
     *
     * @param  mixed    $date Filter value
     * @return Instance For fluid interface
     */
    public function filterByDate($date)
    {
        $this->filter[] = '`date` = '.$this->quote($date);
        return $this;
    }   // filterByDate()

    /**
     * Filter for field "cost"
     *
     * @param  mixed    $cost Filter value
     * @return Instance For fluid interface
     */
    public function filterByCost($cost)
    {
        $this->filter[] = '`cost` = '.$this->quote($cost);
        return $this;
    }   // filterByCost()

    /**
     * Filter for field "time"
     *
     * @param  mixed    $time Filter value
     * @return Instance For fluid interface
     */
    public function filterByTime($time)
    {
        $this->filter[] = '`time` = '.$this->quote($time);
        return $this;
    }   // filterByTime()

    /**
     * Filter for field "days"
     *
     * @param  mixed    $days Filter value
     * @return Instance For fluid interface
     */
    public function filterByDays($days)
    {
        $this->filter[] = '`days` = '.$this->quote($days);
        return $this;
    }   // filterByDays()

    /**
     * Filter for field "tariff"
     *
     * @param  mixed    $tariff Filter value
     * @return Instance For fluid interface
     */
    public function filterByTariff($tariff)
    {
        $this->filter[] = '`tariff` = '.$this->quote($tariff);
        return $this;
    }   // filterByTariff()

    /**
     * Filter for field "time_comment"
     *
     * @param  mixed    $time_comment Filter value
     * @return Instance For fluid interface
     */
    public function filterByTimeComment($time_comment)
    {
        $this->filter[] = '`time_comment` = '.$this->quote($time_comment);
        return $this;
    }   // filterByTimeComment()

    // -----------------------------------------------------------------------
    // PROTECTED
    // -----------------------------------------------------------------------

    /**
     * Table name
     *
     * @var string $table Table name
     */
    protected $table = 'pvlng_tariff_view';

    /**
     * SQL for creation
     *
     * @var string $createSQL
     */
    protected $createSQL = '
        CREATE ALGORITHM=UNDEFINED DEFINER=`pvlng`@`localhost` SQL SECURITY DEFINER VIEW `pvlng_tariff_view` AS select `t1`.`id` AS `id`,`t1`.`name` AS `name`,`t1`.`comment` AS `tariff_comment`,`t2`.`date` AS `date`,`t2`.`cost` AS `cost`,`t3`.`time` AS `time`,`t3`.`days` AS `days`,`t3`.`tariff` AS `tariff`,`t3`.`comment` AS `time_comment` from ((`pvlng_tariff` `t1` left join `pvlng_tariff_date` `t2` on((`t1`.`id` = `t2`.`id`))) left join `pvlng_tariff_time` `t3` on(((`t2`.`id` = `t3`.`id`) and (`t2`.`date` = `t3`.`date`))))
    ';

    /**
     *
     */
    protected $fields = array(
        'id'             => '',
        'name'           => '',
        'tariff_comment' => '',
        'date'           => '',
        'cost'           => '',
        'time'           => '',
        'days'           => '',
        'tariff'         => '',
        'time_comment'   => ''
    );

    /**
     *
     */
    protected $nullable = array(

    );

    /**
     *
     */
    protected $primary = array(

    );

    /**
     *
     */
    protected $autoinc = '';

}
