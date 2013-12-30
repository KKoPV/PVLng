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
class History extends \Channel {

    /**
     * Channel type
     * 0 - undefined, concrete channel decides
     * 1 - numeric, concrete channel decides if sensor or meter
     * 2 - sensor, numeric
     * 3 - meter, numeric
     */
    const TYPE = 1;

    /**
     *
     */
    public function read( $request, $attributes=FALSE ) {

        if (isset($request['period'])) {
            if (preg_match('~^([.\d]+)(i|min|minutes?)$~', $request['period'], $args)) {
                if ($this->threshold) $args[1] *= $this->threshold;
                $request['period'] = ($args[1]/60).'h';
            } elseif ($this->threshold AND
                      preg_match('~^[.\d]+~', $request['period'], $args)) {
                $request['period'] = str_replace($args[0], $args[0]*$this->threshold, $request['period']);
            }
        }

        $this->before_read($request);

        $this->child = $this->getChild(1);

        if (!$this->child) return $this->after_read(new \Buffer, $attributes);

        // Inherit properties from child
        $this->meter = $this->child->meter;

        if (!$this->valid_to) {
            $result = $this->DayRange(
                $request,
                $this->start + $this->valid_from * 60*60*24,
                $this->end
            );
        } else {
            $result = $this->overall($request);
        }

        // Smooth data
        $buffer = new \Buffer;
        $data = array();
        $cnt = 3;
        foreach ($result as $id=>$row) {
            $data[] = $row['data'];
            if (count($data) == $cnt) {
                $row['data'] = array_sum($data) / $cnt;
                array_shift($data);
            }
            $buffer->write($row, $id);
        }

        // Skip validity handling of after_read!
        $this->valid_from = $this->valid_to = NULL;

        return $this->after_read($buffer, $attributes);
    }

    // -----------------------------------------------------------------------
    // PROTECTED
    // -----------------------------------------------------------------------

    /**
     *
     */
    protected $child;

    /**
     *
     */
    protected function DayRange( $request, $start, $end ) {

        $result = new \Buffer;

        $_start = $start;
        $_end = $start + ($this->end - $this->start);

        $i = 1;

        while ($_end <= $end) {
            $request['start'] = $_start;
            $request['end']   = $_end;

            // Skip actual requested period day
            if ($_start != $this->start AND $_end != $this->end) {
                $result = $this->combine($result, $this->child->read($request), $request, $i++);
            }

            $_start += $this->end - $this->start;
            $_end   += $this->end - $this->start;
        }

        return $result;
    }

    /**
     *
     */
    protected function overall( $request ) {
        // Start year
        $year = date('Y') - 11;
        $i = 1;

        $result = new \Buffer;

        while ($year <= date('Y')) {

            // Recalc dates to fetch into year
            list($m, $d) = explode('|', date('m|d', $this->start));
            $start = mktime(0, 0, 0, $m, $d, $year);

            list($m, $d) = explode('|', date('m|d', $this->end));
            $end = mktime(0, 0, 0, $m, $d, $year);
            $buffer = $this->DayRange(
                $request,
                $start + $this->valid_from * 60*60*24,
                $end + $this->valid_to * 60*60*24
            );

            $request['start'] = $this->start;
            $request['end']   = $this->end;

            $result = $this->combine($result, $buffer, $request, $i++);
            $year++;
        }

        return $result;
    }

    /**
     *
     */
    protected function combine( \Buffer $buffer, \Buffer $next, $request, $i ) {

        // Check for data to process
        if (!count($next)) return $buffer;

        $row1 = $buffer->rewind()->current();
        $row2 = $next->rewind()->current();

        $result = new \Buffer;

        while (!empty($row1) OR !empty($row2)) {

            // id is in correct format from previous run, build id
            $id = $row2
                ? floor(($row2['timestamp'] - $request['start']) / 86400) . '|' .
                  substr($row2['datetime'], -8)
                : '';

            if (substr($buffer->key(), -8) == substr($id, -8)) {

                // same timestamp, combine
                $row1['data']        = ($row1['data']*($i-1)        + $row2['data'])        / $i;
                $row1['min']         = ($row1['min']*($i-1)         + $row2['min'])         / $i;
                $row1['max']         = ($row1['max']*($i-1)         + $row2['max'])         / $i;
                $row1['consumption'] = ($row1['consumption']*($i-1) + $row2['consumption']) / $i;
                $row1['count']      += $row2['count'];

                // Save combined data
                $result->write($row1, $buffer->key());

                // read both next rows
                $row1 = $buffer->next()->current();
                $row2 = $next->next()->current();

            } elseif ( $buffer->key() AND $buffer->key() < $id OR $id == '') {

                // missing row 2, save row 1 as is
                $result->write($row1, $buffer->key());

                // read only row 1
                $row1 = $buffer->next()->current();

            } else {

                // missing row 1, save row 2 as is
                $result->write($row2, $id);

                // read only row 2
                $row2 = $next->next()->current();

            }
        }
        $buffer->close();
        $next->close();

        $buffer = new \Buffer;

        // Recalc datetime & timestamp from row Ids
        foreach ($result as $id=>$row) {

            list($offset, $hms) = explode('|', $id);

            $row['datetime']  = date('Y-m-d ', $this->start + $offset * 60*60*24) . $hms;
            $row['timestamp'] = strtotime($row['datetime']);
            $buffer->write($row, $id);
        }

        return $buffer;
    }

}
