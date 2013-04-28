<?php
/**
 *
 *
 * @author      Knut Kohl <github@knutkohl.de>
 * @copyright   2012-2013 Knut Kohl
 * @license     GNU General Public License http://www.gnu.org/licenses/gpl.txt
 * @version     $Id$
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

		$this->config->loadNamespace('Controller.'.$this->router->Controller,
		                             APP_DIR . DS . $this->router->Controller . DS . 'config.php',
		                             FALSE);
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

		$this->view->BaseDir = array(
			APP_DIR . DS . $this->router->Controller . DS . 'tpl',
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
            $version = file('http://pvlng.com/version', FILE_IGNORE_NEW_LINES);
			$this->db->VersionNew = $version[0];
		    Session::set('VersionCheck', time());
		}
		$v = $this->db->VersionNew;
		if ($v AND $v != PVLNG_VERSION) $this->view->VersionNew = $v;

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
	protected function log( $msg, $scope='frontend' ) {
	    static $log;

		if (!$log) $log = new PVLng\Log;

		$log->scope = $scope;
		$log->data = $msg;
		$log->insert();
	}

}