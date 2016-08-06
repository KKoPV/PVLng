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
 * Abstract base class for all Energy meters, responsible for
 * "Power AC Watts" and "Total Watt hours" data
 *
 * @author   Knut Kohl <kohl@top50-solar.de>
 * @license  http://opensource.org/licenses/MIT MIT License (MIT)
 * @version  PVLog JSON 1.1
 * @since    2015-03-14
 * @since    v1.0.0
 */
abstract class EnergyMeter extends PowerSensor
{

    // -----------------------------------------------------------------------
    // PUBLIC
    // -----------------------------------------------------------------------

    /**
     * Class constructor
     *
     * @todo 2.0 - $this->data['lifetime'] = 0;
     *
     * @param array $data Data to build from
     */
    public function __construct($data=array())
    {
        // Set the defaults
        $this->clearTotalWattHours();
        $this->lifeTimeData = false;

        if (isset($data[Properties::ENERGY]) && !is_array($data[Properties::ENERGY]) &&
            isset($data[Properties::POWER]) && count($data[Properties::POWER])) {
            // Build from minutes file, set timestamp of totalWattHours
            // to last timestamp of powerAcWatts
            $timestamps = array_keys($data[Properties::POWER]);
            $data[Properties::ENERGY] = array(max($timestamps) => $data[Properties::ENERGY]);
        }

        parent::__construct($data);
    }

    /**
     * General function to add measuring values
     *
   * @param  string|integer $datetime Timestamp
   * @param  float $value Value
     * @return self For fluid interface
     */
    public function addTotalWattHours($datetime, $value)
    {
        $this->data[Properties::ENERGY][$datetime] = $value;
        return $this;
    }

    /**
     * Set total from PVLog\Classes\Json\Set instance or an array
     *
     * @param Set|array|numeric $data Set instance or an array of arrays
     *                                [ datetime: value ] or single value
     * @return self For fluid interface
     */
    public function setTotalWattHours($data)
    {
        $this->data[Properties::ENERGY] = $data instanceof Set
                                        ? $data
                                        : new Set($data);
        return $this;
    }

    /**
     * Get totals
     *
     * @return PVLog\Classes\Json\Set
     */
    public function getTotalWattHours()
    {
        return $this->data[Properties::ENERGY];
    }

    /**
     * Reset totals
     *
     * @return self For fluid interface
     */
    public function clearTotalWattHours()
    {
        $this->data[Properties::ENERGY] = new Set;
        return $this;
    }

    /**
     * Count of data records
     *
     * @return integer
     */
    public function countTotalWattHours()
    {
        return count($this->data[Properties::ENERGY]);
    }

    /**
     * Setter for creator, must be not empty
     *
     * @param bool $lifeTimeData
     * @return self For fluid interface
     */
    public function setLifeTimeData($lifeTimeData=true)
    {
        // Force boolean value
        $this->lifeTimeData = !!$lifeTimeData;
        return $this;
    }

    /**
     * Getter for creator
     *
     * @return string
     */
    public function getLifeTimeData()
    {
        return $this->lifeTimeData;
    }

    /*
     * Overloaded
     */
    public function interpolate()
    {
        parent::interpolate();

        // If no powerAcWatts was provided, calculate from "energy"
        if (!count($this->data[Properties::POWER]) && count($this->data[Properties::ENERGY])) {
            $last = false;
            foreach ($this->data[Properties::ENERGY] as $timestamp=>$value) {
                // May be, we have here datetimes, so reconvert to timestamp
                $ts = Helper::asTimestamp($timestamp);
                if ($last) {
                    $this->data[Properties::POWER][$timestamp] = 3600 / ($ts - $last[0]) * ($value - $last[1]);
                }
                $last = array($ts, $value);
            }
        }

        // If no totalWattHours was provided, calculate from "power"
        if (!count($this->data[Properties::ENERGY]) && count($this->data[Properties::POWER])) {
            $last  = false;
            $total = 0;
            foreach ($this->data[Properties::POWER] as $timestamp=>$value) {
                // May be, we have here datetimes, so reconvert to timestamp
                $ts = Helper::asTimestamp($timestamp);
                if ($last) {
                    $total += $value * ($ts - $last) / 3600;
                }
                $this->data[Properties::ENERGY][$timestamp] = $total;
                $last = $ts;
            }
        }

        return $this;
    }

    /*
     * Overloaded
     */
    public function asArray($flags=0)
    {
        $result = parent::asArray($flags);

        if (!($flags & self::INTERNAL)) {

            if ($flags & self::EXPORT_POWER) {
                // minutes file
                // Check energy data for lifetime data
                if ($this->lifeTimeData) {
                    // Work on a copy!
                    $a = $result[Properties::ENERGY];
                    // Get 1st value
                    $offset = array_shift($a);
                    if ($offset) {
                        // Reduce all values by $offset
                        foreach ($result[Properties::ENERGY] as $key=>$value) {
                            $result[Properties::ENERGY][$key] = $value - $offset;
                        }
                    }
                }

                // Get only last value of Watt hours array
                $result[Properties::ENERGY] = count($result[Properties::ENERGY])
                                            ? round(array_pop($result[Properties::ENERGY]))
                                            : 0;
            } else {
                // day or month file, no power values
                unset($result[Properties::POWER]);
                // Round energies
                $result[Properties::ENERGY] = array_map('round', $result[Properties::ENERGY]);
            }
        }

        return $result;
    }

    // -----------------------------------------------------------------------
    // PROTECTED
    // -----------------------------------------------------------------------

    /**
     * Watt hours data contains lifetime data, no reset on every day start
     *
     * @var bool $lifeTimeData
     */
    protected $lifeTimeData;

    /**
     * Valid child sections
     *
     * @var array $validSections
     */
    protected $validSections = array(
        Properties::POWER,
        Properties::ENERGY,
    );

}
