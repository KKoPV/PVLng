<?php
/**
 * Common bootstap file
 *
 * @author     Knut Kohl <github@knutkohl.de>
 * @copyright  2012-2014 Knut Kohl
 * @license    MIT License (MIT) http://opensource.org/licenses/MIT
 */

/**
 * Initialize
 */
setlocale(LC_NUMERIC, 'C');

iconv_set_encoding('internal_encoding', 'UTF-8');
mb_internal_encoding('UTF-8');

clearstatcache();

/**
 *
 */
require implode(DIRECTORY_SEPARATOR, [__DIR__, 'core', 'PVLng', 'PVLng.php']);
