<?php
/**
 *
 * @author      Knut Kohl <github@knutkohl.de>
 * @copyright   2012-2013 Knut Kohl
 * @license     GNU General Public License http://www.gnu.org/licenses/gpl.txt
 * @version     $Id$
 */
namespace yMVC;

/**
 *
 *
 * @author      Knut Kohl <github@knutkohl.de>
 * @copyright   2012-2013 Knut Kohl
 * @license     GNU General Public License http://www.gnu.org/licenses/gpl.txt
 * @version     $Id$
 */
abstract class View extends Base {

	/**
	 *
	 */
	public $filename;

	/**
	 *
	 */
	public $RegexVar = '\{([A-Z_][A-Z_0-9]*)\}';

	/**
	 *
	 */
	public $BaseDir = array('.');

	/**
	 *
	 */
	public $Helper;

	/**
	 *
	 */
	public function __construct() {
		parent::__construct();
		$this->Helper = new ViewHelper;
		$this->_vPointer =& $this->_vars;
		$this->Plain = (isset($_GET['plain']) AND $_GET['plain']);

	}

	/**
	 *
	 */
	public function output() {
		if ($this->filename != '' AND !$this->Plain) {
			Header('Cache-Control: no-cache, must-revalidate');
			Header('Expires: Sat, 01 Jan 2000 00:00:00 GMT');
			Header('Content-Disposition: attachment; filename=' . $this->filename);
		}

		if ($this->Plain) $this->ContentType = 'text/plain';

		Header('Content-Type: ' . $this->ContentType . '; charset=UTF-8');
		Header('Content-Length: ' . strlen($this->content));

		echo $this->content;
	}

	/**
	 *
	 */
	public function render( $template ) {
		ob_start();
		$this->show($template);
		return ob_get_clean();
	}

	/**
	 *
	 */
	public function show( $template ) {
		if (file_exists($template)) {
			// Concrete file
			$TplFile = $template;
		} else {
			$TplFile = '';
			// Search template in defined directories
			foreach ((array)$this->BaseDir as $dir) {
				if (file_exists($dir.'/'.$template)) {
					$TplFile = $dir.'/'.$template;
					break;
				}
			}
		}

		if ($TplFile == '') return '';

		$TplFile = realpath($TplFile);
		$TplCompiled = str_replace(APP_DIR, '', $TplFile);
		$TplCompiled = str_replace('/', '~', $TplCompiled);
		$TplCompiled = trim($TplCompiled, '~');

		$TplCompiled = TEMP_DIR . DS . $TplCompiled;
		$reuse = $this->config->get('View.ReuseCode');

		if (!$reuse OR
				!file_exists($TplCompiled) OR
				filemtime($TplCompiled) < filemtime($TplFile)) {
			$html = file_get_contents($TplFile);
			$this->compile($html);
		}

		$cmt = array(
			array('<!--', '-->'),
			array('/*',	 '*/')
		);

		if (isset($html) AND $reuse) {
			if ($this->config->get('View.Verbose')) {
				$c = (substr($TplFile, -4) == '.css') ? $cmt[1] : $cmt[0];
				$html = $c[0] . ' ' . $TplFile . ' >>> ' . $c[1] . PHP_EOL
				      . $html . PHP_EOL
				      . $c[0] . ' <<< ' . $TplFile . ' ' . $c[1] . PHP_EOL;
			}
			file_put_contents($TplCompiled, $html);
		}

		// save data
		$this->_stack[] =& $this->_vars;

		if ($reuse) {
			include $TplCompiled;
		} else {
			eval('?'.'>'.$html);
		}
	}

	/**
	 * Assign template content to a variable
	 *
	 * The file will be parsed like other templates
	 *
	 * @param string $name Varible name
	 * @param string $file File name
	 */
	public function assign( $name, $file ) {
		$this->__set($name, $this->render($file));
	}

	/**
	 *
	 */
	public function set( $name, $value=NULL ) {
		if (is_array($name)) {
			foreach ($name as $key=>$value) {
				$this->__set($key, $value);
			}
		} else {
			$this->_vPointer[strtoupper($name)] = $value;
		}
	}

	/**
	 *
	 */
	public function __set( $name, $value ) {
		$this->set($name, $value);
	}

	/**
	 *
	 */
	public function __get( $name ) {
		$name = strtoupper($name);
		if (substr($name, 0, 2) == '__') {
			$name = substr($name, 2);
			return isset($this->_vars[$name]) ? $this->_vars[$name] : '';
		} else {
			return isset($this->_vPointer[$name]) ? $this->_vPointer[$name] : '';
		}
	}

	// -------------------------------------------------------------------------
	// PROTECTED
	// -------------------------------------------------------------------------

	/**
	 *
	 */
	protected $ContentType = 'text/html';

