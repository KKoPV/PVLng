<?php
/**
 * Timezoner settings
 */
return array(

	/**
	 * The offset between your database timestamp and your local time in hours
	 *
	 * Here:
	 * - Database timestamps in UTC (+00:00)
	 * - Local timezone is Europe/Berlin (+01:00)
	 * so 1 - 0 = 1
	 *
	 * @var float
	 */
	'offset' => 1,

	/**
	 * Flag, if your timezone have daylight savings time
	 *
	 * @var integer (0|1)
	 */
	'dst' => 1

);
