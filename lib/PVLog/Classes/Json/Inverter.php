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
namespace PVLog\Classes\Json;

/**
 * Inverter meter channel with sub channels string and temperature
 *
 * @author   Knut Kohl <kohl@top50-solar.de>
 * @license  http://opensource.org/licenses/MIT MIT License (MIT)
 * @version  PVLog JSON 1.1
 * @since    2015-03-14
 * @since    v1.0.0
 */
class Inverter extends EnergyMeter {

    // -----------------------------------------------------------------------
    // PUBLIC
    // -----------------------------------------------------------------------

    /**
     * Class constructor
     *
     * @param array $data Data to build from
     */
    public function __construct( $data=array() ) {
        // Set the defaults
        $this->clearStrings();

        parent::__construct($data);
    }

    /**
     * Add a string to the inveter
     *
     * @param $string String
     * @return self For fluid interface
     */
    public function addString( Strings $string ) {
        return $this->add(Properties::STRINGS, $string);
    }

    /**
     * Setter for whole string section
     *
     * @param  array $strings Array of PVLog\Classes\Json\Strings objects
     * @return self For fluid interface
     */
    public function setStrings( Array $strings ) {
        return $this->set(Properties::STRINGS, $strings);
  }

    /**
     * Getter for string section
     *
     * @return array of PVLog\Classes\Json\Strings objects
     */
    public function getStrings() {
        return $this->get(Properties::STRINGS);
    }

    /**
     * Clear strings
     *
     * @return self For fluid interface
     */
    public function clearStrings() {
        return $this->set(Properties::STRINGS, array());
    }

    /**
     * Count of strings
     *
     * @return int
     */
    public function countStrings() {
        return $this->_count(Properties::STRINGS);
    }

    /**
     * Setter for temperature section
     *
     * @param Temperature $data
     * @return self For fluid interface
     */
    public function setTemperature( Temperature $data ) {
        return $this->set(Properties::TEMPERATURE, $data);
    }

    /**
     * Getter for temperature section
     *
     * @return PVLog\Classes\Json\Set|NULL
     */
    public function getTemperature() {
        return $this->get(Properties::TEMPERATURE);
    }

    /**
     * Add the inveter to a plant
     *
     * @param  Plant $plant
     * @return self For fluid interface
     */
    public function addToPlant( Plant $plant ) {
        $plant->addInverter($this);
        return $this;
    }

    /*
     * Overloaded for additional properties
     */
    public function add( $property, $data ) {
        switch (TRUE) {
            case $property == Properties::STRINGS && $data instanceof Strings:
                $this->data[Properties::STRINGS][] = $data;
                break;
            default:
                parent::add($property, $data);
        }
        return $this;
    }

    /*
     * Overloaded for additional properties
     */
    public function set( $property, $data ) {
        switch (TRUE) {
            case $property == Properties::STRINGS && is_array($data):
                $err = 0;
                foreach ($data as $value) {
                    if (!($value instanceof Strings)) $err++;
                }
                if ($err) {
                    throw new \InvalidArgumentException(
                        'Property "'.Properties::STRINGS.'" accept only an array of '.__NAMESPACE__.'\Strings'
                    );
                }
                $this->data[Properties::STRINGS] = $data;
                break;
            default:
                parent::set($property, $data);
        }
        return $this;
    }

    /*
     * Overloaded
     */
    public function asArray( $flags=0 ) {
        // Work on a copy of data
        $result = parent::asArray($flags);

        if (!($flags & self::EXPORT_POWER)) {
            // day or month file, no strings
            unset($result['strings']);
        }

        return $result;
    }

    // -----------------------------------------------------------------------
    // PROTECTED
    // -----------------------------------------------------------------------

    /*
     * Overload
     */
    protected $validSections = array(
        Properties::STRINGS,
        Properties::POWER,
        Properties::ENERGY,
        Properties::TEMPERATURE,
    );

}
