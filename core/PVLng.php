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
    public static function Menu( $module, $pos, $route, $label, $hint='' ) {
        while (isset(self::$Menu[$pos])) $pos++;
        self::$Menu[$pos] = array(
            'MODULE' => $module,
            'ROUTE'  => $route,
            'LABEL'  => $label,
            'HINT'   => $hint
        );
    }

    /**
     *
     */
    public static function getMenu() {
        ksort(self::$Menu);
        return self::$Menu;
    }

    /**
     *
     */
    public static function SubMenu( $modules, $pos, $route, $label, $hint='' ) {

        if (!is_array($modules)) $modules = array($modules);

        foreach ($modules as $module) {
            if (!isset(self::$SubMenu[$module])) self::$SubMenu[$module] = array();

            $p = $pos;
            while (isset(self::$SubMenu[$module][$p])) $p++;

            self::$SubMenu[$module][$p] = array(
                'ROUTE'  => $route,
                'LABEL'  => $label,
                'HINT'   => $hint
            );
        }
    }

    /**
     *
     */
    public static function getSubMenu( $module=NULL ) {
        foreach (self::$SubMenu as &$submenu) {
            ksort($submenu);
        }
        if (isset(self::$SubMenu[$module])) {
            return self::$SubMenu[$module];
        } elseif ($module === NULL) {
            return self::$SubMenu;
        };
    }

    /**
     *
     */
    public static function Language( $code, $label, $position=0 ) {
        while (isset(self::$Language[$position])) $position++;
        self::$Language[$position] = array(
            'POSITION' => $position,
            'CODE'     => $code,
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
    protected static $SubMenu = array();

    /**
     *
     */
    protected static $Language = array();

}
