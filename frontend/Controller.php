<?php /* // AOP // */
/**
 *
 *
 * @author      Knut Kohl <github@knutkohl.de>
 * @copyright   2012-2013 Knut Kohl
 * @license     GNU General Public License http://www.gnu.org/licenses/gpl.txt
 * @version     1.0.0
 */
class Controller extends slimMVC\Controller {

    /**
     *
     */
    public function __construct() {
        parent::__construct();

        // Shortcuts
        $this->db = $this->app->db;
        $this->cache = $this->app->cache;
        $this->config = $this->app->config;
        $this->request = $this->app->request;
        $this->view = $this->app->view;

        $this->view->Helper->translate = function() {
            return call_user_func_array('I18N::translate', func_get_args());
        };

        $this->Layout = 'default';

        if ($returnto = $this->app->request->get('returnto')) {
            Session::set('returnto', $returnto);
        }

        if (Session::get('user') == $this->config->get('Admin.User')) {
            // Ok, we have a validated user session
            $this->User = Session::get('user');
        }

        $this->controller = str_replace('Controller\\', '', get_class($this));

        $this->view->Module = strtolower($this->controller);
        $this->view->User = $this->User;
        $this->view->Embedded = $this->app->request->get('embedded');

        $this->view->BaseDir = array(
            APP_DIR . DS . 'View' . DS . $this->controller . DS . 'custom',
            APP_DIR . DS . 'View' . DS . $this->controller,
            APP_DIR . DS . 'View' . DS . 'custom',
            APP_DIR . DS . 'View'
        );

        $this->view->Menu = PVLng::getMenu();
        $this->view->SubMenus = json_encode(PVLng::getSubMenu());
        $this->view->Languages = PVLng::getLanguages();
    }

    /**
     *
     */
    public function after() {
        /* For Logout */
        $this->view->User = $this->User;
        if ($this->User) {
            if ($this->config->get('TokenLogin')) {
                $this->view->Token = \PVLng::getLoginToken();
            }
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
        if ($returnto = Session::get('returnto')) {
            Session::set('returnto');
            $this->app->redirect($returnto);
        }
    }

    /**
     *
     */
    public function finalize( $action ) {
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
        $this->view->Year                = date('Y');

        if ($this->config->get('develop')) {
            $this->view->Branch = shell_exec('git branch | grep \'*\' | cut -b3-');
            $this->view->Development = TRUE;
            $this->config->set('View.Verbose', TRUE);
        }

        $this->view->Language = $this->app->Language;
        $this->view->CurrencyISO = $this->config->get('Currency.ISO');
        $this->view->CurrencySymbol = $this->config->get('Currency.Symbol');
        $this->view->CurrencyDecimals = $this->config->get('Currency.Decimals');

        $this->view->Title = $this->config->get('Title');

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

        // Put all controller specific config also into view
        if ($cfg = $this->config->get('Controller.'.$this->controller)) {
            foreach ($cfg as $key=>$value) {
                $this->view->set($this->controller.'_'.$key, $value);
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
        $this->view->append('Scripts', $this->view->fetch('script.js'));
        $this->view->append('Scripts', $this->view->fetch('script.'.$action.'.js'));

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
    protected $User;

    /**
     *
     */
    protected function PresetAndPeriod() {

        $bk = \BabelKitMySQLi::getInstance();

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
