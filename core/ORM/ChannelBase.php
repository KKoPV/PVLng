<?php
/**
 * Abstract base class for table 'pvlng_channel'
 *
 * *** NEVER EVER EDIT THIS FILE! ***
 *
 * To extend the functionallity, edit "Channel.php"
 *
 * If you make changes here, they will be lost on next upgrade PVLng!
 *
 * @author     Knut Kohl <github@knutkohl.de>
 * @copyright  2015 Knut Kohl
 * @license    MIT License (MIT) http://opensource.org/licenses/MIT
 *
 * @author     PVLng ORM class builder
 * @version    1.2.0 / 2015-03-18
 */
namespace ORM;

/**
 *
 */
abstract class ChannelBase extends \slimMVC\ORM {

    // -----------------------------------------------------------------------
    // PUBLIC
    // -----------------------------------------------------------------------

    // -----------------------------------------------------------------------
    // Setter methods
    // -----------------------------------------------------------------------

    /**
     * 'id' is AutoInc, no setter
     */

    /**
     * Basic setter for field 'guid'
     *
     * @param  mixed    $guid Guid value
     * @return Instance For fluid interface
     */
    public function setGuid( $guid ) {
        $this->fields['guid'] = $guid;
        return $this;
    }   // setGuid()

    /**
     * Basic setter for field 'name'
     *
     * @param  mixed    $name Name value
     * @return Instance For fluid interface
     */
    public function setName( $name ) {
        $this->fields['name'] = $name;
        return $this;
    }   // setName()

    /**
     * Basic setter for field 'description'
     *
     * @param  mixed    $description Description value
     * @return Instance For fluid interface
     */
    public function setDescription( $description ) {
        $this->fields['description'] = $description;
        return $this;
    }   // setDescription()

    /**
     * Basic setter for field 'serial'
     *
     * @param  mixed    $serial Serial value
     * @return Instance For fluid interface
     */
    public function setSerial( $serial ) {
        $this->fields['serial'] = $serial;
        return $this;
    }   // setSerial()

    /**
     * Basic setter for field 'channel'
     *
     * @param  mixed    $channel Channel value
     * @return Instance For fluid interface
     */
    public function setChannel( $channel ) {
        $this->fields['channel'] = $channel;
        return $this;
    }   // setChannel()

    /**
     * Basic setter for field 'type'
     *
     * @param  mixed    $type Type value
     * @return Instance For fluid interface
     */
    public function setType( $type ) {
        $this->fields['type'] = $type;
        return $this;
    }   // setType()

    /**
     * Basic setter for field 'resolution'
     *
     * @param  mixed    $resolution Resolution value
     * @return Instance For fluid interface
     */
    public function setResolution( $resolution ) {
        $this->fields['resolution'] = $resolution;
        return $this;
    }   // setResolution()

    /**
     * Basic setter for field 'unit'
     *
     * @param  mixed    $unit Unit value
     * @return Instance For fluid interface
     */
    public function setUnit( $unit ) {
        $this->fields['unit'] = $unit;
        return $this;
    }   // setUnit()

    /**
     * Basic setter for field 'decimals'
     *
     * @param  mixed    $decimals Decimals value
     * @return Instance For fluid interface
     */
    public function setDecimals( $decimals ) {
        $this->fields['decimals'] = $decimals;
        return $this;
    }   // setDecimals()

    /**
     * Basic setter for field 'meter'
     *
     * @param  mixed    $meter Meter value
     * @return Instance For fluid interface
     */
    public function setMeter( $meter ) {
        $this->fields['meter'] = $meter;
        return $this;
    }   // setMeter()

    /**
     * Basic setter for field 'numeric'
     *
     * @param  mixed    $numeric Numeric value
     * @return Instance For fluid interface
     */
    public function setNumeric( $numeric ) {
        $this->fields['numeric'] = $numeric;
        return $this;
    }   // setNumeric()

    /**
     * Basic setter for field 'offset'
     *
     * @param  mixed    $offset Offset value
     * @return Instance For fluid interface
     */
    public function setOffset( $offset ) {
        $this->fields['offset'] = $offset;
        return $this;
    }   // setOffset()

    /**
     * Basic setter for field 'adjust'
     *
     * @param  mixed    $adjust Adjust value
     * @return Instance For fluid interface
     */
    public function setAdjust( $adjust ) {
        $this->fields['adjust'] = $adjust;
        return $this;
    }   // setAdjust()

    /**
     * Basic setter for field 'cost'
     *
     * @param  mixed    $cost Cost value
     * @return Instance For fluid interface
     */
    public function setCost( $cost ) {
        $this->fields['cost'] = $cost;
        return $this;
    }   // setCost()

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
     * Basic setter for field 'threshold'
     *
     * @param  mixed    $threshold Threshold value
     * @return Instance For fluid interface
     */
    public function setThreshold( $threshold ) {
        $this->fields['threshold'] = $threshold;
        return $this;
    }   // setThreshold()

