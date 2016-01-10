<?php
/**
 * Copyright (c) 2015 PV-Log.com, Top50-Solar
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 */
namespace PVLog\Classes\Json;

/**
 * Helper class
 *
 * @author   Knut Kohl <kohl@top50-solar.de>
 * @license  http://opensource.org/licenses/MIT MIT License (MIT)
 * @version  PVLog JSON 1.1
 * @since    2015-03-14
 * @since    v1.0.0
 */
abstract class Helper
{

    // -----------------------------------------------------------------------
    // PUBLIC
    // -----------------------------------------------------------------------

    /**
     * Set date format for "minutes" file output
     *
     * @return void
     */
    public static function setDateFormatMinutes()
    {
        self::$settings[0] = 'Y-m-d H:i:s';
    }

    /**
     * Set date format for "day" file output
     *
     * @return void
     */
    public static function setDateFormatDay()
    {
        self::$settings[0] = 'Y-m-d';
    }

    /**
     * Set date format for "month" file output
     *
     * @return void
     */
    public static function setDateFormatMonth()
    {
        self::$settings[0] = 'Y-m';
    }

    /**
     * Set date format for output
     *
     * @return string
     */
    public static function getDateFormat()
    {
        return self::$settings[0];
    }

    /**
     * Set timestamp offset to consider for output
     *
     * @param  float  $value Offset in hours
     * @return void
     */
    public static function setTimestampOffset($value)
    {
        self::$settings[1] = +$value;
    }

    /**
     * Get timestamp offset to consider for output
     *
     * @return float
     */
    public static function getTimestampOffset()
    {
        return self::$settings[1];
    }

    /**
     * Convert an timestamp into local time,
     *
     * @example
     * - Your timestamps are in UTC
     * - Your local timezone is MET/MEST
     * - Set offset to 1 hour (2 hours during daylight saving time)
     *   `\PVLog\Classes\JSON\Helper::setTimestampOffset(1);`
     *
     * @param  string|integer $timestamp Date & time or timestamp
     * @return string Y-m-d H:i:s
     */
    public static function localTimestamp($timestamp)
    {
        // Transfor to timestamp if required
        return date(
            'Y-m-d H:i:s',
            self::asTimestamp($timestamp) + self::$settings[1] * 3600
        );
    }

    /**
     * Converts degree Fahrenheit to degree Celsius
     *
     * Source: http://de.wikipedia.org/wiki/Grad_Fahrenheit
     *
     * @param  float|array $value Temperature in degree fahrenheit
     * @return float|array Temperature in degree celsius
     */
    public static function convertFahrenheitToCelsius($value)
    {
        if (is_array($value)) {
            foreach ($value as $key=>$v) {
                $value[$key] = self::convertFahrenheitToCelsius($v);
            }
        } else {
            $value = ($value - 32) * 5 / 9;
        }

        return $value;
    }

    /**
     * Convert datetime to timestamp if required
     *
     * @internal
     * @throws \InvalidArgumentException for invalid datetime strings
     * @param  string|integer $datetime
     * @return integer
     */
    public static function asTimestamp($timestamp)
    {
        // Return numerics as is
        if (is_numeric($timestamp)) {
            return $timestamp;
        }

        // Convert datetime & return on success
        if (($ts = strtotime($timestamp)) !== false) {
            return $ts;
        }

        throw new \InvalidArgumentException(
            'No valid datetime or timestamp format: '.$timestamp
        );
    }

    // -----------------------------------------------------------------------
    // PROTECTED
    // -----------------------------------------------------------------------

    /**
     * Default settings
     *
     * @internal
     */
    protected static $settings = array(
        'Y-m-d H:i:s', // Date format
        0,             // Timestamp offset
    );

}
