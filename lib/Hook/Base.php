<?php
/**
 *
 */
namespace Hook;

/**
 *
 */
abstract class Base
{
    /**
     *
     */
    public static function dataSaveBefore(&$channel, $config)
    {
    }

    /**
     *
     */
    public static function dataSaveAfter(&$channel, $config)
    {
    }

    /**
     *
     */
    public static function dataReadAfter(&$channel, $config)
    {
    }
}
