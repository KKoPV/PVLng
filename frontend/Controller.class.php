<?php
/**
 *
 *
 * @author      Knut Kohl <github@knutkohl.de>
 * @copyright   2012-2013 Knut Kohl
 * @license     GNU General Public License http://www.gnu.org/licenses/gpl.txt
 * @version     $Id: v1.0.0.2-14-g2a8e482 2013-05-01 20:44:21 +0200 Knut Kohl $
 */
class Controller extends yMVC\Controller {

	/**
	 *
	 */
	public function __construct() {
		parent::__construct();

		$this->db = yMVC\MySQLi::getInstance();

		BabelKitMySQLi::setParams(array('table' => 'pvlng_babelkit'));
		BabelKitMySQLi::setDB($this->db);

		I18N::setBabelKit(BabelKitMySQLi::getInstance());
		I18N::setLanguage(LANGUAGE);
		I18N::setCodeSet('app');
		I18N::setAddMissing($this->config->I18N_Add);
		if ($this->config->I18N_Mark) I18N::setMarkMissing();

		NestedSet::Init(array (
			'db' => $this->db,
			'debug'	=> true,
			'lang'	=> 'de',
			'path'	=> LIB_DIR . DS . 'contrib' . DS . 'messages',
			'db_table' => array (
				'tbl' => 'pvlng_tree',
				'nid' => 'id',
				'l'   => 'lft',
				'r'   => 'rgt',
				'mov' => 'moved',
				'pay' => 'entity'
			)
		));
		Registry::set('ns', NestedSet::getInstance());
	}

	/**
	 * Main logic here, because here is the view assigned!
	 */
	public function before() {
		parent::before();

		$this->config->load(APP_DIR . DS . 'config.php', FALSE);

		$this->config->loadNamespace('Controller.'.$this->router->Controller,
		                             APP_DIR . DS . $this->router->Controller . DS . 'config.php',
		                             FALSE);

		if ($returnto = $this->request('returnto')) {
		    Session::set('returnto', $returnto);
		}
	}

	/**
	 *
	 */
	public function after_POST() {
		if ($returnto = Session::get('returnto')) {
		    Session::set('returnto');
		    $this->redirect($returnto);
		}
		parent::after_POST();
	}

	/**
	 *
	 */
	public function after() {
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

		$this->view->Language = LANGUAGE;

		$this->view->CurrencyISO = $this->config->Currency_ISO;
		$this->view->CurrencySymbol = $this->config->Currency_Symbol;
		$this->view->CurrencyDecimals = $this->config->Currency_Decimals;

		$this->view->BaseDir = array(
			APP_DIR . DS . $this->router->Controller . DS . 'tpl' . DS . 'custom',
			APP_DIR . DS . $this->router->Controller . DS . 'tpl',
			APP_DIR . DS . 'tpl' . DS . 'custom',
			APP_DIR . DS . 'tpl'
		);

		$this->view->Year = date('Y');

		$messages = array();
		foreach (Messages::getRaw() as $message) {
			$messages[] = array_change_key_case($message, CASE_UPPER);
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
		if ($cfg = $this->config->get('Controller.'.$this->router->Controller)) {
			foreach ($cfg as $key=>$value) {
				$this->view->set($this->router->Controller.'_'.$key, $value);
			}
		}

		// Check for new version once a day
		if (!API AND Session::get('VersionCheck', 0)+86400 < time()) {
			$version = $this->checkVersion();
			$this->db->VersionNew = isset($version[0]) ? $version[0] : FALSE;
			Session::set('VersionCheck', time());
		}
		$v = $this->db->VersionNew;
		if ($v AND $v != PVLNG_VERSION) $this->view->VersionNew = $v;

		if (Session::get('user') == $this->config->Admin_User) {
			// Ok, we have a validated user session
			$this->view->User = Session::get('user');
		}

   		parent::after();
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

	/**
	 *
	 */
	protected function log( $msg, $scope='frontend' ) {
	    static $log;

		if (!$log) $log = new PVLng\Log;

		$log->scope = $scope;
		$log->data = $msg;
		$log->insert();
	}

}
