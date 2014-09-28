<?php
/**
 *
 * @author     Knut Kohl <github@knutkohl.de>
 * @copyright  2012-2014 Knut Kohl
 * @license    MIT License (MIT) http://opensource.org/licenses/MIT
 * @version    1.0.0
 */

/**
 * Basic menu structure
 */
if (Session::get('User')) PVLng::Menu('10','#', __('MasterData'));

PVLng::Menu('20','#', __('Analysis'));
