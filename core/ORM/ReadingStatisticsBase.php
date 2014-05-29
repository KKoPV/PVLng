<?php
/**
 * Base access class for "pvlng_reading_statistics"
 *
 * NEVER EVER EDIT THIS FILE
 *
 * To extend the functionallity, edit "ReadingStatistics.php"
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
abstract class ReadingStatisticsBase extends \slimMVC\ORM {

    // -----------------------------------------------------------------------
    // PUBLIC
    // -----------------------------------------------------------------------

    // -----------------------------------------------------------------------
    // Setter methods
    // -----------------------------------------------------------------------

    /**
     * 'pvlng_reading_statistics' is a view, no setters
     */

    // -----------------------------------------------------------------------
    // Getter methods
    // -----------------------------------------------------------------------

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
     * Basic getter for field 'unit'
     *
     * @return mixed Unit value
     */
    public function getUnit() {
        return $this->fields['unit'];
    } // getUnit()

    /**
     * Basic getter for field 'type'
     *
     * @return mixed Type value
     */
    public function getType() {
        return $this->fields['type'];
    } // getType()

    /**
     * Basic getter for field 'icon'
     *
     * @return mixed Icon value
     */
    public function getIcon() {
        return $this->fields['icon'];
    } // getIcon()

    /**
     * Basic getter for field 'datetime'
     *
     * @return mixed Datetime value
     */
    public function getDatetime() {
        return $this->fields['datetime'];
    } // getDatetime()

    /**
     * Basic getter for field 'readings'
     *
     * @return mixed Readings value
     */
    public function getReadings() {
        return $this->fields['readings'];
    } // getReadings()

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
     * Filter for field unit
     *
     * @param mixed Field value
     */
    public function filterByUnit( $unit ) {
        return $this->filter('unit', $unit);
    } // filterByUnit()

    /**
     * Filter for field type
     *
     * @param mixed Field value
     */
    public function filterByType( $type ) {
        return $this->filter('type', $type);
    } // filterByType()

    /**
     * Filter for field icon
     *
     * @param mixed Field value
     */
    public function filterByIcon( $icon ) {
        return $this->filter('icon', $icon);
    } // filterByIcon()

    /**
     * Filter for field datetime
     *
     * @param mixed Field value
     */
    public function filterByDatetime( $datetime ) {
        return $this->filter('datetime', $datetime);
    } // filterByDatetime()

    /**
     * Filter for field readings
     *
     * @param mixed Field value
     */
    public function filterByReadings( $readings ) {
        return $this->filter('readings', $readings);
    } // filterByReadings()

    // -----------------------------------------------------------------------
    // PROTECTED
    // -----------------------------------------------------------------------

    /**
     * Table name
     *
     * @var string $table Table name
     */
    protected $table = 'pvlng_reading_statistics';

}
