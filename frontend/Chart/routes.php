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

	'chart'       => array( 'Chart', 'Index' ),
	'chart/:view' => array( 'Chart', 'Index' ),

);