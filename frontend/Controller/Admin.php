<?php
/**
 *
 *
 * @author      Knut Kohl <github@knutkohl.de>
 * @copyright   2012-2013 Knut Kohl
 * @license     GNU General Public License http://www.gnu.org/licenses/gpl.txt
 * @version     1.0.0
 */
namespace Controller;

/**
 *
 */
class Admin extends \Controller {

    /**
     *
     */
    public static function RememberLogin() {
        setcookie(\Session::token(), 1, time()+60*60*24*60, '/');
    }

    /**
     *
     */
    public function LoginPOST_Action() {

        $hasher = new \PasswordHash();

        $user = $this->request->post('user');
        $pass = $this->request->post('pass');

        $AdminUser = $this->config->get('Admin.User');
        $AdminPass = $this->config->get('Admin.Password');

        // Ignore case of user name input
        if (strtolower($AdminUser) == strtolower($user) AND
            $hasher->CheckPassword($pass, $AdminPass)) {

            $this->User = $AdminUser;
            \Session::set('user', $AdminUser);
            \Messages::Success(__('Welcome', $this->User));

            if ($this->request->post('save')) self::RememberLogin();

            if ($r = \Session::get('returnto')) {
                // Clear before redirect
                \Session::set('returnto');
                $this->app->redirect($r);
            } else {
                $this->app->redirect('index');
            }

        } else {
            \Messages::Error(__('UnknownUser'));
        }
    }

    /**
     *
     */
    public function Logout_Action() {
        if ($this->User) {
            $this->view->Message = __('LogoutSuccessful', $this->User);
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
            \Messages::Error(__('PasswordsNotEqual'), TRUE);
            return;
        }

        $hasher = new \PasswordHash();
        $this->view->AdminUser = $this->request->post('u');
        $this->view->AdminPass = $hasher->HashPassword($this->request->post('p1'));
    }

    /**
     *
     */
    public function AdminPassword_Action() {
        $this->view->SubTitle = __('GenerateAdminHash');
    }

    /**
     *
     */
    public function ConfigPOST_Action() {
        foreach ($this->request->post('c') as $key=>$value) {
            $q = \DBQuery::forge()->update('pvlng_config')
                 ->set('value', $value)->whereEQ('key', $key)->limit(1);
            $this->db->query($q);
        }
        \Messages::success(__('DataSaved'));
    }

    /**
     *
     */
    public function Config_Action() {
        $this->view->SubTitle = __('Configuration');

        $q = \DBQuery::forge('pvlng_config')->whereNE('type');
        $this->view->Data = $this->rows2view($this->db->queryRows($q));
    }

}
