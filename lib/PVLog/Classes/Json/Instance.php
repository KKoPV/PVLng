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
 * Top class for plant installation
 *
 * @author   Knut Kohl <kohl@top50-solar.de>
 * @license  http://opensource.org/licenses/MIT MIT License (MIT)
 * @version  PVLog JSON 1.1
 * @since    2015-03-14
 * @since    v1.0.0
 */
class Instance extends Json
{

    // -----------------------------------------------------------------------
    // PUBLIC
    // -----------------------------------------------------------------------

    /**
     * Build an Instance from JSON file
     *
     * @param string $filename JSON file name to build from
     * @throws \InvalidArgumentException on missing JSON file
     */
    public static function fromJsonFile($filename)
    {
        if (!is_file($filename)) {
            throw new \InvalidArgumentException('Missing JSON file: '.$filename);
        }
        return self::fromJson(file_get_contents($filename));
    }

    /**
     * Build an Instance from JSON string
     *
     * @param  string $json JSON string to build from
     * @throws \InvalidArgumentException on invalid JSON
     */
    public static function fromJson($json)
    {
        $data = json_decode($json, true);
        if (json_last_error() != JSON_ERROR_NONE) {
            throw new \InvalidArgumentException('Invalid JSON: '.$json);
        }
        return new Instance($data);
    }

    /**
     * Class constructor
     *
     * @param array $data Data to build from
     */
    public function __construct($data=array())
    {
        // Set the defaults
        $this->data[Properties::CREATOR] = 'www.pv-log.com';
        $this->data[Properties::VERSION] = '1.1';
        // For default type minutes
        $this->setTypeMinutes();
        $this->data[Properties::DELETE_DAY_BEFORE_IMPORT] = 0;
        $this->data[Properties::PLANT] = null;

        parent::__construct($data);
    }

    /**
     * Setter for creator, must be not empty
     *
     * @param string $creator
     * @return self For fluid interface
     */
    public function setCreator($creator)
    {
        if ($creator != '') {
            $this->data[Properties::CREATOR] = $creator;
        }
        return $this;
    }

    /**
     * Getter for creator
     *
     * @return string
     */
    public function getCreator()
    {
        return $this->data[Properties::CREATOR];
    }

    /**
     * Set file type to 'minutes'
     *
     * Set also the correct datetime format for output: <code>Y-m-d H:i:s</code>
     *
     * @return self For fluid interface
     */
    public function setTypeMinutes()
    {
        $this->data[Properties::FILE_CONTENT] = Properties::FILE_CONTENT_MINUTES;
        Helper::setDateFormatMinutes();
        return $this;
    }

    /**
     * Set file type to 'days'
     *
     * @internal
     * @see setTypeDays()
     * @return self For fluid interface
     */
    public function setTypeDay()
    {
        return $this->setTypeDays();
    }

    /**
     * Set file type to 'days'
     *
     * Set also the correct datetime format for output: <code>Y-m-d</code>
     *
     * @return self For fluid interface
     */
    public function setTypeDays()
    {
        $this->data[Properties::FILE_CONTENT] = Properties::FILE_CONTENT_DAYS;
        Helper::setDateFormatDay();
        return $this;
    }

    /**
     * Set file type to 'months'
     *
     * @internal
     * @see setTypeMonths()
     * @return self For fluid interface
     */
    public function setTypeMonth()
    {
        return $this->setTypeMonths();
    }

    /**
     * Set file type to 'months'
     *
     * Set also the correct datetime format for output: <code>Y-m-t</code>
     *
     * <code>t</code> - Last day of month
     *
     * @return self For fluid interface
     */
    public function setTypeMonths()
    {
        $this->data[Properties::FILE_CONTENT] = Properties::FILE_CONTENT_MONTHS;
        Helper::setDateFormatMonth();
        return $this;
    }

    /**
     * Getter for file type
     *
     * @return string
     */
    public function getType()
    {
        return $this->data[Properties::FILE_CONTENT];
    }

    /**
     * Setter for deleteDayBeforeImport
     *
     * @param integer $delete Default 1
     * @return self For fluid interface
     */
    public function setDeleteDayBeforeImport($delete=1)
    {
        // Make 0 or 1
        $this->data[Properties::DELETE_DAY_BEFORE_IMPORT] = +$delete & 1;
        return $this;
    }

    /**
     * Getter for deleteDayBeforeImport
     *
     * @return integer (0|1)
     */
    public function getDeleteDayBeforeImport()
    {
        return $this->data[Properties::DELETE_DAY_BEFORE_IMPORT];
    }

    /**
     * Setter for plant section
     *
     * @param  Plant $data
     * @return self For fluid interface
     */
    public function setPlant(Plant $data)
    {
        return $this->set(Properties::PLANT, $data);
    }

