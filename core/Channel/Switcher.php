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
class Switcher extends \Channel {

    /**
     *
     */
    public function write( $request, $timestamp=NULL ) {

        $this->before_write($request);

        // Get last value and ...
        $last = $this->getLastReading();

        // ... save only on changes
        if ($this->numeric  AND (float)  $last != (float)  $this->value OR
            !$this->numeric AND (string) $last != (string) $this->value) {
            return parent::write($request, $timestamp);
        }
        return 0;
    }
}
