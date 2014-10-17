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
class Config extends \Slim\Helper\Set {

    /**
     *
     */
    public $NamespaceSeparator = '.';

    /**
     *
     */
    public function load( $file, $required=TRUE, $namespace='' ) {
        if ($required AND !file_exists($file)) {
            throw new \Exception('Missing reqiured configuration file: '.$file);
        }
        if (file_exists($file)) {
            $data = include $file;
            $data = $this->array_change_key_case_deep($data);
            $p =& $this->data;
            $key = explode($this->NamespaceSeparator, mb_strtolower($namespace));
            while ($k = array_shift($key)) $p =& $p[$k];
            $p = $this->array_replace_deep($p, $data);
        }
        return $this;
    }

    /**
     *
     */
    public function loadNamespace( $namespace, $file, $required=TRUE ) {
        if (isset($file) AND (file_exists($file) OR $required)) {
            $data = include $file;
            $data = $this->array_change_key_case_deep($data);
            $this->set($namespace, $this->array_replace_deep($this->get($namespace), $data));
        }
        return $this;
    }

    /**
     *
     */
    public function set( $key, $value ) {
        $key = explode($this->NamespaceSeparator, mb_strtolower($key));
        $current =& $this->data;
        while ($k = array_shift($key)) $current =& $current[$k];
        $current = $value;
        return $this;
    }

    /**
     *
     */
    public function __set( $key, $value ) {
        return $this->set(str_replace('_', $this->NamespaceSeparator, $key), $value);
    }

    /**
     *
     */
    public function get( $key, $default=NULL ) {
        $key = explode($this->NamespaceSeparator, mb_strtolower($key));
        $current =& $this->data;
        while ($k = array_shift($key)) {
            if (!isset($current[$k])) return $default;
            $current =& $current[$k];
        }
        return $current;
    }

    /**
     *
     */
    public function __get( $key ) {
        return $this->get(str_replace('_', $this->NamespaceSeparator, $key));
    }

    // -------------------------------------------------------------------------
    // PROTECTED
    // -------------------------------------------------------------------------

    /**
     *
     */
    protected function array_change_key_case_deep( $data ) {
        if (!is_array($data)) return $data;

        $arr = array();
        foreach ($data as $key=>$value) {
            $arr[mb_strtolower($key)] = $this->array_change_key_case_deep($value);
        }
        return $arr;
    }

    /**
     *
     */
    protected function array_replace_deep($base, $replace) {
        // Loop through array key/value pairs
        if (is_array($replace)) {
            foreach ($replace as $key=>$value) {
                if (is_array($value)) {
                    // Value is an array
                    // Traverse the array; replace or add result to original array
                    $base[$key] = $this->array_replace_deep(isset($base[$key])?$base[$key]:array(), $value);
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
