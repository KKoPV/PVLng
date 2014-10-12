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
class Estimate extends InternalCalc {

    /**
     *
     */
    protected $settings;

    /**
     *
     */
    protected function __construct( \ORM\Tree $channel ) {
        parent::__construct($channel);
        // Fake as counter to get the sum of estiamtes for periods greater than day
        $this->counter = TRUE;

        $this->settings = new \ORM\Settings;

        if ($marker = $this->settings->getModelValue('Estimate', 'Marker')) {
            $this->attributes['marker'] = $marker;
        }
    }

    /**
     *
     */
    protected function before_read( $request ) {

        parent::before_read($request);

        $timestamp = max(strtotime(date('Y-m-d 12:00', $this->start)), $this->start);
        $this->end = min(strtotime(date('Y-m-d 12:00', $this->end)), $this->end);

        if ($this->dataExists()) return;

        $estimates = array();
        foreach (explode("\n", $this->extra) as $line) {
            $line = explode(':', $line, 2);
            if (isset($line[1])) {
                if (preg_match('~0?(\d+)-0?(\d+)~', $line[0], $parts)) {
                    $estimates[$parts[1].'-'.$parts[2]] = $line[1];
                } else {
                    $estimates[trim($line[0])] = $line[1];
                }
            }
        }

        // Set also last month of last year and 1st month of next year
        if (isset($estimates[1])) $estimates[13] = $estimates[1];
        if (isset($estimates[12])) $estimates[0] = $estimates[12];

        while ($timestamp <= $this->end) {
            $month = date('n',   $timestamp);
            $day   = date('n-j', $timestamp);

            if (!($ts = $this->settings->getSunset($timestamp))) {
                // Round between 16:00 and 21:30 during the year in seconds
                $ts = (16 + sin((date('z', $timestamp)+10) * M_PI / 366) * 5.5) *60*60;
                // Move into this day using date functions for server time offsets
                $ts = strtotime(date('Y-m-d H:i', strtotime(date('Y-m-d', $timestamp)) + $ts));
            }

            // Search for estimate, 1st for exact day, then for month
            if (isset($estimates[$day])) {
                $this->saveValue($ts, $estimates[$day]);
            } elseif (isset($estimates[$month-1], $estimates[$month], $estimates[$month+1])) {
                if (date('j', $timestamp) < 15) {
                    $e1 = $estimates[$month-1];
                    $e2 = $estimates[$month];
                    $d1 = strtotime('first day of last month', $timestamp) + 1209600; // 15 days
                    $d2 = strtotime('first day of this month', $timestamp) + 1209600;
                } else {
                    $e1 = $estimates[$month];
                    $e2 = $estimates[$month+1];
                    $d1 = strtotime('first day of this month', $timestamp) + 1209600;
                    $d2 = strtotime('first day of next month', $timestamp) + 1209600;
                }
                // round to 10th of Wh -  start + (          0 ... 1         ) * delta
                $this->saveValue($ts, round($e1 + ($timestamp-$d1) / ($d2-$d1) * ($e2-$e1), 2));
            }
            $timestamp += 86400;
        }

        $this->dataCreated();
    }
}
