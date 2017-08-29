<?php
/**
 * Abstract base class for table "pvlng_channel"
 *
 *****************************************************************************
 *                       NEVER EVER EDIT THIS FILE!
 *****************************************************************************
 * To extend functionallity edit "Channel.php"
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
abstract class ChannelBase extends ORM
{

    // -----------------------------------------------------------------------
    // PUBLIC
    // -----------------------------------------------------------------------

    // -----------------------------------------------------------------------
    // Setter methods
    // -----------------------------------------------------------------------

    /**
     * "id" is AutoInc, no setter
     */

    /**
     * Basic setter for field "guid"
     *
     * @param  mixed    $guid Guid value
     * @return Instance For fluid interface
     */
    public function setGuid($guid)
    {
        $this->fields['guid'] = $guid;
        return $this;
    }

    /**
     * Raw setter for field "guid", for INSERT, REPLACE and UPDATE
     *
     * @param  mixed    $guid Guid value
     * @return Instance For fluid interface
     */
    public function setGuidRaw($guid)
    {
        $this->raw['guid'] = $guid;
        return $this;
    }

    /**
     * Basic setter for field "name"
     *
     * @param  mixed    $name Name value
     * @return Instance For fluid interface
     */
    public function setName($name)
    {
        $this->fields['name'] = $name;
        return $this;
    }

    /**
     * Raw setter for field "name", for INSERT, REPLACE and UPDATE
     *
     * @param  mixed    $name Name value
     * @return Instance For fluid interface
     */
    public function setNameRaw($name)
    {
        $this->raw['name'] = $name;
        return $this;
    }

    /**
     * Basic setter for field "description"
     *
     * @param  mixed    $description Description value
     * @return Instance For fluid interface
     */
    public function setDescription($description)
    {
        $this->fields['description'] = $description;
        return $this;
    }

    /**
     * Raw setter for field "description", for INSERT, REPLACE and UPDATE
     *
     * @param  mixed    $description Description value
     * @return Instance For fluid interface
     */
    public function setDescriptionRaw($description)
    {
        $this->raw['description'] = $description;
        return $this;
    }

    /**
     * Basic setter for field "serial"
     *
     * @param  mixed    $serial Serial value
     * @return Instance For fluid interface
     */
    public function setSerial($serial)
    {
        $this->fields['serial'] = $serial;
        return $this;
    }

    /**
     * Raw setter for field "serial", for INSERT, REPLACE and UPDATE
     *
     * @param  mixed    $serial Serial value
     * @return Instance For fluid interface
     */
    public function setSerialRaw($serial)
    {
        $this->raw['serial'] = $serial;
        return $this;
    }

    /**
     * Basic setter for field "channel"
     *
     * @param  mixed    $channel Channel value
     * @return Instance For fluid interface
     */
    public function setChannel($channel)
    {
        $this->fields['channel'] = $channel;
        return $this;
    }

    /**
     * Raw setter for field "channel", for INSERT, REPLACE and UPDATE
     *
     * @param  mixed    $channel Channel value
     * @return Instance For fluid interface
     */
    public function setChannelRaw($channel)
    {
        $this->raw['channel'] = $channel;
        return $this;
    }

    /**
     * Basic setter for field "type"
     *
     * @param  mixed    $type Type value
     * @return Instance For fluid interface
     */
    public function setType($type)
    {
        $this->fields['type'] = $type;
        return $this;
    }

    /**
     * Raw setter for field "type", for INSERT, REPLACE and UPDATE
     *
     * @param  mixed    $type Type value
     * @return Instance For fluid interface
     */
    public function setTypeRaw($type)
    {
        $this->raw['type'] = $type;
        return $this;
    }

    /**
     * Basic setter for field "resolution"
     *
     * @param  mixed    $resolution Resolution value
     * @return Instance For fluid interface
     */
    public function setResolution($resolution)
    {
        $this->fields['resolution'] = $resolution;
        return $this;
    }

    /**
     * Raw setter for field "resolution", for INSERT, REPLACE and UPDATE
     *
     * @param  mixed    $resolution Resolution value
     * @return Instance For fluid interface
     */
    public function setResolutionRaw($resolution)
    {
        $this->raw['resolution'] = $resolution;
        return $this;
    }

    /**
     * Basic setter for field "unit"
     *
     * @param  mixed    $unit Unit value
     * @return Instance For fluid interface
     */
    public function setUnit($unit)
    {
        $this->fields['unit'] = $unit;
        return $this;
    }

    /**
     * Raw setter for field "unit", for INSERT, REPLACE and UPDATE
     *
     * @param  mixed    $unit Unit value
     * @return Instance For fluid interface
     */
    public function setUnitRaw($unit)
    {
        $this->raw['unit'] = $unit;
        return $this;
    }

    /**
     * Basic setter for field "decimals"
     *
     * @param  mixed    $decimals Decimals value
     * @return Instance For fluid interface
     */
    public function setDecimals($decimals)
    {
        $this->fields['decimals'] = $decimals;
        return $this;
    }

    /**
     * Raw setter for field "decimals", for INSERT, REPLACE and UPDATE
     *
     * @param  mixed    $decimals Decimals value
     * @return Instance For fluid interface
     */
    public function setDecimalsRaw($decimals)
    {
        $this->raw['decimals'] = $decimals;
        return $this;
    }

    /**
     * Basic setter for field "meter"
     *
     * @param  mixed    $meter Meter value
     * @return Instance For fluid interface
     */
    public function setMeter($meter)
    {
        $this->fields['meter'] = $meter;
        return $this;
    }

    /**
     * Raw setter for field "meter", for INSERT, REPLACE and UPDATE
     *
     * @param  mixed    $meter Meter value
     * @return Instance For fluid interface
     */
    public function setMeterRaw($meter)
    {
        $this->raw['meter'] = $meter;
        return $this;
    }

    /**
     * Basic setter for field "numeric"
     *
     * @param  mixed    $numeric Numeric value
     * @return Instance For fluid interface
     */
    public function setNumeric($numeric)
    {
        $this->fields['numeric'] = $numeric;
        return $this;
    }

    /**
     * Raw setter for field "numeric", for INSERT, REPLACE and UPDATE
     *
     * @param  mixed    $numeric Numeric value
     * @return Instance For fluid interface
     */
    public function setNumericRaw($numeric)
    {
        $this->raw['numeric'] = $numeric;
        return $this;
    }

    /**
     * Basic setter for field "offset"
     *
     * @param  mixed    $offset Offset value
     * @return Instance For fluid interface
     */
    public function setOffset($offset)
    {
        $this->fields['offset'] = $offset;
        return $this;
    }

    /**
     * Raw setter for field "offset", for INSERT, REPLACE and UPDATE
     *
     * @param  mixed    $offset Offset value
     * @return Instance For fluid interface
     */
    public function setOffsetRaw($offset)
    {
        $this->raw['offset'] = $offset;
        return $this;
    }

    /**
     * Basic setter for field "adjust"
     *
     * @param  mixed    $adjust Adjust value
     * @return Instance For fluid interface
     */
    public function setAdjust($adjust)
    {
        $this->fields['adjust'] = $adjust;
        return $this;
    }

    /**
     * Raw setter for field "adjust", for INSERT, REPLACE and UPDATE
     *
     * @param  mixed    $adjust Adjust value
     * @return Instance For fluid interface
     */
    public function setAdjustRaw($adjust)
    {
        $this->raw['adjust'] = $adjust;
        return $this;
    }

    /**
     * Basic setter for field "cost"
     *
     * @param  mixed    $cost Cost value
     * @return Instance For fluid interface
     */
    public function setCost($cost)
    {
        $this->fields['cost'] = $cost;
        return $this;
    }

    /**
     * Raw setter for field "cost", for INSERT, REPLACE and UPDATE
     *
     * @param  mixed    $cost Cost value
     * @return Instance For fluid interface
     */
    public function setCostRaw($cost)
    {
        $this->raw['cost'] = $cost;
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
     * Basic setter for field "threshold"
     *
     * @param  mixed    $threshold Threshold value
     * @return Instance For fluid interface
     */
    public function setThreshold($threshold)
    {
        $this->fields['threshold'] = $threshold;
        return $this;
    }

    /**
     * Raw setter for field "threshold", for INSERT, REPLACE and UPDATE
     *
     * @param  mixed    $threshold Threshold value
     * @return Instance For fluid interface
     */
    public function setThresholdRaw($threshold)
    {
        $this->raw['threshold'] = $threshold;
        return $this;
    }

    /**
     * Basic setter for field "valid_from"
     *
     * @param  mixed    $valid_from ValidFrom value
     * @return Instance For fluid interface
     */
    public function setValidFrom($valid_from)
    {
        $this->fields['valid_from'] = $valid_from;
        return $this;
    }

    /**
     * Raw setter for field "valid_from", for INSERT, REPLACE and UPDATE
     *
     * @param  mixed    $valid_from ValidFrom value
     * @return Instance For fluid interface
     */
    public function setValidFromRaw($valid_from)
    {
        $this->raw['valid_from'] = $valid_from;
        return $this;
    }

    /**
     * Basic setter for field "valid_to"
     *
     * @param  mixed    $valid_to ValidTo value
     * @return Instance For fluid interface
     */
    public function setValidTo($valid_to)
    {
        $this->fields['valid_to'] = $valid_to;
        return $this;
    }

    /**
     * Raw setter for field "valid_to", for INSERT, REPLACE and UPDATE
     *
     * @param  mixed    $valid_to ValidTo value
     * @return Instance For fluid interface
     */
    public function setValidToRaw($valid_to)
    {
        $this->raw['valid_to'] = $valid_to;
        return $this;
    }

    /**
     * Basic setter for field "public"
     *
     * @param  mixed    $public Public value
     * @return Instance For fluid interface
     */
    public function setPublic($public)
    {
        $this->fields['public'] = $public;
        return $this;
    }

    /**
     * Raw setter for field "public", for INSERT, REPLACE and UPDATE
     *
     * @param  mixed    $public Public value
     * @return Instance For fluid interface
     */
    public function setPublicRaw($public)
    {
        $this->raw['public'] = $public;
        return $this;
    }

    /**
     * Basic setter for field "tags"
     *
     * @param  mixed    $tags Tags value
     * @return Instance For fluid interface
     */
    public function setTags($tags)
    {
        $this->fields['tags'] = $tags;
        return $this;
    }

    /**
     * Raw setter for field "tags", for INSERT, REPLACE and UPDATE
     *
     * @param  mixed    $tags Tags value
     * @return Instance For fluid interface
     */
    public function setTagsRaw($tags)
    {
        $this->raw['tags'] = $tags;
        return $this;
    }

    /**
     * Basic setter for field "extra"
     *
     * @param  mixed    $extra Extra value
     * @return Instance For fluid interface
     */
    public function setExtra($extra)
    {
        $this->fields['extra'] = $extra;
        return $this;
    }

    /**
     * Raw setter for field "extra", for INSERT, REPLACE and UPDATE
     *
     * @param  mixed    $extra Extra value
     * @return Instance For fluid interface
     */
    public function setExtraRaw($extra)
    {
        $this->raw['extra'] = $extra;
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

    /**
     * Basic setter for field "icon"
     *
     * @param  mixed    $icon Icon value
     * @return Instance For fluid interface
     */
    public function setIcon($icon)
    {
        $this->fields['icon'] = $icon;
        return $this;
    }

    /**
     * Raw setter for field "icon", for INSERT, REPLACE and UPDATE
     *
     * @param  mixed    $icon Icon value
     * @return Instance For fluid interface
     */
    public function setIconRaw($icon)
    {
        $this->raw['icon'] = $icon;
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
     * Basic getter for field "type"
     *
     * @return mixed Type value
     */
    public function getType()
    {
        return $this->fields['type'];
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
     * Basic getter for field "cost"
     *
     * @return mixed Cost value
     */
    public function getCost()
    {
        return $this->fields['cost'];
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
     * Basic getter for field "icon"
     *
     * @return mixed Icon value
     */
    public function getIcon()
    {
        return $this->fields['icon'];
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
     * Filter for field "resolution"
     *
     * @param  mixed    $resolution Filter value
     * @return Instance For fluid interface
     */
    public function filterByResolution($resolution)
    {
        return $this->filter('resolution', $resolution);
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
     * Filter for field "decimals"
     *
     * @param  mixed    $decimals Filter value
     * @return Instance For fluid interface
     */
    public function filterByDecimals($decimals)
    {
        return $this->filter('decimals', $decimals);
    }

    /**
     * Filter for field "meter"
     *
     * @param  mixed    $meter Filter value
     * @return Instance For fluid interface
     */
    public function filterByMeter($meter)
    {
        return $this->filter('meter', $meter);
    }

    /**
     * Filter for field "numeric"
     *
     * @param  mixed    $numeric Filter value
     * @return Instance For fluid interface
     */
    public function filterByNumeric($numeric)
    {
        return $this->filter('numeric', $numeric);
    }

    /**
     * Filter for field "offset"
     *
     * @param  mixed    $offset Filter value
     * @return Instance For fluid interface
     */
    public function filterByOffset($offset)
    {
        return $this->filter('offset', $offset);
    }

    /**
     * Filter for field "adjust"
     *
     * @param  mixed    $adjust Filter value
     * @return Instance For fluid interface
     */
    public function filterByAdjust($adjust)
    {
        return $this->filter('adjust', $adjust);
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
     * Filter for field "threshold"
     *
     * @param  mixed    $threshold Filter value
     * @return Instance For fluid interface
     */
    public function filterByThreshold($threshold)
    {
        return $this->filter('threshold', $threshold);
    }

    /**
     * Filter for field "valid_from"
     *
     * @param  mixed    $valid_from Filter value
     * @return Instance For fluid interface
     */
    public function filterByValidFrom($valid_from)
    {
        return $this->filter('valid_from', $valid_from);
    }

    /**
     * Filter for field "valid_to"
     *
     * @param  mixed    $valid_to Filter value
     * @return Instance For fluid interface
     */
    public function filterByValidTo($valid_to)
    {
        return $this->filter('valid_to', $valid_to);
    }

    /**
     * Filter for field "public"
     *
     * @param  mixed    $public Filter value
     * @return Instance For fluid interface
     */
    public function filterByPublic($public)
    {
        return $this->filter('public', $public);
    }

    /**
     * Filter for field "tags"
     *
     * @param  mixed    $tags Filter value
     * @return Instance For fluid interface
     */
    public function filterByTags($tags)
    {
        return $this->filter('tags', $tags);
    }

    /**
     * Filter for field "extra"
     *
     * @param  mixed    $extra Filter value
     * @return Instance For fluid interface
     */
    public function filterByExtra($extra)
    {
        return $this->filter('extra', $extra);
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
        CREATE TABLE IF NOT EXISTS `pvlng_channel` (
          `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
          `guid` char(39) DEFAULT NULL COMMENT \'Unique GUID\',
          `name` varchar(255) NOT NULL DEFAULT \'\' COMMENT \'Unique identifier\',
          `description` varchar(255) NOT NULL DEFAULT \'\' COMMENT \'Longer description\',
          `serial` varchar(30) NOT NULL DEFAULT \'\',
          `channel` varchar(255) NOT NULL DEFAULT \'\',
          `type` smallint(5) unsigned NOT NULL DEFAULT \'0\' COMMENT \'pvlng_type -> id\',
          `resolution` double NOT NULL DEFAULT \'1\',
          `unit` varchar(10) NOT NULL DEFAULT \'\',
          `decimals` tinyint(1) unsigned NOT NULL DEFAULT \'2\',
          `meter` tinyint(1) unsigned NOT NULL DEFAULT \'0\',
          `numeric` tinyint(1) unsigned NOT NULL DEFAULT \'1\',
          `offset` double NOT NULL DEFAULT \'0\',
          `adjust` tinyint(1) unsigned NOT NULL DEFAULT \'0\' COMMENT \'allow auto adjustment of offset\',
          `cost` double DEFAULT NULL COMMENT \'per unit or unit * h\',
          `tariff` int(10) unsigned DEFAULT NULL,
          `threshold` double unsigned DEFAULT NULL,
          `valid_from` double DEFAULT NULL COMMENT \'Numeric min. acceptable value\',
          `valid_to` double DEFAULT NULL COMMENT \'Numeric max. acceptable value\',
          `public` tinyint(1) unsigned NOT NULL DEFAULT \'1\' COMMENT \'Public channels don\'\'t need API key to read\',
          `tags` text COMMENT \'scope:value tags, one per line\',
          `extra` text COMMENT \'Not visible field for models to store extra info\',
          `comment` text COMMENT \'Internal comment\',
          `icon` varchar(255) NOT NULL DEFAULT \'\',
          PRIMARY KEY (`id`),
          UNIQUE KEY `GUID` (`guid`),
          KEY `type` (`type`),
          KEY `tariff` (`tariff`),
          CONSTRAINT `pvlng_channel_ibfk_1` FOREIGN KEY (`type`) REFERENCES `pvlng_type` (`id`) ON UPDATE CASCADE,
          CONSTRAINT `pvlng_channel_ibfk_2` FOREIGN KEY (`tariff`) REFERENCES `pvlng_tariff` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 PACK_KEYS=1 COMMENT=\'The channels defined\'
    ';
    // @codingStandardsIgnoreEnd

    /**
     * Table name
     *
     * @var string $table Table name
     */
    protected $table = 'pvlng_channel';

    /**
     *
     */
    protected $fields = [
        'id'          => '',
        'guid'        => '',
        'name'        => '',
        'description' => '',
        'serial'      => '',
        'channel'     => '',
        'type'        => '',
        'resolution'  => '',
        'unit'        => '',
        'decimals'    => '',
        'meter'       => '',
        'numeric'     => '',
        'offset'      => '',
        'adjust'      => '',
        'cost'        => '',
        'tariff'      => '',
        'threshold'   => '',
        'valid_from'  => '',
        'valid_to'    => '',
        'public'      => '',
        'tags'        => '',
        'extra'       => '',
        'comment'     => '',
        'icon'        => ''
    ];

    /**
     *
     */
    protected $nullable = [
        'id'          => false,
        'guid'        => true,
        'name'        => false,
        'description' => false,
        'serial'      => false,
        'channel'     => false,
        'type'        => false,
        'resolution'  => false,
        'unit'        => false,
        'decimals'    => false,
        'meter'       => false,
        'numeric'     => false,
        'offset'      => false,
        'adjust'      => false,
        'cost'        => true,
        'tariff'      => true,
        'threshold'   => true,
        'valid_from'  => true,
        'valid_to'    => true,
        'public'      => false,
        'tags'        => true,
        'extra'       => true,
        'comment'     => true,
        'icon'        => false
    ];

    /**
     *
     */
    protected $primary = ['id'];

    /**
     *
     */
    protected $autoinc = 'id';
}
