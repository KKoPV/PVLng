<?php
/**
 * Abstract base class for table 'pvlng_type'
 *
 * *** NEVER EVER EDIT THIS FILE! ***
 *
 * To extend the functionallity, edit "ChannelType.php"
 *
 * If you make changes here, they will be lost on next upgrade PVLng!
 *
 * @author     Knut Kohl <github@knutkohl.de>
 * @copyright  2014 Knut Kohl
 * @license    MIT License (MIT) http://opensource.org/licenses/MIT
 *
 * @author     PVLng ORM class builder
 * @version    1.1.0 / 2014-06-04
 */
namespace ORM;

/**
 *
 */
abstract class ChannelTypeBase extends \slimMVC\ORM {

    // -----------------------------------------------------------------------
    // PUBLIC
    // -----------------------------------------------------------------------

    // -----------------------------------------------------------------------
    // Setter methods
    // -----------------------------------------------------------------------

    /**
     * Basic setter for field 'id'
     *
     * @param  mixed    $id Id value
     * @return Instance For fluid interface
     */
    public function setId( $id ) {
        $this->fields['id'] = $id;
        return $this;
    }   // setId()

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
     * Basic setter for field 'model'
     *
     * @param  mixed    $model Model value
     * @return Instance For fluid interface
     */
    public function setModel( $model ) {
        $this->fields['model'] = $model;
        return $this;
    }   // setModel()

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
     * Basic setter for field 'childs'
     *
     * @param  mixed    $childs Childs value
     * @return Instance For fluid interface
     */
    public function setChilds( $childs ) {
        $this->fields['childs'] = $childs;
        return $this;
    }   // setChilds()

    /**
     * Basic setter for field 'read'
     *
     * @param  mixed    $read Read value
     * @return Instance For fluid interface
     */
    public function setRead( $read ) {
        $this->fields['read'] = $read;
        return $this;
    }   // setRead()

    /**
     * Basic setter for field 'write'
     *
     * @param  mixed    $write Write value
     * @return Instance For fluid interface
     */
    public function setWrite( $write ) {
        $this->fields['write'] = $write;
        return $this;
    }   // setWrite()

    /**
     * Basic setter for field 'graph'
     *
     * @param  mixed    $graph Graph value
     * @return Instance For fluid interface
     */
    public function setGraph( $graph ) {
        $this->fields['graph'] = $graph;
        return $this;
    }   // setGraph()

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
     * Basic getter for field 'model'
     *
     * @return mixed Model value
     */
    public function getModel() {
        return $this->fields['model'];
    }   // getModel()

    /**
     * Basic getter for field 'unit'
     *
     * @return mixed Unit value
     */
    public function getUnit() {
        return $this->fields['unit'];
    }   // getUnit()

    /**
     * Basic getter for field 'type'
     *
     * @return mixed Type value
     */
    public function getType() {
        return $this->fields['type'];
    }   // getType()

    /**
     * Basic getter for field 'childs'
     *
     * @return mixed Childs value
     */
    public function getChilds() {
        return $this->fields['childs'];
    }   // getChilds()

    /**
     * Basic getter for field 'read'
     *
     * @return mixed Read value
     */
    public function getRead() {
        return $this->fields['read'];
    }   // getRead()

    /**
     * Basic getter for field 'write'
     *
     * @return mixed Write value
     */
    public function getWrite() {
        return $this->fields['write'];
    }   // getWrite()

    /**
     * Basic getter for field 'graph'
     *
     * @return mixed Graph value
     */
    public function getGraph() {
        return $this->fields['graph'];
    }   // getGraph()

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
     * Filter for field 'childs'
     *
     * @param  mixed    $childs Filter value
     * @return Instance For fluid interface
     */
    public function filterByChilds( $childs ) {
        $this->filter[] = '`childs` = "'.$this->quote($childs).'"';
        return $this;
    }   // filterByChilds()

    /**
     * Filter for field 'read'
     *
     * @param  mixed    $read Filter value
     * @return Instance For fluid interface
     */
    public function filterByRead( $read ) {
        $this->filter[] = '`read` = "'.$this->quote($read).'"';
        return $this;
    }   // filterByRead()

    /**
     * Filter for field 'write'
     *
     * @param  mixed    $write Filter value
     * @return Instance For fluid interface
     */
    public function filterByWrite( $write ) {
        $this->filter[] = '`write` = "'.$this->quote($write).'"';
        return $this;
    }   // filterByWrite()

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
     * Filter for field 'model'
     *
     * @param  mixed    $model Filter value
     * @return Instance For fluid interface
     */
    public function filterByModel( $model ) {
        $this->filter[] = '`model` = "'.$this->quote($model).'"';
        return $this;
    }   // filterByModel()

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
     * Filter for field 'graph'
     *
     * @param  mixed    $graph Filter value
     * @return Instance For fluid interface
     */
    public function filterByGraph( $graph ) {
        $this->filter[] = '`graph` = "'.$this->quote($graph).'"';
        return $this;
    }   // filterByGraph()

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
    protected $table = 'pvlng_type';

}
