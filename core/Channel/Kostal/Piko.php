<?php
/**
 *
 *
 * @author      Knut Kohl <github@knutkohl.de>
 * @copyright   2012-2014 Knut Kohl
 * @license     MIT License (MIT) http://opensource.org/licenses/MIT
 * @version     1.0.0
 */
namespace Channel\Kostal;

/**
 *
 */
use Channel\Channel;

/**
 *
 */
class Piko extends Channel
{
    /**
     *
     */
    public function write($request, $timestamp = null)
    {
        // DON'T call beforeWrite() here, will be called inside each
        // child->write() for each real reding value

        // Split delivered file into lines
        $data = explode("\n", $request);

        if (count($data) < 2) {
            return 0;
        }

        // Get channel names from 1st row
        $names = array_shift($data);
        // Split into array ...
        $names = explode("\t", $names);
        // ... and trim values ...
        $names = array_map('trim', $names);
        // ... and flip to set the names as keys
        $names = array_flip($names);

        $data = explode("\t", $data[0]);
        $data = array_map('trim', $data);

        $ok = 0;

        // find valid child channels
        foreach ($this->getChilds() as $child) {
            if (!$child->write || $child->channel == '') {
                continue;
            }

            // Channel name fornd in data?
            if (!isset($names[$child->channel])) {
                continue;
            }
            $id = $names[$child->channel];

            // Channel value found in data?
            if (!isset($data[$id])) {
                continue;
            }
            $value = $data[$id];

            // Interpret empty numeric value as invalid
            if ($child->numeric && $value == '') {
                continue;
            }

            try { // Simulate $request['data'], timestamp is in row[0]
                $ok += $child->write(array('data' => $value), $data[0]);
            } catch (Exception $e) {
                $code = $e->getCode();
                if ($code != 200 && $code != 201 && $code != 422) {
                    throw $e;
                }
            }
        }

        return $ok;
    }
}
