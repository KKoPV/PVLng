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
 */
use PVLng\PVLng;
use Yryie;

/**
 *
 *
 * @author      Knut Kohl <github@knutkohl.de>
 * @copyright   2012-2013 Knut Kohl
 * @license     GNU General Public License http://www.gnu.org/licenses/gpl.txt
 * @version     1.0.0
 */
class View extends \Slim\View
{

    /**
     *
     */
    const TPL_MAP_FILE = 'template.map.php';

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
    public $Helper;

    /**
     *
     */
    public function __construct()
    {
        parent::__construct();

        $this->app = App::getInstance();

        $this->dataPointer =& $this->data;

        $this->cacheDirectory = rtrim(sys_get_temp_dir(), DIRECTORY_SEPARATOR);

        $map = $this->cacheDirectory . DIRECTORY_SEPARATOR . self::TPL_MAP_FILE;
        $this->templatesMap = file_exists($map) ? include $map : array();
    }

    /**
     * Set the base directory that contains view templates
     * @param string|array $directory
     */
    public function setTemplatesDirectory($directory)
    {
        $this->templatesDirectory = (array) $directory;
    }

    /**
     * Get fully qualified path to template file using templates base directory
     * @param string $file The template file pathname relative to templates base directory
     * @return array
     */
    public function getTemplatePathname($file)
    {
        $files = array();
        foreach ($this->templatesDirectory as $dir) {
            $files[] = rtrim($dir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . ltrim($file, DIRECTORY_SEPARATOR);
        }
        return $files;
    }

    /**
     * Set the cache directory that contains compiled templates
     * @param string $directory
     */
    public function setCacheDirectory($directory)
    {
        $this->cacheDirectory = rtrim($directory, DIRECTORY_SEPARATOR);
    }

    /**
     *
     */
    public function render($template, $data = null)
    {
        if (file_exists($template)) {
            // Concrete file
            $TplFile = $template;
        } else {
            $hash = md5(serialize($this->templatesDirectory).$template);
            if (isset($this->templatesMap[$hash])) {
                $TplFile = $this->templatesMap[$hash];
            } else {
                $TplFile = '';
                // Search template in defined directories
                foreach ($this->getTemplatePathname($template) as $file) {
                    if (file_exists($file)) {
                        $TplFile = $file;
                        break;
                    }
                }

                if ($TplFile == '') {
                    return;
                }

                $this->templatesMap[$hash] = $TplFile;
            }
        }

        $TplFile = realpath($TplFile);
        $TplCompiled = str_replace(PVLng::path(PVLng::$RootDir, 'frontend'), '', $TplFile);
        $TplCompiled = str_replace(DIRECTORY_SEPARATOR, '_', $TplCompiled);
        $TplCompiled = trim($TplCompiled, '_');
        $TplCompiled = $this->cacheDirectory . DIRECTORY_SEPARATOR . $TplCompiled . '.php';

        if (!file_exists($TplCompiled) or filemtime($TplCompiled) < filemtime($TplFile)) {
            /// Yryie::StartTimer($TplFile, 'Compile '.$TplFile, 'Compile template');
            file_put_contents($TplCompiled, $this->compile($TplFile));
            /// Yryie::StopTimer();
        }

        // Save data
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
    public function assign($name, $template)
    {
        $content = $this->fetch($template);
        if (strlen($content)) {
            $this->set($name, $content);
        }
    }

    /**
     * Assign template content to a variable
     *
     * The file will be parsed like other templates
     *
     * @param string $name Varible name
     * @param string $value File name
     */
    public function append($name, $value)
    {
        $this->set($name, $this->get($name) . $value);
    }

    /**
     *
     */
    public function set($name, $value = null)
    {
        $this->arrayChangeKeysUpperCase($value);

        if (is_array($name)) {
            foreach ($name as $key => $value) {
                $this->set($key, $value);
            }
        } else {
            $this->dataPointer[strtoupper($name)] = $value;
        }
    }

    /**
     *
     */
    public function __set($name, $value)
    {
        $this->set($name, $value);
    }

    /**
     *
     */
    public function get($name)
    {
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
        return isset($dataPointer[$name]) ? $dataPointer[$name] : null;
    }

    /**
     *
     */
    public function __get($name)
    {
        return $this->get($name);
    }

    /**
     *
     */
    public function setRenderValueCallback(callable $callback)
    {
        $this->renderValueCallback = $callback;
    }

    /**
     *
     */
    public function __destruct()
    {
        file_put_contents(
            PVLng::path($this->cacheDirectory, self::TPL_MAP_FILE),
            '<?php return '.var_export($this->templatesMap, true).';'
        );
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
    protected $templatesMap;

    /**
     *
     */
    protected $cacheDirectory;

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
    protected $eventComment = array(
        'html' => array('<!-- ', ' -->'),
        'js'   => array('/* ', ' */'),
        'css'  => array('/* ', ' */'),
    );

    /**
     *
     */
    protected function compile($TplFile)
    {
        $html = php_strip_whitespace($TplFile);
        $verbose = $this->app->config->get('View.Verbose');

        if (strpos($html, '<!-- COMPILE OFF -->') === false) {
            // <!-- INCLUDE template.tpl -->
            if (preg_match_all('~<!-- INCLUDE (.*?) -->~', $html, $args, PREG_SET_ORDER)) {
                foreach ($args as $inc) {
                    $html = str_replace(
                        $inc[0],
                        '<?php $this->display("'.$inc[1].'"); ?'.'>',
                        $html
                    );
                }
            }

            // <!-- EVENT event_name -->
            if (preg_match_all('~<!-- EVENT (([a-z_]+?)_(html|js|css)) -->~', $html, $args, PREG_SET_ORDER)) {
                $c1 = $c2 = '';

                foreach ($args as $e) {
                    // Comment?
                    if ($verbose) {
                        $c = $this->eventComment[$e[3]];
                        $c1 = PHP_EOL.$c[0].'EVENT '.$e[1].' >>>'.$c[1].PHP_EOL;
                        $c2 = PHP_EOL.$c[0].'<<< EVENT '.$e[1].$c[1].PHP_EOL;
                    }
                    $html = str_replace(
                        $e[0],
                        $c1.'<?php $this->display("'.$e[2].'.'.$e[3].'"); ?'.'>'.$c2,
                        $html
                    );
                }
            }

            // <!-- DEFINE name -->...<!-- END DEFINE -->
            if (preg_match_all(
                '~<!-- DEFINE (.+?) -->\s*(.+?)\s*<!-- END DEFINE -->~s', $html, $args, PREG_SET_ORDER
            )) {
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
                    if ($data != '' && preg_match_all($reg, $data.',', $params, PREG_SET_ORDER)) {
                        foreach ($params as $param) {
                            $args .= ',' . (
                              $param[4] != ''
                                // escaped value == constant
                              ? '\''.trim($param[2], '"').'\''
                                // OR template variable
                              : '$this->get(\''.$param[2].'\')'
                            );
                        }
                    }

                    if ($this->Helper->isCallable($func)) {
                        $func = '$this->Helper->'.$func;
                    }

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
                function ($m) {
                    return '<?php echo $this->renderValue(\''.$m[1].'\'); ?'.'>';
                },
                $html
            );

            $html = str_replace('\ ', "&nbsp;", $html);

            // unmask masked delimiters
            $html = str_replace(array("\x01", "\x02"), array('{', '}'), $html);
        }

        if (!$verbose) {
            if (substr(strtolower($TplFile), -4) == '.css') {
                $html = $this->compressCSS($html);
            } elseif (substr(strtolower($TplFile), -3) == '.js') {
                $html = $this->compressJS($html);
            } else {
                $html = $this->compressTemplate($html);
            }
            $html = $this->compress($html);
        }

        return $html;
    }

    /**
     *
     */
    protected function compressCSS($html)
    {
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
    protected function compressJS($js)
    {
        return $this->app->JavaScriptPacker->pack($js);
    }

    /**
     *
     */
    protected function compressTemplate($html)
    {
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
    protected function compress($html)
    {
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

        return trim($html);
    }

    /**
     * Called from render()
     */
    protected function renderValue($name)
    {
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
    protected function arrayChangeKeysUpperCase(&$array)
    {
        if (!is_array($array)) {
            return;
        }
        $array = array_change_key_case($array, CASE_UPPER);
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $this->arrayChangeKeysUpperCase($array[$key]);
            }
        }
    }


    // -------------------------------------------------------------------------
    // PRIVATE
    // -------------------------------------------------------------------------

    /**
     *
     */
    private $LoopStart = '
<?php
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
            $this->dataPointer["_LOOP_ID"] = $_i%1$d++;
?'.'>';

    /**
     *
     */
    private $LoopEnd = '
<?php
            if (isset($this->dataPointer["\x00"])):
                $this->dataPointer = $this->dataPointer["\x00"];
            else:
                unset(
                    $this->dataPointer["_LOOP"],
                    $this->dataPointer["_LOOP_FIRST"],
                    $this->dataPointer["_LOOP_LAST"],
                    $this->dataPointer["_LOOP_ID"]
                );
            endif;
        endforeach;
        $this->dataPointer = array_pop($this->dataStack);
    endif;
?'.'>';
}
