<?php
/**
 *
 *
 * @author      Knut Kohl <github@knutkohl.de>
 * @copyright   2012-2013 Knut Kohl
 * @license     GNU General Public License http://www.gnu.org/licenses/gpl.txt
 * @version     1.0.0
 */
namespace Channel;

/**
 *
 */
class Fix extends Sensor {

    /**
     *
     */
    public function read( $request, $attributes=FALSE ) {

        $this->before_read($request);

        // make sure, only until now :-)
        $this->end = min($this->end, time());

        // Buffer $this->db->TimeStep, at least 60 seconds
        $TimeStep = max(60, $this->db->TimeStep);

        $result = new \Buffer;

        // Align start to full minutes
        $ts = floor($this->start / $TimeStep) * $TimeStep;

        $result->write(array(
            'datetime'    => date('Y-m-d H:i:s', $ts),
            'timestamp'   => $ts,
            'data'        => 1,
            'min'         => 1,
            'max'         => 1,
            'count'       => 1,
            'timediff'    => 0,
            'consumption' => 0
        ), $this->start);

        // Align end to full minutes
        $ts = floor($this->end / $TimeStep) * $TimeStep;

        $result->write(array(
            'datetime'    => date('Y-m-d H:i:s', $ts),
            'timestamp'   => $ts,
            'data'        => 1,
            'min'         => 1,
            'max'         => 1,
            'count'       => 1,
            'timediff'    => $this->end-$this->start,
            'consumption' => 0
        ), $this->end);

        return $this->after_read($result, $attributes);
    }

}
