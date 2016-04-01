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
class Switcher extends Channel {

    /**
     *
     */
    protected function check_before_write(&$request) {

        parent::check_before_write($request);

        if ($this->numeric  && ((float)  $this->lastReading == (float)  $this->value) ||
            !$this->numeric && ((string) $this->lastReading == (string) $this->value)) {
            // Skip not changed value since last write
            throw new \Exception(null, 200);
        }
    }
}
