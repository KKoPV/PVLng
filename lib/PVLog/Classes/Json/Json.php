<?php
/**
 * Copyright (c) 2013 PV-Log.com, Top50-Solar
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
namespace PVLog\Classes\Json;

/**
 * Base class for all PVLog\Classes\Json classes
 *
 * @author   Knut Kohl <kohl@top50-solar.de>
 * @license  http://opensource.org/licenses/MIT MIT License (MIT)
 * @version  PVLog JSON 1.1
 * @since    2015-03-14
 * @since    v1.0.0
 */
abstract class Json
{

    // -----------------------------------------------------------------------
    // CONST
    // -----------------------------------------------------------------------

    /**
     * asArray() returns timestamps in data sections as delivered
     *
     * @internal
     */
    const DATETIME = 1;

    /**
     * Force flag, delete day before import
     *
     * @internal
     */
    const FORCE = 2;

    /**
     * Export 'totalWattHours' from meter channels only for day/month files
     *
     * @internal
     */
    const EXPORT_POWER = 4;

    /**
     * Internal asArray, no removal of sections
     *
     * @internal
     */
    const INTERNAL = 8;

    // -----------------------------------------------------------------------
    // PUBLIC
    // -----------------------------------------------------------------------

    /**
     * Class constructor
     *
     * @param array $data Data to build from
     */
    public function __construct($data=array())
    {
        if (is_array($data)) {
            foreach ($data as $key=>$value) {
                $class = self::section2class($key);
                if (class_exists(__NAMESPACE__ . '\\' . $class)) {
                    $value = self::factory($class, $value);
                }
                $this->data[$key] = $value;
            }
        }
    }

    /**
     * Factory method for PVLog\Classes\Json\ classes
     *
     * Need to be reimplemented to factory sub channels correct
     *
     * @internal
     * @param  string $class Class to create WITHOUT PVLog\JSON2 namespace prefix
     * @param  array $data Data for the new instance
     * @return instance Instance of PVLog\JSON2\$class
     */
    public static function factory($class, $data=array())
    {
        $instance = null;

        $class = __NAMESPACE__ . '\\' . self::section2class($class);

        // Numeric indexed array, ONLY for inverters or strings allowed!
        if (is_array($data) &&
            // HAVE to match also empty arrays!
            (array_keys($data) == range(0, count($data)-1) ||
             array_keys($data) == range(1, count($data)))) {
            foreach ($data as $value) {
                $instance[] = new $class($value);
            }
        } else {
            $instance = new $class($data);
        }

        return $instance;
    }

    /**
     * Magic setter for object properties
     *
     * @throws InvalidArgumentException
     * @see    set()
     * @param  string       $name Property name
     * @param  object|array $data Instance of $name or array of Strings
     * @return void
     */
    public function __set($name, $data)
    {
        $this->set($name, $data);
    }

    /**
     * Setter for object properties
     *
     * @throws InvalidArgumentException
     * @see    __set()
     * @param  string       $name Property name
     * @param  object|array $data
     */
    public function set($name, $data)
    {
        if (in_array($name, $this->validSections)) {

            $validClass = __NAMESPACE__.'\\'.self::section2class($name);

            if ($data instanceof $validClass) {
                $this->data[$name] = $data;
                return $this;
            }

            $msg = is_object($data)
                 ? 'object '.get_class($data)
                 : print_r($data, true);

            throw new \InvalidArgumentException(
                'Wrong data '.$msg.' for '.get_class($this).'::'.$name.' - expect '.$validClass
            );
        }

        throw new \InvalidArgumentException(
            'Invalid property '.get_class($this).'::'.$name
        );
    }

    /**
     * Setter for array typed object properties
     *
     * @throws InvalidArgumentException
     * @see    set()
     * @param  string  $name Property name
     * @param  object  $data Instance of $name
     */
    public function add($name, $data)
    {
        throw new \InvalidArgumentException(
            'Unknown property: '.get_class($this).'::'.$name
        );
    }

    /**
     * Magic getter for properties
     *
     * @see    get()
     * @param  string $name Property name
     * @return mixed
     */
    public function __get($name)
    {
        return $this->get($name);
    }

    /**
     * Getter for properties
     *
     * @see    __get()
     * @param  string $name Property name
     * @return mixed
     */
    public function get($name)
    {
        return array_key_exists($name, $this->data)
             ? $this->data[$name]
             : null;
    }

    /**
     * Interpolate the whole instance data
     *
     * @return self For fluid interface
     */
    public function interpolate()
    {
        foreach ($this->data as $key1=>$value1) {
            if ($value1 instanceof Json) {
                $value1->interpolate();
            } elseif (is_array($value1)) {
                // Inverter or Strings
                foreach($value1 as $key2=>$value2) {
                    $value2->interpolate();
                }
            }
        }

        return $this;
    }

    /**
     * Return the whole data (recursive) as array
     *
     * @param integer $flags Feature flags, see PVLog\Classes\Json\Json constants
     * @return array
     */
    public function asArray($flags=0)
    {
        // Work on a copy of data
        $data = array();

        foreach ($this->data as $key1=>$value1) {
            if (is_array($value1)) {
                foreach($value1 as $key2=>$value2) {
                    $data[$key1][$key2] = $value2->asArray($flags);
                }
            } elseif ($value1 instanceof Json) {
                $data[$key1] = $value1->asArray($flags);
            } else {
                $data[$key1] = $value1;
            }
        }

        return $data;
    }

    /**
     * Find different class name for section defined in self::$classMap
     *
     * @internal
     * @param  string $section Section name
     * @return string Section with 1st char uppercase if no different class was found
     */
    public static function section2class($section)
    {
        $section = strtolower($section);
        return isset(self::$classMap[$section])
             ? self::$classMap[$section]
             : ucwords($section);
    }

    // -----------------------------------------------------------------------
    // PROTECTED
    // -----------------------------------------------------------------------

    /**
     * Valid child property sections
     *
     * @var array $validSections
     */
    protected $validSections = array();

    /**
     * @internal
     * @var array $classMap Mapping of section to class name for not standard section names
     */
    protected static $classMap = array(
        'poweracwatts'          => 'Set',
        'totalwatthours'        => 'Set',
        // Need mappings for UpperCamelCase class names
        'feedin'                => 'FeedIn',
        'gridconsumption'       => 'GridConsumption',
        'totalconsumption'      => 'TotalConsumption',
        'selfconsumption'       => 'SelfConsumption',
        'batteryin'             => 'BatteryIn',
        'batteryout'            => 'BatteryOut',
        'batterychargestatus'   => 'BatteryChargeStatus'
    );

    /**
     * @internal
     * @var array $data Data storage
     */
    protected $data = array();

    /**
     * Count of array properties
     *
     * @internal
     * @param  string $name Property name
     * @return int
     */
    protected function _count($name)
    {
        return (array_key_exists($name, $this->data) && is_array($this->data[$name]))
             ? count($this->data[$name])
             : 0;
    }

}
