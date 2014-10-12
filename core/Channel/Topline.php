<?php
/**
 *
 *
 * @author      Knut Kohl <github@knutkohl.de>
 * @copyright   2012-2013 Knut Kohl
 * @license     GNU General Public License http://www.gnu.org/licenses/gpl.txt
 * @version     1.0.0
 *
 * 1.0.0
 * - initial creation
 */
namespace Channel;

/**
 *
 */
class Topline extends InternalCalc {

    /**
     *
     */
    public function before_read( $request ) {

        parent::before_read($request);

        if ($this->dataExists()) return;

        $max = -PHP_INT_MAX;
        $ts_min = FALSE;

        foreach ($this->getChild(1)->read($request) as $row) {
            if ($ts_min === FALSE) $ts_min = $row['timestamp'];
            $max = max($max, $row['data']);
        }

        if ($ts_min !== FALSE) {
            $this->saveValues(array(
                $ts_min           => $max,
                $row['timestamp'] => $max
            ));
        }

        $this->dataCreated();
    }
}
