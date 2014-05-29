<?php
/**
 * Base access class for "pvlng_channel"
 *
 * NEVER EVER EDIT THIS FILE
 *
 * To extend the functionallity, edit "Channel.php"
 *
 * If you make changes here, they will be lost on next upgrade PVLng!
 *
 * @author     PVLng ORM class builder
 * @version    1.0.0
 */
namespace ORM;

/**
 *
 */
abstract class ChannelBase extends \slimMVC\ORM {

    // -----------------------------------------------------------------------
    // PUBLIC
    // -----------------------------------------------------------------------

    /**
     * Forge method for key field(s)
     *
     * @param mixed Field value
     */
    public static function forge( $id ) {
        return new static(array($id));
    } // forge()

    // -----------------------------------------------------------------------
    // Setter methods
    // -----------------------------------------------------------------------

    /**
     * "id" is AutoInc, no setter
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
    } // setGuid()

    /**
     * Basic setter for field 'name'
     *
     * @param  mixed    $name Name value
     * @return Instance For fluid interface
     */
    public function setName( $name ) {
        $this->fields['name'] = $name;
        return $this;
    } // setName()

    /**
     * Basic setter for field 'description'
     *
     * @param  mixed    $description Description value
     * @return Instance For fluid interface
     */
    public function setDescription( $description ) {
        $this->fields['description'] = $description;
        return $this;
    } // setDescription()

    /**
     * Basic setter for field 'serial'
     *
     * @param  mixed    $serial Serial value
     * @return Instance For fluid interface
     */
    public function setSerial( $serial ) {
        $this->fields['serial'] = $serial;
        return $this;
    } // setSerial()

    /**
     * Basic setter for field 'channel'
     *
     * @param  mixed    $channel Channel value
     * @return Instance For fluid interface
     */
    public function setChannel( $channel ) {
        $this->fields['channel'] = $channel;
        return $this;
    } // setChannel()

    /**
     * Basic setter for field 'type'
     *
     * @param  mixed    $type Type value
     * @return Instance For fluid interface
     */
    public function setType( $type ) {
        $this->fields['type'] = $type;
        return $this;
    } // setType()

    /**
     * Basic setter for field 'resolution'
     *
     * @param  mixed    $resolution Resolution value
     * @return Instance For fluid interface
     */
    public function setResolution( $resolution ) {
        $this->fields['resolution'] = $resolution;
        return $this;
    } // setResolution()

    /**
     * Basic setter for field 'unit'
     *
     * @param  mixed    $unit Unit value
     * @return Instance For fluid interface
     */
    public function setUnit( $unit ) {
        $this->fields['unit'] = $unit;
        return $this;
    } // setUnit()

    /**
     * Basic setter for field 'decimals'
     *
     * @param  mixed    $decimals Decimals value
     * @return Instance For fluid interface
     */
    public function setDecimals( $decimals ) {
        $this->fields['decimals'] = $decimals;
        return $this;
    } // setDecimals()

    /**
     * Basic setter for field 'meter'
     *
     * @param  mixed    $meter Meter value
     * @return Instance For fluid interface
     */
    public function setMeter( $meter ) {
        $this->fields['meter'] = $meter;
        return $this;
    } // setMeter()

    /**
     * Basic setter for field 'numeric'
     *
     * @param  mixed    $numeric Numeric value
     * @return Instance For fluid interface
     */
    public function setNumeric( $numeric ) {
        $this->fields['numeric'] = $numeric;
        return $this;
    } // setNumeric()

    /**
     * Basic setter for field 'offset'
     *
     * @param  mixed    $offset Offset value
     * @return Instance For fluid interface
     */
    public function setOffset( $offset ) {
        $this->fields['offset'] = $offset;
        return $this;
    } // setOffset()

    /**
     * Basic setter for field 'adjust'
     *
     * @param  mixed    $adjust Adjust value
     * @return Instance For fluid interface
     */
    public function setAdjust( $adjust ) {
        $this->fields['adjust'] = $adjust;
        return $this;
    } // setAdjust()

    /**
     * Basic setter for field 'cost'
     *
     * @param  mixed    $cost Cost value
     * @return Instance For fluid interface
     */
    public function setCost( $cost ) {
        $this->fields['cost'] = $cost;
        return $this;
    } // setCost()

    /**
     * Basic setter for field 'tariff'
     *
     * @param  mixed    $tariff Tariff value
     * @return Instance For fluid interface
     */
    public function setTariff( $tariff ) {
        $this->fields['tariff'] = $tariff;
        return $this;
    } // setTariff()

    /**
     * Basic setter for field 'threshold'
     *
     * @param  mixed    $threshold Threshold value
     * @return Instance For fluid interface
     */
    public function setThreshold( $threshold ) {
        $this->fields['threshold'] = $threshold;
        return $this;
    } // setThreshold()

    /**
     * Basic setter for field 'valid_from'
     *
     * @param  mixed    $valid_from ValidFrom value
     * @return Instance For fluid interface
     */
    public function setValidFrom( $valid_from ) {
        $this->fields['valid_from'] = $valid_from;
        return $this;
    } // setValidFrom()

