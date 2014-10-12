<?php
/**
 * Abstract base class for table 'pvlng_settings_keys'
 *
 * *** NEVER EVER EDIT THIS FILE! ***
 *
 * To extend the functionallity, edit "SettingsKeys.php"
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
abstract class SettingsKeysBase extends \slimMVC\ORM {

    // -----------------------------------------------------------------------
    // PUBLIC
    // -----------------------------------------------------------------------

    // -----------------------------------------------------------------------
    // Setter methods
    // -----------------------------------------------------------------------

    /**
     * 'pvlng_settings_keys' is a view, no setters
     */

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

    // -----------------------------------------------------------------------
    // PROTECTED
    // -----------------------------------------------------------------------

    /**
     * Table name
     *
     * @var string $table Table name
     */
    protected $table = 'pvlng_settings_keys';

}
