<?php
/**
 *
 *
 * @link       https://github.com/KKoPV/PVLng
 * @author     Knut Kohl <github@knutkohl.de>
 * @copyright  2012-2016 Knut Kohl
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
class TXT extends Formatter
{
    /**
     *
     */
    public function render($result)
    {
        if ($result instanceof Buffer || is_array($result)) {
            // Reformat only iterable content
            $cnt = count($result);
            $line = 0;

            foreach ($result as $key => $value) {
                if (is_array($value)) {
                    $value = implode(' ', $value);
                }
                $value = str_replace("\r", '', $value);
                $value = str_replace("\n", '\n', $value);
                if ($cnt > 1) {
                    echo trim($key . ': ' . $value);
                    if (++$line < $cnt) {
                        echo ' / ';
                    }
                } else {
                    echo $value, PHP_EOL;
                }
            }
        } else {
            echo $result;
        }
    }
}
