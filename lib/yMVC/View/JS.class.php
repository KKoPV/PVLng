<?php
/**
 *
 * @author      Knut Kohl <github@knutkohl.de>
 * @copyright   2012-2013 Knut Kohl
 * @license     GNU General Public License http://www.gnu.org/licenses/gpl.txt
 * @version     $Id$
 */
namespace yMVC\View;

/**
 *
 */
use yMVC\View;

/**
 *
 * Uses this vars:
 * - content - convert to JS (required)
 *
 * @author      Knut Kohl <github@knutkohl.de>
 * @copyright   2012-2013 Knut Kohl
 * @license     GNU General Public License http://www.gnu.org/licenses/gpl.txt
 * @version     $Id$
 */
class JS extends View {

	/**
	 *
	 */
	public function __construct() {
		parent::__construct();
		// 0, 10, 62, 95, FALSE to skip!
		$this->encoding = $this->config->get('View.JavaScriptPacker', 0);
	}

	/**
	 *
	 */
	public function output() {
		if (!$this->config->get('View.Verbose') AND $this->encoding !== FALSE) {
			$packer = new \yMVC\JavaScriptPacker($this->content, $this->encoding);
			$this->content = $packer->pack();
		}

		parent::output();
	}

	// -------------------------------------------------------------------------
	// PROTECTED
	// -------------------------------------------------------------------------

	/**
	 *
	 */
	protected $ContentType = 'application/x-javascript';

	/**
	 *
	 */
	protected $encoding;

}