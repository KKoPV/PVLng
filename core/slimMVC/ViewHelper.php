<?php
/**
 *
 * @author      Knut Kohl <github@knutkohl.de>
 * @copyright   2012 Knut Kohl
 * @license     GNU General Public License http://www.gnu.org/licenses/gpl.txt
 */
namespace slimMVC;

/**
 *
 */
class ViewHelper
{
    /**
     *
     */
    public function isCallable($method)
    {
        return isset($this->closures[$method]);
    }

    /**
     *
     */
    public function __set($method, $closure)
    {
        $this->closures[$method] = $closure;
    }

    /**
     *
     */
    public function __call($method, $args)
    {
        if (isset($this->closures[$method])) {
            return call_user_func_array($this->closures[$method], $args);
        }
    }

    // -----------------------------------------------------------------------
    // PROTECTED
    // -----------------------------------------------------------------------

    /**
     *
     */
    protected $closures = array();
}
