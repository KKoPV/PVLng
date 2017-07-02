<?php
/**
 *
 *
 * @link       https://github.com/KKoPV/PVLng
 * @author     Knut Kohl <github@knutkohl.de>
 * @copyright  2012-2016 Knut Kohl
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
