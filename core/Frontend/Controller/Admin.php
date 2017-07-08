<?php
/**
 * PVLng - PhotoVoltaic Logger new generation
 *
 * @link       https://github.com/KKoPV/PVLng
 * @link       https://pvlng.com/
 * @author     Knut Kohl <github@knutkohl.de>
 * @copyright  2012 Knut Kohl
 * @license    MIT License (MIT) http://opensource.org/licenses/MIT
 */
namespace Frontend\Controller;

use Core\Messages;
use Core\PVLng;
use Core\Session;
use Frontend\Controller;
use ORM\Settings as ORMSettings;
use I18N;
use PasswordHash;

/**
 *
 */
class Admin extends Controller
{
    /**
     *
     */
    public function loginPostAction()
    {
        $hasher = new PasswordHash();

        if ($hasher->CheckPassword($this->request->post('pass'), $this->config->get('Core.Password'))) {
            Session::login($this->config->get('Core.Password'));

            $this->request->post('save') && Session::remember(7*24*60*60);

            if ($r = Session::get('returnto')) {
                // Clear before redirect
                Session::set('returnto');
                $this->app->redirect($r);
            } elseif (isset($_SERVER['HTTP_REFERER'])) {
                $this->app->redirect($_SERVER['HTTP_REFERER']);
            } else {
                $this->app->redirect();
            }
        } else {
            Messages::error(I18N::translate('UnknownUser'));
        }
    }

    /**
     * Token login
     */
    public function loginGetAction()
    {
        if ($this->app->params->get('token') == PVLng::getLoginToken()) {
            Session::login($this->config->get('Core.Password'));
            $this->app->redirect();
        }
    }

    /**
     *
     */
    public function logoutAction()
    {
        // Remember messages
        $msgs = Session::get(Messages::$SessionVar);
        Session::logout();
        Session::destroy();
        Session::start();
        Session::set(Messages::$SessionVar, $msgs);
        $this->app->redirect();
    }

    /**
     *
     */
    public function adminPasswordPostAction()
    {
        $p1 = $this->request->post('p1');
        $p2 = $this->request->post('p2');

        if ($p1 == '' || $p2 == '') {
            Messages::Error(I18N::translate('AdminAndPasswordRequired'), true);
            $this->view->Ok = false;
            return;
        }

        if ($p1 != $p2) {
            Messages::error(I18N::translate('PasswordsNotEqual'), true);
            $this->view->Ok = false;
            return;
        }

        $hasher   = new PasswordHash();
        $settings = new ORMSettings;
        $settings->setScope('core')
                 ->setKey('Password')
                 ->setValue($hasher->HashPassword($p1))
                 ->replace();

        $settings->filterByScopeNameKey('core', '', 'Password')->findOne();

        $this->view->Ok = ($settings->getValue() != '');

        if ($this->view->Ok) {
            Messages::success(I18N::translate('PasswordSaved'));
            Session::login($settings->getValue());
            $this->app->user = true;
            $this->app->redirect('/');
        }
    }

    /**
     *
     */
    public function adminPasswordAction()
    {
        if ($this->config->get('Core.Password')) {
            Messages::Error('Administration password still defined! Please change it via settings menu!');
            $this->app->redirect('/');
        }
        $this->view->SubTitle = I18N::translate('GenerateAdminHash');
    }

    /**
     *
     */
    public function locationPostAction()
    {
        if ($loc = $this->app->request->post('loc')) {
            $settings = new ORMSettings;
            foreach ($loc as $key => $value) {
                $settings->reset()
                         ->filterByScopeNameKey('core', '', $key)->findOne()
                         ->setValue($value)->update();
            }
            Messages::success(I18N::translate('DataSaved'));
        }
        $this->app->redirect('/');
    }

    /**
     *
     */
    public function locationAction()
    {
        $this->view->SubTitle = I18N::translate('FindYourLocation');
    }

    /**
     *
     */
    public function clearCachePostAction()
    {
        $info = $this->app->cache->info();
        if ($this->request->post('tpl')) {
            $i = 0;
            $filemask = PVLng::path(PVLng::$TempDir, '*');
            foreach (glob($filemask) as $i => $file) {
                // Don't delete .githold ...
                if (strpos($file, '.githold') === false) {
                    $i += (int) unlink($file); // Success == TRUE => 1
                }
            }
            Messages::Success(sprintf('Removed %d files', $i));
            if ($info['class'] != 'Cache\APC' && extension_loaded('apc') && ini_get('apc.enabled')) {
                apc_clear_cache();
                Messages::Success('Cleared also APC files cache');
            }
        }
        if ($this->request->post('cache')) {
            $this->app->cache->flush();
            Messages::Success('Cleared caches of '.addslashes($info['class']));
        }
        $this->app->redirect('/cc');
    }

    /**
     *
     */
    public function clearCacheAction()
    {
        $this->view->SubTitle = 'Clear caches';
        $this->view->TempDir = PVLng::$TempDir;
    }
}
