<?php
/**
 *
 *
 * @author      Knut Kohl <github@knutkohl.de>
 * @copyright   2012-2013 Knut Kohl
 * @license     GNU General Public License http://www.gnu.org/licenses/gpl.txt
 * @version     $Id$
 */
class View extends yMVC\View {

	/**
	 *
	 */
	public function setLayout( $layout ) {
		$this->layout = $layout;
	}

	/**
	 *
	 */
	public function output() {
		$this->Helper->numf = function( $number, $decimals=0 ) {
			return number_format($number, $decimals, I18N::_('DSEP'), I18N::_('TSEP'));
		};

		$this->Layout = $this->layout;

		// Missing files are ok
		$this->Head .= $this->render('head.tpl');
		$this->Head .= $this->render('head.'.$this->router->Action.'.tpl');

		// Styles
		$file = DS . 'frontend' . DS . $this->router->Controller
		      . DS . 'tpl' . DS . 'style.css';
		if (file_exists(ROOT_DIR . $file)) {
			$this->Head .= '<link rel="stylesheet" href="' . $file . '">';
		}

		$file = DS . 'frontend' . DS . $this->router->Controller
		      . DS . 'tpl' . DS . 'style.' . $this->router->Action . '.css';
		if (file_exists(ROOT_DIR . $file)) {
			$this->Head .= '<link rel="stylesheet" href="' . $file . '">';
		}

		// Missing files are ok
		$this->assign('Content', 'content.'.$this->router->Action.'.tpl');
		$this->assign('Content', 'content.tpl');

		// Missing files are ok
		$this->Scripts .= $this->render('script.js');
		$this->Scripts .= $this->render('script.'.$this->router->Action.'.js');

		$this->content = $this->render($this->layout.'.tpl');

		Header('Content-type: text/html; charset=UTF-8');

		parent::output();
	}

	//--------------------------------------------------------------------------
	// PROTECTED
	//--------------------------------------------------------------------------

	/**
	 *
	 */
	protected $layout = 'default';

}