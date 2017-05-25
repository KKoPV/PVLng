<?php
/**
 * Slug generator
 *
 * @author      Knut Kohl <github@knutkohl.de>
 * @copyright   2012-2014 Knut Kohl
 * @license     GNU General Public License http://www.gnu.org/licenses/gpl.txt
 * @version     1.0.0
 */
namespace Core;

/**
 *
 */
abstract class Slug
{
    /**
     * Make slug from string
     *
     * @param string $text
     * @return string
     */
    public static function encode($text)
    {
        $slug = strtolower(strtr($text, self::$trmap));

        $slug = preg_replace('~\s\s+~', ' ', $slug);
        $slug = preg_replace('~[^\w\d@_-]~', '-', $slug);
        $slug = preg_replace('~--+~', '-', $slug);

        return trim($slug, '-');
    }

    /**
     * Make slug from string
     *
     * @param string $text
     * @return string
     */
    public static function decode($slug)
    {
        return ucwords(str_replace('-', ' ', $slug));
    }

    //---------------------------------------------------------------------------
    // PROTECTED
    //---------------------------------------------------------------------------

    /**
     * Translation table
     *
     * @var array $trmap
     */
    protected static $trmap = array(
        'Š' => 'S',  'š' => 's',  'Đ' => 'Dj', 'đ' => 'dj', 'Ž' => 'Z',
        'ž' => 'z',  'Č' => 'C',  'č' => 'c',  'Ć' => 'C',  'ć' => 'c',
        'À' => 'A',  'Á' => 'A',  'Â' => 'A',  'Ã' => 'A',  'Ä' => 'Ae',
        'Å' => 'A',  'Æ' => 'A',  'Ç' => 'C',  'È' => 'E',  'É' => 'E',
        'Ê' => 'E',  'Ë' => 'E',  'Ì' => 'I',  'Í' => 'I',  'Î' => 'I',
        'Ï' => 'I',  'Ñ' => 'N',  'Ò' => 'O',  'Ó' => 'O',  'Ô' => 'O',
        'Õ' => 'O',  'Ö' => 'Oe', 'Ø' => 'O',  'Ù' => 'U',  'Ú' => 'U',
        'Û' => 'U',  'Ü' => 'Ue', 'Ý' => 'Y',  'Þ' => 'B',  'ß' => 'Ss',
        'à' => 'a',  'á' => 'a',  'â' => 'a',  'ã' => 'a',  'ä' => 'ae',
        'å' => 'a',  'æ' => 'a',  'ç' => 'c',  'è' => 'e',  'é' => 'e',
        'ê' => 'e',  'ë' => 'e',  'ì' => 'i',  'í' => 'i',  'î' => 'i',
        'ï' => 'i',  'ð' => 'o',  'ñ' => 'n',  'ò' => 'o',  'ó' => 'o',
        'ô' => 'o',  'õ' => 'o',  'ö' => 'oe', 'ø' => 'o',  'ù' => 'u',
        'ú' => 'u',  'û' => 'u',  'ü' => 'ue', 'ý' => 'y',  'ý' => 'y',
        'þ' => 'b',  'ÿ' => 'y',  'Ŕ' => 'R',  'ŕ' => 'r'
    );
}
