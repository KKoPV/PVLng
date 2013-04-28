<?php
/**
 *
 * @author      Knut Kohl <github@knutkohl.de>
 * @copyright   2012-2013 Knut Kohl
 * @license     GNU General Public License http://www.gnu.org/licenses/gpl.txt
 * @version     $Id$
 */
class ErrorHandler {

	/**
	 *
	 */
	public static function register() {
		set_error_handler(array('ErrorHandler','handle'));
	}

	/**
	 *
	 */
	public static function handle( $errno, $errstr, $errfile, $errline ) {

		if (!(error_reporting() & $errno)) return;

		switch ($errno) {
			case E_NOTICE:
			case E_USER_NOTICE:
				$errors = 'Hinweis';
				break;
			case E_WARNING:
			case E_USER_WARNING:
				$errors = 'Warnung';
				break;
			case E_ERROR:
			case E_USER_ERROR:
				$errors = 'Fataler Fehler';
				break;
			default:
				$errors = 'Unbekannter Fehler';
				break;
		}

		$CLI = !isset($_SERVER['REQUEST_METHOD']);

		if (!$CLI) echo '<pre style="text-align:left;font-family:monospace">';

		printf (PHP_EOL.'%s: %s in %s [%d]'.PHP_EOL, $errors, $errstr, $errfile, $errline);

		$bt = debug_backtrace();
		array_shift($bt);

		// start backtrace
		foreach (array_reverse($bt) as $v) {
			if (!empty($v['class'])) {
				$in = (isset($v['file']) ? $v['file'] . ' ' : '')
				    . (isset($v['line']) ? '[' . $v['line'] . '] ' : '');
				echo 'in ', $in, $v['class'], '::', $v['function'], '(';
				echo ')', PHP_EOL;
			} elseif (!empty($v['function'])) {
				echo 'in ', $v['function'], '(';
				echo ')', PHP_EOL;
			} else {
				print_r($v);
			}
		}
		if (!$CLI) echo '</pre>';
	}

}