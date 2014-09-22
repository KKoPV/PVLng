<?php
/**
 * Run latest API version
 *
 * @author     Knut Kohl <github@knutkohl.de>
 * @copyright  2012-2014 Knut Kohl
 * @license    MIT License (MIT) http://opensource.org/licenses/MIT
 * @version    1.0.0
 */

$version = file_get_contents('..'.DIRECTORY_SEPARATOR.'.latest');

include '..'.DIRECTORY_SEPARATOR.$version.DIRECTORY_SEPARATOR.'index.php';