    /**
     * Basic setter for field 'valid_from'
     *
     * @param  mixed    $valid_from ValidFrom value
     * @return Instance For fluid interface
     */
    public function setValidFrom( $valid_from ) {
        $this->fields['valid_from'] = $valid_from;
        return $this;
    }   // setValidFrom()

    /**
     * Basic setter for field 'valid_to'
     *
     * @param  mixed    $valid_to ValidTo value
     * @return Instance For fluid interface
     */
    public function setValidTo( $valid_to ) {
        $this->fields['valid_to'] = $valid_to;
        return $this;
    }   // setValidTo()

    /**
     * Basic setter for field 'public'
     *
     * @param  mixed    $public Public value
     * @return Instance For fluid interface
     */
    public function setPublic( $public ) {
        $this->fields['public'] = $public;
        return $this;
    }   // setPublic()

    /**
     * Basic setter for field 'extra'
     *
     * @param  mixed    $extra Extra value
     * @return Instance For fluid interface
     */
    public function setExtra( $extra ) {
        $this->fields['extra'] = $extra;
        return $this;
    }   // setExtra()

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

    /**
     * Basic setter for field 'icon'
     *
     * @param  mixed    $icon Icon value
     * @return Instance For fluid interface
     */
    public function setIcon( $icon ) {
        $this->fields['icon'] = $icon;
        return $this;
    }   // setIcon()

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
     * Basic getter for field 'guid'
     *
     * @return mixed Guid value
     */
    public function getGuid() {
        return $this->fields['guid'];
    }   // getGuid()

    /**
     * Basic getter for field 'name'
     *
     * @return mixed Name value
     */
    public function getName() {
        return $this->fields['name'];
    }   // getName()

    /**
     * Basic getter for field 'description'
     *
     * @return mixed Description value
     */
    public function getDescription() {
        return $this->fields['description'];
    }   // getDescription()

    /**
     * Basic getter for field 'serial'
     *
     * @return mixed Serial value
     */
    public function getSerial() {
        return $this->fields['serial'];
    }   // getSerial()

    /**
     * Basic getter for field 'channel'
     *
     * @return mixed Channel value
     */
    public function getChannel() {
        return $this->fields['channel'];
    }   // getChannel()

    /**
     * Basic getter for field 'type'
     *
     * @return mixed Type value
     */
    public function getType() {
        return $this->fields['type'];
    }   // getType()

    /**
     * Basic getter for field 'resolution'
     *
     * @return mixed Resolution value
     */
    public function getResolution() {
        return $this->fields['resolution'];
    }   // getResolution()

    /**
     * Basic getter for field 'unit'
     *
     * @return mixed Unit value
     */
    public function getUnit() {
        return $this->fields['unit'];
    }   // getUnit()

    /**
     * Basic getter for field 'decimals'
     *
     * @return mixed Decimals value
     */
    public function getDecimals() {
        return $this->fields['decimals'];
    }   // getDecimals()

    /**
     * Basic getter for field 'meter'
     *
     * @return mixed Meter value
     */
    public function getMeter() {
        return $this->fields['meter'];
    }   // getMeter()

    /**
     * Basic getter for field 'numeric'
     *
     * @return mixed Numeric value
     */
    public function getNumeric() {
        return $this->fields['numeric'];
    }   // getNumeric()

    /**
     * Basic getter for field 'offset'
     *
     * @return mixed Offset value
     */
    public function getOffset() {
        return $this->fields['offset'];
    }   // getOffset()

    /**
     * Basic getter for field 'adjust'
     *
     * @return mixed Adjust value
     */
    public function getAdjust() {
        return $this->fields['adjust'];
    }   // getAdjust()

    /**
     * Basic getter for field 'cost'
     *
     * @return mixed Cost value
     */
    public function getCost() {
        return $this->fields['cost'];
    }   // getCost()

    /**
     * Basic getter for field 'tariff'
     *
     * @return mixed Tariff value
     */
    public function getTariff() {
        return $this->fields['tariff'];
    }   // getTariff()

    /**
     * Basic getter for field 'threshold'
     *
     * @return mixed Threshold value
     */
    public function getThreshold() {
        return $this->fields['threshold'];
    }   // getThreshold()

    /**
     * Basic getter for field 'valid_from'
     *
     * @return mixed ValidFrom value
     */
    public function getValidFrom() {
        return $this->fields['valid_from'];
    }   // getValidFrom()

    /**
     * Basic getter for field 'valid_to'
     *
     * @return mixed ValidTo value
     */
    public function getValidTo() {
        return $this->fields['valid_to'];
    }   // getValidTo()

