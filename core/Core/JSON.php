<?php
/**
 * Wrapper for JSON handling
 *
 * @author     Knut Kohl <github@knutkohl.de>
 * @copyright  2012 Knut Kohl
 * @license    MIT License (MIT) http://opensource.org/licenses/MIT
 */
namespace Core;

/**
 *
 */
use Exception;

/**
 *
 */
abstract class JSON
{
    /**
     *
     */
    public static function decode($json, $asArray = false)
    {
        $data = json_decode(trim($json), $asArray);

        if ($err = static::check()) {
            throw new Exception($err);
        }

        return $data;
    }

    /**
     *
     */
    public static function encode($data)
    {
        return json_encode($data);
    }

    /**
     *
     */
    public static function check($error = null)
    {
        if (is_null($error)) {
            $error = json_last_error();
        }

        if ($error != JSON_ERROR_NONE) {
            return isset(static::$errorMessages[$error])
                 ? 'JSON ERROR - ' . static::$errorMessages[$error]
                 : 'JSON ERROR ('.$error.') - Unknown error';
        }

        return false;
    }

    /**
     *
     */
    public static function xPath($json, array $path)
    {
        $data = static::decode($json, true);

        // Root pointer
        $pointer = &$data;

        foreach ($path as $key) {
            if (is_array($pointer) and isset($pointer[$key])) {
                // Move pointer foreward ...
                $pointer = &$pointer[$key];
            } else {
                // Invalid key
                throw new Exception('Invalid JSON xPath: '.implode('->', $path));
            }
        }

        // Key found, return its value
        return $pointer;
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

    // -----------------------------------------------------------------------
    // PROTECTED
    // -----------------------------------------------------------------------

    /**
     * English messages
     */
    protected static $errorMessages = [
        JSON_ERROR_DEPTH          => 'Maximum stack depth exceeded',
        JSON_ERROR_STATE_MISMATCH => 'Underflow or the modes mismatch',
        JSON_ERROR_CTRL_CHAR      => 'Unexpected control character found',
        JSON_ERROR_SYNTAX         => 'Syntax error, malformed JSON',
        JSON_ERROR_UTF8           => 'Malformed UTF-8 characters, possibly incorrectly encoded'
    ];
}
