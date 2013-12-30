<?php
/**
 *
 *
 * @author      Knut Kohl <github@knutkohl.de>
 * @copyright   2012-2013 Knut Kohl
 * @license     GNU General Public License http://www.gnu.org/licenses/gpl.txt
 * @version     1.0.0
 *
 * 1.0.0
 * - initial creation
 */
namespace Channel;

/**
 *
 */
class Baseline extends Sensor {

    /**
     *
     */
    public function read( $request, $attributes=FALSE ) {

        $baseline = $ts_min = $ts_max = NAN;

        foreach ($this->getChild(1)->read($request) as $row) {
            $baseline = min($baseline, $row['data']);
            $ts_min = min($ts_min, $row['timestamp']);
            $ts_max = max($row['timestamp'], $ts_max);
        }

        $result = new \Buffer;

        if ($baseline != NAN) {

            $result->write(array(
                'datetime'    => date('Y-m-d H:i:s', $ts_min),
                'timestamp'   => $ts_min,
                'data'        => $baseline,
                'min'         => $baseline,
                'max'         => $baseline,
                'count'       => 1,
                'timediff'    => 0,
                'consumption' => 0
            ), $this->start);

            $result->write(array(
                'datetime'    => date('Y-m-d H:i:s', $ts_max),
                'timestamp'   => $ts_max,
                'data'        => $baseline,
                'min'         => $baseline,
                'max'         => $baseline,
                'count'       => 1,
                'timediff'    => 0,
                'consumption' => 0
            ), $this->end);

        }

        return $this->after_read($result, $attributes);
    }

}
