<?php
/**
 *
 *
 * @author      Knut Kohl <github@knutkohl.de>
 * @copyright   2012-2013 Knut Kohl
 * @license     GNU General Public License http://www.gnu.org/licenses/gpl.txt
 * @version     $Id: v1.0.0.2-27-gf2cf3da 2013-05-06 15:24:30 +0200 Knut Kohl $
 */
class Buffer {

	/**
	 * Use PHPs internal temp stream, use file for data greater 5 MB
	 */
	public static function create( $size=5 ) {
		return fopen('php://temp/maxmemory:'.(1024 * 1024 * $size), 'w+');
	}

	/**
	 *
	 */
	public static function rewind( $fh ) {
		return rewind($fh);
	}

	/**
	 *
	 */
	public static function close( $fh ) {
		return fclose($fh);
	}

	/**
	 *
	 */
	public static function encode( $row, $id ) {
		return $id . self::$SEP1
		     . implode(self::$SEP2, array_keys($row)) . self::$SEP1
		     . implode(self::$SEP2, array_values($row));
	}

	/**
	 *
	 */
	public static function write( $fh, $row, $id ) {
	    return ($row != '')
		     ? fwrite($fh, self::encode($row, $id) . PHP_EOL)
		     : TRUE;
	}

	/**
	 *
	 */
	public static function swrite( $fh, $data ) {
	    return !empty($data)
		     ? fwrite($fh, serialize($data) . PHP_EOL)
		     : TRUE;
	}

	/**
	 *
	 */
	public static function decode( &$row, &$id ) {
	    $row = trim($row);
		$id = '';
		if ($row != '') {
			list($id, $keys, $values) = explode(self::$SEP1, $row);
			$row = array_combine(explode(self::$SEP2, $keys),
			                     explode(self::$SEP2, $values));
		}
	}

	/**
	 *
	 */
	public static function read( $fh, &$row, &$id ) {
	    $row = fgets($fh);
	    self::decode($row, $id);
	    return ($row != '');
	}

	/**
	 *
	 */
	public static function size( $fh ) {
		fseek($fh, 0, SEEK_END);
		return ftell($fh);
	}

	// -----------------------------------------------------------------------
	// PROTECTED
	// -----------------------------------------------------------------------

	protected static $SEP1 = "\x00";
	protected static $SEP2 = "\x01";

}