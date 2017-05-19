<?php
/**
 * Accept JSON data from several equipments, like SMA Webboxes, Fronius
 * inverters or SmartGrid
 *
 * @author      Knut Kohl <github@knutkohl.de>
 * @copyright   2012-2014 Knut Kohl
 * @license     MIT License (MIT) http://opensource.org/licenses/MIT
 * @version     1.0.0
 */
namespace Channel;

/**
 *
 */
class JSON extends Channel
{
    /**
     * Path separator in channel definition
     *
     * section->subsection->subsubsection
     */
    const SEPARATOR = '->';

    /**
     *
     */
    public function write($request, $timestamp=null)
    {
        $ok = 0;

        // Find valid child channels
        foreach ($this->getChilds() as $child) {

            // Find only writable channels with filled "channel" attribute
            if (!$child->write OR $child->channel == '') continue;

            // Check all keys in lowercase
            $path = explode(self::SEPARATOR, strtolower($child->channel));

            // Root pointer
            $value = &$request;

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
                if ($code != 200 AND $code != 201 AND $code != 422) throw $e;
            }
        }

        return $ok;
    }
}
