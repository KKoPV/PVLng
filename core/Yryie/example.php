<?php
/**
 * Yryie example
 *
 * @author     Knut Kohl <github@knutkohl.de>
 * @copyright  2011 Knut Kohl
 * @license    http://www.gnu.org/licenses/gpl.txt GNU General Public License
 *
 * @codingStandardsIgnoreFile
 */
?>
<html>
<head>
    <title>Yryie example</title>
    <style>
        .linenum {
            text-align:right;
            background:#FDECE1;
            border:1px solid #cc6666;
            padding:0px 1px 0px 1px;
            font-family:Courier New, Courier;
            float:left;
            width:17px;
            margin:3px 0px 30px 0px;
        }
        code {
            font-family: Courier New, Courier;
        }
        .linetext{
            width:700px;
            text-align:left;
            background:white;
            border:1px solid #cc6666;
            border-left:0px;
            padding:0px 1px 0px 8px;
            font-family:Courier New, Courier;
            float:left;
            margin:3px 0px 30px 0px;
        }
        br.clear    {
            clear:both;
        }
    </style>
</head>
<body>
<pre>
<?php
    preg_match('~^// >>(.*)^// <<~ms', file_get_contents(__FILE__), $args);
    highlight_string('<?php'.PHP_EOL.trim($args[1]));
?>
</pre>
<?php
// >>
// Handle all errors by Yryie
error_reporting(-1);

require_once 'Yryie.php';

// Add version infos, set BEFORE include!
Yryie::versions();

// Register error handler
Yryie::register();

Yryie::info('An information...');
Yryie::code('<b>code example</b>');
Yryie::state('State');
Yryie::SQL('SELECT * FROM table');
Yryie::debug(array( 1, 2 ));
Yryie::warning('A Warning.');
Yryie::error('An ERROR!');

function DoTrace($level = 1, $full = false)
{
    Yryie::trace($level, $full);
}

Yryie::info('Trace 1:');
DoTrace();
Yryie::info('Trace 2:');
DoTrace(0, true);

function Error(&$a)
{
    // Force an error to capture
    $a = $b;
}
error($a);

// Let's output all
// 1st finalize it
Yryie::finalize();
// Output with the default script and CSS
Yryie::output(true, true);
// <<

?>
</body>
</html>
