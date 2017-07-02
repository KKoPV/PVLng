<?php
/**
 *
 * @author     Knut Kohl <github@knutkohl.de>
 * @copyright  2012-2014 Knut Kohl
 * @license    MIT License (MIT) http://opensource.org/licenses/MIT
 * @version    1.0.0
 */
namespace slimMVC;

/**
 *
 */
use Slim\Helper\Set;
use Exception;
use Spyc;

/**
 *
 */
class Config extends Set
{
    /**
     *
     */
    public $NamespaceSeparator = '.';

    /**
     *
     */
    public function load($file, $required = true)
    {
        if ($required && !file_exists($file)) {
            throw new Exception('Missing reqiured configuration file: '.$file);
        }

        if (file_exists($file)) {
            $data = Spyc::YAMLLoad($file);
            $data = $this->arrayChangeKeyCaseDeep($data);
            $this->data = $this->arrayReplaceDeep($this->data, $data);
        }

        return $this;
    }

    /**
     *
     */
    public function loadNamespace($namespace, $file, $required = true)
    {
        if ($required && !file_exists($file)) {
            throw new Exception('Missing reqiured configuration file: '.$file);
        }

        if (file_exists($file)) {
            $data = Spyc::YAMLLoad($file);
            $data = $this->arrayChangeKeyCaseDeep($data);
            $this->set($namespace, $this->arrayReplaceDeep($this->get($namespace), $data));
        }

        return $this;
    }

    /**
     *
     */
    public function set($key, $value)
    {
        $key = explode($this->NamespaceSeparator, mb_strtolower($key));
        $current =& $this->data;
        while ($k = array_shift($key)) {
            $current =& $current[$k];
        }
        $current = $value;
        return $this;
    }

    /**
     *
     */
    public function __set($key, $value)
    {
        return $this->set(str_replace('_', $this->NamespaceSeparator, $key), $value);
    }

    /**
     *
     */
    public function get($key, $default = null)
    {
        $key = explode($this->NamespaceSeparator, mb_strtolower($key));
        $current =& $this->data;
        while ($k = array_shift($key)) {
            if (!isset($current[$k])) {
                return $default;
            }
            $current =& $current[$k];
        }
        return $current;
    }

    /**
     *
     */
    public function __get($key)
    {
        return $this->get(str_replace('_', $this->NamespaceSeparator, $key));
    }

    // -------------------------------------------------------------------------
    // PROTECTED
    // -------------------------------------------------------------------------

    /**
     *
     */
    protected function arrayChangeKeyCaseDeep($data)
    {
        if (!is_array($data)) {
            return $data;
        }

        $arr = array();
        foreach ($data as $key => $value) {
            $arr[mb_strtolower($key)] = $this->arrayChangeKeyCaseDeep($value);
        }
        return $arr;
    }

    /**
     *
     */
    protected function arrayReplaceDeep($base, $replace)
    {
        // Loop through array key/value pairs
        if (is_array($replace)) {
            foreach ($replace as $key => $value) {
                if (is_array($value)) {
                    // Value is an array
                    // Traverse the array; replace or add result to original array
                    $base[$key] = $this->arrayReplaceDeep(isset($base[$key])?$base[$key]:array(), $value);
                } else {
                    // Value is not an array
                    // Replace or add current value to original array
                    $base[$key] = $value;
                }
            }
        }
        // Return the joined array
        return $base;
    }
}
