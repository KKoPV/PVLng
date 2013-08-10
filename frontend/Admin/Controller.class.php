<?php
/**
 *
 *
 * @author      Knut Kohl <github@knutkohl.de>
 * @copyright   2012-2013 Knut Kohl
 * @license     GNU General Public License http://www.gnu.org/licenses/gpl.txt
 * @version     $Id: v1.0.0.2-22-g7bc4608 2013-05-05 22:07:15 +0200 Knut Kohl $
 */
class Admin_Controller extends ControllerAuth {

	/**
	 *
	 */
	public function Login_Post_Action() {

		$hasher = new PasswordHash();

		if ($this->config->Admin_User == $this->request('user') AND
		    $hasher->CheckPassword($this->request('pass'), $this->config->Admin_Password)) {

			$this->User = $this->request('user');
			Session::set('user', $this->User);

			if ($this->request('save')) {
				setcookie(Session::token(), 1, time()+60*60*24*7, '/');
			}
			Messages::Success(I18N::_('Welcome', $this->User));

			if ($r = Session::get('returnto')) {
				// clear before redirect
				Session::set('returnto');
				$this->redirect($r);
			} else {
				$this->redirect('');
			}

		} else {
			Messages::Error(I18N::_('UnknownUser'));
		}
	}

	/**
	 *
	 */
	public function Logout_Action() {
		if ($this->User) {
			$this->view->Message = I18N::_('LogoutSuccessful', $this->User);
		}
		$this->User = '';
		Session::destroy();
		setcookie(Session::token(), '', time()-60*60*24, '/');
	}

	/**
	 *
	 */
	public function AdminPassword_POST_Action() {
		if ($this->request('u') == '' OR $this->request('p1') == '' OR $this->request('p2') == '') {
			Messages::Error(I18N::_('AdminAndPasswordRequired'), TRUE);
			return;
		}

		if ($this->request('p1') != $this->request('p2')) {
			Messages::Error(I18N::_('PasswordsNotEqual'), TRUE);
			return;
		}

		$hasher = new PasswordHash();
		$this->view->AdminPass = $hasher->HashPassword($this->request('p1'));
	}

	/**
	 *
	 */
	public function AdminPassword_Action() {
		$this->view->SubTitle = I18N::_('GenerateAdminHash');
		$this->view->AdminUser = $this->request('u');
	}

}
