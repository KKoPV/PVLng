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

	'api/r1/*' => array( 'API', 'Index_r1', array('GET', 'PUT') ),
	'api/log'  => array( 'API', 'Log' ),

#	'api/r2/*' => array( 'API', 'Index_r2' ),

### OLD
	'api/r2/:guid/attributes/:attribute?' => array( 'API', 'Index_r2' ),

	'api/r2/:guid/save'                   => array( 'API', 'Index_r2', array('PUT') ),
	'api/r2/:guid'                        => array( 'API', 'Index_r2' ),
	'api/r2/:guid/data/:p1?/:p2?'         => array( 'API', 'Index_r2' ),

	'api/r2/log'                          => array( 'API', 'Index_r2', array('PUT') ),
	'api/r2/log/:id'                      => array( 'API', 'Index_r2' ),
	'api/r2/log/:id'                      => array( 'API', 'Index_r2', array('POST') ),
	'api/r2/log/:id'                      => array( 'API', 'Index_r2', array('DELETE') ),
###

	'api/r2/help'                         => array( 'API', 'Index_r2' ),

	'api/r2/status/:section?'             => array( 'API', 'Index_r2' ),

	'api/r2/attributes/:guid/:attribute?' => array( 'API', 'Index_r2' ),

	'api/r2/data/:guid'                   => array( 'API', 'Index_r2', array('PUT') ),
	'api/r2/data/:guid/:mode?'            => array( 'API', 'Index_r2' ),
	'api/r2/data/:guid/attributes/:mode?' => array( 'API', 'Index_r2' ),

	'api/r2/batch/:guid'                  => array( 'API', 'Index_r2', array('PUT') ),

	'api/r2/log'                          => array( 'API', 'Index_r2', array('PUT') ),
	'api/r2/log/:id'                      => array( 'API', 'Index_r2' ),
	'api/r2/log/:id'                      => array( 'API', 'Index_r2', array('POST') ),
	'api/r2/log/:id'                      => array( 'API', 'Index_r2', array('DELETE') ),



);