<?php
/**
 * Base access class for "pvlng_config"
 *
 * NEVER EVER EDIT THIS FILE
 *
 * To extend the functionallity, edit "Config.php"
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
abstract class ConfigBase extends \slimMVC\ORM {

    // -----------------------------------------------------------------------
    // PUBLIC
    // -----------------------------------------------------------------------

    /**
     * Forge method for key field(s)
     *
     * @param mixed Field value
     */
    public static function forge( $key ) {
        return new static(array($key));
    } // forge()

    // -----------------------------------------------------------------------
    // Setter methods
    // -----------------------------------------------------------------------

    /**
     * Basic setter for field 'key'
     *
     * @param  mixed    $key Key value
     * @return Instance For fluid interface
     */
    public function setKey( $key ) {
        $this->fields['key'] = $key;
        return $this;
    } // setKey()

    /**
     * Basic setter for field 'value'
     *
     * @param  mixed    $value Value value
     * @return Instance For fluid interface
     */
    public function setValue( $value ) {
        $this->fields['value'] = $value;
        return $this;
    } // setValue()

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
     * Basic setter for field 'type'
     *
     * @param  mixed    $type Type value
     * @return Instance For fluid interface
     */
    public function setType( $type ) {
        $this->fields['type'] = $type;
        return $this;
    } // setType()

    // -----------------------------------------------------------------------
    // Getter methods
    // -----------------------------------------------------------------------

    /**
     * Basic getter for field 'key'
     *
     * @return mixed Key value
     */
    public function getKey() {
        return $this->fields['key'];
    } // getKey()

    /**
     * Basic getter for field 'value'
     *
     * @return mixed Value value
     */
    public function getValue() {
        return $this->fields['value'];
    } // getValue()

    /**
     * Basic getter for field 'comment'
     *
     * @return mixed Comment value
     */
    public function getComment() {
        return $this->fields['comment'];
    } // getComment()

    /**
     * Basic getter for field 'type'
     *
     * @return mixed Type value
     */
    public function getType() {
        return $this->fields['type'];
    } // getType()

    // -----------------------------------------------------------------------
    // Filter methods
    // -----------------------------------------------------------------------

    /**
     * Filter for field key
     *
     * @param mixed Field value
     */
    public function filterByKey( $key ) {
        return $this->filter('key', $key);
    } // filterByKey()

    /**
     * Filter for field value
     *
     * @param mixed Field value
     */
    public function filterByValue( $value ) {
        return $this->filter('value', $value);
    } // filterByValue()

    /**
     * Filter for field comment
     *
     * @param mixed Field value
     */
    public function filterByComment( $comment ) {
        return $this->filter('comment', $comment);
    } // filterByComment()

    /**
     * Filter for field type
     *
     * @param mixed Field value
     */
    public function filterByType( $type ) {
        return $this->filter('type', $type);
    } // filterByType()

    // -----------------------------------------------------------------------
    // PROTECTED
    // -----------------------------------------------------------------------

    /**
     * Table name
     *
     * @var string $table Table name
     */
    protected $table = 'pvlng_config';

}
