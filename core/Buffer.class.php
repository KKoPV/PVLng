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
	public function __construct( $size=5 ) {
		$this->fh = fopen('php://temp/maxmemory:'.(1024 * 1024 * $size), 'w+');
	}

	/**
	 *
	 */
	public function rewind() {
		return rewind($this->fh);
	}

	/**
	 *
	 */
	public function close() {
		fclose($this->fh);
		unset($this);
	}

	/**
	 *
	 */
	public function write( $row, $id ) {
	    if ($row != '') {
			$encoded = $id . self::SEP1
			         . implode(self::SEP2, array_keys($row)) . self::SEP1
			         . implode(self::SEP2, array_values($row));
	    	return fwrite($this->fh, $encoded . PHP_EOL);
		}
		return FALSE;
	}

	/**
	 *
	 */
	public function read( &$row, &$id, $rewind=FALSE ) {
	    if ($rewind) $this->rewind();
	    $row = trim(fgets($this->fh));
		$id = '';
		if ($row != '') {
			list($id, $keys, $values) = explode(self::SEP1, $row);
			$row = array_combine(explode(self::SEP2, $keys),
			                     explode(self::SEP2, $values));
		}
	    return ($row != '');
	}

	/**
	 *
	 */
	public function swrite( $data ) {
	    return !empty($data)
		     ? fwrite($this->fh, serialize($data) . PHP_EOL)
		     : TRUE;
	}

	/**
	 *
	 */
	public function sread( &$row, $rewind=FALSE ) {
	    if ($rewind) $this->rewind();
	    $row = trim(fgets($this->fh));
		if ($row != '') $row = unserialize($row);
	    return ($row != '');
	}

	/**
	 *
	 */
	public function size() {
		fseek($this->fh, 0, SEEK_END);
		return ftell($this->fh);
	}

	/**
	 *
	 */
	public function ressource() {
		return $this->fh;
	}

	// -----------------------------------------------------------------------
	// PROTECTED
	// -----------------------------------------------------------------------

	/**
	 * Separators for encoding/decoding row data
	 */
	const SEP1 = "\x00";
	const SEP2 = "\x01";

}