<?php
/**
 * Base access class for "pvlng_tree_view"
 *
 * NEVER EVER EDIT THIS FILE
 *
 * To extend the functionallity, edit "Tree.php"
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
abstract class TreeBase extends \slimMVC\ORM {

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
     * 'pvlng_tree_view' is a view, no setters
     */

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
     * Basic getter for field 'entity'
     *
     * @return mixed Entity value
     */
    public function getEntity() {
        return $this->fields['entity'];
    } // getEntity()

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
     * Basic getter for field 'description'
     *
     * @return mixed Description value
     */
    public function getDescription() {
        return $this->fields['description'];
    } // getDescription()

    /**
     * Basic getter for field 'resolution'
     *
     * @return mixed Resolution value
     */
    public function getResolution() {
        return $this->fields['resolution'];
    } // getResolution()

    /**
     * Basic getter for field 'cost'
     *
     * @return mixed Cost value
     */
    public function getCost() {
        return $this->fields['cost'];
    } // getCost()

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
     * Basic getter for field 'type_id'
     *
     * @return mixed TypeId value
     */
    public function getTypeId() {
        return $this->fields['type_id'];
    } // getTypeId()

    /**
     * Basic getter for field 'type'
     *
     * @return mixed Type value
     */
    public function getType() {
        return $this->fields['type'];
    } // getType()

    /**
     * Basic getter for field 'model'
     *
     * @return mixed Model value
     */
    public function getModel() {
        return $this->fields['model'];
    } // getModel()

    /**
     * Basic getter for field 'childs'
     *
     * @return mixed Childs value
     */
    public function getChilds() {
        return $this->fields['childs'];
    } // getChilds()

    /**
     * Basic getter for field 'read'
     *
     * @return mixed Read value
     */
    public function getRead() {
        return $this->fields['read'];
    } // getRead()

    /**
     * Basic getter for field 'write'
     *
     * @return mixed Write value
     */
    public function getWrite() {
        return $this->fields['write'];
    } // getWrite()

    /**
     * Basic getter for field 'graph'
     *
     * @return mixed Graph value
     */
    public function getGraph() {
        return $this->fields['graph'];
    } // getGraph()

    /**
     * Basic getter for field 'icon'
     *
     * @return mixed Icon value
     */
    public function getIcon() {
        return $this->fields['icon'];
    } // getIcon()

    /**
     * Basic getter for field 'alias'
     *
     * @return mixed Alias value
     */
    public function getAlias() {
        return $this->fields['alias'];
    } // getAlias()

    /**
     * Basic getter for field 'alias_of'
     *
     * @return mixed AliasOf value
     */
    public function getAliasOf() {
        return $this->fields['alias_of'];
    } // getAliasOf()

    /**
     * Basic getter for field 'level'
     *
     * @return mixed Level value
     */
    public function getLevel() {
        return $this->fields['level'];
    } // getLevel()

    /**
     * Basic getter for field 'haschilds'
     *
     * @return mixed Haschilds value
     */
    public function getHaschilds() {
        return $this->fields['haschilds'];
    } // getHaschilds()

    /**
     * Basic getter for field 'lower'
     *
     * @return mixed Lower value
     */
    public function getLower() {
        return $this->fields['lower'];
    } // getLower()

    /**
     * Basic getter for field 'upper'
     *
     * @return mixed Upper value
     */
    public function getUpper() {
        return $this->fields['upper'];
    } // getUpper()

    // -----------------------------------------------------------------------
    // Filter methods
    // -----------------------------------------------------------------------

    /**
     * Filter for field id
     *
     * @param mixed Field value
     */
    public function filterById( $id ) {
        return $this->filter('id', $id);
    } // filterById()

    /**
     * Filter for field entity
     *
     * @param mixed Field value
     */
    public function filterByEntity( $entity ) {
        return $this->filter('entity', $entity);
    } // filterByEntity()

    /**
     * Filter for field guid
     *
     * @param mixed Field value
     */
    public function filterByGuid( $guid ) {
        return $this->filter('guid', $guid);
    } // filterByGuid()

    /**
     * Filter for field name
     *
     * @param mixed Field value
     */
    public function filterByName( $name ) {
        return $this->filter('name', $name);
    } // filterByName()

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
     * Filter for field description
     *
     * @param mixed Field value
     */
    public function filterByDescription( $description ) {
        return $this->filter('description', $description);
    } // filterByDescription()

    /**
     * Filter for field resolution
     *
     * @param mixed Field value
     */
    public function filterByResolution( $resolution ) {
        return $this->filter('resolution', $resolution);
    } // filterByResolution()

    /**
     * Filter for field cost
     *
     * @param mixed Field value
     */
    public function filterByCost( $cost ) {
        return $this->filter('cost', $cost);
    } // filterByCost()

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
     * Filter for field type_id
     *
     * @param mixed Field value
     */
    public function filterByTypeId( $type_id ) {
        return $this->filter('type_id', $type_id);
    } // filterByTypeId()

    /**
     * Filter for field type
     *
     * @param mixed Field value
     */
    public function filterByType( $type ) {
        return $this->filter('type', $type);
    } // filterByType()

    /**
     * Filter for field model
     *
     * @param mixed Field value
     */
    public function filterByModel( $model ) {
        return $this->filter('model', $model);
    } // filterByModel()

    /**
     * Filter for field childs
     *
     * @param mixed Field value
     */
    public function filterByChilds( $childs ) {
        return $this->filter('childs', $childs);
    } // filterByChilds()

    /**
     * Filter for field read
     *
     * @param mixed Field value
     */
    public function filterByRead( $read ) {
        return $this->filter('read', $read);
    } // filterByRead()

    /**
     * Filter for field write
     *
     * @param mixed Field value
     */
    public function filterByWrite( $write ) {
        return $this->filter('write', $write);
    } // filterByWrite()

    /**
     * Filter for field graph
     *
     * @param mixed Field value
     */
    public function filterByGraph( $graph ) {
        return $this->filter('graph', $graph);
    } // filterByGraph()

    /**
     * Filter for field icon
     *
     * @param mixed Field value
     */
    public function filterByIcon( $icon ) {
        return $this->filter('icon', $icon);
    } // filterByIcon()

    /**
     * Filter for field alias
     *
     * @param mixed Field value
     */
    public function filterByAlias( $alias ) {
        return $this->filter('alias', $alias);
    } // filterByAlias()

    /**
     * Filter for field alias_of
     *
     * @param mixed Field value
     */
    public function filterByAliasOf( $alias_of ) {
        return $this->filter('alias_of', $alias_of);
    } // filterByAliasOf()

    /**
     * Filter for field level
     *
     * @param mixed Field value
     */
    public function filterByLevel( $level ) {
        return $this->filter('level', $level);
    } // filterByLevel()

    /**
     * Filter for field haschilds
     *
     * @param mixed Field value
     */
    public function filterByHaschilds( $haschilds ) {
        return $this->filter('haschilds', $haschilds);
    } // filterByHaschilds()

    /**
     * Filter for field lower
     *
     * @param mixed Field value
     */
    public function filterByLower( $lower ) {
        return $this->filter('lower', $lower);
    } // filterByLower()

    /**
     * Filter for field upper
     *
     * @param mixed Field value
     */
    public function filterByUpper( $upper ) {
        return $this->filter('upper', $upper);
    } // filterByUpper()

    // -----------------------------------------------------------------------
    // PROTECTED
    // -----------------------------------------------------------------------

    /**
     * Table name
     *
     * @var string $table Table name
     */
    protected $table = 'pvlng_tree_view';

}
