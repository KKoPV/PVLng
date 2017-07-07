<?php
/**
 * PVLng - PhotoVoltaic Logger new generation
 *
 * @link       https://github.com/KKoPV/PVLng
 * @link       https://pvlng.com/
 * @author     Knut Kohl <github@knutkohl.de>
 * @copyright  2012 Knut Kohl
 * @license    MIT License (MIT) http://opensource.org/licenses/MIT
 */
abstract class JSON
{
    /**
     *
     */
    public static function check($error = null)
    {
        if (is_null($error)) {
            $error = json_last_error();
        }

        switch ($error) {
            case JSON_ERROR_NONE:
                return '';
                break;
            case JSON_ERROR_DEPTH:
                return 'JSON ERROR - Maximum stack depth exceeded';
                break;
            case JSON_ERROR_STATE_MISMATCH:
                return 'JSON ERROR - Underflow or the modes mismatch';
                break;
            case JSON_ERROR_CTRL_CHAR:
                return 'JSON ERROR - Unexpected control character found';
                break;
            case JSON_ERROR_SYNTAX:
                return 'JSON ERROR - Syntax error, malformed JSON';
                break;
            case JSON_ERROR_UTF8:
                return 'JSON ERROR - Malformed UTF-8 characters, possibly incorrectly encoded';
                break;
            default:
                return 'JSON ERROR ('.$error.') - Unknown error';
                break;
        }
    }

    /**
     *
     */
    public static function prettyPrint($json, $indent = "\t")
    {
        $result = '';
        $level = 0;
        $prev_char = '';
        $in_quotes = false;
        $ends_line_level = null;
        $json_length = strlen($json);

        for ($i = 0; $i < $json_length; $i++) {
            $char = $json[$i];
            $new_line_level = null;
            $post = "";
            if ($ends_line_level !== null) {
                $new_line_level = $ends_line_level;
                $ends_line_level = null;
            }
            if ($char === '"' && $prev_char != '\\') {
                $in_quotes = !$in_quotes;
            } elseif (! $in_quotes) {
                switch ($char) {
                    case '}':
                    case ']':
                        $level--;
                        $ends_line_level = null;
                        $new_line_level = $level;
                        break;

                    case '{':
                    case '[':
                        $level++;
                        // fall-through
                    case ',':
                        $ends_line_level = $level;
                        break;

                    case ':':
                        $post = " ";
                        break;

                    case " ":
                    case "\t":
                    case "\n":
                    case "\r":
                        $char = "";
                        $ends_line_level = $new_line_level;
                        $new_line_level = null;
                        break;
                }
            }
            if ($new_line_level !== null) {
                $result .= "\n".str_repeat($indent, $new_line_level);
            }
            $result .= $char.$post;
            $prev_char = $char;
        }

        return $result;
    }
}
