<?php
/**
 * Abstract base class for table 'pvlng_config'
 *
 * *** NEVER EVER EDIT THIS FILE! ***
 *
 * To extend the functionallity, edit "Config.php"
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
abstract class ConfigBase extends \slimMVC\ORM {

    // -----------------------------------------------------------------------
    // PUBLIC
    // -----------------------------------------------------------------------

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
    }   // setKey()

    /**
     * Basic setter for field 'value'
     *
     * @param  mixed    $value Value value
     * @return Instance For fluid interface
     */
    public function setValue( $value ) {
        $this->fields['value'] = $value;
        return $this;
    }   // setValue()

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
    }   // getKey()

    /**
     * Basic getter for field 'value'
     *
     * @return mixed Value value
     */
    public function getValue() {
        return $this->fields['value'];
    }   // getValue()

    /**
     * Basic getter for field 'comment'
     *
     * @return mixed Comment value
     */
    public function getComment() {
        return $this->fields['comment'];
    }   // getComment()

    // -----------------------------------------------------------------------
    // Filter methods
    // -----------------------------------------------------------------------

    /**
     * Filter for field 'key'
     *
     * @param  mixed    $key Filter value
     * @return Instance For fluid interface
     */
    public function filterByKey( $key ) {
        $this->filter[] = '`key` = "'.$this->quote($key).'"';
        return $this;
    }   // filterByKey()

    /**
     * Filter for field 'value'
     *
     * @param  mixed    $value Filter value
     * @return Instance For fluid interface
     */
    public function filterByValue( $value ) {
        $this->filter[] = '`value` = "'.$this->quote($value).'"';
        return $this;
    }   // filterByValue()

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

    // -----------------------------------------------------------------------
    // PROTECTED
    // -----------------------------------------------------------------------

    /**
     * Table name
     *
     * @var string $table Table name
     */
    protected $table = 'pvlng_config';

    /**
     * SQL for creation
     *
     * @var string $createSQL
     */
    protected $createSQL = '
        CREATE TABLE `pvlng_config` (
          `key` varchar(50) NOT NULL,
          `value` varchar(1000) NOT NULL,
          `comment` varchar(255) NOT NULL,
          PRIMARY KEY (`key`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 PACK_KEYS=1 COMMENT=\'Application settings\'
    ';

}
