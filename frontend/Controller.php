<?php
/**
 *
 *
 * @author      Knut Kohl <github@knutkohl.de>
 * @copyright   2012-2014 Knut Kohl
 * @license     MIT License (MIT) http://opensource.org/licenses/MIT
 * @version     1.0.0
 */
class Controller extends slimMVC\Controller {

    /**
     *
     */
    public function __construct() {
        parent::__construct();

        // Shortcuts
        $app = $this->app;

        $this->db = $app->db;
        $this->cache = $app->cache;
        $this->config = $app->config;
        $this->request = $app->request;

        $this->view = $app->view;

        $this->view->Helper->translate = function() {
            return call_user_func_array('I18N::translate', func_get_args());
        };

        $this->Layout = 'default';

        if ($returnto = $this->app->request->get('returnto') OR $returnto = $this->app->request->post('returnto')) {
            Session::set('returnto', $returnto);
        }

        $this->controller = str_replace('Controller\\', '', get_class($this));

        $this->view->Module = strtolower($this->controller);
        $this->view->User = $app->user;
        $this->view->Embedded = $this->app->request->get('embedded') ?: 0;

        $this->view->BaseDir = array(
            APP_DIR . DS . 'View' . DS . $this->controller . DS . 'custom',
            APP_DIR . DS . 'View' . DS . $this->controller,
            APP_DIR . DS . 'View' . DS . 'custom',
            APP_DIR . DS . 'View'
        );

        $this->view->Menu = $app->menu->get();
        $this->view->Languages = $app->languages->get();
    }

    /**
     *
     */
    public function __destruct() {
        // Send statistics each 6 hours if activated
        if ($this->config->SendStatistics) {
            PVLng::SendStatistics();
        }
    }

    /**
     *
     */
    public function after() {
        /* For Logout */
        $this->view->User = $this->app->user;
        if ($this->app->user) {
            if ($this->config->get('Core.TokenLogin')) {
                $this->view->Token = \PVLng::getLoginToken();
            }
            $APIkey = null;
            while ($this->cache->save('APIkey', $APIkey)) {
                $APIkey = (new \ORM\Config)->getAPIkey();
            }
            $this->view->APIkey = $APIkey;
        }
        parent::after();
    }

    /**
     *
     */
    public function afterPOST() {
        $returnto = Session::get('returnto');
        if ($returnto) {
            Session::set('returnto');
            $this->app->redirect($returnto);
        }
    }

    /**
     *
     */
    public function finalize( $action ) {

        // If no layout is set, assume raw data was generated
        if (!$this->Layout) {
            return;
        }

        if ($this->app->config('mode') == 'development') {
            $this->view->Branch = shell_exec('git branch | grep \'*\' | cut -b3-');
            $this->view->Development = true;
            $this->config->set('View.Verbose', true);
        }

        $this->view->Language  = $this->app->Language;
        $this->view->Year                = date('Y');

        $this->config2Vview();

        $messages = array();
        foreach (Messages::getRaw() as $message) {
            $messages[] = array(
                'TYPE'    => $message['type'],
                'MESSAGE' => str_replace(
                                 array('\'',    '"',      "\n"),
                                 array('&#39;', '&quot;', '\\n'),
                                 $message['message']
                             )
            );
        }
        $this->view->MessagesRaw = $messages;

        $this->view->PVLng = PVLNG;
        $this->view->Version = PVLNG_VERSION;
        $this->view->VersionDate = PVLNG_VERSION_DATE;

        $this->view->PHPVersion = PHP_VERSION;
        $this->view->MySQLVersion = $this->db->queryOne('SELECT version()');
        $this->view->ServerName = $_SERVER['HTTP_HOST'];
        $this->view->ServerVersion = $_SERVER['SERVER_SOFTWARE'];

        // Put all controller configurations into view
        foreach ($this->config->Controller as $c=>$cfg) {
            foreach ($cfg as $key=>$value) {
                $this->view->set($c.'_'.$key, $value);
            }
        }

        // Check for new version once a hour
        if (Session::get('VersionCheck', 0)+3600 < time()) {
            $version = $this->checkVersion();
            $this->db->VersionNew = isset($version[0]) ? $version[0] : FALSE;
            Session::set('VersionCheck', time());
        }
        $this->view->VersionNew = ($this->db->VersionNew > PVLNG_VERSION) ? $this->db->VersionNew : NULL;

        // Missing files are ok
        // Head append
        $this->view->append('Head', $this->view->fetch('head.'.$action.'.tpl'));

        // Styles
        $this->view->append('Styles', $this->view->fetch(APP_DIR . DS . 'View' . DS . $this->controller . DS . 'style.css'));
        $this->view->append('Styles', $this->view->fetch(APP_DIR . DS . 'View' . DS . $this->controller . DS . 'style.' . $action . '.css'));

        // Content
        $this->view->assign('Content', 'content.'.$action.'.tpl');
        $this->view->assign('Content', 'content.tpl');

        // Scripts
        $this->view->append('Scripts', $this->view->fetch('script.js.html'));
        $this->view->append('Scripts', $this->view->fetch('script.'.$action.'.js.html'));

        $this->view->display($this->Layout.'.tpl');
    }

