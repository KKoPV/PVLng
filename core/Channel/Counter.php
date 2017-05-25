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
class Counter extends Channel
{

    /**
     *
     */
    protected function beforeWrite($request)
    {
        // Used as ticker/marker
        if (!isset($request['data'])) {
            $request['data'] = 1;
        }

        parent::beforeWrite($request);
    }

    /**
     *
     */
    public function read($request)
    {

        $this->beforeRead($request);

        $result = new \Buffer;

        $last = 0;

        foreach (parent::read($request) as $id => $row) {
            // skip 1st row for plain data
            if ($row['timediff'] || $last) {
                if (!$row['timediff']) {
                    // no period calculations
                    // get time difference from row to row
                    $row['timediff'] = $row['timestamp'] - $last;
                }

                $row['data']        *= 3600 / $row['timediff'];
                $row['min']         *= 3600 / $row['timediff'];
                $row['max']         *= 3600 / $row['timediff'];
                $row['consumption'] *= 3600 / $row['timediff'];

                $result->write($row, $id);
            }

            $last = $row['timestamp'];
        }

        // Switch resolution
        $this->resolution = 1 / $this->resolution;

        return $this->afterRead($result);
    }

    // -------------------------------------------------------------------------
    // PROTECTED
    // -------------------------------------------------------------------------

    /**
     *
     */
    protected $counter = 1;
}