    /**
     * Basic getter for field 'public'
     *
     * @return mixed Public value
     */
    public function getPublic() {
        return $this->fields['public'];
    }   // getPublic()

    /**
     * Basic getter for field 'extra'
     *
     * @return mixed Extra value
     */
    public function getExtra() {
        return $this->fields['extra'];
    }   // getExtra()

    /**
     * Basic getter for field 'comment'
     *
     * @return mixed Comment value
     */
    public function getComment() {
        return $this->fields['comment'];
    }   // getComment()

    /**
     * Basic getter for field 'icon'
     *
     * @return mixed Icon value
     */
    public function getIcon() {
        return $this->fields['icon'];
    }   // getIcon()

    // -----------------------------------------------------------------------
    // Filter methods
    // -----------------------------------------------------------------------

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
     * Filter for field 'guid'
     *
     * @param  mixed    $guid Filter value
     * @return Instance For fluid interface
     */
    public function filterByGuid( $guid ) {
        $this->filter[] = '`guid` = "'.$this->quote($guid).'"';
        return $this;
    }   // filterByGuid()

    /**
     * Filter for field 'type'
     *
     * @param  mixed    $type Filter value
     * @return Instance For fluid interface
     */
    public function filterByType( $type ) {
        $this->filter[] = '`type` = "'.$this->quote($type).'"';
        return $this;
    }   // filterByType()

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
     * Filter for field 'name'
     *
     * @param  mixed    $name Filter value
     * @return Instance For fluid interface
     */
    public function filterByName( $name ) {
        $this->filter[] = '`name` = "'.$this->quote($name).'"';
        return $this;
    }   // filterByName()

    /**
     * Filter for field 'description'
     *
     * @param  mixed    $description Filter value
     * @return Instance For fluid interface
     */
    public function filterByDescription( $description ) {
        $this->filter[] = '`description` = "'.$this->quote($description).'"';
        return $this;
    }   // filterByDescription()

    /**
     * Filter for field 'serial'
     *
     * @param  mixed    $serial Filter value
     * @return Instance For fluid interface
     */
    public function filterBySerial( $serial ) {
        $this->filter[] = '`serial` = "'.$this->quote($serial).'"';
        return $this;
    }   // filterBySerial()

    /**
     * Filter for field 'channel'
     *
     * @param  mixed    $channel Filter value
     * @return Instance For fluid interface
     */
    public function filterByChannel( $channel ) {
        $this->filter[] = '`channel` = "'.$this->quote($channel).'"';
        return $this;
    }   // filterByChannel()

    /**
     * Filter for field 'resolution'
     *
     * @param  mixed    $resolution Filter value
     * @return Instance For fluid interface
     */
    public function filterByResolution( $resolution ) {
        $this->filter[] = '`resolution` = "'.$this->quote($resolution).'"';
        return $this;
    }   // filterByResolution()

    /**
     * Filter for field 'unit'
     *
     * @param  mixed    $unit Filter value
     * @return Instance For fluid interface
     */
    public function filterByUnit( $unit ) {
        $this->filter[] = '`unit` = "'.$this->quote($unit).'"';
        return $this;
    }   // filterByUnit()

    /**
     * Filter for field 'decimals'
     *
     * @param  mixed    $decimals Filter value
     * @return Instance For fluid interface
     */
    public function filterByDecimals( $decimals ) {
        $this->filter[] = '`decimals` = "'.$this->quote($decimals).'"';
        return $this;
    }   // filterByDecimals()

    /**
     * Filter for field 'meter'
     *
     * @param  mixed    $meter Filter value
     * @return Instance For fluid interface
     */
    public function filterByMeter( $meter ) {
        $this->filter[] = '`meter` = "'.$this->quote($meter).'"';
        return $this;
    }   // filterByMeter()

    /**
     * Filter for field 'numeric'
     *
     * @param  mixed    $numeric Filter value
     * @return Instance For fluid interface
     */
    public function filterByNumeric( $numeric ) {
        $this->filter[] = '`numeric` = "'.$this->quote($numeric).'"';
        return $this;
    }   // filterByNumeric()

    /**
     * Filter for field 'offset'
     *
     * @param  mixed    $offset Filter value
     * @return Instance For fluid interface
     */
    public function filterByOffset( $offset ) {
        $this->filter[] = '`offset` = "'.$this->quote($offset).'"';
        return $this;
    }   // filterByOffset()

    /**
     * Filter for field 'adjust'
     *
     * @param  mixed    $adjust Filter value
     * @return Instance For fluid interface
     */
    public function filterByAdjust( $adjust ) {
        $this->filter[] = '`adjust` = "'.$this->quote($adjust).'"';
        return $this;
    }   // filterByAdjust()

