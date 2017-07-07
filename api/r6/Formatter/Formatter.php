<?php
/**
 * PVLng - PhotoVoltaic Logger new generation
 *
 * @link       https://github.com/KKoPV/PVLng
 * @link       https://pvlng.com/
 * @author     Knut Kohl <github@knutkohl.de>
 * @copyright  2012 Knut Kohl
 * @license    MIT License (MIT) http://opensource.org/licenses/MIT
 */
namespace Formatter;

/**
 *
 */
use Api\Api;

/**
 *
 */
abstract class Formatter
{
    // -----------------------------------------------------------------------
    // ABSTRACT
    // -----------------------------------------------------------------------
    /**
     *
     */
    abstract public function render($result);

    // -----------------------------------------------------------------------
    // PUBLIC
    // -----------------------------------------------------------------------

    /**
     *
     */
    public function __construct()
    {
        $this->app = Api::getInstance();
    }

    // -----------------------------------------------------------------------
    // PROTECTED
    // -----------------------------------------------------------------------

    /**
     *
     */
    protected $app;
}
