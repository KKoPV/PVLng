<?php
/**
 *
 */
abstract class PVLng {

    /**
     *
     */
    public static function Menu( Array $menu ) {
        $menu = array_merge(array(
            'position' => 0,
            'label'    => '',
            'hint'     => '',
            'route'    => '',
            'login'    => FALSE,
        ), $menu);
        while (isset(self::$Menu[$menu['position']])) $menu['position']++;
        self::$Menu[$menu['position']] = array_change_key_case($menu, CASE_UPPER);
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
    public static function Language( Array $language ) {
        $language = array_merge(array(
            'position' => 0,
            'code'     => '',
            'label'    => ''
        ), $language);
        while (isset(self::$Language[$language['position']])) $language['position']++;
        self::$Language[$language['position']] = array_change_key_case($language, CASE_UPPER);
    }

    /**
     *
     */
    public static function getLanguages() {
        ksort(self::$Language);
        return self::$Language;
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

}
