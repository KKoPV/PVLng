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
class HTML extends Formatter
{
    /**
     *
     */
    public function render($result)
    {
        echo '<html>
<head>
    <title>'.$result['title'].'</title>
    <style>
        body { font-family: Verdana,Arial,sans-serif }
        tt { font-size: 120% }
    </style>
</head>
<body>
';
        echo $result['body'];
        echo '</body></html>';
    }
}
