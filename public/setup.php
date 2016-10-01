<?php error_reporting(-1); ini_set('show_errors', 1);
/**
 *
 *
 * @author     Knut Kohl <github@knutkohl.de>
 * @copyright  2012-2014 Knut Kohl
 * @license    MIT License (MIT) http://opensource.org/licenses/MIT
 * @version    1.0.0
 */
?>
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
        body { width: 60%; margin: 1em auto }
        tt { font-size: 140% }
    </style>
</head>
<body>

<h2>PVLng basic setup</h2>

<?php

// ---------------------------------------------------------------------------
$config = array(

    /**
     * Minimal required PHP version
     */
    'PHPVersion' => '5.3',

    /**
     *
     */
    'PHPExtensions' => array(
        'curl'     => array('cURL support'),
        'gd'       => array('Image processing'),
        'json'     => array('JSON support'),
        'mbstring' => array('Multibyte Support'),
        'mysqli'   => array('MySQLi support'),
        'pcre'     => array('PCRE support'),
        'session'  => array('Session support'),
        'apc'      => array('Alternative PHP Cache (APC)', false), // not required, but recommended
        'memcache' => array('Memcache', false), // not required, but recommended
    ),

    /**
     *
     */
    'Composer' => array(
        'root' => dirname(__DIR__)
    ),

    /**
     *
     */
    'Permissions' => array(
        '../tmp' => 'is_writable'
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

);

// ---------------------------------------------------------------------------
include 'setup.classes.php';

if (Setup\Setup::run($config) === FALSE):

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
