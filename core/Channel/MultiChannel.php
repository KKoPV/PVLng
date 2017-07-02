<?php
/**
 * Accept data from several equipments
 * eg. SMA Webboxes, Fronius inverters, SmartGrid, weather stations
 *
 * @author    Knut Kohl <github@knutkohl.de>
 * @copyright 2012-2014 Knut Kohl
 * @license   MIT License (MIT) http://opensource.org/licenses/MIT
 */
namespace Channel;

/**
 *
 */
class MultiChannel extends Channel
{
    /**
     * Path separator in channel definition
     *
     * section->subsection->subsubsection
     */
    const SEPARATOR = '->';

    /**
     * Accepts data array in $request['data']
     */
    public function write($request, $timestamp = null)
    {
        if (empty($request['data'])) {
            return 0;
        }

        $ok = 0;

        // Find valid child channels
        foreach ($this->getChilds() as $child) {
            // Find only writable channels with filled "channel" attribute
            if (!$child->write || $child->channel == '') {
                continue;
            }

            // Check all keys in lowercase
            $path = explode(self::SEPARATOR, strtolower($child->channel));

            // Root pointer
            $value = &$request['data'];

            // To handle [0] array keys use all as strings and array_key_exists
            while (($key = array_shift($path)) != '') {
                // Check all keys in lowercase
                $value = array_change_key_case($value);
                if (array_key_exists($key, $value)) {
                    // Key found, move pointer to next level
                    $value = &$value[$key];
                } else {
                    // Requested key not found in delivered data, skip child
                    continue 2;
                }
            }
            try {                    // Simulate $request['data']
                $ok += $child->write(array('data' => $value), $timestamp);
            } catch (\Exception $e) {
                $code = $e->getCode();
                if ($code != 200 && $code != 201 && $code != 422) {
                    throw $e;
                }
            }
        }

        return $ok;
    }
}
