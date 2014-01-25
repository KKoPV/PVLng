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
     * Channel type
     * UNDEFINED_CHANNEL - concrete channel decides
     * NUMERIC_CHANNEL   - concrete channel decides if sensor or meter
     * SENSOR_CHANNEL    - numeric
     * METER_CHANNEL     - numeric
     */
    const TYPE = SENSOR_CHANNEL;

    /**
     * Run additional code before data saved to database
     * Read latitude / longitude from extra attribute
     */
    public static function beforeEdit( \ORM\Channel $channel, Array &$fields ) {
        $fields['estimates']['VALUE'] = $channel->extra;
    }

    /**
     * Run additional code before data saved to database
     * Save latitude / longitude to extra attribute
     */
    public static function beforeSave( Array &$fields, \ORM\Channel $channel ) {
        $channel->extra = $fields['estimates']['VALUE'];
    }

    /**
     *
     */
    protected function __construct( \ORM\Tree $channel ) {
        parent::__construct($channel);
        // Fake as counter to get the sum of estiamtes for periods greater than day
        $this->counter = TRUE;
    }

    /**
     *
     */
    protected function before_read( $request ) {

        parent::before_read($request);

        $timestamp = max(strtotime(date('Y-m-d 12:00', $this->start)), $this->start);
        $this->end = min(strtotime(date('Y-m-d 12:00', $this->end)), $this->end);

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

        $lat = $this->config->get('Location.Latitude');
        $lon = $this->config->get('Location.Longitude');

        while ($timestamp <= $this->end) {
            $day = date('n-j', $timestamp);
            $month = date('n', $timestamp);

            if ($lat != '' AND $lon != '') {
                // Set to sunset time
                $ts = date_sunset($timestamp, SUNFUNCS_RET_TIMESTAMP, $lat, $lon, 90, date('Z')/3600);
            } else {
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
    }
}
