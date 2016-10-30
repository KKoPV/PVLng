<?php
/**
 * Abstract base class for table "pvlng_tree_view"
 *
 * *** NEVER EVER EDIT THIS FILE! ***
 *
 * To extend the functionallity, edit "Tree.php"!
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
abstract class TreeBase extends \slimMVC\ORM
{

    // -----------------------------------------------------------------------
    // PUBLIC
    // -----------------------------------------------------------------------

    // -----------------------------------------------------------------------
    // Setter methods
    // -----------------------------------------------------------------------

    /**
     * "pvlng_tree_view" is a view, no setters
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
     * Basic getter for field "entity"
     *
     * @return mixed Entity value
     */
    public function getEntity()
    {
        return $this->fields['entity'];
    }

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
     * Basic getter for field "description"
     *
     * @return mixed Description value
     */
    public function getDescription()
    {
        return $this->fields['description'];
    }

    /**
     * Basic getter for field "resolution"
     *
     * @return mixed Resolution value
     */
    public function getResolution()
    {
        return $this->fields['resolution'];
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
     * Basic getter for field "meter"
     *
     * @return mixed Meter value
     */
    public function getMeter()
    {
        return $this->fields['meter'];
    }

    /**
     * Basic getter for field "numeric"
     *
     * @return mixed Numeric value
     */
    public function getNumeric()
    {
        return $this->fields['numeric'];
    }

    /**
     * Basic getter for field "offset"
     *
     * @return mixed Offset value
     */
    public function getOffset()
    {
        return $this->fields['offset'];
    }

    /**
     * Basic getter for field "adjust"
     *
     * @return mixed Adjust value
     */
    public function getAdjust()
    {
        return $this->fields['adjust'];
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
     * Basic getter for field "decimals"
     *
     * @return mixed Decimals value
     */
    public function getDecimals()
    {
        return $this->fields['decimals'];
    }

    /**
     * Basic getter for field "threshold"
     *
     * @return mixed Threshold value
     */
    public function getThreshold()
    {
        return $this->fields['threshold'];
    }

    /**
     * Basic getter for field "valid_from"
     *
     * @return mixed ValidFrom value
     */
    public function getValidFrom()
    {
        return $this->fields['valid_from'];
    }

    /**
     * Basic getter for field "valid_to"
     *
     * @return mixed ValidTo value
     */
    public function getValidTo()
    {
        return $this->fields['valid_to'];
    }

    /**
     * Basic getter for field "public"
     *
     * @return mixed Public value
     */
    public function getPublic()
    {
        return $this->fields['public'];
    }

    /**
     * Basic getter for field "tags"
     *
     * @return mixed Tags value
     */
    public function getTags()
    {
        return $this->fields['tags'];
    }

    /**
     * Basic getter for field "extra"
     *
     * @return mixed Extra value
     */
    public function getExtra()
    {
        return $this->fields['extra'];
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

    /**
     * Basic getter for field "type_id"
     *
     * @return mixed TypeId value
     */
    public function getTypeId()
    {
        return $this->fields['type_id'];
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
     * Basic getter for field "model"
     *
     * @return mixed Model value
     */
    public function getModel()
    {
        return $this->fields['model'];
    }

    /**
     * Basic getter for field "childs"
     *
     * @return mixed Childs value
     */
    public function getChilds()
    {
        return $this->fields['childs'];
    }

    /**
     * Basic getter for field "read"
     *
     * @return mixed Read value
     */
    public function getRead()
    {
        return $this->fields['read'];
    }

    /**
     * Basic getter for field "write"
     *
     * @return mixed Write value
     */
    public function getWrite()
    {
        return $this->fields['write'];
    }

    /**
     * Basic getter for field "graph"
     *
     * @return mixed Graph value
     */
    public function getGraph()
    {
        return $this->fields['graph'];
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
     * Basic getter for field "alias"
     *
     * @return mixed Alias value
     */
    public function getAlias()
    {
        return $this->fields['alias'];
    }

    /**
     * Basic getter for field "alias_of"
     *
     * @return mixed AliasOf value
     */
    public function getAliasOf()
    {
        return $this->fields['alias_of'];
    }

    /**
     * Basic getter for field "entity_of"
     *
     * @return mixed EntityOf value
     */
    public function getEntityOf()
    {
        return $this->fields['entity_of'];
    }

    /**
     * Basic getter for field "level"
     *
     * @return mixed Level value
     */
    public function getLevel()
    {
        return $this->fields['level'];
    }

    /**
     * Basic getter for field "haschilds"
     *
     * @return mixed Haschilds value
     */
    public function getHaschilds()
    {
        return $this->fields['haschilds'];
    }

    /**
     * Basic getter for field "lower"
     *
     * @return mixed Lower value
     */
    public function getLower()
    {
        return $this->fields['lower'];
    }

    /**
     * Basic getter for field "upper"
     *
     * @return mixed Upper value
     */
    public function getUpper()
    {
        return $this->fields['upper'];
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
        $this->filter[] = $this->field('id').' = '.$this->quote($id);
        return $this;
    }

    /**
     * Filter for field "entity"
     *
     * @param  mixed    $entity Filter value
     * @return Instance For fluid interface
     */
    public function filterByEntity($entity)
    {
        $this->filter[] = $this->field('entity').' = '.$this->quote($entity);
        return $this;
    }

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
    }

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
    }

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
    }

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
    }

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
    }

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
    }

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
    }

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
    }

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
    }

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
    }

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
    }

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
    }

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
    }

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
    }

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
    }

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
    }

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
    }

    /**
     * Filter for field "tags"
     *
     * @param  mixed    $tags Filter value
     * @return Instance For fluid interface
     */
    public function filterByTags($tags)
    {
        $this->filter[] = $this->field('tags').' = '.$this->quote($tags);
        return $this;
    }

    /**
     * Filter for field "extra"
     *
     * @param  mixed    $extra Filter value
     * @return Instance For fluid interface
     */
    public function filterByExtra($extra)
    {
        $this->filter[] = $this->field('extra').' = '.$this->quote($extra);
        return $this;
    }

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
    }

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
    }

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
    }

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
    }

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
    }

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
    }

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
    }

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
    }

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
    }

    /**
     * Filter for field "alias"
     *
     * @param  mixed    $alias Filter value
     * @return Instance For fluid interface
     */
    public function filterByAlias($alias)
    {
        $this->filter[] = $this->field('alias').' = '.$this->quote($alias);
        return $this;
    }

    /**
     * Filter for field "alias_of"
     *
     * @param  mixed    $alias_of Filter value
     * @return Instance For fluid interface
     */
    public function filterByAliasOf($alias_of)
    {
        $this->filter[] = $this->field('alias_of').' = '.$this->quote($alias_of);
        return $this;
    }

    /**
     * Filter for field "entity_of"
     *
     * @param  mixed    $entity_of Filter value
     * @return Instance For fluid interface
     */
    public function filterByEntityOf($entity_of)
    {
        $this->filter[] = $this->field('entity_of').' = '.$this->quote($entity_of);
        return $this;
    }

    /**
     * Filter for field "level"
     *
     * @param  mixed    $level Filter value
     * @return Instance For fluid interface
     */
    public function filterByLevel($level)
    {
        $this->filter[] = $this->field('level').' = '.$this->quote($level);
        return $this;
    }

    /**
     * Filter for field "haschilds"
     *
     * @param  mixed    $haschilds Filter value
     * @return Instance For fluid interface
     */
    public function filterByHaschilds($haschilds)
    {
        $this->filter[] = $this->field('haschilds').' = '.$this->quote($haschilds);
        return $this;
    }

    /**
     * Filter for field "lower"
     *
     * @param  mixed    $lower Filter value
     * @return Instance For fluid interface
     */
    public function filterByLower($lower)
    {
        $this->filter[] = $this->field('lower').' = '.$this->quote($lower);
        return $this;
    }

    /**
     * Filter for field "upper"
     *
     * @param  mixed    $upper Filter value
     * @return Instance For fluid interface
     */
    public function filterByUpper($upper)
    {
        $this->filter[] = $this->field('upper').' = '.$this->quote($upper);
        return $this;
    }

    // -----------------------------------------------------------------------
    // PROTECTED
    // -----------------------------------------------------------------------

    /**
     * Table name
     *
     * @var string $table Table name
     */
    protected $table = 'pvlng_tree_view';

    /**
     * SQL for creation
     *
     * @var string $createSQL
     */
    protected $createSQL = '
        CREATE ALGORITHM=UNDEFINED DEFINER=`pvlng`@`localhost` SQL SECURITY DEFINER VIEW `pvlng_tree_view` AS select `n`.`id` AS `id`,`n`.`entity` AS `entity`,ifnull(`n`.`guid`,`c`.`guid`) AS `guid`,if(`co`.`id`,`co`.`name`,`c`.`name`) AS `name`,if(`co`.`id`,`co`.`serial`,`c`.`serial`) AS `serial`,`c`.`channel` AS `channel`,if(`co`.`id`,`co`.`description`,`c`.`description`) AS `description`,if(`co`.`id`,`co`.`resolution`,`c`.`resolution`) AS `resolution`,if(`co`.`id`,`co`.`cost`,`c`.`cost`) AS `cost`,if(`co`.`id`,`co`.`meter`,`c`.`meter`) AS `meter`,if(`co`.`id`,`co`.`numeric`,`c`.`numeric`) AS `numeric`,if(`co`.`id`,`co`.`offset`,`c`.`offset`) AS `offset`,if(`co`.`id`,`co`.`adjust`,`c`.`adjust`) AS `adjust`,if(`co`.`id`,`co`.`unit`,`c`.`unit`) AS `unit`,if(`co`.`id`,`co`.`decimals`,`c`.`decimals`) AS `decimals`,if(`co`.`id`,`co`.`threshold`,`c`.`threshold`) AS `threshold`,if(`co`.`id`,`co`.`valid_from`,`c`.`valid_from`) AS `valid_from`,if(`co`.`id`,`co`.`valid_to`,`c`.`valid_to`) AS `valid_to`,if(`co`.`id`,`co`.`public`,`c`.`public`) AS `public`,if(`co`.`id`,`co`.`tags`,`c`.`tags`) AS `tags`,if(`co`.`id`,`co`.`extra`,`c`.`extra`) AS `extra`,if(`co`.`id`,`co`.`comment`,`c`.`comment`) AS `comment`,`t`.`id` AS `type_id`,`t`.`name` AS `type`,`t`.`model` AS `model`,`t`.`childs` AS `childs`,`t`.`read` AS `read`,`t`.`write` AS `write`,`t`.`graph` AS `graph`,if(`co`.`id`,`co`.`icon`,`c`.`icon`) AS `icon`,`ca`.`id` AS `alias`,`ta`.`id` AS `alias_of`,`ta`.`entity` AS `entity_of`,(((count(1) - 1) + (`n`.`lft` > 1)) + 1) AS `level`,round((((`n`.`rgt` - `n`.`lft`) - 1) / 2),0) AS `haschilds`,((((min(`p`.`rgt`) - `n`.`rgt`) - (`n`.`lft` > 1)) / 2) > 0) AS `lower`,((`n`.`lft` - max(`p`.`lft`)) > 1) AS `upper` from ((((((`pvlng_tree` `n` USE INDEX (PRIMARY) left join `pvlng_channel` `c` on((`n`.`entity` = `c`.`id`))) left join `pvlng_type` `t` on((`c`.`type` = `t`.`id`))) left join `pvlng_channel` `ca` on(((if(`t`.`childs`,`n`.`guid`,`c`.`guid`) = `ca`.`channel`) and (`ca`.`type` = 0)))) left join `pvlng_tree` `ta` on((`c`.`channel` = `ta`.`guid`))) left join `pvlng_channel` `co` on(((`ta`.`entity` = `co`.`id`) and (`c`.`type` = 0)))) join `pvlng_tree` `p`) where (((`n`.`lft` between `p`.`lft` and `p`.`rgt`) and (`p`.`id` <> `n`.`id`)) or (`n`.`lft` = 1)) group by `n`.`id` order by `n`.`lft`
    ';

    /**
     *
     */
    protected $fields = array(
        'id'          => '',
        'entity'      => '',
        'guid'        => '',
        'name'        => '',
        'serial'      => '',
        'channel'     => '',
        'description' => '',
        'resolution'  => '',
        'cost'        => '',
        'meter'       => '',
        'numeric'     => '',
        'offset'      => '',
        'adjust'      => '',
        'unit'        => '',
        'decimals'    => '',
        'threshold'   => '',
        'valid_from'  => '',
        'valid_to'    => '',
        'public'      => '',
        'tags'        => '',
        'extra'       => '',
        'comment'     => '',
        'type_id'     => '',
        'type'        => '',
        'model'       => '',
        'childs'      => '',
        'read'        => '',
        'write'       => '',
        'graph'       => '',
        'icon'        => '',
        'alias'       => '',
        'alias_of'    => '',
        'entity_of'   => '',
        'level'       => '',
        'haschilds'   => '',
        'lower'       => '',
        'upper'       => ''
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
