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
 * Class to hold all property names used as JSON object keys
 *
 * @author   Knut Kohl <kohl@top50-solar.de>
 * @license  http://opensource.org/licenses/MIT MIT License (MIT)
 * @version  PVLog JSON 1.1
 * @since    2015-04-08
 * @since    v1.0.0
 */
abstract class Properties
{

    // -----------------------------------------------------------------------
    // PUBLIC
    // -----------------------------------------------------------------------

    /**
     * Property name for creator (Instance)
     */
    const CREATOR = 'creator';

    /**
     * Property name for version (Instance)
     */
    const VERSION = 'version';

    /**
     * Property name for delete day before import flag (Instance)
     */
    const DELETE_DAY_BEFORE_IMPORT = 'deleteDayBeforeImport';

    /**
     * Property name for file content type (Instance)
     */
    const FILE_CONTENT = 'fileContent';

    /**
     * Value for minutes file content type (Instance)
     */
    const FILE_CONTENT_MINUTES = 'minutes';

    /**
     * Value for days file content type (Instance)
     */
    const FILE_CONTENT_DAYS = 'days';

    /**
     * Value for months file content type (Instance)
     */
    const FILE_CONTENT_MONTHS = 'months';

    /**
     * Property name for plant section (Instance)
     */
    const PLANT = 'plant';

    /**
     * Property name for grid feed in data section (Instance)
     */
    const FEED_IN = 'feedIn';

    /**
     * Property name for grid consumption data section (Instance)
     */
    const GRID_CONSUMPTION = 'gridConsumption';

    /**
     * Property name for total consumption data section (Instance)
     */
    const TOTAL_CONSUMPTION = 'totalConsumption';

    /**
     * Property name for self consumption data section (Instance)
     */
    const SELF_CONSUMPTION = 'selfConsumption';

    /**
     * Property name for irradiation data section (Instance)
     */
    const IRRADIATION = 'irradiation';

    /**
     * Property name for temperature data in °C (Instance, Inverter)
     */
    const TEMPERATURE = 'temperature';

    /**
     * Property name for battery input data section (Instance)
     */
    const BATTERY_IN = 'batteryIn';

    /**
     * Property name for battery output data section (Instance)
     */
    const BATTERY_OUT = 'batteryOut';

    /**
     * Property name for battery charge status data in % (Instance)
     */
    const BATTERY_CHARGE_STATUS = 'batteryChargeStatus';

    /**
     * Property name for inverter section (Plant)
     */
    const INVERTER = 'inverter';

    /**
     * Property name for inverter strings section (Inverter)
     */
    const STRINGS = 'strings';

    /**
     * Property name for power data in watts (PowerSensor and derived)
     */
    const POWER = 'powerAcWatts';

    /**
     * Property name for energy data in kilo watt hours (EnergyMeter and derived)
     */
    const ENERGY = 'totalWattHours';

}
