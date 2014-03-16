<?php
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
        $this->config = $this->app->config;
        $this->request = $this->app->request;

        $model = str_replace('Controller', 'Model', get_class($this));

        if (class_exists($model)) {
            // Controler has its own model
            $this->model = new $model;
        } else {
            // General model
            $this->model = new Model;
        }

        $this->view = $this->app->view;
        $this->Layout = 'default';

        $controller = str_replace('Controller\\', '', get_class($this));

        $this->config->loadNamespace('Controller.'.$controller,
                                     APP_DIR . DS . 'Controller' . DS . $controller . '.config.php',
                                     FALSE);

        if ($returnto = $this->app->request->get('returnto')) {
            Session::set('returnto', $returnto);
        }

        if (Session::get('user') == $this->config->get('Admin.User')) {
            // Ok, we have a validated user session
            $this->User = Session::get('user');
        }

        $this->view->User = $this->User;
        $this->view->Embedded = $this->app->request->get('embedded');
        $controller = str_replace('Controller\\', '', get_class($this));

        $this->view->BaseDir = array(
            APP_DIR . DS . 'View' . DS . $controller . DS . 'custom',
            APP_DIR . DS . 'View' . DS . $controller,
            APP_DIR . DS . 'View' . DS . 'custom',
            APP_DIR . DS . 'View'
        );

        $this->view->Menu = PVLng::getMenu();
        $this->view->Languages = PVLng::getLanguages();
    }

    /**
     *
     */
    public function after() {
        /* For Logout */
        $this->view->User = $this->User;
        if ($this->User) $this->view->APIkey = (new \ORM\Config)->getAPIkey();
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
        $bk = BabelKitMySQLi::getInstance();

        $this->view->DateTimeFormat      = $bk->render('locale', LANGUAGE, 'DateTime');
        $this->view->DateTimeFormatShort = $bk->render('locale', LANGUAGE, 'DateTimeShort');
        $this->view->DateFormat          = $bk->render('locale', LANGUAGE, 'Date');
        $this->view->DateFormatShort     = $bk->render('locale', LANGUAGE, 'DateShort');
        $this->view->TimeFormat          = $bk->render('locale', LANGUAGE, 'Time');
        $this->view->TimeFormatShort     = $bk->render('locale', LANGUAGE, 'TimeShort');
        $this->view->MonthFormat         = $bk->render('locale', LANGUAGE, 'MonthDefault');
        $this->view->TSep                = $bk->render('locale', LANGUAGE, 'ThousandSeparator');
        $this->view->DSep                = $bk->render('locale', LANGUAGE, 'DecimalPoint');
        $this->view->DateFormatJS        = $bk->render('locale', LANGUAGE, 'DateJS');
        $this->view->Year                = date('Y');

        if ($this->config->get('develop')) {
            $this->view->Branch = shell_exec('git branch | grep \'*\' | cut -b3-');
            $this->view->Development = TRUE;
        }
        $this->view->Language = LANGUAGE;

        $this->view->CurrencyISO = $this->config->get('Currency.ISO');
        $this->view->CurrencySymbol = $this->config->get('Currency.Symbol');
        $this->view->CurrencyDecimals = $this->config->get('Currency.Decimals');

        $this->view->Title = $this->config['Title'];

        $controller = str_replace('Controller\\', '', get_class($this));

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
        $this->view->MySQLVersion = $this->model->getDatabaseVersion();
        $this->view->ServerName = $_SERVER['HTTP_HOST'];
        $this->view->ServerVersion = $_SERVER['SERVER_SOFTWARE'];

        // Put all controller specific config also into view
        if ($cfg = $this->config->get('Controller.'.$controller)) {
            foreach ($cfg as $key=>$value) {
                $this->view->set($controller.'_'.$key, $value);
            }
        }

        // Check for new version once a hour
        if (Session::get('VersionCheck', 0)+3600 < time()) {
            $version = $this->checkVersion();
            $this->db->VersionNew = isset($version[0]) ? $version[0] : FALSE;
            Session::set('VersionCheck', time());
        }
        $v = $this->db->VersionNew;
        if ($v AND $v != PVLNG_VERSION) $this->view->VersionNew = $v;

        // Missing files are ok
        $this->view->append('Head', $this->view->fetch('head.tpl'));
        $this->view->append('Head', $this->view->fetch('head.'.$action.'.tpl'));

        // Styles
        $file = APP_DIR . DS . 'View' . DS . $controller . DS . 'style.css';
        if (file_exists($file)) {
            $this->view->append('Styles', $this->view->fetch($file));
        }

        $file = APP_DIR . DS . 'View' . DS . $controller . DS . 'style.' . $action . '.css';
        if (file_exists($file)) {
            $this->view->append('Styles', $this->view->fetch($file));
        }

        // Missing files are ok
        $this->view->assign('Content', 'content.'.$action.'.tpl');
        $this->view->assign('Content', 'content.tpl');

        // Missing files are ok
        $this->view->append('Scripts', $this->view->fetch('script.js'));
        $this->view->append('Scripts', $this->view->fetch('script.'.$action.'.js'));

        $this->view->display($this->Layout.'.tpl');
    }

    /**
     * Helper function to convert array of db result rows into well formed
     * array for a view
     *
     * @param array $rows
     */
    public function rows2view( $rows ) {
        $data = array();
        foreach ($rows as $row) {
            $data[] = array_change_key_case((array) $row, CASE_UPPER);
        }
        return $data;
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
    protected function checkVersion() {
        $url = "https://raw.github.com/K-Ko/PVLng/master/.version";

        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2);
        curl_setopt($ch, CURLOPT_TIMEOUT, 2);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

        $version = explode("\n", curl_exec($ch));

        curl_close($ch);

        return $version;
    }

}
