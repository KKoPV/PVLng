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

	/**
	 * API r2
	 */
	'api/r2/help'                         => array( 'API', 'Index_r2', array('GET') ),

	'api/r2/status/:section?'             => array( 'API', 'Index_r2', array('GET') ),
	'api/r2/statistics/:section?'         => array( 'API', 'Index_r2', array('GET') ),

	'api/r2/data/:guid'                   => array( 'API', 'Index_r2', array('PUT') ),
	// 2 add. parameters used by extrator classes like PVLog
	'api/r2/data/:guid/:p1?/:p2?'         => array( 'API', 'Index_r2', array('GET') ),

	'api/r2/batch/:guid'                  => array( 'API', 'Index_r2', array('PUT') ),

	'api/r2/attributes/:guid/:attribute?' => array( 'API', 'Index_r2', array('GET') ),

	'api/r2/log'                          => array( 'API', 'Index_r2', array('PUT') ),
	'api/r2/log/:id'                      => array( 'API', 'Index_r2', array('GET', 'POST', 'DELETE') ),

	/**
	 * API r1
	 *
	 * DEPRECIATED
	 *
	 */
	'api/r1/*' => array( 'API', 'Index_r1', array('GET', 'PUT') ),
	'api/log'  => array( 'API', 'Log',      array('GET', 'PUT') ),

);