    // -------------------------------------------------------------------------
    // PROTECTED
    // -------------------------------------------------------------------------

    /**
     *
     */
    protected $db;

    /**
     *
     */
    protected $cache;

    /**
     *
     */
    protected $config;

    /**
     *
     */
    protected $request;

    /**
     *
     */
    protected $model;

    /**
     *
     */
    protected $controller;

    /**
     *
     */
    protected $view;

    /**
     *
     */
    protected $Layout;

    /**
     * 
     */
    protected function config2Vview() {
        $this->view->Title               = $this->config->get('Core.Title');
        $this->view->Latitude            = $this->config->get('Core.Latitude');
        $this->view->Longitude           = $this->config->get('Core.Longitude');

        $this->view->CurrencyISO         = $this->config->get('Core.Currency.ISO');
        $this->view->CurrencySymbol      = $this->config->get('Core.Currency.Symbol');
        $this->view->CurrencyDecimals    = $this->config->get('Core.Currency.Decimals');
        $this->view->CurrencyFormat      = $this->config->get('Core.Currency.Format');

        $this->view->DateTimeFormat      = $this->config->get('Locale.DateTime');
        $this->view->DateTimeFormatShort = $this->config->get('Locale.DateTimeShort');
        $this->view->DateFormat          = $this->config->get('Locale.Date');
        $this->view->DateFormatShort     = $this->config->get('Locale.DateShort');
        $this->view->TimeFormat          = $this->config->get('Locale.Time');
        $this->view->TimeFormatShort     = $this->config->get('Locale.TimeShort');
        $this->view->MonthFormat         = $this->config->get('Locale.MonthDefault');
        $this->view->TSep                = $this->config->get('Locale.ThousandSeparator');
        $this->view->DSep                = $this->config->get('Locale.DecimalPoint');
        $this->view->DateFormatJS        = $this->config->get('Locale.DateJS');
    }

    /**
     *
     */
    protected function preparePresetAndPeriod() {

        $bk = \BabelKitMySQLi::getInstance();

        $preset = $period = null;

        /// \Yryie::StartTimer('LoadPreset', NULL, 'CacheDB');
        while ($this->app->cache->save('preset/'.$this->app->Language, $preset)) {
            /// \Yryie::Info('Load preset from Database');
            $preset = $bk->select('preset', $this->app->Language);
        }
        /// \Yryie::StopTimer('LoadPreset');
        $this->view->PresetSelect = $preset;

        /// \Yryie::StartTimer('LoadPeriod', NULL, 'CacheDB');
        while ($this->app->cache->save('period/'.$this->app->Language, $period)) {
            /// \Yryie::Info('Load period from Database');
            $period = $bk->select('period', $this->app->Language);
        }
        /// \Yryie::StopTimer('LoadPeriod');
        $this->view->PeriodSelect = $period;
    }

    /**
     *
     */
    protected function checkVersion() {
        $url = 'https://raw.github.com/KKoPV/PVLng/master/.version';

        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2);
        curl_setopt($ch, CURLOPT_TIMEOUT, 2);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

        $version = curl_exec($ch);

        curl_close($ch);

        return explode("\n", $version);
    }

}