	/**
	 *
	 */
	protected $Plain;

	/**
	 *
	 */
	protected $_vars = array();

	/**
	 *
	 */
	protected $_vPointer;

	/**
	 *
	 */
	protected $_stack = array();

	/**
	 *
	 */
	protected function compile( &$html ) {
		if (strpos($html, '<!-- COMPILE OFF -->') === FALSE) {

			// mask masked delimiters
			$html = str_replace(array('\{', '\}'), array("\x01", "\x02"), $html);

			// Translations
			if (preg_match_all('~\{\{([^}]+?)\}\}~', $html, $args, PREG_SET_ORDER)) {
				foreach ($args as $data) {
					$html = str_replace($data[0], $this->e('I18N::_(\''.$data[1].'\')'), $html);
				}
			}

			// <!-- IF ... -->...<!-- ELSE -->...<!-- ENDIF -->
			if (preg_match_all('~<!-- IF (.*?) -->~', $html, $ifs, PREG_SET_ORDER)) {
				foreach ($ifs as $if) {
					if (preg_match_all('~'.$this->RegexVar.'~', $if[1], $matches, PREG_SET_ORDER)) {
						foreach ($matches as $match) {
							$if[1] = str_replace($match[0], '$this->__get(\''.$match[1].'\')', $if[1]);
						}
					}
					$html = str_replace($if[0], '<?php if ('.$if[1].'): ?'.'>', $html);
				}
				$html = str_replace('<!-- ELSE -->', '<?php else: ?'.'>', $html);
				$html = str_replace('<!-- ENDIF -->', '<?php endif; ?'.'>', $html);
			}

			// Functions
			# $reg = '~( ( (\") ( [^\"] | \\\\. )* \\3 | ( [^,] | \\\\. )* ),\s* )~x';
			$reg = '~( ( (\") ( [^\"] )* \\3 | ( [^,] )* ),\s* )~x';

			// function calls
			if (preg_match_all('~\{(\w+):([^}]*)\}~', $html, $matches, PREG_SET_ORDER)) {

				foreach ($matches as $match) {

					list(, $func, $data) = $match;

					$args = '';

					// function parameters
					if ($data != '' AND preg_match_all($reg, $data.',', $params, PREG_SET_ORDER)) {
						foreach ($params as $param) {
							$args .= ',' . (
							  $param[4] != ''
							    // escaped value == constant
							  ? '\''.trim($param[2],'"').'\''
							    // OR template variable
							  : '$this->__get(\''.$param[2].'\')'
							);
						}
					}

					if ($this->Helper->callable($func)) $func = '$this->Helper->'.$func;

					$html = str_replace($match[0],
					                    $this->e($func.'('.substr($args, 1).')'),
					                    $html);
				}
			}

			// Loops
			if (preg_match_all('~<!-- BEGIN (\w[\w\d_]*) -->~', $html, $matches, PREG_SET_ORDER)) {
				foreach ($matches as $match) {
					$id = rand(100, 999);
					$html = str_replace($match[0], '<?php '
					      . 'if ($_'.$id.' = $this->__get(\''.$match[1].'\')): '
					      . '$this->_stack[] =& $this->_vPointer; '
					      . 'foreach ($_'.$id.' as &$this->_vPointer):'
					      . ' ?'.'>', $html);
				}
			}

			$html = str_replace('<!-- END -->',
			                    '<?php endforeach; '
			                   .'$this->_vPointer = array_pop($this->_stack); '
			                   .'endif; ?'.'>',
			                    $html);

			// Variables
			$html = preg_replace('~'.$this->RegexVar.'~e',
			                     '$this->e(\'$this->__get(\\\'$1\\\')\')', $html);

			$html = str_replace('\ ', "&nbsp;", $html);

			// unmask masked delimiters
			$html = str_replace(array("\x01", "\x02"), array('{', '}'), $html);

		}

		$this->compressCode($html);
	}

	/**
	 *
	 */
	protected function e( $str ) {
		return '<?php echo ' . $str . ' ?'.'>';
	}

	/**
	 *
	 */
	protected function compressCode( &$html ) {
		if (!$this->config->View_Verbose) {
			$pre = array(array(), array());
			// mask <pre>...</pre>
			if (preg_match_all('~<pre.*?</pre>~is', $html, $matches)) {
				foreach ($matches as $match) {
					$pre[0][] = md5($match[0]);
					$pre[1][] = $match[0];
				}
				$html = str_replace($pre[1], $pre[0], $html);
			}
			$html = preg_replace('~<!--.*?-->~s', '', $html);
			$html = preg_replace('~/\*.*?\*/~s', '', $html);
			$html = preg_replace('~\s+~s', ' ', $html);
			$html = str_replace($pre[0], $pre[1], $html);
		}
		$html = trim($html);
	}

}