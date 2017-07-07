<?php
/**
 * PVLng - PhotoVoltaic Logger new generation
 *
 * @link       https://github.com/KKoPV/PVLng
 * @link       https://pvlng.com/
 * @author     Knut Kohl <github@knutkohl.de>
 * @copyright  2012 Knut Kohl
 * @license    MIT License (MIT) http://opensource.org/licenses/MIT
 */
namespace Core;

/**
 *
 */
class Localizer
{

    /**
     *
     */
    public static function setThousandSeparator($value)
    {
        static::$ThousandSeparator = $value;
    }

    /**
     *
     */
    public static function setDecimalPoint($value)
    {
        static::$DecimalPoint = $value;
    }

    /**
     *
     */
    public static function toLocale($value)
    {
        if (is_numeric($value)) {
            return number_format(
                $value,
                // Find right-most position of comma in value to get count of decimals
                strlen(substr(strrchr($value, '.'), 1)),
                static::$DecimalPoint,
                static::$ThousandSeparator
            );
        } else {
            return $value;
        }
    }

    /**
     *
     */
    public static function fromLocale($value)
    {
        if (is_array($value)) {
            // Call recursive for each array member
            foreach ($value as $k => $v) {
                $value[$k] = static::fromLocale($v);
            }
        } elseif (preg_match('~^[ 0-9.,-]+$~', $value)) {
            // Remove thousand separators
            $value = str_replace(static::$ThousandSeparator, '', $value);
            // Replace decimal point
            $value = str_replace(static::$DecimalPoint, '.', $value);
        }
        return $value;
    }

    // -----------------------------------------------------------------------
    // PROTECTED
    // -----------------------------------------------------------------------

    /**
     *
     */
    protected static $ThousandSeparator = ',';

    /**
     *
     */
    protected static $DecimalPoint = '.';
}
