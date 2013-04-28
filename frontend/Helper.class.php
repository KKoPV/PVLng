<?php
/**
 *
 *
 * @author      Knut Kohl <github@knutkohl.de>
 * @copyright   2012-2013 Knut Kohl
 * @license     GNU General Public License http://www.gnu.org/licenses/gpl.txt
 * @version     $Id$
 */
class Helper {

	/**
	 * Day string to timestamp
	 */
	public static function str2ts( $day ) {
		if ($day == '') return time();

		if (strpos($day, '-') !== FALSE) {
			// YYYY-mm-dd ... YY-m-d
			$date = explode('-', $day);
		} elseif (strpos($day, '.') !== FALSE) {
			// dd.mm.YYYY ... d.m.YY
			$date = array_reverse(explode('.', $day));
		} elseif (strlen($day) == 8 AND $day*1 == $day) {
			// YYYYmmdd
			$date = array(substr($day, 0, 4), substr($day, 4, 2), substr($day, 6, 2));
		} elseif (is_numeric($day)) {
			// Timestamp
			return $day;
		} else {
			throw new Exception('Unknown date format: '.$day);
		}
		// Is day set?
		if (!isset($date[2])) $date[2] = 1;
		list($y, $m, $d) = $date;
		return mktime(0, 0, 0, (int) $m, (int) $d, (int) $y);
	}

	/**
	 *
	 */
	public static function hms( $t ) {
		return sprintf("%02d:%02d:%02d", floor($t/3600), ($t/60)%60, $t%60);
	}

	/**
	 * recursive
	 */
	public static function array_max( $array ) {
		$max = -PHP_INT_MAX;
		foreach ($array as $value) {
			if (is_array($value)) {
				$max = max($max, self::array_max($value));
			} else {
				$max = max($max, $value);
			}
		}
		return $max;
	}
}