<?php
/**
 *
 *
 * @author      Knut Kohl <github@knutkohl.de>
 * @copyright   2012-2013 Knut Kohl
 * @license     GNU General Public License http://www.gnu.org/licenses/gpl.txt
 * @version     $Id$
 */
return array(

	// -----------------------------------------------------------------------
	// INTERNAL SETTINGS, DO NOT TOUCH
	// -----------------------------------------------------------------------
	'Router' => array(
		'Class'      => 'yMVC\Router',
		'ErrorRoute' => 'index',
	),

	'Default' => array(
		'Controller' => 'Controller',
		'Model'      => 'Model',
		'View'       => 'View',
	),

	/**
	 * View settings
	 */
	'View' => array(
		'ReuseCode'        => TRUE,
		'Verbose'          => FALSE,
		'JavaScriptPacker' => 62, // FALSE, 0, 10, 62, (95 is not working with UTF-8)

		// XML View settings
		'XML' => array(
			'Node' => 'node',
			'Data' => 'data',
		),
	),

);
