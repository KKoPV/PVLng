<?php
/**
 * Abstract API r2 handler class
 *
 * @author      Knut Kohl <github@knutkohl.de>
 * @copyright   2012-2013 Knut Kohl
 * @license     GNU General Public License http://www.gnu.org/licenses/gpl.txt
 * @version     $Id$
 */
namespace API\r2;

/**
 *
 */
abstract class Handler {

	/**
	 *
	 */
	public function __construct( $GUID ) {
	    $this->GUID = $GUID;
	}

	/**
	 *
	 */
	public function __call( $method, $params ) {
	    $this->send(405, 'Request method '.$method.' is not allowed here!');
	}

	/**
	 *
	 */
	public function send( $code=200, $msg='' ) {
	    throw new \Exception($msg, $code);
	}

	// -----------------------------------------------------------------------
	// PROTECTED
	// -----------------------------------------------------------------------

	/**
	 *
	 */
	protected $GUID;

}
