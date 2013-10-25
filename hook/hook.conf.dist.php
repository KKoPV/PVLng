<?php
/**
 * Hooks must be defined in directory public_html/hooks
 *
 * The system
 * - looks for a file <hook name>.class.php
 * - require_once $file
 * - calls <hook name>( &$entity, $config )
 *
 * Example
 *
 * 'data.save.after' => array(
 *   'MyHook' => array(
 *     config data ...
 *   )
 * ),
 *
 * file: MyHook.class.php
 * function: data_save_after( &$entity, $config )
 *
 * So multiple hook functions can be defined in file
 *
 */
return array(

	'data.save.before' => array(
	),

	'data.save.after' => array(
	),

	'data.read.after' => array(
	),

);
