<?php
/**
 *
 * @author      Knut Kohl <github@knutkohl.de>
 * @copyright   2012-2013 Knut Kohl
 * @license     GNU General Public License http://www.gnu.org/licenses/gpl.txt
 * @version     1.0.0
 */
namespace slimMVC;

/**
 *
 *
 * @author      Knut Kohl <github@knutkohl.de>
 * @copyright   2012-2013 Knut Kohl
 * @license     GNU General Public License http://www.gnu.org/licenses/gpl.txt
 * @version     1.0.0
 */
class View extends \Slim\View {

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

        $this->app = App::getInstance();
        $this->dataPointer =& $this->data;

        $this->Helper = new ViewHelper;
        $this->Helper->numf = function( $number, $decimals=0 ) {
            return number_format($number, $decimals, \I18N::translate('DSEP'), \I18N::translate('TSEP'));
        };
    }

    /**
     *
     */
    public function render( $template ) {
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

        if ($TplFile == '') return;

        $TplFile = realpath($TplFile);
        $TplCompiled = str_replace(APP_DIR, '', $TplFile);
        $TplCompiled = str_replace('/', '~', $TplCompiled);
        $TplCompiled = trim($TplCompiled, '~');
        $TplCompiled = TEMP_DIR . DS . $TplCompiled . '.php';

        $reuse = $this->app->config->get('View.ReuseCode');

        if (!$reuse OR !file_exists($TplCompiled) OR
            filemtime($TplCompiled) < filemtime($TplFile)) {
            $html = php_strip_whitespace($TplFile);
            $this->compile($html);
        }

        if (isset($html) AND $reuse) file_put_contents($TplCompiled, $html);

        // save data
        $this->dataStack[] =& $this->data;

        ob_start();

        if ($reuse) {
            include $TplCompiled;
        } else {
            eval('?'.'>'.$html);
        }

        return ob_get_clean();
    }

    /**
     * Assign template content to a variable
     *
     * The file will be parsed like other templates
     *
     * @param string $name Varible name
     * @param string $template Template file name
     */
    public function assign( $name, $template ) {
        $content = $this->fetch($template);
        if (strlen($content)) $this->set($name, $content);
    }

    /**
     * Assign template content to a variable
     *
     * The file will be parsed like other templates
     *
     * @param string $name Varible name
     * @param string $value File name
     */
    public function append( $name, $value ) {
        $this->set($name, $this->get($name) . $value);
    }

    /**
     *
     */
    public function set( $name, $value=NULL ) {
        if (is_array($name)) {
            foreach ($name as $key=>$value) {
                $this->set($key, $value);
            }
        } else {
            $this->dataPointer[strtoupper($name)] = $value;
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
    public function get( $name ) {
        $name = strtoupper($name);
        if (substr($name, 0, 2) == '__') {
            // Top level variable
            $name = substr($name, 2);
            return isset($this->data[$name]) ? $this->data[$name] : '';
        } else {
            return isset($this->dataPointer[$name]) ? $this->dataPointer[$name] : '';
        }
    }

    /**
     *
     */
    public function __get( $name ) {
        return $this->get($name);
    }

    // -------------------------------------------------------------------------
    // PROTECTED
    // -------------------------------------------------------------------------

    /**
     *
     */
    protected $app;

    /**
     *
     */
    protected $data = array();

    /**
     *
     */
    protected $dataPointer;

    /**
     *
     */
    protected $dataStack = array();

    /**
     *
     */
    protected function compile( &$html ) {
        if (strpos($html, '<!-- COMPILE OFF -->') === FALSE) {

            // <!-- INCLUDE template.tpl -->
            if (preg_match_all('~<!-- INCLUDE (.*?) -->~', $html, $args, PREG_SET_ORDER)) {
                foreach ($args as $inc) {
                    $html = str_replace($inc[0], '<?php $this->display(\''.$inc[1].'\'); ?'.'>', $html);
                }
            }

            // <!-- DEFINE MACRO name -->...<!-- END DEFINE -->
            if (preg_match_all('~<!-- DEFINE MACRO (.+?) -->(.+?)<!-- END DEFINE -->~s', $html, $args, PREG_SET_ORDER)) {
                foreach ($args as $macro) {
                    // Remove macro definition
                    $html = str_replace($macro[0], '', $html);
                    // Replace all macro uses
                    $html = str_replace('<!-- MACRO '.$macro[1].' -->', $macro[2], $html);
                }
            }

            // mask masked delimiters
            $html = str_replace(array('\{', '\}'), array("\x01", "\x02"), $html);

            // Translations
            if (preg_match_all('~\{\{([^}]+?)\}\}~', $html, $args, PREG_SET_ORDER)) {
                foreach ($args as $data) {
                    $html = str_replace($data[0], $this->e('I18N::translate(\''.$data[1].'\')'), $html);
                }
            }

            // <!-- (ELSE)?IF ... -->...<!-- ELSE -->...<!-- ENDIF -->
            if (preg_match_all('~<!-- (ELSE)?IF (.*?) -->~', $html, $args, PREG_SET_ORDER)) {
                foreach ($args as $if) {
                    if (preg_match_all('~'.$this->RegexVar.'~', $if[2], $matches, PREG_SET_ORDER)) {
                        foreach ($matches as $match) {
                            $if[2] = str_replace($match[0], '$this->__get(\''.$match[1].'\')', $if[2]);
                        }
                    }
                    $html = str_replace($if[0], '<?php '.$if[1].'IF ('.$if[2].'): ?'.'>', $html);
                }
                $html = str_replace('<!-- ELSE -->', '<?php ELSE: ?'.'>', $html);
                $html = str_replace('<!-- ENDIF -->', '<?php ENDIF; ?'.'>', $html);
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
            if (preg_match_all('~<!-- BEGIN (\w[\w\d_]*) -->~', $html, $args, PREG_SET_ORDER)) {
                foreach ($args as $match) {
                    $id = rand(100, 999);
                    $html = str_replace($match[0], '<?php '
                          . 'if ($_'.$id.' = $this->__get(\''.$match[1].'\')): '
                          . 'array_push($this->dataStack, $this->dataPointer); '
                          . 'foreach ($_'.$id.' as &$this->dataPointer):'
                          . ' ?'.'>', $html);
                }
            }

            $html = preg_replace('~<!-- END .*?-->~',
                                 '<?php endforeach; '
                                .'$this->dataPointer = array_pop($this->dataStack); '
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
        return '<?php echo ' . $str . '; ?'.'>';
    }

    /**
     *
     */
    protected function compressCode( &$html ) {
        if (!$this->app->config->get('View.Verbose')) {
            $pre = array();
            // mask <pre>, <code> and <tt> sequences
            if (preg_match_all('~<(pre|code|tt).*?</\\1>~is', $html, $matches, PREG_SET_ORDER)) {
                foreach ($matches as $match) {
                    $hash = md5($match[0]);
                    $pre[$hash] = $match[0];
                    $html = str_replace($match[0], $hash, $html);
                }
            }

            // Remove HTML comments
            $html = preg_replace('~<!--.*?-->~s', '', $html);
            // Remove JS/PHP comments
            $html = preg_replace('~/\*.*?\*/~s', '', $html);
            // Replace multiple white spaces/new lines with one space
            $html = preg_replace('~\s{2,}~s', ' ', $html);
            // Remove whitespaces between tags
            $html = preg_replace('~>\s+<~s', '><', $html);

            /* remove pairs of ?><?php */
            $html = preg_replace('~\s*\?'.'><\?php\s*~', ' ', $html);

            // Restore <pre>...</pre> sections
            $html = str_replace(array_keys($pre), array_values($pre), $html);
        }

        if ($xy = $this->app->config->get('View.InlineImages') AND
            preg_match_all('~<img [^>]*src=(["\'])([^"\']+?)\\1~', $html, $images, PREG_SET_ORDER)) {
            foreach ($images as $img) {
                $file = BASE_DIR . DS . $img[2];
                if (file_exists($file) AND
                    $info = getimagesize(BASE_DIR . DS . $img[2]) AND
                    $info[0] <= $xy AND $info[1] <= $xy) {
                    $html = str_replace($img[2],
                            'data:'.$info['mime'].';base64,'.base64_encode(file_get_contents($file)),
                            $html);
                }
            }
        }

        $html = trim($html);
    }

}
