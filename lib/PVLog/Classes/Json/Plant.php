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
 * Class for plant
 *
 * @author   Knut Kohl <kohl@top50-solar.de>
 * @license  http://opensource.org/licenses/MIT MIT License (MIT)
 * @version  PVLog JSON 1.1
 * @since    2015-03-14
 * @since    v1.0.0
 */
class Plant extends EnergyMeter
{

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
        // Set the defaults
        $this->clearInverters();
        parent::__construct($data);
    }

    /**
     * Add an inverter to inverter section
     *
     * @param  Inverter $inverter
     * @return self For fluid interface
     */
    public function addInverter(Inverter $inverter)
    {
        return $this->add(Properties::INVERTER, $inverter);
    }

    /**
     * Setter for whole inverter section
     *
     * @throws \InvalidArgumentException for invalid data
     * @param  array $inverters Array of PVLog\Classes\Json\Inverter objects
     * @return self For fluid interface
     */
    public function setInverters(Array $inverters)
    {
        return $this->set(Properties::INVERTER, $inverters);
    }

    /**
     * Getter for inverter section
     *
     * @return array of PVLog\Classes\Json\Inverter objects
     */
    public function getInverters()
    {
        return $this->get(Properties::INVERTER);
    }

    /**
     * Clear inverters
     *
     * @return self For fluid interface
     */
    public function clearInverters()
    {
        return $this->set(Properties::INVERTER, array());
    }

    /**
     * Count of inverters
     *
     * @return int
     */
    public function countInverters()
    {
        return $this->_count(Properties::INVERTER);
    }

    /*
     * Overloaded for specific properties
     */
    public function add($property, $data)
    {
        switch (true) {
            case $property == Properties::INVERTER && $data instanceof Inverter:
                $this->data[Properties::INVERTER][] = $data;
                break;
            default:
                parent::add($property, $data);
        }
        return $this;
    }

    /*
     * Overloaded for specific properties
     */
    public function set($property, $data)
    {
        switch (true) {
            case $property == Properties::INVERTER && is_array($data):
                $err = 0;
                foreach ($data as $value) {
                    if (!($value instanceof Inverter)) $err++;
                }
                if ($err) {
                    throw new \InvalidArgumentException(
                        'Property "'.Properties::INVERTER.'" accept only an array of '.__NAMESPACE__.'\Inverter'
                    );
                }
                $this->data[Properties::INVERTER] = $data;
                break;
            default:
                parent::set($property, $data);
        }
        return $this;
    }

    /**
     * Set the plant to an instance
     *
     * @param  Instance $instance
     * @return self For fluid interface
     */
    public function setToInstance(Instance $instance)
    {
        $instance->setPlant($this);
        return $this;
    }

    /*
     * Overloaded
     */
    public function interpolate()
    {
        parent::interpolate();

        // Accumulate data from inverters down to here
        $this->calcFromDataInverters(Properties::POWER, false);
        $this->calcFromDataInverters(Properties::ENERGY, true);

        return $this;
    }

    // -----------------------------------------------------------------------
    // PROTECTED
    // -----------------------------------------------------------------------

    /*
     * Overloaded
     */
    protected $validSections = array(
        Properties::INVERTER,
        Properties::POWER,
        Properties::ENERGY,
    );

    /**
     * Calc power or energy section from inverters sums
     * @internal
     */
    protected function calcFromDataInverters($property, $energy)
    {
        $inverterCount = count($this->data[Properties::INVERTER]);
        if (!$inverterCount) return;

        // Remember RAW DATA to re-apply later!
        // Assume here correct prefilled data!!
        // (without asArray() we would get a pointer to the instance)
        $save = $this->data[$property]->asArray();

        if ($inverterCount == 1) {
            // Take data from 1st inverter 1:1
            $this->data[$property] = clone $this->data[Properties::INVERTER][0]->get($property);
        } else {
            // Combine all inverter data
            // Fin max. timestamp for energy data
            if ($energy) {
                $maxTimestamp = 0;
                foreach ($this->data[Properties::INVERTER] as $inverter) {
                    $maxTimestamp = max($maxTimestamp, max(array_keys($inverter->get($property)->asArray(Json::INTERNAL))));
                }
            }

            foreach ($this->data[Properties::INVERTER] as $inverter) {
                foreach ($inverter->get($property) as $timestamp=>$value) {
                    if (!isset($this->data[$property][$timestamp])) {
                        // 1st value for this timestamp
                        $this->data[$property][$timestamp] = $value;
                    } else {
                        $this->data[$property][$timestamp] += $value;
                    }
                }
                if ($energy) {
                    $last = $this->data[$property][$timestamp];
                    while ($timestamp < $maxTimestamp) {
                        $timestamp += 300;
                        $this->data[$property][$timestamp] += $value;
                    }
                }
            }
        } 

        // Merge given data over calculated data!
        foreach ($save as $timestamp=>$value) {
            $this->data[$property][$timestamp] = $value;
        }

        // Sort by timestamp
        $this->data[$property]->sort();
    }

}
