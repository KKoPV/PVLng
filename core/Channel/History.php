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
class History extends InternalCalc {

    /**
     *
     */
    public function read( $request, $attributes=FALSE ) {

        $this->before_read($request);

        $this->child = $this->getChild(1);

        if (!$this->child) return $this->after_read(new \Buffer, $attributes);

        // Inherit properties from child
        $this->meter = $this->child->meter;

        // Fetch all data, compress later
        unset($request['period']);

        if ($this->valid_to == 0) {
            // Last x days, move request start backwards
            $request['start'] = $this->start + $this->valid_from * 60*60*24;
            // Save data into temp. table
            $this->saveValues($this->child->read($request));
        } else {
            // Find earliest data
            $q = new \DBQuery('pvlng_reading_num');
            $q->get($q->FROM_UNIXTIME($q->MIN('timestamp'), '%Y'));

            for ($i=$this->db->queryOne($q)-date('Y'); $i<=0; $i++) {
                $request['start'] = strtotime(date('Y-m-d ', $this->start + $this->valid_from * 60*60*24).$i.'years');
                $request['end']   = strtotime(date('Y-m-d ', $this->end + $this->valid_to * 60*60*24).$i.'years');
                // Save data into temp. table
                $this->saveValues($this->child->read($request));
            }
        }

        if ($this->period[1] == self::NO) {
            // Smooth result at least 5 times time step
            $this->period = array(5 * $this->db->TimeStep/60, self::MINUTE);
        } elseif ($this->threshold AND $this->period[1] == self::MINUTE) {
            // Smooth result by cut period by "threshold", only for minutes
            $this->period[0] *= $this->threshold;
        }

        $q = new \DBQuery('pvlng_reading_num_tmp');
        $q->get($q->FROM_UNIXTIME('timestamp', '%H'), 'hour')
          ->get($q->FROM_UNIXTIME('timestamp', '%i'), 'minute');

        if ($this->meter) {
            $q->get($q->MAX('data'), 'data');
        } elseif ($this->counter) {
            $q->get($q->SUM('data'), 'data');
        } else {

            switch (\slimMVC\Config::getInstance()->get('Model.History.Average')) {
                default:
                    // Linear average
                    $q->get($q->AVG('data'), 'data');
                    break;
                case 1:
                    // harm. avg.: count(val) / sum(1/val) as hmean
                    $q->get($q->COUNT('data').'/'.$q->SUM('1/`data`'), 'data');
                    break;
                case 2:
                    // geom. avg.: exp(avg(ln(val)))
                    $q->get($q->EXP($q->AVG($q->LN('data'))), 'data');
                    break;
            }
        }

        $q->get($q->MIN('data'), 'min')
          ->get($q->MAX('data'), 'max')
          ->get($q->COUNT(0), 'count')
          ->get($this->periodGrouping(), 'g')
          ->whereEQ('id', $this->entity)
          ->groupBy('g');
        $inner = $q->SQL();
        $q->select("\n(\n".$inner."\n) t")
          ->groupBy('hour')
          ->groupBy('minute');

#echo $q;

        $result = new \Buffer;

        $day   = date('d', ($this->start+$this->end)/2);
        $month = date('m', ($this->start+$this->end)/2);
        $year  = date('Y', ($this->start+$this->end)/2);

        if ($res = $this->db->query($q)) {
            while ($row = $res->fetch_object()) {
                $ts = mktime($row->hour, $row->minute, 0, $month, $day, $year);
                $result->write(array(
                    'datetime'    => date('Y-m-d H:i:s', $ts),
                    'timestamp'   => $ts,
                    'data'        => +$row->data,
                    'min'         => +$row->min,
                    'max'         => +$row->max,
                    'count'       => +$row->count,
                    'timediff'    => 0,
                    'consumption' => 0
                ), $row->g);
            }
        }

        // Skip validity handling of after_read!
        $this->valid_from = $this->valid_to = NULL;

        return $this->after_read($result, $attributes);
    }

    // -----------------------------------------------------------------------
    // PROTECTED
    // -----------------------------------------------------------------------

    /**
     *
     */
    protected $child;

}
