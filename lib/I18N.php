<?php
/**
 * Translation class
 *
 * @author      Knut Kohl <github@knutkohl.de>
 * @copyright   2012-2013 Knut Kohl
 * @license     GNU General Public License http://www.gnu.org/licenses/gpl.txt
 * @version     1.0.0
 */
abstract class I18N {

    /**
     * Namespace separator
     */
    const SEP = '::';

    /**
     *
     */
    public static function setBabelKit( BabelKit $bk ) {
        self::$bk = $bk;
    }

    /**
     *
     */
    public static function setBBCode( BBCode $bbcode ) {
        self::$bbcode = $bbcode;
        self::$bbcode->SetEnableSmileys(FALSE);
        self::$bbcode->ClearSmileys();
        self::$bbcode->AddRule('tt', array(
            'simple_start' => '<tt>',
            'simple_end' => '</tt>',
            'class' => 'inline',
            'allow_in' => array('listitem', 'block', 'columns', 'inline', 'link')
        ));
    }

    /**
     *
     */
    public static function setLanguage( $language ) {
        self::$language = $language;
    }

    /**
     *
     */
    public static function setCodeSet() {
        self::$code_sets = func_get_args();
    }

    /**
     *
     */
    public static function setAddMissing( $add ) {
        self::$add = $add;
    }

    /**
     *
     */
    public static function setMarkMissing(
        $mark='<span style="background-color:#FF9966">%s</span>'
    ) {
        self::$mark = $mark;
    }

    /**
     *
     */
    public static function _() {
        return call_user_func_array('I18N::translate', func_get_args());
    }

    /**
     *
     */
    public static function translate( $str ) {
        $fargs = func_get_args();
        $str = array_shift($fargs);
        $cnt = (isset($fargs[0]) AND intval($fargs[0]) == $fargs[0]) ? +$fargs[0] : FALSE;

        if (strpos($str, self::SEP) !== FALSE) {
            // Defined code set
            list($code_set, $code) = explode(self::SEP, $str, 2);
            $trans = self::$bk->render($code_set, self::$language, $code);
        } else {
            $code_set = '';
            $code = substr($str, 0, 32);
            // Search all code sets
            foreach (self::$code_sets as $cs) {
                $trans = self::$bk->render($cs, self::$language, $code);
                if ($trans !== $code) break;
            }
        }

        if ($trans !== $code) {
            if (strpos($trans, '|') === FALSE) {
                $str = $trans;
            } else {
                // Analyse translation for 0, 1 ... n markers
                $parts = array();
                foreach (explode('|', $trans) as $part) {
                    $part = explode(':', $part);
                    $parts[$part[0]] = $parts[1];
                }

                if (isset($parts[$cnt])) {
                    $str = $parts[$cnt];
                } elseif (isset($parts['n'])) {
                    $str = $parts['n'];
                } else {
                    $str = $trans;
                }
            }
        } elseif (self::$add) {
            if ($code_set == '') $code_set = self::$code_sets[0];
            self::$bk->slave($code_set, $code, $code);
        }

        if (self::$bbcode) {
            // Disable temporary notices from parser :-(
            error_reporting(($e = error_reporting()) ^ E_NOTICE);
            $str = self::$bbcode->Parse($str);
            error_reporting($e);
        }

        if ($str === $code AND self::$mark != '') {
            // Possibly not found
            $str = '<span style="background-color:#FF9966">'.$str.'</span>';
        }

        return vsprintf($str, $fargs);
    }

    // -------------------------------------------------------------------------
    // PROTECTED
    // -------------------------------------------------------------------------

    /**
     *
     */
    protected static $add = FALSE;

    /**
     *
     */
    protected static $mark = '';

    /**
     *
     */
    protected static $bk;

    /**
     *
     */
    protected static $bbcode;

    /**
     *
     */
    protected static $code_sets = array();

    /**
     *
     */
    protected static $language = 'en';

}

/**
 * Shortcut function
 */
function __() {
    return call_user_func_array('I18N::translate', func_get_args());
}
