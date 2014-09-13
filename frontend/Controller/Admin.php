<?php
/**
 *
 *
 * @author      Knut Kohl <github@knutkohl.de>
 * @copyright   2012-2014 Knut Kohl
 * @license     MIT License (MIT) http://opensource.org/licenses/MIT
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
     * Token login
     */
    public function LoginGET_Action() {
        $AdminUser = $this->config->get('Admin.User');
        $AdminPass = $this->config->get('Admin.Password');

        if ($this->request->get('token') == md5($_SERVER['REMOTE_ADDR'].$AdminUser.$AdminPass)) {
            \Session::set('user', $AdminUser);
            \Messages::Success(__('Welcome', $this->User));
        }
        $this->app->redirect('index');
    }

    /**
     *
     */
    public function Logout_Action() {
        \Session::destroy();
        setcookie(\Session::token(), '', time()-60*60*24, '/');
        $this->app->redirect('index');
    }

    /**
     *
     */
    public function AdminPasswordPOST_Action() {
        if ($this->request->post('u') == '' OR
            $this->request->post('p1') == '' OR
            $this->request->post('p2') == '') {
            \Messages::Error(\I18N::_('AdminAndPasswordRequired'), TRUE);
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
        if ($this->config->get('Admin.User') != '') {
            \Messages::Error('Admin credentials still defined! You can\'t change them for security reasons without clearing the "Admin > User" entry in config/config.php');
            $this->app->redirect('index');
        }
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
        $this->view->Data = $this->db->queryRows($q);
    }

    /**
     *
     */
    public function ClearcachePOST_Action() {
        $info = $this->app->cache->info();
        if ($this->request->post('tpl')) {
            $i = 0;
            foreach (glob(TEMP_DIR.DS.'*') as $i=>$file) {
                // Don't delete .githold ...
                if (strpos($file, '.githold') === FALSE) $i += (int) unlink($file); // Success == TRUE => 1
            }
            \Messages::Success(sprintf('Removed %d files', $i));
            if ($info['class'] != 'Cache\APC' AND extension_loaded('apc') AND ini_get('apc.enabled')) {
                apc_clear_cache();
                \Messages::Success('Cleared also APC files cache');
            }
        }
        if ($this->request->post('cache')) {
            $this->app->cache->flush();
            \Messages::Success('Cleared caches of '.addslashes($info['class']));
        }
        $this->app->redirect('/cc');
    }

    /**
     *
     */
    public function Clearcache_Action() {
        $this->view->SubTitle = 'Clear caches';
        $this->view->TempDir = TEMP_DIR;
    }

}
