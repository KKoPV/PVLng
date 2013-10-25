<?php
/**
 *
 *
 * @author      Knut Kohl <github@knutkohl.de>
 * @copyright   2012-2013 Knut Kohl
 * @license     GNU General Public License http://www.gnu.org/licenses/gpl.txt
 * @version     $Id: v1.0.0.2-22-g7bc4608 2013-05-05 22:07:15 +0200 Knut Kohl $
 */
namespace Controller;

/**
 *
 */
class Admin extends \Controller {

	/**
	 *
	 */
	public function LoginPOST_Action() {

		$hasher = new \PasswordHash();

		$user = $this->request->post('user');
		$pass = $this->request->post('pass');

		if ($this->config->get('Admin.User') == $user AND
		    $hasher->CheckPassword($pass, $this->config->get('Admin.Password'))) {

			$this->User = $user;
			\Session::set('user', $user);

			if ($this->request->post('save')) {
				setcookie(\Session::token(), 1, time()+60*60*24*7, '/');
			}
			\Messages::Success(\I18N::_('Welcome', $this->User));

			if ($r = \Session::get('returnto')) {
				// Clear before redirect
				\Session::set('returnto');
				$this->app->redirect($r);
			} else {
				$this->app->redirect('index');
			}

		} else {
			\Messages::Error(\I18N::_('UnknownUser'));
		}
	}

	/**
	 *
	 */
	public function Logout_Action() {
		if ($this->User) {
			$this->view->Message = \I18N::_('LogoutSuccessful', $this->User);
		}
		$this->User = '';
		\Session::destroy();
		setcookie(\Session::token(), '', time()-60*60*24, '/');
	}

	/**
	 *
	 */
	public function AdminPasswordPOST_Action() {
		if ($this->request->post('u') == '' OR
		    $this->request->post('p1') == '' OR
		    $this->request->post('p2') == '') {
			\Messages::Error(I18N::_('AdminAndPasswordRequired'), TRUE);
            return;
		}

		if ($this->request->post('p1') != $this->request->post('p2')) {
			\Messages::Error(\I18N::_('PasswordsNotEqual'), TRUE);
            return;
		}

		$hasher = new \PasswordHash();
		$this->view->AdminUser = $this->request->post('u');
		$this->view->AdminPass = $hasher->HashPassword($this->request('p1'));
	}

	/**
	 *
	 */
	public function AdminPassword_Action() {
		$this->view->SubTitle = \I18N::_('GenerateAdminHash');
	}

}