    /**
     * Getter for plant section
     *
     * @return Plant|NULL
     */
    public function getPlant()
    {
        return $this->get(Properties::PLANT);
    }

    /**
     * Setter for feed in section
     *
     * @param  FeedIn $data
     * @return self For fluid interface
     */
    public function setFeedIn(FeedIn $data)
    {
        return $this->set(Properties::FEED_IN, $data);
    }

    /**
     * Getter for feed in section
     *
     * @return FeedIn|NULL
     */
    public function getFeedIn()
    {
        return $this->get(Properties::FEED_IN);
    }

    /**
     * Setter for grid consumption section
     *
     * @param  GridConsumption $data
     * @return self For fluid interface
     */
    public function setGridConsumption(GridConsumption $data)
    {
        return $this->set(Properties::GRID_CONSUMPTION, $data);
    }

    /**
     * Getter for grid consumption section
     *
     * @return GridConsumption|NULL
     */
    public function getGridConsumption()
    {
        return $this->get(Properties::GRID_CONSUMPTION);
    }

    /**
     * Setter for total consumption section
     *
     * @param  TotalConsumption $data
     * @return self For fluid interface
     */
    public function setTotalConsumption(TotalConsumption $data)
    {
        return $this->set(Properties::TOTAL_CONSUMPTION, $data);
    }

    /**
     * Getter for grid consumption section
     *
     * @return TotalConsumption|NULL
     */
    public function getTotalConsumption()
    {
        return $this->get(Properties::TOTAL_CONSUMPTION);
    }

    /**
     * Setter for self consumption section
     *
     * @param  SelfConsumption $data
     * @return self For fluid interface
     */
    public function setSelfConsumption(SelfConsumption $data)
    {
        return $this->set(Properties::SELF_CONSUMPTION, $data);
    }

    /**
     * Getter for self consumption section
     *
     * @return SelfConsumption|NULL
     */
    public function getSelfConsumption()
    {
        return $this->get(Properties::SELF_CONSUMPTION);
    }

    /**
     * Setter for Irradiation section
     *
     * @param  Irradiation $data
     * @return self For fluid interface
     */
    public function setIrradiation(Irradiation $data)
    {
        return $this->set(Properties::IRRADIATION, $data);
    }

    /**
     * Setter for Irradiation section
     *
     * @return Irradiation|NULL
     */
    public function getIrradiation()
    {
        return $this->get(Properties::IRRADIATION);
    }

    /**
     * Setter for temperature section
     *
     * @param Temperature $data
     * @return self For fluid interface
     */
    public function setTemperature(Temperature $data)
    {
        return $this->set(Properties::TEMPERATURE, $data);
    }

    /**
     * Getter for temperature section
     *
     * @return Temperature|NULL
     */
    public function getTemperature()
    {
        return $this->get(Properties::TEMPERATURE);
    }

    /**
     * Setter for battery input section
     *
     * @param BatteryIn $data
     * @return self For fluid interface
     */
    public function setBatteryIn(BatteryIn $data)
    {
        return $this->set(Properties::BATTERY_IN, $data);
    }

    /**
     * Getter for battery input section
     *
     * @return BatteryIn|NULL
     */
    public function getBatteryIn()
    {
        return $this->get(Properties::BATTERY_IN);
    }

    /**
     * Setter for battery output section
     *
     * @param BatteryOut $data
     * @return self For fluid interface
     */
    public function setBatteryOut(BatteryOut $data)
    {
        return $this->set(Properties::BATTERY_OUT, $data);
    }

    /**
     * Getter for battery output section
     *
     * @return BatteryOut|NULL
     */
    public function getBatteryOut()
    {
        return $this->get(Properties::BATTERY_OUT);
    }

    /**
     * Setter for battery charge status section
     *
     * @param BatteryChargeStatus $data
     * @return self For fluid interface
     */
    public function setBatteryChargeStatus(BatteryChargeStatus $data)
    {
        return $this->set(Properties::BATTERY_CHARGE_STATUS, $data);
    }

    /**
     * Getter for battery charge status section
     *
     * @return BatteryChargeStatus|NULL
     */
    public function getBatteryChargeStatus()
    {
        return $this->get(Properties::BATTERY_CHARGE_STATUS);
    }

    /**
     * Setter for global pretty JSON flag
     *
     * @param  bool $pretty
     * @return self For fluid interface
     */
    public function setPrettyJson($pretty)
    {
        // Force boolean value with not(not(...))
        $this->prettyJson = !!$pretty;
        return $this;
    }

    /**
     * Getter for raw data
     *
     * Used by merge() to build new data array
     *
     * @internal
     * @return array
     */
    public function getRaw() 
    {
        return $this->data;
    }

