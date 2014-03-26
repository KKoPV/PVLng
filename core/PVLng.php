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
