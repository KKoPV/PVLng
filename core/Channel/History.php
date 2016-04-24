<?php
/**
 *
 *
 * @author     Knut Kohl <github@knutkohl.de>
 * @copyright  2012-2014 Knut Kohl
 * @license    MIT License (MIT) http://opensource.org/licenses/MIT
 * @version    1.0.0
 */
namespace Channel;

/**
 *
 */
class History extends InternalCalc {

    /**
     *
     */
    public static function checkData( Array &$fields, $add2tree ) {
        $ok = parent::checkData($fields, $add2tree);

        if ($fields['valid_from']['VALUE'] <= 0) {
            $fields['valid_from']['ERROR'][] = __('ValueMustGTzero');
            $ok = FALSE;
        }
        if ($fields['valid_to']['VALUE'] < 0) {
            $fields['valid_to']['ERROR'][] = __('ValueMustGEzero');
            $ok = FALSE;
        }

        return $ok;
    }

    /**
     *
     */
    public function read( $request ) {

        $this->before_read($request);

        $result = new \Buffer;

        if ($this->period[0] * self::$Grouping[$this->period[1]][0] < 600) {
            // Smooth result at least 10 minutes
            $this->period = array(10, self::MINUTE);
        }

        // Prepare inner query
        $q = new \DBQuery('pvlng_reading_num_tmp');
        $q->get($q->FROM_UNIXTIME('timestamp', '"%H"'), 'hour')
          ->get($q->FROM_UNIXTIME('timestamp', '"%i"'), 'minute');

        if ($this->meter) {
            $q->get($q->MAX('data'), 'data');
        } elseif ($this->counter) {
            $q->get($q->SUM('data'), 'data');
        } else {

            switch (\ORM\Settings::getModelValue('History', 'Average')) {
                default:
                    // Linear average
                    $q->get($q->AVG('data'), 'data');
                    break;
                case 1:
                    // harm. avg.: count(val) / sum(1/val)
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
          ->filter('id', $this->entity)
          ->groupBy('g');

        $inner = $q;

        $day = $this->start;

        do {
            $h = clone $inner;
            $h->filter('timestamp', array('bt' => array($day - $this->valid_from * 86400, $day - $this->valid_from * 86400 + 86400)));

            $q = (new \DBQuery)->select('('.substr($h, 0, -1).') t')->groupBy('hour')->groupBy('minute');

#echo $q;
            $d = date('d', $day);
            $m = date('m', $day);
            $y = date('Y', $day);

            if ($res = $this->db->query($h)) {
                $first = TRUE;
                while ($row = $res->fetch_object()) {
                    $ts = mktime($row->hour, $row->minute, 0, $m, $d, $y);
                    if ($first) {
                        $result->write(array(
                            'datetime'    => date('Y-m-d H:i:s', $ts-60),
                            'timestamp'   => $ts-60,
                            'data'        => 0,
                            'min'         => 0,
                            'max'         => 0,
                            'count'       => 1,
                            'timediff'    => 0,
                            'consumption' => 0
                        ), $row->g-1);
                        $first = FALSE;
                    }
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

                    $last = $row;
                }
                if (isset($last)) {
                    $result->write(array(
                        'datetime'    => date('Y-m-d H:i:s', $ts+60),
                        'timestamp'   => $ts+60,
                        'data'        => 0,
                        'min'         => 0,
                        'max'         => 0,
                        'count'       => 1,
                        'timediff'    => 0,
                        'consumption' => 0
                    ), $last->g+1);
                }
            }

            $day += 24*60*60;
        } while ($day < $this->end);

        // Skip validity handling of after_read!
        $this->valid_from = $this->valid_to = NULL;

        return $this->after_read($result);
    }

    // -----------------------------------------------------------------------
    // PROTECTED
    // -----------------------------------------------------------------------

    /**
     *
     */
    protected function before_read( &$request ) {

        parent::before_read($request);

        if ($this->dataExists(12*60*60)) return; // Buffer for 12h

        $child = $this->getChild(1);

        if (!$child) return;

        // Read out all data
        $request['period'] = '1i';

        // Inherit properties from child
        $this->meter = $child->meter;

        if ($this->valid_to == 0) {
            // Last x days, move request start backwards
            $request['start'] = $this->start - $this->valid_from * 86400;
            // Move request end backwards to 00:00
            $request['end'] = strtotime('today');

            // Save data into temp. table
            $this->saveValues($child->read($request));
        } else {
            // Find earliest data
            $q = new \DBQuery('pvlng_reading_num');
            $q->get($q->FROM_UNIXTIME($q->MIN('timestamp'), '"%Y"'));
            for ($i=$this->db->queryOne($q)-date('Y'); $i<=0; $i++) {
                $request['start'] = strtotime(date('Y-m-d ', $this->start - $this->valid_from * 86400).$i.'years');
                $request['end']   = strtotime(date('Y-m-d ', $this->end   + $this->valid_to   * 86400).$i.'years');
                // Save data into temp. table
                $this->saveValues($child->read($request));
            }
        }
        $this->dataCreated(TRUE);
    }

}
