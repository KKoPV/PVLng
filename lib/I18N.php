<?php
/**
 * Translation class based on BabelKit and BBCode
 *
 * @author     Knut Kohl <github@knutkohl.de>
 * @copyright  2012-2016 Knut Kohl
 * @license    MIT License (MIT) http://opensource.org/licenses/MIT
 * @version    1.0.0
 */
abstract class I18N
{

    /**
     * Namespace separator
     */
    const SEP = '::';

    /**
     *
     */
    public static function setBabelKit(BabelKit $bk)
    {
        self::$bk = $bk;
    }

    /**
     *
     */
    public static function setBBCode(BBCode $bbcode)
    {
        self::$bbcode = $bbcode;
        self::$bbcode->SetEnableSmileys(false);
        self::$bbcode->ClearSmileys();
        self::$bbcode->AddRule(
            'tt',
            array(
                'simple_start' => '<tt>',
                'simple_end' => '</tt>',
                'class' => 'inline',
                'allow_in' => array('listitem', 'block', 'columns', 'inline', 'link')
            )
        );
        self::$bbcode->AddRule(
            'small',
            array(
                'simple_start' => '<small>',
                'simple_end' => '</small>',
                'class' => 'inline',
                'allow_in' => array('listitem', 'block', 'columns', 'inline', 'link')
            )
        );
    }

    /**
     *
     */
    public static function setLanguage($language)
    {
        self::$language = $language;
    }

    /**
     *
     */
    public static function setCodeSet()
    {
        self::$codeSets = func_get_args();
    }

    /**
     *
     */
    public static function setAddMissing($add)
    {
        self::$add = !!$add;
    }

    /**
     *
     */
    public static function setMarkMissing(
        $mark = '<span style="background-color:#FF9966">%s</span>'
    ) {

        self::$mark = $mark;
    }

    /**
     * Shortcut for translate()
     */
    public static function _()
    {
        return call_user_func_array('I18N::translate', func_get_args());
    }

    /**
     *
     */
    public static function translate($str)
    {
        $fargs = func_get_args();
        $str = array_shift($fargs);
        $cnt = (isset($fargs[0]) && intval($fargs[0]) == $fargs[0]) ? +$fargs[0] : false;

        if (strpos($str, self::SEP) !== false) {
            // Defined code set
            list($code_set, $code) = explode(self::SEP, $str, 2);
            $trans = self::$bk->render($code_set, self::$language, $code);
        } else {
            $code_set = '';
            $code = substr($str, 0, 32);
            // Search all code sets
            foreach (self::$codeSets as $cs) {
                $trans = self::$bk->render($cs, self::$language, $code);
                if ($trans !== $code) {
                    break;
                }
            }
        }

        if ($trans !== $code) {
            if (strpos($trans, '|') === false) {
                $str = $trans;
            } else {
                // Analyse translation for 0, 1 ... n markers
                $parts = array();
                foreach (explode('||', $trans) as $part) {
                    $part = explode(':', trim($part));
                    $parts[trim($part[0])] = $part[1];
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
            if ($code_set == '') {
                $code_set = self::$codeSets[0];
            }
            self::$bk->slave($code_set, $code, $code);
        }

        if (self::$bbcode) {
            // Disable temporary notices from parser :-(
            error_reporting(($e = error_reporting()) ^ E_NOTICE);
            $str = self::$bbcode->Parse($str);
            error_reporting($e);
        }

        if ($str === $code && self::$mark != '') {
            // Possibly not found
            $str = '<span style="background-color:#FF9966">'.$str.'</span>';
        }

        $str = str_replace(array("\r", "\n"), '', $str);

        return vsprintf($str, $fargs);
    }

    // -------------------------------------------------------------------------
    // PROTECTED
    // -------------------------------------------------------------------------

    /**
     *
     */
    protected static $add = false;

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
    protected static $codeSets = array();

    /**
     *
     */
    protected static $language = 'en';
}
