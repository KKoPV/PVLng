<?php
/**
 * PVLng - PhotoVoltaic Logger new generation
 *
 * @link      https://github.com/KKoPV/PVLng
 * @link      https://pvlng.com/
 * @author    Knut Kohl <github@knutkohl.de>
 * @copyright 2012 Knut Kohl
 * @license   MIT License (MIT) http://opensource.org/licenses/MIT
 */

ini_set('display_errors', 1);
error_reporting(-1);

// ---------------------------------------------------------------------------

$config = [

    /**
     * Minimal required PHP version
     */
    'PHPVersion' => '5.5',

    /**
     *
     */
    'Extensions' => [
        'curl'     => [ 'cURL support' ],
        'gd'       => [ 'Image processing' ],
        'json'     => [ 'JSON support' ],
        'mbstring' => [ 'Multibyte Support' ],
        'mysqli'   => [ 'MySQLi support' ],
        'pcre'     => [ 'PCRE support' ],
        'session'  => [ 'Session support' ],
        'apc'      => [ 'Alternative PHP Cache (APC)', false ], // not required, recommended
        'memcache' => [ 'Memcache', false ], // not required, recommended
    ],

    /**
     *
     */
    'Composer' => [ dirname(__DIR__) ],

    /**
     *
     */
    'Permissions' => [
        '../tmp' => 'is_writable'
    ],

    /**
     *
     */
    'Configuration' => [
        'default' => '../config/config.default.yaml',
        'config'  => '../config/config.yaml',
    ],

    /**
     *
     */
    'MySQLi' => [
        'config'   => '../config/config.yaml',
        'host'     => 'database.host',
        'socket'   => 'database.socket',
        'port'     => 'database.port',
        'username' => 'database.username',
        'password' => 'database.password',
        'database' => 'database.database'
    ],

];

// ---------------------------------------------------------------------------

// Load classes in defined order
$path = 'setup' . DIRECTORY_SEPARATOR;
require $path . 'Setup.php';
require $path . 'SetupTask.php';
require $path . 'Composer.php';
require $path . 'Configuration.php';
require $path . 'Extensions.php';
require $path . 'MySQLi.php';
require $path . 'PHPVersion.php';
require $path . 'Permissions.php';

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

<?php if (!Setup\Setup::run($config)) : ?>

<p>
    <form><input type="submit" value="Reload"></form>
</p>

<?php else : ?>

<h2>Next</h2>

<p>
    <form action="/adminpass"><input type="submit" value="Definition of your administration user" /></form>
</p>

<?php endif ?>

</body>
</html>
