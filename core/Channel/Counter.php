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
class Counter extends Sensor {

    /**
     *
     */
    public function read( $request, $attributes=FALSE ) {

        $this->before_read($request);

        $result = new \Buffer;

        $last = 0;

        foreach (parent::read($request) as $id=>$row) {

            // skip 1st row for plain data
            if ($row['timediff'] OR $last) {

                if (!$row['timediff']) {
                    // no period calculations
                    // get time difference from row to row
                    $row['timediff'] = $row['timestamp'] - $last;
                }

                // remove resolution, will be applied in after_read
                $factor = 3600 / $row['timediff'] / $this->resolution /
                          $this->resolution / $this->resolution;

                $row['data']        *= $factor;
                $row['min']         *= $factor;
                $row['max']         *= $factor;
                $row['consumption'] *= $factor;

                $result->write($row, $id);
            }

            $last = $row['timestamp'];
        }

        return $this->after_read($result, $attributes);
    }

    // -------------------------------------------------------------------------
    // PROTECTED
    // -------------------------------------------------------------------------

    /**
     *
     */
    protected $counter = 1;

}
