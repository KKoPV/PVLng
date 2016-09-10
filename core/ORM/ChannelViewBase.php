<?php
/**
 * Abstract base class for table "pvlng_channel_view"
 *
 * *** NEVER EVER EDIT THIS FILE! ***
 *
 * To extend the functionallity, edit "ChannelView.php"!
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
abstract class ChannelViewBase extends \slimMVC\ORM
{

    // -----------------------------------------------------------------------
    // PUBLIC
    // -----------------------------------------------------------------------

    // -----------------------------------------------------------------------
    // Setter methods
    // -----------------------------------------------------------------------

    /**
     * "pvlng_channel_view" is a view, no setters
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
     * Basic getter for field "description"
     *
     * @return mixed Description value
     */
    public function getDescription()
    {
        return $this->fields['description'];
    }   // getDescription()

    /**
     * Basic getter for field "resolution"
     *
     * @return mixed Resolution value
     */
    public function getResolution()
    {
        return $this->fields['resolution'];
    }   // getResolution()

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
     * Basic getter for field "numeric"
     *
     * @return mixed Numeric value
     */
    public function getNumeric()
    {
        return $this->fields['numeric'];
    }   // getNumeric()

    /**
     * Basic getter for field "offset"
     *
     * @return mixed Offset value
     */
    public function getOffset()
    {
        return $this->fields['offset'];
    }   // getOffset()

    /**
     * Basic getter for field "adjust"
     *
     * @return mixed Adjust value
     */
    public function getAdjust()
    {
        return $this->fields['adjust'];
    }   // getAdjust()

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
     * Basic getter for field "decimals"
     *
     * @return mixed Decimals value
     */
    public function getDecimals()
    {
        return $this->fields['decimals'];
    }   // getDecimals()

    /**
     * Basic getter for field "meter"
     *
     * @return mixed Meter value
     */
    public function getMeter()
    {
        return $this->fields['meter'];
    }   // getMeter()

    /**
     * Basic getter for field "threshold"
     *
     * @return mixed Threshold value
     */
    public function getThreshold()
    {
        return $this->fields['threshold'];
    }   // getThreshold()

    /**
     * Basic getter for field "valid_from"
     *
     * @return mixed ValidFrom value
     */
    public function getValidFrom()
    {
        return $this->fields['valid_from'];
    }   // getValidFrom()

    /**
     * Basic getter for field "valid_to"
     *
     * @return mixed ValidTo value
     */
    public function getValidTo()
    {
        return $this->fields['valid_to'];
    }   // getValidTo()

    /**
     * Basic getter for field "public"
     *
     * @return mixed Public value
     */
    public function getPublic()
    {
        return $this->fields['public'];
    }   // getPublic()

    /**
     * Basic getter for field "type_id"
     *
     * @return mixed TypeId value
     */
    public function getTypeId()
    {
        return $this->fields['type_id'];
    }   // getTypeId()

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
     * Basic getter for field "model"
     *
     * @return mixed Model value
     */
    public function getModel()
    {
        return $this->fields['model'];
    }   // getModel()

    /**
     * Basic getter for field "childs"
     *
     * @return mixed Childs value
     */
    public function getChilds()
    {
        return $this->fields['childs'];
    }   // getChilds()

    /**
     * Basic getter for field "read"
     *
     * @return mixed Read value
     */
    public function getRead()
    {
        return $this->fields['read'];
    }   // getRead()

    /**
     * Basic getter for field "write"
     *
     * @return mixed Write value
     */
    public function getWrite()
    {
        return $this->fields['write'];
    }   // getWrite()

    /**
     * Basic getter for field "graph"
     *
     * @return mixed Graph value
     */
    public function getGraph()
    {
        return $this->fields['graph'];
    }   // getGraph()

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
     * Basic getter for field "tree"
     *
     * @return mixed Tree value
     */
    public function getTree()
    {
        return $this->fields['tree'];
    }   // getTree()

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
        $this->filter[] = $this->field('id').' = '.$this->quote($id);
        return $this;
    }   // filterById()

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
     * Filter for field "resolution"
     *
     * @param  mixed    $resolution Filter value
     * @return Instance For fluid interface
     */
    public function filterByResolution($resolution)
    {
        $this->filter[] = $this->field('resolution').' = '.$this->quote($resolution);
        return $this;
    }   // filterByResolution()

    /**
     * Filter for field "cost"
     *
     * @param  mixed    $cost Filter value
     * @return Instance For fluid interface
     */
    public function filterByCost($cost)
    {
        $this->filter[] = $this->field('cost').' = '.$this->quote($cost);
        return $this;
    }   // filterByCost()

    /**
     * Filter for field "numeric"
     *
     * @param  mixed    $numeric Filter value
     * @return Instance For fluid interface
     */
    public function filterByNumeric($numeric)
    {
        $this->filter[] = $this->field('numeric').' = '.$this->quote($numeric);
        return $this;
    }   // filterByNumeric()

    /**
     * Filter for field "offset"
     *
     * @param  mixed    $offset Filter value
     * @return Instance For fluid interface
     */
    public function filterByOffset($offset)
    {
        $this->filter[] = $this->field('offset').' = '.$this->quote($offset);
        return $this;
    }   // filterByOffset()

    /**
     * Filter for field "adjust"
     *
     * @param  mixed    $adjust Filter value
     * @return Instance For fluid interface
     */
    public function filterByAdjust($adjust)
    {
        $this->filter[] = $this->field('adjust').' = '.$this->quote($adjust);
        return $this;
    }   // filterByAdjust()

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
     * Filter for field "decimals"
     *
     * @param  mixed    $decimals Filter value
     * @return Instance For fluid interface
     */
    public function filterByDecimals($decimals)
    {
        $this->filter[] = $this->field('decimals').' = '.$this->quote($decimals);
        return $this;
    }   // filterByDecimals()

    /**
     * Filter for field "meter"
     *
     * @param  mixed    $meter Filter value
     * @return Instance For fluid interface
     */
    public function filterByMeter($meter)
    {
        $this->filter[] = $this->field('meter').' = '.$this->quote($meter);
        return $this;
    }   // filterByMeter()

    /**
     * Filter for field "threshold"
     *
     * @param  mixed    $threshold Filter value
     * @return Instance For fluid interface
     */
    public function filterByThreshold($threshold)
    {
        $this->filter[] = $this->field('threshold').' = '.$this->quote($threshold);
        return $this;
    }   // filterByThreshold()

    /**
     * Filter for field "valid_from"
     *
     * @param  mixed    $valid_from Filter value
     * @return Instance For fluid interface
     */
    public function filterByValidFrom($valid_from)
    {
        $this->filter[] = $this->field('valid_from').' = '.$this->quote($valid_from);
        return $this;
    }   // filterByValidFrom()

    /**
     * Filter for field "valid_to"
     *
     * @param  mixed    $valid_to Filter value
     * @return Instance For fluid interface
     */
    public function filterByValidTo($valid_to)
    {
        $this->filter[] = $this->field('valid_to').' = '.$this->quote($valid_to);
        return $this;
    }   // filterByValidTo()

    /**
     * Filter for field "public"
     *
     * @param  mixed    $public Filter value
     * @return Instance For fluid interface
     */
    public function filterByPublic($public)
    {
        $this->filter[] = $this->field('public').' = '.$this->quote($public);
        return $this;
    }   // filterByPublic()

    /**
     * Filter for field "type_id"
     *
     * @param  mixed    $type_id Filter value
     * @return Instance For fluid interface
     */
    public function filterByTypeId($type_id)
    {
        $this->filter[] = $this->field('type_id').' = '.$this->quote($type_id);
        return $this;
    }   // filterByTypeId()

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
     * Filter for field "model"
     *
     * @param  mixed    $model Filter value
     * @return Instance For fluid interface
     */
    public function filterByModel($model)
    {
        $this->filter[] = $this->field('model').' = '.$this->quote($model);
        return $this;
    }   // filterByModel()

    /**
     * Filter for field "childs"
     *
     * @param  mixed    $childs Filter value
     * @return Instance For fluid interface
     */
    public function filterByChilds($childs)
    {
        $this->filter[] = $this->field('childs').' = '.$this->quote($childs);
        return $this;
    }   // filterByChilds()

    /**
     * Filter for field "read"
     *
     * @param  mixed    $read Filter value
     * @return Instance For fluid interface
     */
    public function filterByRead($read)
    {
        $this->filter[] = $this->field('read').' = '.$this->quote($read);
        return $this;
    }   // filterByRead()

    /**
     * Filter for field "write"
     *
     * @param  mixed    $write Filter value
     * @return Instance For fluid interface
     */
    public function filterByWrite($write)
    {
        $this->filter[] = $this->field('write').' = '.$this->quote($write);
        return $this;
    }   // filterByWrite()

    /**
     * Filter for field "graph"
     *
     * @param  mixed    $graph Filter value
     * @return Instance For fluid interface
     */
    public function filterByGraph($graph)
    {
        $this->filter[] = $this->field('graph').' = '.$this->quote($graph);
        return $this;
    }   // filterByGraph()

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
     * Filter for field "tree"
     *
     * @param  mixed    $tree Filter value
     * @return Instance For fluid interface
     */
    public function filterByTree($tree)
    {
        $this->filter[] = $this->field('tree').' = '.$this->quote($tree);
        return $this;
    }   // filterByTree()

    // -----------------------------------------------------------------------
    // PROTECTED
    // -----------------------------------------------------------------------

    /**
     * Table name
     *
     * @var string $table Table name
     */
    protected $table = 'pvlng_channel_view';

    /**
     * SQL for creation
     *
     * @var string $createSQL
     */
    protected $createSQL = '
        CREATE ALGORITHM=UNDEFINED DEFINER=`pvlng`@`localhost` SQL SECURITY DEFINER VIEW `pvlng_channel_view` AS select `c`.`id` AS `id`,`c`.`guid` AS `guid`,if(`a`.`id`,`a`.`name`,`c`.`name`) AS `name`,if(`a`.`id`,`a`.`serial`,`c`.`serial`) AS `serial`,`c`.`channel` AS `channel`,if(`a`.`id`,`a`.`description`,`c`.`description`) AS `description`,if(`a`.`id`,`a`.`resolution`,`c`.`resolution`) AS `resolution`,if(`a`.`id`,`a`.`cost`,`c`.`cost`) AS `cost`,if(`a`.`id`,`a`.`numeric`,`c`.`numeric`) AS `numeric`,if(`a`.`id`,`a`.`offset`,`c`.`offset`) AS `offset`,if(`a`.`id`,`a`.`adjust`,`c`.`adjust`) AS `adjust`,if(`a`.`id`,`a`.`unit`,`c`.`unit`) AS `unit`,if(`a`.`id`,`a`.`decimals`,`c`.`decimals`) AS `decimals`,if(`a`.`id`,`a`.`meter`,`c`.`meter`) AS `meter`,if(`a`.`id`,`a`.`threshold`,`c`.`threshold`) AS `threshold`,if(`a`.`id`,`a`.`valid_from`,`c`.`valid_from`) AS `valid_from`,if(`a`.`id`,`a`.`valid_to`,`c`.`valid_to`) AS `valid_to`,if(`a`.`id`,`a`.`public`,`c`.`public`) AS `public`,`t`.`id` AS `type_id`,`t`.`name` AS `type`,`t`.`model` AS `model`,`t`.`childs` AS `childs`,if(`ta`.`id`,`ta`.`read`,`t`.`read`) AS `read`,`t`.`write` AS `write`,if(`ta`.`id`,`ta`.`graph`,`t`.`graph`) AS `graph`,if(`a`.`id`,`a`.`icon`,`c`.`icon`) AS `icon`,(select count(1) from `pvlng_tree` where (`pvlng_tree`.`entity` = `c`.`id`)) AS `tree` from ((((`pvlng_channel` `c` join `pvlng_type` `t` on((`c`.`type` = `t`.`id`))) left join `pvlng_tree` `tr` on((`c`.`channel` = `tr`.`guid`))) left join `pvlng_channel` `a` on((`tr`.`entity` = `a`.`id`))) left join `pvlng_type` `ta` on((`a`.`type` = `ta`.`id`))) where (`c`.`id` <> 1)
    ';

    /**
     *
     */
    protected $fields = array(
        'id'          => '',
        'guid'        => '',
        'name'        => '',
        'serial'      => '',
        'channel'     => '',
        'description' => '',
        'resolution'  => '',
        'cost'        => '',
        'numeric'     => '',
        'offset'      => '',
        'adjust'      => '',
        'unit'        => '',
        'decimals'    => '',
        'meter'       => '',
        'threshold'   => '',
        'valid_from'  => '',
        'valid_to'    => '',
        'public'      => '',
        'type_id'     => '',
        'type'        => '',
        'model'       => '',
        'childs'      => '',
        'read'        => '',
        'write'       => '',
        'graph'       => '',
        'icon'        => '',
        'tree'        => ''
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
