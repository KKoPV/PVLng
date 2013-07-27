<?php
/**
 *
 * @author      Knut Kohl <github@knutkohl.de>
 * @copyright   2012-2013 Knut Kohl
 * @license     GNU General Public License http://www.gnu.org/licenses/gpl.txt
 * @version     $Id: v1.0.0.2-24-gffc9108 2013-05-05 22:20:01 +0200 Knut Kohl $
 */
class ControllerAuth extends Controller {

	/**
	 *
	 */
	public function before() {

		if ($this->config->Admin_User == '' AND
		    $this->router->Route != 'adminpass') {
			$this->redirect('adminpass');
		}

		if ($this->router->Route != 'login') {

			if (Session::get('user') == $this->config->Admin_User) {
				// Ok, we have a validated user session
				$this->User = Session::get('user');
			} elseif (isset($_COOKIE[Session::token()])) {
				// Ok, we have a remembered user
				Session::set('user', $this->config->Admin_User);
				$this->User = $this->config->Admin_User;
			} else {
				// Login!
				Session::set('returnto', $this->router->Route == 'logout' ? 'index' : $this->router->Route);
				$this->redirect('login');
			}
		}

		parent::before();
	}

	/**
	 *
	 */
	public function after() {
		$this->view->User = $this->User;
		parent::after();
	}

	// -------------------------------------------------------------------------
	// PROTECTED
	// -------------------------------------------------------------------------

	/**
	 *
	 */
	protected $User;

}
