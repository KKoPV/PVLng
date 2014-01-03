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
class Random extends InternalCalc {

    /**
     *
     */
    protected function before_read( $request ) {

        parent::before_read($request);

        // make sure, only until now :-)
        $this->end = min($this->end, time());

        $timestamp = $this->start;
        // max. change +- 5
        $threshold = $this->threshold ?: 5;
        // buffer once
        $randMax = mt_getrandmax();

        $TimeStep = max(60, $this->db->TimeStep);

        if ($this->meter) {
            $timestamp -= $this->TimestampMeterOffset[$this->period[1]];
            $value = is_null($this->valid_from) ? 0 : $this->valid_from;
            $minRand = 0;
        } else {
            // Init value in middle of valid range
            $value = ((is_null($this->valid_from) ? 0 : $this->valid_from) +
                      (is_null($this->valid_to) ? 100 : $this->valid_to)) / 2;
            $minRand = -1; // to get negative steps
        }
        $values = array($timestamp => $value);

        while ($timestamp <= $this->end) {
            // calc next value;
            $timestamp += $TimeStep;
            $value += mt_rand() / $randMax * $threshold * mt_rand($minRand, 1);
            $values[$timestamp] = $value;
        }

        $this->saveValues($values);
    }

}
