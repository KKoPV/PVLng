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

    <link rel="stylesheet" href="/css/default.css" />

    <title>PVLng initial setup</title>
    <style>
        body { width: 60%; margin: 1em auto; }
        tt { font-size: 130%; font-weight: bold; }
    </style>
</head>
<body>

<h2>PVLng initial setup</h2>

<?php

ini_set('show_errors', 0);
error_reporting(0);

$configFile = '../../config/config.php';

if (file_exists($configFile)) {
    $config = include $configFile;
    $db = new MySQLi($config['Database']['Host'], $config['Database']['Username'],
                     $config['Database']['Password'], $config['Database']['Database']);
    if (!$db->connect_error AND $config['Admin']['User']) {
        echo '<p>Your PVLng installation is successful configured!</p>';
        echo '<p>Please start <a href="/">here</a>.</p>';
        exit;
    }
}

$ok = TRUE;

/**
 *
 */
function checkExtension( $ext, $name ) {
    global $ok;
    echo '<li>'.$name.': ';
    if (extension_loaded($ext)) {
        echo '<strong style="color:green">ok</strong>';
    } else {
        echo '<strong style="color:red">failed</strong> - please install extension <strong>'.$ext.'</strong>';
        $ok = FALSE;
    }
    echo '</li>';
}

/**
 *
 */
function checkOk() {
    global $ok;
    if (!$ok) die('<p><a href="?">Reload page</a></p>');
}

?>

<h3>1. Check required extensions</h3>

<ul>
<?php
checkExtension('bcmath', 'BCMath support');
checkExtension('curl', 'cURL support');
checkExtension('json', 'JSON support');
checkExtension('mbstring', 'Multibyte Support');
checkExtension('mysqli', 'MySQLi support');
checkExtension('curl', 'cURL support');
checkExtension('pcre', 'PCRE Support');
checkExtension('session', 'Session Support');
?>
</ul>

<?php checkOk(); ?>

<h3>2. Check configuration</h3>

<p>
    <tt><strong>config/config.php</strong></tt>:

<?php

if (file_exists($configFile)) {
    echo '<strong style="color:green"> exists</strong>';
} else {
    echo '<strong style="color:red"> missing</strong>';
    echo '</p><p>Copy <tt>config/config.php.dist</tt> to <tt>config/config.php</tt>';
    $ok = FALSE;
}
?>
</p>

<?php checkOk(); ?>

<h3>3. Check database settings</h3>

<p>
    Connect to database:

<?php

if (!$db->connect_error) {
    echo '<strong style="color:green"> ok</strong>';
} else {
    echo '<strong style="color:red"> Error (', $db->connect_errno, ') ', $db->connect_error, '</strong>';
    echo '</p><p>';
    echo 'Please check your database settings in <tt>config/config.php</tt> section <tt>"Database"</tt>.';
    $ok = FALSE;
}
?>
</p>

<?php checkOk(); ?>

<h3>Finished</h3>

<p>
    Please proceed to <a href="/adminpass">definition of your administration user</a>.
</p>

</body>
</html>
