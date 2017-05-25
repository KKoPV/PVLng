<?php
/**
 * Handle application messages
 *
 * @author      Knut Kohl <github@knutkohl.de>
 * @copyright   2012-2013 Knut Kohl
 * @license     GNU General Public License http://www.gnu.org/licenses/gpl.txt
 * @version     1.0.0
 */
namespace Core;

/**
 *
 */
abstract class Messages
{

    /**
     * @name Message type
     * @{
     */
    const INFO    = 'info';
    const SUCCESS = 'success';
    const ERROR   = 'error';
    const CODE    = 'code';
    /** @} */

    /**
     * Variable name to store messages into session
     *
     * @var string $SessionVar
     */
    public static $SessionVar = 'MESSAGES';

    /**
     * html code to format messages for output
     *
     * string param 1: message type: info|success|error|code
     * string param 2: message text
     *
     * @var string $toStrFormat
     */
    public static $toStrFormat = '<div class="msg%1$s">%2$s</div>';

    /**
     * Format message string
     *
     * @param mixed $msg Message(s)
     * @param string $type Message type
     * @param bool $formated Message is still HTML formated
     * @return string
     */
    public static function toStr($msg, $type = self::INFO, $formated = false)
    {
        if (is_array($msg)) {
            if (!$formated) {
                $msg = array_map_recursive('htmlspecialchars', $msg);
            }
        } else {
            if (strpos($msg, 'html:') === 0) {
                $formated = true;
                list(, $msg) = explode(':', $msg, 2);
            }
            if (!$formated) {
                $msg = htmlspecialchars($msg, ENT_QUOTES);
            }
        }
        $return = '';
        foreach ((array)$msg as $m) {
            $return .= sprintf(self::$toStrFormat, $type, $m)."\n";
        }

        return $return;
    }

    /**
     * Store info message into buffer
     *
     * @param mixed $msg Message(s)
     * @param bool $formated Message is still HTML formated
     */
    public static function info($msg, $formated = false)
    {
        self::add($msg, self::INFO, $formated);
    }

    /**
     * Store success message into buffer
     *
     * @param mixed $msg Message(s)
     * @param bool $formated Message is still HTML formated
     */
    public static function success($msg, $formated = false)
    {
        self::add($msg, self::SUCCESS, $formated);
    }

    /**
     * Store error message into buffer
     *
     * @param mixed $msg Message(s)
     * @param bool $formated Message is still HTML formated
     * @param bool $mark Mark message, only if $msg is not an array
     */
    public static function error($msg, $formated = false, $mark = false)
    {
        if (!is_array($msg) && $mark) {
            $msg = '[Error] '.$msg;
        }
        self::add($msg, self::ERROR, $formated);
    }

    /**
     * Store code message into buffer
     *
     * @param mixed $msg Message(s)
     * @param bool $formated Message is still HTML formated
     */
    public static function code($msg, $formated = false)
    {
        self::add($msg, self::CODE, $formated);
    }

    /**
     * Generic function to store message into buffer
     *
     * @param mixed $msg Message(s)
     * @param string $type Message type
     * @param bool $formated Message is still HTML formated
     */
    public static function add($msg, $type, $formated)
    {
        Session::add(self::$SessionVar, array($msg, $type, $formated));
    }

    /**
     * Get messages from buffer an clear buffer if requested
     *
     * @param bool $clear Clear buffer
     * @return array
     */
    public static function get($clear = true)
    {
        $msgs = array();
        foreach ((array) Session::get(self::$SessionVar) as $msg) {
            $msgs[] = self::toStr($msg[0], $msg[1], $msg[2]);
        }
        if ($clear) {
            self::clear();
        }
        return $msgs;
    }

    /**
     * Get messages from buffer an clear buffer if requested
     *
     * @param bool $clear Clear buffer
     * @return array
     */
    public static function getRaw($clear = true)
    {
        $msgs = array();
        foreach ((array) Session::get(self::$SessionVar) as $msg) {
            if (is_array($msg[0])) {
                $msg[0] = implode("\n", $msg[0]);
            }
            $msgs[] = array(
                'type'    => $msg[1],
                'message' => $msg[2] ? $msg[0] : htmlspecialchars($msg[0], ENT_QUOTES)
            );
        }
        if ($clear) {
            self::clear();
        }
        return $msgs;
    }

    /**
     * Get message count from buffer according to type
     *
     * @param bool $type If not set return count of all messages
     * @return int
     */
    public static function count($type = null)
    {
        if (!isset($type)) {
            $cnt = count((array) Session::get(self::$SessionVar));
        } else {
            $msgs = (array) Session::get(self::$SessionVar);
            $cnt = 0;
            foreach ($msgs as $msg) {
                if ($msg[1] == $type) {
                    $cnt++;
                }
            }
        }
        return $cnt;
    }

    /**
     * Clear message buffer
     */
    public static function clear()
    {
        Session::delete(self::$SessionVar);
    }
}