    /**
     * Filter for field 'cost'
     *
     * @param  mixed    $cost Filter value
     * @return Instance For fluid interface
     */
    public function filterByCost( $cost ) {
        $this->filter[] = '`cost` = "'.$this->quote($cost).'"';
        return $this;
    }   // filterByCost()

    /**
     * Filter for field 'threshold'
     *
     * @param  mixed    $threshold Filter value
     * @return Instance For fluid interface
     */
    public function filterByThreshold( $threshold ) {
        $this->filter[] = '`threshold` = "'.$this->quote($threshold).'"';
        return $this;
    }   // filterByThreshold()

    /**
     * Filter for field 'valid_from'
     *
     * @param  mixed    $valid_from Filter value
     * @return Instance For fluid interface
     */
    public function filterByValidFrom( $valid_from ) {
        $this->filter[] = '`valid_from` = "'.$this->quote($valid_from).'"';
        return $this;
    }   // filterByValidFrom()

    /**
     * Filter for field 'valid_to'
     *
     * @param  mixed    $valid_to Filter value
     * @return Instance For fluid interface
     */
    public function filterByValidTo( $valid_to ) {
        $this->filter[] = '`valid_to` = "'.$this->quote($valid_to).'"';
        return $this;
    }   // filterByValidTo()

    /**
     * Filter for field 'public'
     *
     * @param  mixed    $public Filter value
     * @return Instance For fluid interface
     */
    public function filterByPublic( $public ) {
        $this->filter[] = '`public` = "'.$this->quote($public).'"';
        return $this;
    }   // filterByPublic()

    /**
     * Filter for field 'extra'
     *
     * @param  mixed    $extra Filter value
     * @return Instance For fluid interface
     */
    public function filterByExtra( $extra ) {
        $this->filter[] = '`extra` = "'.$this->quote($extra).'"';
        return $this;
    }   // filterByExtra()

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

    /**
     * Filter for field 'icon'
     *
     * @param  mixed    $icon Filter value
     * @return Instance For fluid interface
     */
    public function filterByIcon( $icon ) {
        $this->filter[] = '`icon` = "'.$this->quote($icon).'"';
        return $this;
    }   // filterByIcon()

    // -----------------------------------------------------------------------
    // PROTECTED
    // -----------------------------------------------------------------------

    /**
     * Table name
     *
     * @var string $table Table name
     */
    protected $table = 'pvlng_channel';

    /**
     * SQL for creation
     *
     * @var string $createSQL
     */
    protected $createSQL = '
        CREATE TABLE `pvlng_channel` (
          `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
          `guid` char(39) DEFAULT NULL COMMENT \'Unique GUID\',
          `name` varchar(255) NOT NULL COMMENT \'Unique identifier\',
          `description` varchar(255) NOT NULL COMMENT \'Longer description\',
          `serial` varchar(30) NOT NULL,
          `channel` varchar(255) NOT NULL,
          `type` smallint(5) unsigned NOT NULL COMMENT \'pvlng_type -> id\',
          `resolution` double NOT NULL DEFAULT \'1\',
          `unit` varchar(10) NOT NULL,
          `decimals` tinyint(1) unsigned NOT NULL DEFAULT \'2\',
          `meter` tinyint(1) unsigned NOT NULL,
          `numeric` tinyint(1) unsigned NOT NULL DEFAULT \'1\',
          `offset` double NOT NULL,
          `adjust` tinyint(1) unsigned NOT NULL COMMENT \'allow auto adjustment of offset\',
          `cost` double DEFAULT NULL COMMENT \'per unit or unit * h\',
          `tariff` int(10) unsigned DEFAULT NULL,
          `threshold` double unsigned DEFAULT NULL,
          `valid_from` double DEFAULT NULL COMMENT \'Numeric min. acceptable value\',
          `valid_to` double DEFAULT NULL COMMENT \'Numeric max. acceptable value\',
          `public` tinyint(1) unsigned NOT NULL DEFAULT \'1\' COMMENT \'Public channels don\'\'t need API key to read\',
          `extra` text NOT NULL COMMENT \'Not visible field for models to store extra info\',
          `comment` text NOT NULL COMMENT \'Internal comment\',
          `icon` varchar(255) NOT NULL,
          PRIMARY KEY (`id`),
          UNIQUE KEY `GUID` (`guid`),
          KEY `type` (`type`),
          KEY `tariff` (`tariff`),
          CONSTRAINT `pvlng_channel_ibfk_1` FOREIGN KEY (`type`) REFERENCES `pvlng_type` (`id`) ON UPDATE CASCADE,
          CONSTRAINT `pvlng_channel_ibfk_2` FOREIGN KEY (`tariff`) REFERENCES `pvlng_tariff` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
        ) ENGINE=InnoDB AUTO_INCREMENT=662 DEFAULT CHARSET=utf8 PACK_KEYS=1 COMMENT=\'The channels defined\'
    ';

}
