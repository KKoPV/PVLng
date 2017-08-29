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
class Random extends InternalCalc
{
    // -----------------------------------------------------------------------
    // PROTECTED
    // -----------------------------------------------------------------------

    /**
     *
     */
    protected function beforeRead(&$request)
    {

        parent::beforeRead($request);

        // Force recreation of data
        $this->dataExists(0);

        $timestamp = $this->start;
        // make sure, only until now :-)
        $this_end = min($this->end, time());
        // max. change +- 5
        $threshold = $this->threshold ?: 5;
        // buffer once
        $randMax = mt_getrandmax();

        if ($this->meter) {
            $timestamp -= self::$secondsPerPeriod[$this->period[1]];
            $value = is_null($this->valid_from) ? 0 : $this->valid_from;
            $minRand = 0;
        } else {
            // Init value in middle of valid range
            $value = ((is_null($this->valid_from) ? 0 : $this->valid_from) +
                      (is_null($this->valid_to) ? 100 : $this->valid_to)) / 2;
            $minRand = -1; // to get negative steps
        }
        $values = array($timestamp => $value);

        while ($timestamp <= $this_end) {
            // calc next value;
            $timestamp += 60;
            $value     += mt_rand() / $randMax * $threshold * mt_rand($minRand, 1);
            $this->saveValue($timestamp, $value);
        }

        $this->dataCreated();
    }
}