    /**
     * Return a JSON repesentation of the whole instance
     *
     * @param  bool $pretty Pretty print JSON, if not provided fallback to $prettyJson
     * @return string|FALSE Return FALSE on error encoding data to JSON
     */
    public function asJson($pretty=null)
    {
        // Force as object
        $flags = JSON_FORCE_OBJECT;
        // Pretty print?
        if (is_null($pretty)) {
            $pretty = $this->prettyJson;
        }
        if ($pretty) {
            // Pretty print JSON data
            $flags |= JSON_PRETTY_PRINT;
        }
        return json_encode(
            $this->interpolate()->asArray(self::DATETIME),
            $flags
        );
    }

    /**
     * Return a JSON repesentation of the whole instance, not interpolated
     *
     * @param  bool $pretty Pretty print JSON, if not provided fallback to $prettyJson
     * @return string|FALSE Return FALSE on error encoding data to JSON
     */
    public function asJsonRaw($pretty=null)
    {
        // Force as object
        $flags = JSON_FORCE_OBJECT;
        // Pretty print?
        if (is_null($pretty)) {
            $pretty = $this->prettyJson;
        }
        if ($pretty) {
            // Pretty print JSON data
            $flags |= JSON_PRETTY_PRINT;
        }
        return json_encode($this->asArray(self::INTERNAL), $flags);
    }

    /**
     * Save the JSON representation to a file
     *
     * @param  string  $filename File name to save to
     * @param  bool    $pretty Pretty print JSON
     * @return integer Bytes written to file
     */
    public function saveJsonToFile($filename, $pretty=null)
    {
        return file_put_contents($filename, $this->asJson($pretty));
    }

    /**
     * Save the JSON representation to a file, not interpolated
     *
     * @param  string  $filename File name to save to
     * @param  bool    $pretty Pretty print JSON
     * @return integer Bytes written to file
     */
    public function saveJsonRawToFile($filename, $pretty=null)
    {
        return file_put_contents($filename, $this->asJsonRaw($pretty));
    }

    /**
     * Return the whole instance as JSON string for string type cast
     *
     * @example <code>
     * $instance = new PVLog\Classes\Json\Instance;
     * echo $instance;
     * </code>
     *
     * @return string
     */
    public function __toString()
    {
        return $this->asJSON();
    }

    /**
     * Return the whole data recursive as array
     *
     * @param integer $flags Feature flags, see PVLog\JSON2 constants
     * @return array
     */
    public function asArray($flags=0)
    {
        if ($this->getType() == Properties::FILE_CONTENT_MINUTES) {
            $flags |= self::EXPORT_POWER;
        }

        return parent::asArray($flags);
    }

    /**
     * Merge delta data array into a given array
     *
     * @todo   Respect deleteDayBeforeImport loads
     *
     * @throws InvalidArgumentException
     * @param  Instance $new Data to merge
     * @return array Returns the merged data array
     */
    public function merge(Instance $new)
    {
        // Full data
        $new = $new->asArray(self::INTERNAL);
        if ($this->data[Properties::VERSION] != $new[Properties::VERSION]) {
            throw new \InvalidArgumentException(
                'Can only merge instances of same version '
              . $this->data[Properties::VERSION]
            );
        }
        if ($this->data[Properties::FILE_CONTENT] != $new[Properties::FILE_CONTENT]) {
            throw new \InvalidArgumentException(
                'Can only merge instances of same file content '
              . $this->data[Properties::FILE_CONTENT]
            );
        }
        $data = $this->_merge($this->asArray(self::INTERNAL), $new);
        // Rebuid data from temp. build instance
        $this->data = self::factory('instance', $data)->getRaw();
        $this->interpolate();
    }

    // -----------------------------------------------------------------------
    // PROTECTED
    // -----------------------------------------------------------------------

    /**
     * Export prettified JSON, only way to manipulate output via __toString()
     *
     * @see __toString()
     * @var bool $prettyJson
     */
    protected $prettyJson = false;

    /*
     * Overload
     */
    protected $validSections = array(
        Properties::PLANT,
        Properties::FEED_IN,
        Properties::GRID_CONSUMPTION,
        Properties::TOTAL_CONSUMPTION,
        Properties::SELF_CONSUMPTION,
        Properties::IRRADIATION,
        Properties::TEMPERATURE,
        Properties::BATTERY_IN,
        Properties::BATTERY_OUT,
        Properties::BATTERY_CHARGE_STATUS
    );

    /**
     * Merge recursive
     *
     * @internal
     * @param  array $old Existing data
     * @param  array $new Data to merge
     * @return array Returns the merged data array
     */
    protected function _merge(Array $old, Array $new)
    {
        foreach ($new as $key=>$value) {
            if (is_array($value)) {
                if  (isset($old[$key])) {
                    $old[$key] = $this->_merge($old[$key], $value);
                } else {
                    $old[$key] = $this->_merge(array(), $value);
                }
            } else {
                $old[$key] = $value;
            }
        }

        return $old;
    }

}
