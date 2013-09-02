<?php
/**
 * Hooks must be defined in directory public_hamt/hooks
 *
 * The system
 * - looks for a file <hook name>.php
 * - require_once $file
 * - calls <hook>_<hook name>($entity, $value)
 *
 * Example
 *
 * 'data_save_after' => array(
 *   'MyHook'
 * ),
 *
 * file: MyHook.php
 * function: data_save_after_MyHook($entity, value)
 *
 * So multiple hook functions can be defined in file
 *
 */
return array(

	// Hook function must return $value, also if unchanged!
	'data_save_before' => array(
	),

	'data_save_after' => array(
	),

	// Hook function must return $value, also if unchanged!
	'data_read_after' => array(
	),

);
