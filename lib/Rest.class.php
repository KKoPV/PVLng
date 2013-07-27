<?php
/**
 *
 * @author      Knut Kohl <github@knutkohl.de>
 * @copyright   2012-2013 Knut Kohl
 * @license     GNU General Public License http://www.gnu.org/licenses/gpl.txt
 * @version     $Id$
 */
class Rest {

	/**
	 *
	 */
	const GET    = 'GET';
	const POST	 = 'POST';
	const PUT    = 'PUT';
	const DELETE = 'DELETE';

	/**
	 *
	 */
	public $ForceDataArray = FALSE;

	/**
	 *
	 */
	public $IgnoreJSONError = FALSE;

	/**
	 *
	 */
	public $ContentType = 'text/plain';

	/**
	 *
	 */
	public function process() {

		$this->method = strtoupper($_SERVER['REQUEST_METHOD']);

		switch ($this->method) {

			// ---------------
			case self::GET:
				$this->request = $this->cleanInputs($_GET);
				break;

			// ---------------
			case self::POST:
				$this->request = $this->cleanInputs($_POST);
				break;

			// ---------------
			case self::PUT:
			case self::DELETE:
			    $request = file_get_contents('php://input');
			    // Try to interpret as raw JSON
   	    		$this->request = json_decode($request, TRUE);
                if (json_last_error() != JSON_ERROR_NONE) {
                    // assume parameter=value data
    				parse_str($request, $this->request);
                }

                // Deep clean
				$this->request = $this->cleanInputs($this->request);
				break;

			// ---------------
			default:
				$this->response(406, 'Only PUT, GET, POST and DELETE are supported.');
		}

		if (isset($this->request['data'])) {
			$data = json_decode($this->request['data'], $this->ForceDataArray);

			$error = json_last_error();
			if (!$this->IgnoreJSONError AND $error != JSON_ERROR_NONE)
				$this->response(400, JSON::check($error));

			$this->data = ($error == JSON_ERROR_NONE) ? $data : $this->request['data'];
		}

		$this->request['format'] = '';

		if (empty($this->request['format']) AND
				isset($_SERVER['PATH_INFO']) AND $_SERVER['PATH_INFO']) {
			$this->path_info = explode('/', trim($_SERVER['PATH_INFO'], '/'));
			$last = count($this->path_info)-1;
			if ($last AND preg_match('~^(.*?)\.([^.]+)$~', $this->path_info[$last], $args)) {
				// Extract format from last path info part
				$this->path_info[$last]	= $args[1];
				$this->request['format'] = $args[2];
			}
		}

		if (isset($_SERVER['HTTP_ACCEPT'])) {

			$accept = array();

			// break up string into pieces (content type and q factors)
			if (preg_match_all('~([^;, ]+)\s*(?:;\s*q\s*=\s*([.0-9]+),?)?~i',
			                   $_SERVER['HTTP_ACCEPT'], $args, PREG_SET_ORDER)) {

				foreach ($args as $arg) {
					// set default to 1 for any without q factor
					$accept[$arg[1]] = isset($arg[2]) ? $arg[2] : 1;
				}
				// sort list based on q factor
				arsort($accept, SORT_NUMERIC);

				foreach (array_keys($accept) as $key) {
					switch ($key) {
						// ----------------------
						case 'application/json':
    					    $this->ContentType = $key;
							$this->request['format'] = 'json';
							break 2; // switch & foreach
						// ----------------------
						case 'application/csv':
    					    $this->ContentType = $key;
							$this->request['format'] = 'csv';
							break 2; // switch & foreach
						// ----------------------
						case 'application/tsv':
    					    $this->ContentType = $key;
							$this->request['format'] = 'tsv';
							break 2; // switch & foreach
						// ----------------------
						case 'application/xml':
    					    $this->ContentType = $key;
							$this->request['format'] = 'xml';
							break 2; // switch & foreach
						// ----------------------
						case 'text/plain':
    					    $this->ContentType = $key;
							$this->request['format'] = 'text';
							break 2; // switch & foreach
					}
				}
			}
		}
	}

	/**
	 *
	 */
	public function response( $code=200, $data='' ) {
		Header('HTTP/1.1 '.$this->StatusMessage($code));
		Header('Content-Type: '.$this->ContentType.'; charset=UTF-8');
		if ($data != '') {
			Header('Content-Length: '.strlen($data));
            die($data);
		} else {
			exit;
		}
	}

	/**
	 *
	 */
	public function RequestMethod() {
		return $this->method;
	}

	/**
	 *
	 */
	public function Request( $field=NULL ) {
		if (isset($field))
			return array_key_exists($field, $this->request) ? $this->request[$field] : NULL;
		else
			return $this->request;
	}

	/**
	 *
	 */
	public function PathInfo() {
		return $this->path_info;
	}

	/**
	 *
	 */
	public function Data() {
		return $this->data;
	}

	/**
	 *
	 */
	public function Referer() {
		return $_SERVER['HTTP_REFERER'];
	}

	/**
	 *
	 */
	public function StatusMessage( $code ) {
		$status = array(
			100 => 'Continue',
			101 => 'Switching Protocols',
			200 => 'OK',
			201 => 'Created',
			202 => 'Accepted',
			203 => 'Non-Authoritative Information',
			204 => 'No Content',
			205 => 'Reset Content',
			206 => 'Partial Content',
			300 => 'Multiple Choices',
			301 => 'Moved Permanently',
			302 => 'Found',
			303 => 'See Other',
			304 => 'Not Modified',
			305 => 'Use Proxy',
			306 => '(Unused)',
			307 => 'Temporary Redirect',
			400 => 'Bad Request',
			401 => 'Unauthorized',
			402 => 'Payment Required',
			403 => 'Forbidden',
			404 => 'Not Found',
			405 => 'Method Not Allowed',
			406 => 'Not Acceptable',
			407 => 'Proxy Authentication Required',
			408 => 'Request Timeout',
			409 => 'Conflict',
			410 => 'Gone',
			411 => 'Length Required',
			412 => 'Precondition Failed',
			413 => 'Request Entity Too Large',
			414 => 'Request-URI Too Long',
			415 => 'Unsupported Media Type',
			416 => 'Requested Range Not Satisfiable',
			417 => 'Expectation Failed',
			500 => 'Internal Server Error',
			501 => 'Not Implemented',
			502 => 'Bad Gateway',
			503 => 'Service Unavailable',
			504 => 'Gateway Timeout',
			505 => 'HTTP Version Not Supported'
		);

		return isset($status[$code])
				 ? $code . ' ' . $status[$code]
				 : '500 ' . $status[500];
	}

	// -------------------------------------------------------------------------
	// PROTECTED
	// -------------------------------------------------------------------------

	/**
	 *
	 */
	protected $request = array();

	/**
	 *
	 */
	protected $data = array();

	/**
	 *
	 */
	protected $path_info = array();

	/**
	 *
	 */
	protected $method = '';

	/**
	 *
	 */
	protected function cleanInputs($data) {
		$clean_input = array();
		if (is_array($data)) {
			foreach ($data as $k => $v) {
                $clean_input[$k] = $this->cleanInputs($v);
			}
		} else {
			if (get_magic_quotes_gpc()) {
				$data = trim(stripslashes($data));
			}
			$clean_input = trim(strip_tags($data));
		}

		return $clean_input;
	}

}
