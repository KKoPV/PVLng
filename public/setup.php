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

ini_set('display_errors', 0);
error_reporting(0);

$configFile = '../config/config.php';

if (file_exists($configFile)) {
    $config = include $configFile;
    $db = new MySQLi($config['Database']['Host'], $config['Database']['Username'],
                     $config['Database']['Password'], $config['Database']['Database'],
                     +$config['Database']['Port'], $config['Database']['Socket']);
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
    echo '<p><div style="float:left;width:12em">'.$name.':</div> ';
    if (extension_loaded($ext)) {
        echo '<strong style="color:green">ok</strong>';
    } else {
        echo '<strong style="color:red">failed</strong> - please install extension <strong>'.$ext.'</strong>';
        $ok = FALSE;
    }
    echo '</p>';
}

/**
 *
 */
function checkOk() {
    global $ok;
    if (!$ok) die('<form><input type="submit" value="Reload" /></form>');
}

?>

<h3>1. Check required extensions</h3>

<?php

checkExtension('bcmath', 'BCMath support');
checkExtension('curl', 'cURL support');
checkExtension('json', 'JSON support');
checkExtension('mbstring', 'Multibyte Support');
checkExtension('mysqli', 'MySQLi support');
checkExtension('pcre', 'PCRE Support');
checkExtension('session', 'Session Support');

checkOk();

?>

<h3>2. Check configuration</h3>

<p>
    Configuration file <tt><strong>config/config.php</strong></tt>:

<?php

if (file_exists($configFile)) {
    echo '<strong style="color:green"> exists</strong>';
} else {
    echo '<strong style="color:red"> missing</strong></p>';
    echo '<p>Try to create from <tt>config/config.php.dist</tt> ...';
    copy($configFile.'.dist', $configFile);
    if (file_exists($configFile)) {
        echo '<strong style="color:green"> done</strong>';
        $config = include $configFile;
        $db = new MySQLi($config['Database']['Host'], $config['Database']['Username'],
                         $config['Database']['Password'], $config['Database']['Database'],
                         +$config['Database']['Port'], $config['Database']['Socket']);
    } else {
        echo '<strong style="color:red"> failed</strong></p>';
        echo '<p>Copy <tt>config/config.php.dist</tt> to <tt>config/config.php</tt>';
        $ok = FALSE;
    }
}
?>
</p>

<?php checkOk(); ?>

<h3>3. Check database</h3>

<p>
    Connect to database:

<?php

if (!$db->connect_error) {
    echo '<strong style="color:green"> ok</strong>';
} else {
    echo '<strong style="color:red"> ', htmlspecialchars($db->connect_error), '</strong>';
    echo '</p><p>';
    echo 'Please check your database settings in <tt>config/config.php</tt> section <tt>"Database"</tt>.';
    $ok = FALSE;
}

if ($ok):

?>
</p>

<p>
    Check database content:

<?php

    $db->query('SELECT count(0) FROM `pvlng_babelkit`');

    if (!$db->error) {
        echo '<strong style="color:green"> ok</strong>';
    } else {
        echo '<strong style="color:red"> failed</strong>';
        echo '</p><p>';
        echo 'Did you loaded the SQL data from <tt><strong>sql/pvlng.sql</strong></tt>?!';
        $ok = FALSE;
    }

endif;

?>
</p>

<?php checkOk(); ?>

<h3>Finished</h3>

<p>
    <form action="/adminpass"><input type="submit" value="Definition of your administration user" /></form>
</p>

</body>
</html>
