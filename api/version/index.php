<?php
/**
 * Return latest API version
 *
 * @author     Knut Kohl <github@knutkohl.de>
 * @copyright  2012-2014 Knut Kohl
 * @license    MIT License (MIT) http://opensource.org/licenses/MIT
 * @version    1.0.0
 */

Header('Content-Type: text/plain');

die(file_get_contents('..'.DIRECTORY_SEPARATOR.'.latest'));
