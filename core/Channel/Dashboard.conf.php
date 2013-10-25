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

	/**
	 * Possible keys:
	 * - visible			 : (TRUE|FALSE), default TRUE
	 * - default			 : default value
	 */
	'serial' => array(
		'visible' => FALSE,
	),
	'numeric' => array(
		'visible' => FALSE,
	),
	'meter' => array(
		/* Get the info from the child */
		'visible' => FALSE,
	),
	'threshold' => array(
		'visible' => FALSE,
	),
	'cost' => array(
		'visible' => FALSE,
	),
	'valid_from' => array(
		'required' => TRUE,
	),
	'valid_to' => array(
		'required' => TRUE,
	)

);
