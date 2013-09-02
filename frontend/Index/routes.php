<?php
/**
 *
 *
 * @author      Knut Kohl <github@knutkohl.de>
 * @copyright   2012-2013 Knut Kohl
 * @license     GNU General Public License http://www.gnu.org/licenses/gpl.txt
 * @version     $Id: v1.0.0.2-14-g2a8e482 2013-05-01 20:44:21 +0200 Knut Kohl $
 */

/**
 * <route> => array( <Controller>, <Action> ),
 */
return array(

	''                  => array( 'Index', 'Index' ),
	'index'             => array( 'Index', 'Index' ),
	'index/index'       => array( 'Index', 'Index' ),

	'index'             => array( 'Index', 'Index', array('GET', 'POST') ),
	'index/:view'       => array( 'Index', 'Index', array('GET', 'POST') ),
	'index/:view/:date' => array( 'Index', 'Index' ),

	'chart/:view'       => array( 'Index', 'Index' ),
	'chart/:view/:date' => array( 'Index', 'Index' ),

);
