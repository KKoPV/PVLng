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
 * Abstract power sensor class, responsible for the "Power AC Watts" data
 *
 * @author   Knut Kohl <kohl@top50-solar.de>
 * @license  http://opensource.org/licenses/MIT MIT License (MIT)
 * @version  PVLog JSON 1.1
 * @since    2015-03-14
 * @since    v1.0.0
 */
abstract class PowerSensor extends Json
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
        $this->clearPowerAcWatts();
        parent::__construct($data);
    }

    /**
     * General function to add measuring values
     *
     * @param  string|integer $datetime Timestamp
     * @param  float $value Value
     * @return self For fluid interface
     */
    public function addPowerAcWatts($datetime, $value)
    {
        $this->data[Properties::POWER][$datetime] = $value;
        return $this;
    }

    /**
     * Set data from PVLog\Classes\Json\Set instance or an array
     *
     * @param Set|array|numeric $data Set instance or an array of arrays
     *                                [ datetime: value ] or single value
     * @return self For fluid interface
     */
    public function setPowerAcWatts($data)
    {
        $this->data[Properties::POWER] = $data instanceof Set ? $data : new Set($data);
        return $this;
    }

    /**
     * Get all data
     *
     * @return PVLog\Classes\Json\Set
     */
    public function getPowerAcWatts()
    {
        return $this->data[Properties::POWER];
    }

    /**
     * Reset data
     *
     * @return self For fluid interface
     */
    public function clearPowerAcWatts()
    {
        $this->setPowerAcWatts(new Set);
        return $this;
    }

    /**
     * Count of data records
     *
     * @return integer
     */
    public function countPowerAcWatts()
    {
        return count($this->data[Properties::POWER]);
    }

    /*
     * Overloaded
     */
    public function asArray($flags=0)
    {
        $result = parent::asArray($flags);

        if ($flags & self::EXPORT_POWER) {
            // Minutes file, round powers
            $result[Properties::POWER] = array_map('round', $result[Properties::POWER]);
        }

        return $result;
    }

    // -----------------------------------------------------------------------
    // PROTECTED
    // -----------------------------------------------------------------------

    /**
     * Valid child sections
     *
     * @var array $validSections
     */
    protected $validSections = array(
        Properties::POWER
    );

}
