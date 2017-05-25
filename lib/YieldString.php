<?php
/**
 *
 */
class YieldString extends AbstractYield
{

    /**
     *
     */
    protected $recordTimestampStart;

    /**
     *
     */
    protected $recordTimestampEnd;

    /**
     *
     */
    protected $data = array(
        'currentTotalWh' => 0,
        'totalWh'        => 0,
        'powerAcW'       => array(),
    );

    /**
     *
     */
    public function __construct($powerValues = array(), $currentTotalWattHours = 0, $dailyWattHours = 0)
    {
        $this->data['powerAcW'] = $powerValues;
        $this->data['totalWh'] = (int)$dailyWattHours;
        $this->data['currentTotalWh'] = (int)$currentTotalWattHours;
    }

    /**
     *
     */
    public function setCurrentTotalWattHours($currentTotalWattHours)
    {
        $this->data['currentTotalWh'] = (int)$currentTotalWattHours;
        return $this;
    }

    /**
     *
     */
    public function getCurrentTotalWattHours()
    {
        return $this->data['currentTotalWh'];
    }

    /**
     *
     */
    public function setDailyWattHours($value)
    {
        $this->data['totalWh'] = (int)$value;
        return $this;
    }

    /**
     *
     */
    public function getDailyWattHours()
    {
        return $this->data['totalWh'];
    }

    /**
     *
     */
    public function setPowerValues($values)
    {
        $this->data['powerAcW'] = $values;
        return $this;
    }

    /**
     *
     */
    public function getPowerValues()
    {
        return $this->data['powerAcW'];
    }

    /**
     *
     */
    public function addPowerValue($value)
    {
        $this->data['powerAcW'][] = (int)$value;
        return $this;
    }

    /**
     *
     */
    public function setTimestampStart($timestamp)
    {
        $this->recordTimestampStart = (int)$timestamp;
        return $this;
    }

    /**
     *
     */
    public function getTimestampStart()
    {
        return $this->recordTimestampStart;
    }

    /**
     *
     */
    public function setTimestampEnd($timestamp)
    {
        $this->recordTimestampEnd = (int)$timestamp;
        return $this;
    }

    /**
     *
     */
    public function getTimestampEnd()
    {
        return $this->recordTimestampEnd;
    }
}
