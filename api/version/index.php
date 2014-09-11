<?php
/**
 * Detect latest API version
 *
 * @author     Knut Kohl <github@knutkohl.de>
 * @copyright  2012-2014 Knut Kohl
 * @license    MIT License (MIT) http://opensource.org/licenses/MIT
 * @version    1.0.0
 */

// Send plain text
header('Content-Type: text/plain');

// Search version directories
$version = glob('..'.DIRECTORY_SEPARATOR.'r*', GLOB_ONLYDIR);

// Get last one ...
$version = array_pop($version);
// and split
$version = explode(DIRECTORY_SEPARATOR, $version);

die(array_pop($version));
