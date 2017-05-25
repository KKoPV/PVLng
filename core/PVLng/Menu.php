<?php
/**
 *
 *
 * @author      Knut Kohl <github@knutkohl.de>
 * @copyright   2012-2014 Knut Kohl
 * @license     MIT License (MIT) http://opensource.org/licenses/MIT
 * @version     1.0.0
 */
namespace PVLng;

/**
 *
 */
use I18N;

/**
 *
 */
class Menu
{
    /**
     *
     */
    public $PathSeparator = '.';

    /**
     *
     */
    public function add($path, $route, $label, $active = true, $hint = '')
    {
        $path = explode($this->PathSeparator, $path);
        // Root pointer
        $p =& $this->items;

        // Move pointer along the path
        foreach ($path as $id => $key) {
            if ($id == 0) {
                $p = &$p[$key];
                if (!is_array($p)) {
                    $p = array();
                }
            } else {
                $sub = 'SUBMENU'.$id;
                if (!array_key_exists($sub, $p)) {
                    $p[$sub] = array();
                }
                if ($key == '') {
                    $key = count($p[$sub])*100;
                }
                $p = &$p[$sub][$key];
            }
        }
        $p['ROUTE']  = $route;
        $p['ACTIVE'] = $active;
        $p['_LABEL'] = $label;
        $p['_HINT']  = $hint;

        return $key;
    }

    /**
     *
     */
    public function get()
    {
        // Sort menu and sub menus by key
        $this->sortMenu($this->items); // recursive
        return $this->items;
    }

    // -----------------------------------------------------------------------
    // PROTECTED
    // -----------------------------------------------------------------------

    /**
     *
     */
    protected $items = array();

    /**
     *
     */
    protected function sortMenu(&$menu, $level = 0)
    {
        ksort($menu);
        foreach ($menu as &$item) {
            // If text starts with ':', it is raw data, don't translate
            if (isset($item['_LABEL'])) {
                $item['LABEL'] = preg_match('~^:(.*)$~', $item['_LABEL'], $args)
                               ? $args[1]
                               : I18N::translate($item['_LABEL']);
            }
            if (isset($item['_HINT'])) {
                $item['HINT'] = preg_match('~^:(.*)$~', $item['_HINT'], $args)
                              ? $args[1]
                              : I18N::translate($item['_HINT']);
            }
            $sub = 'SUBMENU'.($level+1);
            if (isset($item[$sub])) {
                $this->sortMenu($item[$sub], $level+1);
            }
        }
    }
}
