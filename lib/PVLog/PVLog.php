<?php
/**
 * Copyright (c) 2015 PV-Log.com, Top50-Solar
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 */
namespace PVLog;

/**
 * Class provides autoload funtionality for all PVLog classes
 *
 * This class is only required, if you use the classes without composer.
 * You can register then the autoloader for the classes
 * with **`PVLog\PVLog::registerAutoloader();`**
 *
 * @author   Knut Kohl <kohl@top50-solar.de>
 * @license  MIT License (MIT) http://opensource.org/licenses/MIT
 * @version  PVLog JSON 1.1
 * @since    2015-04-08
 * @since    v1.0.0
 */
abstract class PVLog
{

    // -----------------------------------------------------------------------
    // PUBLIC
    // -----------------------------------------------------------------------

    /**
     * Register PVLog autoloader
     *
     * @return bool Returns TRUE on success or FALSE on failure.
     */
    public static function registerAutoloader()
    {
        return spl_autoload_register(__NAMESPACE__ . '\\PVLog::autoload');
    }

    /**
     * PVLog autoloader
     *
     * @internal
     * @param string $class Class to load
     */
    public static function autoload($class)
    {
        $class = str_replace('PVLog\\', '', $class);
        $class = str_replace('\\', DIRECTORY_SEPARATOR, $class);
        $fileName = __DIR__ . DIRECTORY_SEPARATOR . $class . '.php';
        if (file_exists($fileName)) {
            require $fileName;
            return true;
        }
        return false;
    }
}
