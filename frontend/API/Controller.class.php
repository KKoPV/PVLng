<?php
/**
 *
 *
 * @author      Knut Kohl <github@knutkohl.de>
 * @copyright   2012-2013 Knut Kohl
 * @license     GNU General Public License http://www.gnu.org/licenses/gpl.txt
 * @version     $Id$
 */
class API_Controller extends Controller {

	/**
	 *
	 */
	public function before() {

		try {
			$this->Rest = new Rest;
			$this->Rest->ForceDataArray = TRUE;
			$this->Rest->IgnoreJSONError = TRUE;
			$this->Rest->process();

			switch ($this->Rest->RequestMethod()) {
				// --------------
				case Rest::PUT:
					// check API key
					if (!isset($_SERVER['HTTP_X_PVLNG_KEY'])) {
						throw new \Exception(I18N::_('MissingAPIkey'), 401);
					} elseif (trim($_SERVER['HTTP_X_PVLNG_KEY']) != $this->model->getAPIkey()) {
						throw new \Exception(I18N::_('NotAuthorized'), 401);
					}
					break;
				// --------------
				case Rest::GET:
					// nothing to do
					break;
				// --------------
				default:
					// not allow request method
					throw new Exception('Unsupported request method: '.$this->Rest->RequestMethod(), 405);
			}
		} catch(Exception $exception) {
			$this->ErrorResponse($exception);
		}
	}

	/**
	 *
	 */
	public function Index_r1_Action() {

		try {

			$PathInfo = $this->Rest->PathInfo();

			// parameter id 2 is the GUID
			if (!isset($PathInfo[2])) throw new Exception('Missing GUID', 400);

			$channel = Channel::byGUID($PathInfo[2]);

			switch ($this->Rest->RequestMethod()) {

				// ------------
				case Rest::PUT:
				    if ($data = $this->Rest->Data()) {
						if ($channel->write($data)) {
							// Created
							throw new Exception('', 201);
						}
					} elseif ($batch = $this->Rest->Request('batch')) {
						$readings = array();
					    foreach (explode(';', $batch) as $tupel) {
							if ($tupel == '') continue;

					        $data = explode(',', $tupel);
					        if (count($data) == 2) {
						        // timestamp and data
								$readings[$data[0]] = $data[1];
							} elseif (count($data) == 3) {
						        // date, time and data
								$timestamp = strtotime($data[0] . ' ' . $data[1]);
								if ($timestamp === false) {
									throw new Exception('Invalid timestamp in data: '.$tupel, 400);
								}
								$readings[$timestamp] = $data[2];
							} else {
								throw new Exception('Invalid batch data: '.$tupel, 400);
							}
						}
					    $res = 0;
						foreach ($readings as $timestamp=>$data) {
					        $res += $channel->write($data, $timestamp);
                        }
						if ($res) {
							// Created
							throw new Exception('Rows inserted: '.$res, 201);
						}
					}
					// Accepted but not saved (inside update interval)
					throw new Exception('', 200);
					break;

				// ------------
				case Rest::GET:
					// remove the 1st 3 parameters: api/r1/<GUID>
					$request = array_merge($this->Rest->Request(), array_slice($PathInfo, 3));

					if (preg_match('~(\w+)$~', $request['format'], $args)) {
						$ViewClass = 'yMVC\View\\'.strtoupper($args[1]);
					} else {
						// Default
						$ViewClass = 'yMVC\View\CSV';
					}
					if (!class_exists($ViewClass))
						throw new Exception('Unsupported request format, '
															 .'missing class: '.$ViewClass.')', 400);

					$this->view = new $ViewClass;
					$this->view->content = array_key_exists('attributes', $request)
															 ? $channel->getAttributes($request['attributes'])
															 : $channel->read($request, TRUE);
					break;
			}

		} catch(Exception $exception) {
			$this->ErrorResponse($exception);
		}
	}

	/**
	 *
	 */
	public function Log_Action() {
		$request = $this->Rest->Request();

		if ($this->Rest->Request('message')) {
			$this->log($this->Rest->Request('message'), $this->Rest->Request('scope'));
		}

		$this->Rest->response(200);
	}

	// -------------------------------------------------------------------------
	// PROTECTED
	// -------------------------------------------------------------------------

	/**
	 *
	 */
	protected $Rest;

	/**
	 *
	 */
	protected function ErrorResponse( Exception $exception ) {
		$code = $exception->getCode() ?: 500;
		$msg  = $exception->getMessage();
		$msg  = $this->Rest->StatusMessage($code) . ( $msg ? ' - ' . $msg : '' );
		$this->Rest->response($code, $msg);
	}

}