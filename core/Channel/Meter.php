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
class Meter extends \Channel {

    /**
     * Channel type
     * 0 - undefined, concrete channel decides
     * 1 - numeric, concrete channel decides if sensor or meter
     * 2 - sensor, numeric
     * 3 - meter, numeric
     */
    const TYPE = 2;

}
