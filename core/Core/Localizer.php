<?php
/**
 *
 * @author     Knut Kohl <github@knutkohl.de>
 * @copyright  2012-2014 Knut Kohl
 * @license    MIT License (MIT) http://opensource.org/licenses/MIT
 * @version    1.0.0
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
        self::$ThousandSeparator = $value;
    }

    /**
     *
     */
    public static function setDecimalPoint($value)
    {
        self::$DecimalPoint = $value;
    }

    /**
     *
     */
    public static function toLocale($value)
    {
        return is_numeric($value)
             ? number_format(
                   $value,
                   // Find right-most position of comma in value to get count of decimals
                   strlen(substr(strrchr($value, '.'), 1)),
                   self::$DecimalPoint,
                   self::$ThousandSeparator
               )
             : $value;
    }

    /**
     *
     */
    public static function fromLocale($value)
    {
        if (is_array($value)) {
            // Call recursive for each array member
            foreach ($value as $k => $v) {
                $value[$k] = self::fromLocale($v);
            }
        } elseif (preg_match('~^[ 0-9.,-]+$~', $value)) {
            // Remove thousand separators
            $value = str_replace(self::$ThousandSeparator, '', $value);
            // Replace decimal point
            $value = str_replace(self::$DecimalPoint, '.', $value);
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
