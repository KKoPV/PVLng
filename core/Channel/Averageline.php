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
class Averageline extends InternalCalc {

    /**
     *
     */
    public function before_read( $request ) {

        parent::before_read($request);

        if ($this->dataExists()) return;

        $ts = $cnt = $sum = 0;

        /**
         * Calulated with the Hölder mean fomulas
         * http://en.wikipedia.org/wiki/H%C3%B6lder_mean
         */
        $p = $this->extra;

        foreach ($this->getChild(1)->read($request) as $row) {
            if (!$ts) $ts = $row['timestamp'];
            $sum += pow($row['data'], $p);
            $cnt++;
        }

        if ($cnt) {
            $avg = pow($sum / $cnt, 1/$p);

            foreach ($this->getChild(1)->read($request) as $row) {
                $this->saveValue($row['timestamp'], $avg);
            }
        }

        $this->dataCreated();
    }
}
