<?php
/**
 *
 *
 * @author      Knut Kohl <github@knutkohl.de>
 * @copyright   2012-2013 Knut Kohl
 * @license     GNU General Public License http://www.gnu.org/licenses/gpl.txt
 * @version     1.1.0
 *
 * 1.1.0
 * - change child channel logic to save child channel GUID in channel attribute
 *
 * 1.0.0
 * - initial creation
 */
namespace Channel;

/**
 *
 */
class Dashboard extends Calculator {

    /**
     *
     */
    public function read( $request ) {

        $channel = $this->getChild(1);

        // Get some properties from child
        $this->meter = $channel->meter;

        /**
         * Read all data, regardsless of valid ranges, gauge will handle values outside
         * valid range via "overshoot" property
         */

        // Save this valid range
        $f = $this->valid_from;
        $t = $this->valid_to;

        $channel->valid_from = $channel->vaild_to = $this->valid_from = $this->valid_to = NULL;

        $result = $this->after_read($channel->read($request));

        // Reset this valid range, required for gauge axis limits
        $this->valid_from = $f;
        $this->valid_to = $t;

        return $result;
    }

}
