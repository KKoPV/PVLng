<?php
/**
 * Abstract base class for table "pvlng_reading_statistics"
 *
 *****************************************************************************
 *                       NEVER EVER EDIT THIS FILE!
 *****************************************************************************
 * To extend functionallity edit "ReadingStatistics.php"
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
abstract class ReadingStatisticsBase extends ORM
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
     * Basic getter for field "description"
     *
     * @return mixed Description value
     */
    public function getDescription()
    {
        return $this->fields['description'];
    }

    /**
     * Basic getter for field "serial"
     *
     * @return mixed Serial value
     */
    public function getSerial()
    {
        return $this->fields['serial'];
    }

    /**
     * Basic getter for field "channel"
     *
     * @return mixed Channel value
     */
    public function getChannel()
    {
        return $this->fields['channel'];
    }

    /**
     * Basic getter for field "unit"
     *
     * @return mixed Unit value
     */
    public function getUnit()
    {
        return $this->fields['unit'];
    }

    /**
     * Basic getter for field "type"
     *
     * @return mixed Type value
     */
    public function getType()
    {
        return $this->fields['type'];
    }

    /**
     * Basic getter for field "icon"
     *
     * @return mixed Icon value
     */
    public function getIcon()
    {
        return $this->fields['icon'];
    }

    /**
     * Basic getter for field "datetime"
     *
     * @return mixed Datetime value
     */
    public function getDatetime()
    {
        return $this->fields['datetime'];
    }

    /**
     * Basic getter for field "readings"
     *
     * @return mixed Readings value
     */
    public function getReadings()
    {
        return $this->fields['readings'];
    }

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
        return $this->filter('guid', $guid);
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
     * Filter for field "description"
     *
     * @param  mixed    $description Filter value
     * @return Instance For fluid interface
     */
    public function filterByDescription($description)
    {
        return $this->filter('description', $description);
    }

    /**
     * Filter for field "serial"
     *
     * @param  mixed    $serial Filter value
     * @return Instance For fluid interface
     */
    public function filterBySerial($serial)
    {
        return $this->filter('serial', $serial);
    }

    /**
     * Filter for field "channel"
     *
     * @param  mixed    $channel Filter value
     * @return Instance For fluid interface
     */
    public function filterByChannel($channel)
    {
        return $this->filter('channel', $channel);
    }

    /**
     * Filter for field "unit"
     *
     * @param  mixed    $unit Filter value
     * @return Instance For fluid interface
     */
    public function filterByUnit($unit)
    {
        return $this->filter('unit', $unit);
    }

    /**
     * Filter for field "type"
     *
     * @param  mixed    $type Filter value
     * @return Instance For fluid interface
     */
    public function filterByType($type)
    {
        return $this->filter('type', $type);
    }

    /**
     * Filter for field "icon"
     *
     * @param  mixed    $icon Filter value
     * @return Instance For fluid interface
     */
    public function filterByIcon($icon)
    {
        return $this->filter('icon', $icon);
    }

    /**
     * Filter for field "datetime"
     *
     * @param  mixed    $datetime Filter value
     * @return Instance For fluid interface
     */
    public function filterByDatetime($datetime)
    {
        return $this->filter('datetime', $datetime);
    }

    /**
     * Filter for field "readings"
     *
     * @param  mixed    $readings Filter value
     * @return Instance For fluid interface
     */
    public function filterByReadings($readings)
    {
        return $this->filter('readings', $readings);
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
        CREATE VIEW `pvlng_reading_statistics` AS select `c`.`guid` AS `guid`,`c`.`name` AS `name`,`c`.`description` AS `description`,`c`.`serial` AS `serial`,`c`.`channel` AS `channel`,`c`.`unit` AS `unit`,`t`.`name` AS `type`,`t`.`icon` AS `icon`,from_unixtime(`u`.`timestamp`) AS `datetime`,ifnull(`u`.`readings`,0) AS `readings` from ((`pvlng_channel` `c` join `pvlng_type` `t` on((`c`.`type` = `t`.`id`))) left join `pvlng_reading_count` `u` on((`c`.`id` = `u`.`id`))) where ((`t`.`childs` = 0) and (`t`.`write` <> 0))
    ';
    // @codingStandardsIgnoreEnd

    /**
     * Table name
     *
     * @var string $table Table name
     */
    protected $table = 'pvlng_reading_statistics';

    /**
     *
     */
    protected $fields = [
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
