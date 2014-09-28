<?php
/**
 *
 *
 * @author      Knut Kohl <github@knutkohl.de>
 * @copyright   2012-2014 Knut Kohl
 * @license     MIT License (MIT) http://opensource.org/licenses/MIT
 * @version     1.0.0
 */
abstract class PVLng {

    /**
     *
     */
    public static $MenuPathSeparator = '.';

    /**
     *
     */
    public static function Menu( $path, $route, $label, $hint='' ) {
    $path = explode(self::$MenuPathSeparator, $path);
        // Root pointer
        $menu =& self::$Menu;

        // Move pointer along the path
        foreach ($path as $id=>$key) {
            if ($id == 0) {
                $menu = &$menu[$key];
            } else {
                $sub = 'SUBMENU'.$id;
                if (!array_key_exists($sub, $menu)) $menu[$sub] = array();
                if ($key == '') $key = count($menu[$sub]);
                $menu = &$menu[$sub][$key];
            }
        }
        $menu['ROUTE'] = $route;
        $menu['LABEL'] = $label;
        $menu['HINT']  = $hint;

        return $key;
    }

    /**
     *
     */
    public static function getMenu() {
        // Sort menu and sub menus by key
        self::sortMenu(self::$Menu);
        return self::$Menu;
    }

    /**
     *
     */
    public static function Language( $code, $label, $icon=NULL, $position=0 ) {
        while (isset(self::$Language[$position])) $position++;
        self::$Language[$position] = array(
            'CODE'     => $code,
            'ICON'     => $icon ?: $code,
            'LABEL'    => $label
        );
    }

    /**
     *
     */
    public static function getLanguages() {
        ksort(self::$Language);
        return self::$Language;
    }

    /**
     *
     */
    public static function getLoginToken() {
        $app = slimMVC\App::getInstance();
        return sha1(__FILE__ . "\x00" . sha1(
            $_SERVER['REMOTE_ADDR'] . "\x00" .

            strtolower($app->config->get('Admin.User')) . "\x00" .
            $app->config->get('Admin.Password')
        ));
    }

    // -----------------------------------------------------------------------
    // PROTECTED
    // -----------------------------------------------------------------------

    /**
     *
     */
    protected static $Menu = array();

    /**
     *
     */
    protected static $Language = array();

    /**
     *
     */
    protected static function sortMenu(&$menu, $level='') {
        ksort($menu);
        foreach ($menu as &$item) {
            $sub = 'SUBMENU'.($level+1);
            if (isset($item[$sub])) self::sortMenu($item[$sub]);
        }
    }

}
