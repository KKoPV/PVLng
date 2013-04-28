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

	'channel'            => array( 'Channel', 'Index' ),
	'channel/index'      => array( 'Channel', 'Index' ),

	'channel/add'        => array( 'Channel', 'Add' ),
	'channel/add/:clone' => array( 'Channel', 'Add' ),

	'channel/edit'       => array( 'Channel', 'Edit' ),
	'channel/edit/:id'   => array( 'Channel', 'Edit' ),

	'channel/delete'     => array( 'Channel', 'Delete' ),

);