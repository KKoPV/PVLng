<?php
/**
 * Accept JSON data from Solar Edge API
 *
 * @author      Knut Kohl <github@knutkohl.de>
 * @copyright   2014 Knut Kohl
 * @license     MIT License (MIT) http://opensource.org/licenses/MIT
 * @version     1.0.0
 */
namespace Channel\SE;

/**
 *
 */
use \Channel\MultiChannel;

/**
 *
 */
class Optimizer extends MultiChannel
{
    /**
     * Recieve CSV data
     */
    public function write($request, $timestamp = null)
    {
        $csv = array();
        // Split CSV data nd transform to array of arrays
        foreach (explode("\n", trim($request)) as $line) {
            $csv[] = str_getcsv($line);
        }

        // Check for at least ONE data line
        if (count($csv) < 2) {
            return;
        }

        // Channel keys from 1st row
        $keys = array_shift($csv);
        // Remove "Time" from 1st position
        array_shift($keys);

        $ok = 0;

        foreach ($csv as $row) {
            // Extract timestamp from 1st position
            $timestamp = strtotime(array_shift($row));

            $data = array();
            foreach ($keys as $id => $key) {
                if ($row[$id] != '') {
                    $data[$key] = $row[$id];
                }
            }
            $ok += parent::write(array('data' => $data), $timestamp);
        }

        return $ok;
    }
}
