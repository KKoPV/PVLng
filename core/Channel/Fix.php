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
class Fix extends InternalCalc {

    /**
     *
     */
    protected function before_read( $request ) {

        parent::before_read($request);

        $ts = $this->start;

        // Show pseudo reading at each considation range point or at least each hour
        $delta = $this->TimestampMeterOffset[$this->period[1]];
        $delta = $delta ?: 3600; // 1hr

        while ($ts <= $this->end) {
            $this->saveValue($ts, 1);
            $ts += $delta;
        }
    }
}
