<?php
/**
 * Abstract base class for table "pvlng_reading_statistics"
 *
 * *** NEVER EVER EDIT THIS FILE! ***
 *
 * To extend the functionallity, edit "ReadingStatistics.php"!
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
abstract class ReadingStatisticsBase extends \slimMVC\ORM
{

    // -----------------------------------------------------------------------
    // PUBLIC
    // -----------------------------------------------------------------------

    // -----------------------------------------------------------------------
    // Setter methods
    // -----------------------------------------------------------------------

    /**
     * "pvlng_reading_statistics" is a view, no setters
     */

    // -----------------------------------------------------------------------
    // Getter methods
    // -----------------------------------------------------------------------

    /**
     * Basic getter for field "guid"
     *
     * @return mixed Guid value
     */
    public function getGuid()
    {
        return $this->fields['guid'];
    }   // getGuid()

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
     * Basic getter for field "description"
     *
     * @return mixed Description value
     */
    public function getDescription()
    {
        return $this->fields['description'];
    }   // getDescription()

    /**
     * Basic getter for field "serial"
     *
     * @return mixed Serial value
     */
    public function getSerial()
    {
        return $this->fields['serial'];
    }   // getSerial()

    /**
     * Basic getter for field "channel"
     *
     * @return mixed Channel value
     */
    public function getChannel()
    {
        return $this->fields['channel'];
    }   // getChannel()

    /**
     * Basic getter for field "unit"
     *
     * @return mixed Unit value
     */
    public function getUnit()
    {
        return $this->fields['unit'];
    }   // getUnit()

    /**
     * Basic getter for field "type"
     *
     * @return mixed Type value
     */
    public function getType()
    {
        return $this->fields['type'];
    }   // getType()

    /**
     * Basic getter for field "icon"
     *
     * @return mixed Icon value
     */
    public function getIcon()
    {
        return $this->fields['icon'];
    }   // getIcon()

    /**
     * Basic getter for field "datetime"
     *
     * @return mixed Datetime value
     */
    public function getDatetime()
    {
        return $this->fields['datetime'];
    }   // getDatetime()

    /**
     * Basic getter for field "readings"
     *
     * @return mixed Readings value
     */
    public function getReadings()
    {
        return $this->fields['readings'];
    }   // getReadings()

    // -----------------------------------------------------------------------
    // Filter methods
    // -----------------------------------------------------------------------

    /**
     * Filter for field "guid"
     *
     * @param  mixed    $guid Filter value
     * @return Instance For fluid interface
     */
    public function filterByGuid($guid)
    {
        $this->filter[] = $this->field('guid').' = '.$this->quote($guid);
        return $this;
    }   // filterByGuid()

    /**
     * Filter for field "name"
     *
     * @param  mixed    $name Filter value
     * @return Instance For fluid interface
     */
    public function filterByName($name)
    {
        $this->filter[] = $this->field('name').' = '.$this->quote($name);
        return $this;
    }   // filterByName()

    /**
     * Filter for field "description"
     *
     * @param  mixed    $description Filter value
     * @return Instance For fluid interface
     */
    public function filterByDescription($description)
    {
        $this->filter[] = $this->field('description').' = '.$this->quote($description);
        return $this;
    }   // filterByDescription()

    /**
     * Filter for field "serial"
     *
     * @param  mixed    $serial Filter value
     * @return Instance For fluid interface
     */
    public function filterBySerial($serial)
    {
        $this->filter[] = $this->field('serial').' = '.$this->quote($serial);
        return $this;
    }   // filterBySerial()

    /**
     * Filter for field "channel"
     *
     * @param  mixed    $channel Filter value
     * @return Instance For fluid interface
     */
    public function filterByChannel($channel)
    {
        $this->filter[] = $this->field('channel').' = '.$this->quote($channel);
        return $this;
    }   // filterByChannel()

    /**
     * Filter for field "unit"
     *
     * @param  mixed    $unit Filter value
     * @return Instance For fluid interface
     */
    public function filterByUnit($unit)
    {
        $this->filter[] = $this->field('unit').' = '.$this->quote($unit);
        return $this;
    }   // filterByUnit()

    /**
     * Filter for field "type"
     *
     * @param  mixed    $type Filter value
     * @return Instance For fluid interface
     */
    public function filterByType($type)
    {
        $this->filter[] = $this->field('type').' = '.$this->quote($type);
        return $this;
    }   // filterByType()

    /**
     * Filter for field "icon"
     *
     * @param  mixed    $icon Filter value
     * @return Instance For fluid interface
     */
    public function filterByIcon($icon)
    {
        $this->filter[] = $this->field('icon').' = '.$this->quote($icon);
        return $this;
    }   // filterByIcon()

    /**
     * Filter for field "datetime"
     *
     * @param  mixed    $datetime Filter value
     * @return Instance For fluid interface
     */
    public function filterByDatetime($datetime)
    {
        $this->filter[] = $this->field('datetime').' = '.$this->quote($datetime);
        return $this;
    }   // filterByDatetime()

    /**
     * Filter for field "readings"
     *
     * @param  mixed    $readings Filter value
     * @return Instance For fluid interface
     */
    public function filterByReadings($readings)
    {
        $this->filter[] = $this->field('readings').' = '.$this->quote($readings);
        return $this;
    }   // filterByReadings()

    // -----------------------------------------------------------------------
    // PROTECTED
    // -----------------------------------------------------------------------

    /**
     * Table name
     *
     * @var string $table Table name
     */
    protected $table = 'pvlng_reading_statistics';

    /**
     * SQL for creation
     *
     * @var string $createSQL
     */
    protected $createSQL = '
        CREATE ALGORITHM=UNDEFINED DEFINER=`pvlng`@`localhost` SQL SECURITY DEFINER VIEW `pvlng_reading_statistics` AS select `c`.`guid` AS `guid`,`c`.`name` AS `name`,`c`.`description` AS `description`,`c`.`serial` AS `serial`,`c`.`channel` AS `channel`,`c`.`unit` AS `unit`,`t`.`name` AS `type`,`t`.`icon` AS `icon`,from_unixtime(`u`.`timestamp`) AS `datetime`,ifnull(`u`.`readings`,0) AS `readings` from ((`pvlng_channel` `c` join `pvlng_type` `t` on((`c`.`type` = `t`.`id`))) left join `pvlng_reading_count` `u` on((`c`.`id` = `u`.`id`))) where ((`t`.`childs` = 0) and `t`.`write`)
    ';

    /**
     *
     */
    protected $fields = array(
        'guid'        => '',
        'name'        => '',
        'description' => '',
        'serial'      => '',
        'channel'     => '',
        'unit'        => '',
        'type'        => '',
        'icon'        => '',
        'datetime'    => '',
        'readings'    => ''
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
