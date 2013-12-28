<?php
/**
 *
 */
namespace Hook;

/**
 *
 */
abstract class Base {

    /**
     *
     */
    public static function data_save_before ( &$channel, $config ) {}

    /**
     *
     */
    public static function data_save_after ( &$channel, $config ) {}

    /**
     *
     */
    public static function data_read_after ( &$channel, $config ) {}

}
