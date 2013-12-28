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
        $TimeStep = max($this->db->TimeStep, 60);

        $data = explode("\n", $this->comment);
        $estimates = array();
        foreach ($data as $line) {
            $line = explode(':', $line, 2);
            if (isset($line[1])) $estimates[trim($line[0])] = $line[1];
        }

        $values = array();

        while ($timestamp <= $this->end) {
            $day   = date('m-d', $timestamp);
            $month = date('m', $timestamp);

            if (date('Ymd', $timestamp) == date('Ymd')) {
                // Use now for todays view
                $ts = strtotime(date('Y-m-d H:i'));
            } else {
                // Round between 15:30 and 21:00 during year in seconds
                $ts = (15.5 + sin(date('z', $timestamp) * M_PI / 366) * 5.5) *60*60;

                // Move into this day using date functions for server time offsets
                $ts = strtotime(date('Y-m-d H:i', strtotime(date('Y-m-d', $timestamp)) + $ts));
            }

            // Align to data time step
            $ts = bcdiv($ts, $TimeStep) * $TimeStep;

            // Search for estimate, 1st for exact day, then for month
            if (isset($estimates[$day])) {
                $values[$ts] = $estimates[$day];
            } elseif (isset($estimates[$month])) {
                $values[$ts] = $estimates[$month];
            }
            $timestamp += 86400;
        }

        $this->saveValues($values);
    }

}
