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
namespace Formatter;

/**
 *
 */
use Buffer;

/**
 *
 */
class CSV extends Formatter
{
    /**
     *
     */
    public function render($result)
    {
        if (!$result instanceof Buffer && !is_array($result)) {
            $result = array($result);
        }

        foreach ($result as $row) {
            $line = '';
            foreach ((array) $row as $value) {
                // Mask line breaks
                $value = str_replace("\r", '', $value);
                $value = str_replace(PHP_EOL, '\n', $value);
                if (strstr($value, $this->separartor)) {
                    $value = '"' . $value . '"';
                }
                $line .= $this->separartor . $value;
            }
            // Trim leading separator
            echo substr($line, 1), PHP_EOL;
        }
    }

    // -----------------------------------------------------------------------
    // PROTECTED
    // -----------------------------------------------------------------------

    /**
     *
     */
    protected $separartor = ';';
}
