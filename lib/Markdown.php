<?php
/**
 * PSR 1 compatible wrapper for Markdown parser class
 *
 * @author     Knut Kohl <github@knutkohl.de>
 * @copyright  2012 Knut Kohl
 * @license    MIT License (MIT) http://opensource.org/licenses/MIT
 */
use Core\PVLng;

/**
 * Load original class
 */
require_once PVLng::path(__DIR__, 'contrib', 'markdown.php');

/**
 * Wrapper class
 */
class Markdown extends Markdown_Parser
{
}
