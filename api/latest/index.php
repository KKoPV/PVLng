<?php
/**
 * Detect latest API version and run it
 *
 * @author     Knut Kohl <github@knutkohl.de>
 * @copyright  2012-2014 Knut Kohl
 * @license    MIT License (MIT) http://opensource.org/licenses/MIT
 * @version    1.0.0
 */

// Search version directories
$version = glob('..'.DIRECTORY_SEPARATOR.'r*', GLOB_ONLYDIR);

// Get last one ...
$version = array_pop($version);
// and split
$version = explode(DIRECTORY_SEPARATOR, $version);

include '..'.DIRECTORY_SEPARATOR.array_pop($version).DIRECTORY_SEPARATOR.'index.php';
