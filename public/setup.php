<!--
/**
 *
 *
 * @author      Knut Kohl <github@knutkohl.de>
 * @copyright   2012-2013 Knut Kohl
 * @license     GNU General Public License http://www.gnu.org/licenses/gpl.txt
 * @version     1.0.0
 */
-->
<!doctype html>
<html class="no-js" lang="en">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />

    <meta http-equiv="Content-Style-Type" content="text/css" />

    <link rel="stylesheet" href="/css/normalize.min.css" />
    <link rel="stylesheet" href="/css/default.min.css" />

    <title>PVLng initial setup</title>

    <style>
        body { width: 60%; margin: 1em auto; }
    </style>
</head>
<body>

<h2>PVLng basic setup</h2>

<?php

ini_set('display_errors', 1);
error_reporting(-1);
ini_set('display_errors', 0);
error_reporting(0);

// ---------------------------------------------------------------------------
$config = array(

    /**
     * Minimal required PHP version
     */
    'PHPVersion' => array( '5.3' ),

    /**
     *
     */
    'PHPExtensions' => array(
        'bcmath'   => 'BCMath support',
        'curl'     => 'cURL support',
        'json'     => 'JSON support',
        'mbstring' => 'Multibyte Support',
        'mysqli'   => 'MySQLi support',
        'pcre'     => 'PCRE Support',
        'session'  => 'Session Support'
    ),

    /**
     *
     */
    'Configuration' => array(
        'default' => '../config/config.default.php',
        'config'  => '../config/config.php',
    ),

    /**
     *
     */
    'MySQLi' => array(
        'config'   => '../config/config.php',
        'host'     => 'Database.Host',
        'socket'   => 'Database.Socket',
        'port'     => 'Database.Port',
        'user'     => 'Database.Username',
        'pass'     => 'Database.Password',
        'db'       => 'Database.Database'
     ),
//
//     /**
//      *
//      */
//     'Directories' => array(
//     ),

);

// ---------------------------------------------------------------------------
include 'setup.classes.php';

if (!Setup\Setup::run($config)):

?>

<p>
    <form><input type="submit" value="Reload"></form>
</p>

<?php else: ?>

<h2>Next</h2>

<p>
    <form action="/adminpass"><input type="submit" value="Definition of your administration user" /></form>
</p>

<?php endif; ?>

</body>
</html>
