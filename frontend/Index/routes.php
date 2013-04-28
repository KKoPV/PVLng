<?php
/**
 *
 *
 * @author      Knut Kohl <github@knutkohl.de>
 * @copyright   2012-2013 Knut Kohl
 * @license     GNU General Public License http://www.gnu.org/licenses/gpl.txt
 * @version     $Id$
 */

/**
 * '{route}' => array( '{controller}', '{action}' ),
 */
return array(

	''                  => array( 'Index', 'Index' ),
	'index'             => array( 'Index', 'Index' ),
	'index/index'       => array( 'Index', 'Index' ),

	'login'             => array( 'Index', 'Login' ),
	'logout'            => array( 'Index', 'Logout' ),

	'index/:Action'     => array( 'Index', NULL ),
	'index/:Action/#id' => array( 'Index', NULL ),

	'adminpass'         => array( 'Index', 'AdminPassword' )

);