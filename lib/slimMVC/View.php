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
    public $RegexVar = '\{([A-Z_][A-Z0-9_.]*)\}';

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

        $this->app->container->singleton('JavaScriptPacker', function() {
            return new JavaScriptPacker(0);
        });

        $this->dataPointer =& $this->data;

        $this->Helper = new ViewHelper;
        $this->Helper->numf = function( $number, $decimals=0 ) {
            return number_format($number, $decimals, \I18N::translate('DSEP'), \I18N::translate('TSEP'));
        };
        $this->Helper->raw = function( $value ) {
            return $value;
        };
    }

    /**
     *
     */
    public function render( $template, $data=NULL ) {
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

        if (!file_exists($TplCompiled) OR filemtime($TplCompiled) < filemtime($TplFile)) {
            #\Yryie::StartTimer($TplFile, 'Compile '.$TplFile, 'Compile template');
            file_put_contents($TplCompiled, $this->compile($TplFile));
            #\Yryie::StopTimer();
        }

        // save data
        $this->dataStack[] =& $this->data;

        ob_start();
        include $TplCompiled;
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
        $this->arrayChangeKeysUpperCase($value);

        if (is_array($name)) {
            foreach ($name as $key=>$value) $this->set($key, $value);
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
            $dataPointer = &$this->data;
        } else {
            $parent = 0;
            while (strpos($name, '_PARENT.') === 0) {
                $parent++;
                $name = substr($name, 8);
            }
            if ($parent) {
                 $dataPointer = &$this->dataStack[count($this->dataStack)-$parent];
            } else {
                 $dataPointer = &$this->dataPointer;
            }
        }
        return isset($dataPointer[$name]) ? $dataPointer[$name] : NULL;
    }

    /**
     *
     */
    public function __get( $name ) {
        return $this->get($name);
    }

    /**
     *
     */
    public function setRenderValueCallback( callable $callback ) {
        $this->renderValueCallback = $callback;
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
    protected $JavaScriptPacker;

    /**
     *
     */
    protected $renderValueCallback;

    /**
     *
     */
    protected function compile( $TplFile ) {
        $html = php_strip_whitespace($TplFile);

        if (strpos($html, '<!-- COMPILE OFF -->') === FALSE) {

            // <!-- INCLUDE template.tpl -->
            if (preg_match_all('~<!-- INCLUDE (.*?) -->~', $html, $args, PREG_SET_ORDER)) {
                foreach ($args as $inc) {
                    $html = str_replace(
                        $inc[0],
                        '<?php /* INCLUDE */ $this->display("'.$inc[1].'"); ?'.'>',
                        $html
                    );
                }
            }

            // <!-- DEFINE name -->...<!-- END DEFINE -->
            if (preg_match_all('~<!-- DEFINE (.+?) -->\s*(.+?)\s*<!-- END DEFINE -->~s', $html, $args, PREG_SET_ORDER)) {
                foreach ($args as $macro) {
                    // Remove macro definition
                    $html = str_replace($macro[0], '', $html);
                    // Replace all macro uses
                    $html = str_replace('<!-- MACRO '.$macro[1].' -->', $macro[2], $html);
                }
            }

            // Mask masked delimiters
            $html = str_replace(array('\{', '\}'), array("\x01", "\x02"), $html);

            // <!-- (ELSE)?IF ... -->...<!-- ELSE -->...<!-- ENDIF -->
            if (preg_match_all('~<!-- (ELSE)?IF (.*?) -->~', $html, $args, PREG_SET_ORDER)) {
                foreach ($args as $if) {
                    if (preg_match_all('~'.$this->RegexVar.'~', $if[2], $matches, PREG_SET_ORDER)) {
                        foreach ($matches as $match) {
                            $if[2] = str_replace($match[0], '$this->renderValue(\''.$match[1].'\')', $if[2]);
                        }
                    }
                    $html = str_replace($if[0], '<?php '.$if[1].'IF ('.$if[2].'): ?'.'>', $html);
                }
                $html = str_replace('<!-- ELSE -->', '<?php ELSE: ?'.'>', $html);
                $html = str_replace('<!-- ENDIF -->', '<?php ENDIF; ?'.'>', $html);
            }

            // Translations {{...}} are shortcuts to helper function "translate", which have to be defined!
            if (preg_match_all('~\{\{([^}]+?)\}\}~', $html, $args, PREG_SET_ORDER)) {
                foreach ($args as $data) {
                    $html = str_replace($data[0], '{translate:"'.$data[1].'"}', $html);
                }
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
                              : '$this->get(\''.$param[2].'\')'
                            );
                        }
                    }

                    if ($this->Helper->is_callable($func)) $func = '$this->Helper->'.$func;

                    $html = str_replace(
                        $match[0],
                        '<?php echo '.$func.'('.substr($args, 1).'); ?'.'>',
                        $html
                    );
                }
            }

            // Loops
            if (preg_match_all('~<!-- BEGIN ([A-Z][A-Z0-9_]*) -->~', $html, $args, PREG_SET_ORDER)) {
                foreach ($args as $match) {
                    $id = rand(10000, 99999);
                    $html = str_replace($match[0], sprintf($this->LoopStart, $id, $match[1]), $html);
                }
            }

            $html = preg_replace('~<!-- END .*?-->~', $this->LoopEnd, $html);

            // Variables
            $html = preg_replace_callback(
                '~'.$this->RegexVar.'~',
                function($m) { return '<?php echo $this->renderValue(\''.$m[1].'\'); ?'.'>'; },
                $html
            );

            $html = str_replace('\ ', "&nbsp;", $html);

            // unmask masked delimiters
            $html = str_replace(array("\x01", "\x02"), array('{', '}'), $html);

        }

        if (!$this->app->config->get('View.Verbose')) {
            if (substr(strtolower($TplFile), -4) == '.css') {
                $html = $this->compressCSS($html);
            } elseif (substr(strtolower($TplFile), -3) == '.js') {
                $html = $this->compressJS($html);
            } else {
                $html = $this->compressTemplate($html);
            }
            $html = $this->compress($html);
        }

        return trim($html);
    }

    /**
     *
     */
    protected function compressCSS( $html ) {
        return preg_replace(
            array(
                /* remove whitespace on both sides of colons : , and ; */
                '~\s*([,:;])\s*~',
                /* remove whitespace on both sides of curly brackets {} */
                '~;?\s*([{}])\s*~',
            ),
            '$1',
            $html
        );
    }

    /**
     *
     */
    protected function compressJS( $html ) {
        return $this->app->JavaScriptPacker->pack($html);
    }

    /**
     *
     */
    protected function compressTemplate( $html ) {
        // Remove empty pairs of <style></style>
        $html = preg_replace('~\s*<style>\s*</style>\s*~', '', $html);
        // Compress inline CSS
        if (preg_match_all('~<style>.*?</style>~is', $html, $matches, PREG_SET_ORDER)) {
            foreach ($matches as $match) {
                $html = str_replace($match[0], $this->compressCSS($match[0]), $html);
            }
        }

        // Remove empty pairs of <script></script>
        $html = preg_replace('~\s*<script>\s*</script>\s*~', '', $html);
        // Compress inline JS
        if (preg_match_all('~<script>.*?</script>~is', $html, $matches, PREG_SET_ORDER)) {
            foreach ($matches as $match) {
                $html = str_replace($match[0], $this->compressJS($match[0]), $html);
            }
        }

        return $html;
    }

    /**
     *
     */
    protected function compress( $html ) {

        // Remember explicit spaces between variables
        $html = preg_replace('~ \?'.'> +<\?php ~', "\x01", $html);

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
        $html = preg_replace('~\s+~s', ' ', $html);
        // Remove whitespaces between tags
        $html = preg_replace('~>\s+<~s', '><', $html);

        // Remove pairs of ?><?php
        $html = preg_replace('~\s*\?'.'><\?php\s*~', ' ', $html);

        // Restore explicit spaces between variables
        $html = str_replace("\x01", '?'.'> <?php ', $html);

        // Restore <pre>...</pre> sections
        $html = str_replace(array_keys($pre), array_values($pre), $html);

        return $html;
    }

    /**
     * Called from render()
     */
    protected function renderValue( $name ) {
        // Raw value requested, independent from locale etc.
        if (preg_match('~^(.*?)_RAW$~', $name, $args)) {
            $value = $this->get($args[1]);
        } else {
            $value = $this->get($name);
            if ($this->renderValueCallback) {
                $value = call_user_func($this->renderValueCallback, $value);
            }
        }
        return $value;
    }

    /**
     *
     */
    protected function arrayChangeKeysUpperCase( &$array ) {
        if (!is_array($array)) return;
        $array = array_change_key_case($array, CASE_UPPER);
        foreach ($array as $key => $value) {
            if (is_array($value)) $this->arrayChangeKeysUpperCase($array[$key]);
        }
    }

    // -------------------------------------------------------------------------
    // PRIVATE
    // -------------------------------------------------------------------------

    /**
     *
     */
    private $LoopStart = '<?php
        if ($_%1$d = $this->__get("%2$s")):
          array_push($this->dataStack, $this->dataPointer);
          $_f%1$d = TRUE; $_c%1$d = count($_%1$d); $_i%1$d = 1;
          foreach ($_%1$d as $_k%1$d => &$this->dataPointer):
            if (!is_array($this->dataPointer)):
              $this->dataPointer = array("\x00" => $this->dataPointer, "%2$s" => $this->dataPointer);
            endif;
            $this->dataPointer["_LOOP"] = $_k%1$d;
            $this->dataPointer["_LOOP_FIRST"] = $_f%1$d; $_f%1$d = FALSE;
            $this->dataPointer["_LOOP_LAST"] = ($_i%1$d == $_c%1$d);
            $this->dataPointer["_LOOP_ID"] = $_i%1$d++; ?>';

    /**
     *
     */
    private $LoopEnd = '<?php
           if (isset($this->dataPointer["\x00"])):
             $this->dataPointer = $this->dataPointer["\x00"];
           else:
             unset($this->dataPointer["_LOOP"], $this->dataPointer["_LOOP_FIRST"], $this->dataPointer["_LOOP_LAST"], $this->dataPointer["_LOOP_ID"]);
           endif;
         endforeach;
         $this->dataPointer = array_pop($this->dataStack);
       endif; ?>';

}
