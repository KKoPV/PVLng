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
    public static function checkData(Array &$fields, $add2tree)
    {
        $ok = parent::checkData($fields, $add2tree);

        if ($fields['valid_from']['VALUE'] <= 0) {
            $fields['valid_from']['ERROR'][] = __('ValueMustGTzero');
            $ok = false;
        }
        if ($fields['valid_to']['VALUE'] < 0) {
            $fields['valid_to']['ERROR'][] = __('ValueMustGEzero');
            $ok = false;
        }
        if ($fields['valid_to']['VALUE'] != '' && $fields['extra']['VALUE'] == '') {
            $fields['extra']['ERROR'][] = __('YearsToReadMissing');
            $ok = false;
        }

        return $ok;
    }

    /**
     *
     */
    public function read($request)
    {
        $this->before_read($request);

        $result = new \Buffer;

        if ($this->period[0] * self::$secondsPerPeriod[$this->period[1]] < 600) {
            // Smooth result at least 10 minutes
            $this->period = array(10, self::MINUTE);
        }

        $secondsRange = $this->period[0] * self::$secondsPerPeriod[$this->period[1]];

        $timestamp = $this->groupTimestampByPeriod();

        // Prepare inner query
        $q = new \DBQuery('pvlng_reading_num_tmp');
        $q->get($q->FROM_UNIXTIME($timestamp, '"%H"'), 'hour')
          ->get($q->FROM_UNIXTIME($timestamp, '"%i"'), 'minute');

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
          ->get($timestamp, 'g')
          ->filter('id', $this->entity)
          ->groupBy('g');

        $inner = $q;

        $day = $this->start;

        do {
            $h = clone $inner;

            $start = $day - $this->valid_from * 86400;

#            $h->filter('timestamp', array('bt' => array($start, $start + $this->valid_from * 86400)));

            $h->filter(sprintf(
                'FROM_UNIXTIME(`timestamp`, "%%j") BETWEEN
                 IF (FROM_UNIXTIME(%1$s, "%%j") < FROM_UNIXTIME(%2$s, "%%j"),
                     FROM_UNIXTIME(%1$s, "%%j"), FROM_UNIXTIME(%1$s, "%%j") - 365)
                 AND
                 IF (FROM_UNIXTIME(%2$s, "%%j") > FROM_UNIXTIME(%1$s, "%%j"),
                     FROM_UNIXTIME(%2$s, "%%j"), FROM_UNIXTIME(%2$s, "%%j") + 365)',
                $start, $start + $this->valid_from * 86400
            ));

            $q = (new \DBQuery)->select('('.substr($h, 0, -1).') t');
            $q->get('hour')
              ->get('minute')
              ->get('data')
              ->get($q->MIN('min'), 'min')
              ->get($q->MAX('max'), 'max')
              ->get($q->SUM('count'), 'count')
              ->groupBy('hour')
              ->groupBy('minute');

            $this->SQLHeader($request, $q);

            $d = date('d', $day);
            $m = date('m', $day);
            $y = date('Y', $day);

            if ($res = $this->db->query($q)) {
                $first = true;

                while ($row = $res->fetch_object()) {
                    $ts = mktime($row->hour, $row->minute, 0, $m, $d, $y);

                    if ($first) {
                        $result->write(array(
                            'datetime'    => date('Y-m-d H:i:s', $ts-$secondsRange),
                            'timestamp'   => $ts-$secondsRange,
                            'data'        => 0,
                            'min'         => 0,
                            'max'         => 0,
                            'count'       => 1,
                            'timediff'    => 0,
                            'consumption' => 0
                        ), $ts-$secondsRange);
                        $first = false;
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
                    ), $ts);

                    $last = $row;
                }

                if (isset($last)) {
                    $result->write(array(
                        'datetime'    => date('Y-m-d H:i:s', $ts+$secondsRange),
                        'timestamp'   => $ts+$secondsRange,
                        'data'        => 0,
                        'min'         => 0,
                        'max'         => 0,
                        'count'       => 1,
                        'timediff'    => 0,
                        'consumption' => 0
                    ), $ts+$secondsRange);
                }

            }

            $day += 86400;

        } while ($day < $this->end);

        // Skip validity handling of after_read!
        $this->valid_from = $this->valid_to = null;

        return $this->after_read($result);
    }

    // -----------------------------------------------------------------------
    // PROTECTED
    // -----------------------------------------------------------------------

    /**
     *
     */
    protected function before_read(&$request)
    {
        parent::before_read($request);

        if ($this->dataExists(12*60*60)) return; // Buffer for 12h

        $child = $this->getChild(1);

        if (!$child) return;

        // Read out all data
        unset($request['period']);

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
            $back = $this->valid_from * 86400;
            $foreward = $this->valid_to * 86400;

            // Clone request
            $r = array_merge(array(), $request);

            for ($year=date('Y')-$this->extra; $year<date('Y'); $year++) {
                $r['start'] = date($year.'-m-d', $this->start) . ' -' . $this->valid_from . 'days';
                $r['end']   = date($year.'-m-d', $this->start) . ' +' . $this->valid_to . 'days';
                // Save data into temp. table
                $this->saveValues($child->read($r));
            }
        }

        $this->dataCreated(true);
    }

}
