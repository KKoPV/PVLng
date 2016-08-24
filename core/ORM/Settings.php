<?php
/**
 * Real access class for table 'pvlng_settings'
 *
 * To extend the functionallity, edit here
 *
 * @author     Knut Kohl <github@knutkohl.de>
 * @copyright  2014 Knut Kohl
 * @license    MIT License (MIT) http://opensource.org/licenses/MIT
 * @version    1.0.0
 *
 * 1.0.0
 * - Initial creation
 */
namespace ORM;

/**
 *
 */
class Settings extends SettingsBase
{

    /**
     *
     */
    public function checkValueType($scope, $name, $key, $value)
    {
        $this->reset()
             ->filterByScopeNameKey($scope, $name, $key)
             ->findOne();

        switch ($this->getType()) {
            case 'num':  return is_numeric($value);
            case 'bool': return (is_numeric($value) AND ($value == 0 OR $value == 1));
            default:     return true;
        }
    }

    /**
     *
     */
    public static function getScopeValue($scope, $name, $key, $default=null)
    {
        $self = new self;
        $self->filterByScopeNameKey($scope, $name, $key)->findOne();
        return $self->getKey() ? $self->getValue() : $default;
    }

    /**
     *
     */
    public static function getCoreValue($name, $key, $default=null)
    {
        return self::getScopeValue('core', $name, $key, $default);
    }

    /**
     *
     */
    public static function setCoreValue($name, $key, $value)
    {
        return self::setScopeValue('core', $name, $key, $value);
    }

    /**
     *
     */
    public static function getControllerValue($name, $key, $default=null)
    {
        return self::getScopeValue('controller', $name, $key, $default);
    }

    /**
     *
     */
    public static function getModelValue($name, $key, $default=null)
    {
        return self::getScopeValue('model', $name, $key, $default);
    }

    /**
     *
     */
    public static function getSunrise($day)
    {
        return date_sunrise(
            $day,
            SUNFUNCS_RET_TIMESTAMP,
            +self::getScopeValue('core', '', 'Latitude'),
            +self::getScopeValue('core', '', 'Longitude'),
            90 + 5/6,
            date('Z')/3600
        );
    }

    /**
     *
     */
    public static function getSunset($day)
    {
        return date_sunset(
            $day,
            SUNFUNCS_RET_TIMESTAMP,
            +self::getScopeValue('core', '', 'Latitude'),
            +self::getScopeValue('core', '', 'Longitude'),
            90 + 5/6,
            date('Z')/3600
        );
    }

    // -----------------------------------------------------------------------
    // PROTECTED
    // -----------------------------------------------------------------------

    /**
     *
     */
    protected static function setScopeValue($scope, $name, $key, $value)
    {
        $self = new self;
        $self->filterByScopeNameKey($scope, $name, $key)->findOne();
        if ($self->getKey()) {
            return $self->setValue($value)->update();
        }
    }

}