    /**
     * Basic setter for field 'valid_to'
     *
     * @param  mixed    $valid_to ValidTo value
     * @return Instance For fluid interface
     */
    public function setValidTo( $valid_to ) {
        $this->fields['valid_to'] = $valid_to;
        return $this;
    } // setValidTo()

    /**
     * Basic setter for field 'public'
     *
     * @param  mixed    $public Public value
     * @return Instance For fluid interface
     */
    public function setPublic( $public ) {
        $this->fields['public'] = $public;
        return $this;
    } // setPublic()

    /**
     * Basic setter for field 'extra'
     *
     * @param  mixed    $extra Extra value
     * @return Instance For fluid interface
     */
    public function setExtra( $extra ) {
        $this->fields['extra'] = $extra;
        return $this;
    } // setExtra()

    /**
     * Basic setter for field 'comment'
     *
     * @param  mixed    $comment Comment value
     * @return Instance For fluid interface
     */
    public function setComment( $comment ) {
        $this->fields['comment'] = $comment;
        return $this;
    } // setComment()

    /**
     * Basic setter for field 'icon'
     *
     * @param  mixed    $icon Icon value
     * @return Instance For fluid interface
     */
    public function setIcon( $icon ) {
        $this->fields['icon'] = $icon;
        return $this;
    } // setIcon()

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
    } // getId()

    /**
     * Basic getter for field 'guid'
     *
     * @return mixed Guid value
     */
    public function getGuid() {
        return $this->fields['guid'];
    } // getGuid()

    /**
     * Basic getter for field 'name'
     *
     * @return mixed Name value
     */
    public function getName() {
        return $this->fields['name'];
    } // getName()

    /**
     * Basic getter for field 'description'
     *
     * @return mixed Description value
     */
    public function getDescription() {
        return $this->fields['description'];
    } // getDescription()

    /**
     * Basic getter for field 'serial'
     *
     * @return mixed Serial value
     */
    public function getSerial() {
        return $this->fields['serial'];
    } // getSerial()

    /**
     * Basic getter for field 'channel'
     *
     * @return mixed Channel value
     */
    public function getChannel() {
        return $this->fields['channel'];
    } // getChannel()

    /**
     * Basic getter for field 'type'
     *
     * @return mixed Type value
     */
    public function getType() {
        return $this->fields['type'];
    } // getType()

    /**
     * Basic getter for field 'resolution'
     *
     * @return mixed Resolution value
     */
    public function getResolution() {
        return $this->fields['resolution'];
    } // getResolution()

    /**
     * Basic getter for field 'unit'
     *
     * @return mixed Unit value
     */
    public function getUnit() {
        return $this->fields['unit'];
    } // getUnit()

    /**
     * Basic getter for field 'decimals'
     *
     * @return mixed Decimals value
     */
    public function getDecimals() {
        return $this->fields['decimals'];
    } // getDecimals()

    /**
     * Basic getter for field 'meter'
     *
     * @return mixed Meter value
     */
    public function getMeter() {
        return $this->fields['meter'];
    } // getMeter()

    /**
     * Basic getter for field 'numeric'
     *
     * @return mixed Numeric value
     */
    public function getNumeric() {
        return $this->fields['numeric'];
    } // getNumeric()

    /**
     * Basic getter for field 'offset'
     *
     * @return mixed Offset value
     */
    public function getOffset() {
        return $this->fields['offset'];
    } // getOffset()

    /**
     * Basic getter for field 'adjust'
     *
     * @return mixed Adjust value
     */
    public function getAdjust() {
        return $this->fields['adjust'];
    } // getAdjust()

    /**
     * Basic getter for field 'cost'
     *
     * @return mixed Cost value
     */
    public function getCost() {
        return $this->fields['cost'];
    } // getCost()

    /**
     * Basic getter for field 'tariff'
     *
     * @return mixed Tariff value
     */
    public function getTariff() {
        return $this->fields['tariff'];
    } // getTariff()

    /**
     * Basic getter for field 'threshold'
     *
     * @return mixed Threshold value
     */
    public function getThreshold() {
        return $this->fields['threshold'];
    } // getThreshold()

    /**
     * Basic getter for field 'valid_from'
     *
     * @return mixed ValidFrom value
     */
    public function getValidFrom() {
        return $this->fields['valid_from'];
    } // getValidFrom()

    /**
     * Basic getter for field 'valid_to'
     *
     * @return mixed ValidTo value
     */
    public function getValidTo() {
        return $this->fields['valid_to'];
    } // getValidTo()

    /**
     * Basic getter for field 'public'
     *
     * @return mixed Public value
     */
    public function getPublic() {
        return $this->fields['public'];
    } // getPublic()

    /**
     * Basic getter for field 'extra'
     *
     * @return mixed Extra value
     */
    public function getExtra() {
        return $this->fields['extra'];
    } // getExtra()

    /**
     * Basic getter for field 'comment'
     *
     * @return mixed Comment value
     */
    public function getComment() {
        return $this->fields['comment'];
    } // getComment()

    /**
     * Basic getter for field 'icon'
     *
     * @return mixed Icon value
     */
    public function getIcon() {
        return $this->fields['icon'];
    } // getIcon()

    // -----------------------------------------------------------------------
    // Filter methods
    // -----------------------------------------------------------------------

    /**
     * Filter for field guid
     *
     * @param mixed Field value
     */
    public function filterByGuid( $guid ) {
        return $this->filter('guid', $guid);
    } // filterByGuid()

    /**
     * Filter for field id
     *
     * @param mixed Field value
     */
    public function filterById( $id ) {
        return $this->filter('id', $id);
    } // filterById()

    /**
     * Filter for field name
     *
     * @param mixed Field value
     */
    public function filterByName( $name ) {
        return $this->filter('name', $name);
    } // filterByName()

    /**
     * Filter for field description
     *
     * @param mixed Field value
     */
    public function filterByDescription( $description ) {
        return $this->filter('description', $description);
    } // filterByDescription()

    /**
     * Filter for field serial
     *
     * @param mixed Field value
     */
    public function filterBySerial( $serial ) {
        return $this->filter('serial', $serial);
    } // filterBySerial()

    /**
     * Filter for field channel
     *
     * @param mixed Field value
     */
    public function filterByChannel( $channel ) {
        return $this->filter('channel', $channel);
    } // filterByChannel()

    /**
     * Filter for field type
     *
     * @param mixed Field value
     */
    public function filterByType( $type ) {
        return $this->filter('type', $type);
    } // filterByType()

    /**
     * Filter for field resolution
     *
     * @param mixed Field value
     */
    public function filterByResolution( $resolution ) {
        return $this->filter('resolution', $resolution);
    } // filterByResolution()

    /**
     * Filter for field unit
     *
     * @param mixed Field value
     */
    public function filterByUnit( $unit ) {
        return $this->filter('unit', $unit);
    } // filterByUnit()

    /**
     * Filter for field decimals
     *
     * @param mixed Field value
     */
    public function filterByDecimals( $decimals ) {
        return $this->filter('decimals', $decimals);
    } // filterByDecimals()

    /**
     * Filter for field meter
     *
     * @param mixed Field value
     */
    public function filterByMeter( $meter ) {
        return $this->filter('meter', $meter);
    } // filterByMeter()

    /**
     * Filter for field numeric
     *
     * @param mixed Field value
     */
    public function filterByNumeric( $numeric ) {
        return $this->filter('numeric', $numeric);
    } // filterByNumeric()

    /**
     * Filter for field offset
     *
     * @param mixed Field value
     */
    public function filterByOffset( $offset ) {
        return $this->filter('offset', $offset);
    } // filterByOffset()

    /**
     * Filter for field adjust
     *
     * @param mixed Field value
     */
    public function filterByAdjust( $adjust ) {
        return $this->filter('adjust', $adjust);
    } // filterByAdjust()

    /**
     * Filter for field cost
     *
     * @param mixed Field value
     */
    public function filterByCost( $cost ) {
        return $this->filter('cost', $cost);
    } // filterByCost()

    /**
     * Filter for field tariff
     *
     * @param mixed Field value
     */
    public function filterByTariff( $tariff ) {
        return $this->filter('tariff', $tariff);
    } // filterByTariff()

    /**
     * Filter for field threshold
     *
     * @param mixed Field value
     */
    public function filterByThreshold( $threshold ) {
        return $this->filter('threshold', $threshold);
    } // filterByThreshold()

    /**
     * Filter for field valid_from
     *
     * @param mixed Field value
     */
    public function filterByValidFrom( $valid_from ) {
        return $this->filter('valid_from', $valid_from);
    } // filterByValidFrom()

    /**
     * Filter for field valid_to
     *
     * @param mixed Field value
     */
    public function filterByValidTo( $valid_to ) {
        return $this->filter('valid_to', $valid_to);
    } // filterByValidTo()

    /**
     * Filter for field public
     *
     * @param mixed Field value
     */
    public function filterByPublic( $public ) {
        return $this->filter('public', $public);
    } // filterByPublic()

    /**
     * Filter for field extra
     *
     * @param mixed Field value
     */
    public function filterByExtra( $extra ) {
        return $this->filter('extra', $extra);
    } // filterByExtra()

    /**
     * Filter for field comment
     *
     * @param mixed Field value
     */
    public function filterByComment( $comment ) {
        return $this->filter('comment', $comment);
    } // filterByComment()

    /**
     * Filter for field icon
     *
     * @param mixed Field value
     */
    public function filterByIcon( $icon ) {
        return $this->filter('icon', $icon);
    } // filterByIcon()

    // -----------------------------------------------------------------------
    // PROTECTED
    // -----------------------------------------------------------------------

    /**
     * Table name
     *
     * @var string $table Table name
     */
    protected $table = 'pvlng_channel';

}
