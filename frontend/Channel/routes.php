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

	'channel/index'      => array( 'Channel', 'Index',  array('GET') ),

	'channel/add'        => array( 'Channel', 'Add',    array('GET', 'POST') ),
	'channel/add/:clone' => array( 'Channel', 'Add',    array('GET') ),

	'channel/edit'       => array( 'Channel', 'Edit',   array('GET', 'POST') ),
	'channel/edit/:id'   => array( 'Channel', 'Edit',   array('GET', 'POST') ),

	'channel/delete'     => array( 'Channel', 'Delete', array('POST') ),

);