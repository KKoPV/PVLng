<?php
/**
 *
 *
 * @author      Knut Kohl <github@knutkohl.de>
 * @copyright   2012-2013 Knut Kohl
 * @license     GNU General Public License http://www.gnu.org/licenses/gpl.txt
 * @version     $Id: v1.0.0.1-5-gea5a016 2013-04-29 21:16:23 +0200 Knut Kohl $
 */

/**
 * <route> => array( <Controller>, <Action> ),
 */
return array(

	'chart'       => array( 'Chart', 'Index' ),
	'chart/:view' => array( 'Chart', 'Index' ),

);