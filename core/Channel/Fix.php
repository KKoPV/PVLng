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
        $this->end = floor(min($this->end, time()) / 60) * 60;

        $result = new \Buffer;

        $result->write(array(
            'datetime'    => date('Y-m-d H:i', $this->start),
            'timestamp'   => $this->start,
            'data'        => 1,
            'min'         => 1,
            'max'         => 1,
            'count'       => 1,
            'timediff'    => 0,
            'consumption' => 0
        ), $this->start);

        $result->write(array(
            'datetime'    => date('Y-m-d H:i', $this->end),
            'timestamp'   => $this->end,
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
