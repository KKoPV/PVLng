<?php
/**
 * Kaco Pawador grouping channel
 *
 * Accepts a row of data like
 * ["2016-03-31","13:08:41","*010","4","358.2","8.90","3187","233.9","13.13","3048","46","8042","?","4000xi"]
 *
 * @author      Knut Kohl <github@knutkohl.de>
 * @copyright   2016 Knut Kohl
 * @license     MIT License (MIT) http://opensource.org/licenses/MIT
 * @version     1.0.0
 */
namespace Channel\Kaco;

/**
 *
 */
class RS485 extends \Channel
{

    /**
     *
     */
    public function write($request, $timestamp=null)
    {

        $data = explode(' ', $request['data']);

        // Something went wrong...
        if (count($data) < 2) return 0;

        // Timestamp is in $data[0] + $data[1]
        $datetime  = implode(' ', array_splice($data, 0, 2));

        // Transform to integer
        if (!($timestamp = strtotime($datetime))) {
            throw new \InvalidArgumentException('Invalid timestamp: '.$datetime);
        }

        $count = 0;

        // find valid child channels
        foreach ($this->getChilds() as $child) {
            // Writable channel with parameter position?
            if (!$child->write || ($child->channel == '') || !is_numeric($child->channel)) continue;

            // Array index of parameter is 1 lower
            $param = $child->channel - 1;

            // Channel value found in data?
            if (!($value = $this->array_value($data, $param))) continue;

            // Interpret empty numeric value as invalid
            if ($child->numeric && ($value == '')) continue;

            try { // Simulate $request['data'],
                $count += $child->write(array('data' => $value), $timestamp);
            } catch (\Exception $e) {
                $code = $e->getCode();
                if (($code != 200) && ($code != 201) && ($code != 422)) throw $e;
            }
        }

        return $count;
    }

}
