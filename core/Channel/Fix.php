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
class Fix extends InternalCalc
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

        if ($this->dataExists()) {
            return;
        }

        // Read out all data
        $request['period'] = '1i';

        $ts = $this->start;

        if ($this->isChild) {
            $delta = 60;
        } else {
            // Show pseudo reading at each consolidation range point
            $delta = self::$secondsPerPeriod[$this->period[1]];
        }

        while ($ts <= $this->end) {
            $this->saveValue($ts, 1);
            $ts += $delta;
        }

        $this->dataCreated();
    }
}
