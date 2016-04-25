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
    public function LoginPOST_Action() {

        $hasher = new \PasswordHash();

        if ($hasher->CheckPassword($this->request->post('pass'), $this->config->get('Core.Password'))) {
            \Session::login($this->config->get('Core.Password'));

            $this->request->post('save') && \Session::remember(7*24*60*60);

            if ($r = \Session::get('returnto')) {
                // Clear before redirect
                \Session::set('returnto');
                $this->app->redirect($r);
            } else {
                $this->app->redirect();
            }

        } else {
            \Messages::Error(__('UnknownUser'));
        }
    }

    /**
     * Token login
     */
    public function LoginGET_Action() {
        if ($this->app->params->get('token') == \PVLng::getLoginToken()) {
            \Session::login($this->config->get('Core.Password'));
        }
        $this->app->redirect();
    }

    /**
     *
     */
    public function Logout_Action() {
        // Remember messages
        $msgs = \Session::get(\Messages::$SessionVar);
        \Session::logout();
        \Session::destroy();
        \Session::start();
        \Session::set(\Messages::$SessionVar, $msgs);
        $this->app->redirect();
    }

    /**
     *
     */
    public function AdminPasswordPOST_Action() {

        $p1 = $this->request->post('p1');
        $p2 = $this->request->post('p2');

        if ($p1 == '' OR $p2 == '') {
            \Messages::Error(\I18N::_('AdminAndPasswordRequired'), TRUE);
            $this->view->Ok = FALSE;
            return;
        }

        if ($p1 != $p2) {
            \Messages::Error(__('PasswordsNotEqual'), TRUE);
            $this->view->Ok = FALSE;
            return;
        }

        $hasher   = new \PasswordHash();
        $settings = new \ORM\Settings;
        $settings->setScope('core')
                 ->setKey('Password')
                 ->setValue($hasher->HashPassword($p1))
                 ->replace();

        $settings->filterByScopeNameKey('core', '', 'Password')->findOne();

        $this->view->Ok = ($settings->getValue() != '');

        if ($this->view->Ok) {
            \Messages::Success(__('PasswordSaved'));
            \Session::set('user', TRUE);
            $this->app->user = TRUE;
            $this->app->redirect('/');
        }
    }

    /**
     *
     */
    public function AdminPassword_Action() {
        if ($this->config->get('Core.Password')) {
            \Messages::Error('Administration password still defined! Please change it via settings menu!');
            $this->app->redirect('/');
        }
        $this->view->SubTitle = __('GenerateAdminHash');
    }

    /**
     *
     */
    public function LocationPOST_Action() {
        if ($loc = $this->app->request->post('loc')) {
            $settings = new \ORM\Settings;
            foreach ($loc as $key=>$value) {
                $settings->reset()
                         ->filterByScopeNameKey('core', '', $key)->findOne()
                         ->setValue($value)->update();
            }
            \Messages::Success(__('DataSaved'));
        }
        $this->app->redirect('/');
    }

    /**
     *
     */
    public function Location_Action() {
        $this->view->SubTitle = __('FindYourLocation');
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
