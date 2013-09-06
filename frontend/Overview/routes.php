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

	'overview'             => array( 'Overview', 'Index', array('GET') ),

	'overview/:Action'     => array( 'Overview', NULL,    array('GET', 'POST') ),
	'overview/:Action/#id' => array( 'Overview', NULL,    array('GET', 'POST') ),

);
