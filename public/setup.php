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

$setup = [

    /**
     * Title and description
     */
    'title'       => 'PVLng basic setup',
    'description' => 'Checking your server settings ...',

    /**
     * Tasks to perform
     */
    'tasks' => [

        /**
         * Minimal required PHP version
         */
        'PHPVersion' => 5.5,

        /**
         *
         */
        'Extensions' => [
            'mysqli'   => [ 'MySQL support' ],
            'curl'     => [ 'cURL support' ],
            'json'     => [ 'JSON support' ],
            'mbstring' => [ 'Multibyte Support' ],
            'pcre'     => [ 'PCRE support' ],
            'session'  => [ 'Session support' ],
            'gd'       => [ 'Image processing' ],
            'apc'      => [ 'Alternative PHP Cache (APC)', false ], // recommended
            'memcache' => [ 'Memcache', false ]                     // recommended
        ],

        /**
         *
         */
        'Composer' => [
            dirname(__DIR__)
        ],

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
            'config'  => '../config/config.yaml',
            'default' => '../config/config.default.yaml'
        ],

        /**
         *
         */
        'MySQLi' => [
            'config'   => '../config/config.yaml',

            'credentials' => [
                'host'     => 'database.host',
                'socket'   => 'database.socket',
                'port'     => 'database.port',
                'username' => 'database.username',
                'password' => 'database.password',
                'database' => 'database.database'
            ],

            'variables' => [
                'event_scheduler' => [
                    ['ON', 1],
                    'https://dev.mysql.com/doc/refman/en/server-options.html#option_mysqld_event-scheduler'
                ]
            ]
        ]
    ]
];

// ---------------------------------------------------------------------------
// Load classes in defined order!
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

    <title><?php echo $setup['title'] ?></title>

    <style>
        body { width: 640px; margin: 1em auto }
        tt { font-size: 140% }
    </style>
</head>
<body>

<?php if (!Setup\Setup::run($setup)) : ?>

<p>
    <form><input type="submit" value="Reload"></form>
</p>

<?php else : ?>

<h2><?php echo count($setup['tasks'])+1 ?>. Next</h2>

<p>
    <form action="/adminpass"><input type="submit" value="Define your administration user" /></form>
</p>

<?php endif ?>

</body>
</html>
