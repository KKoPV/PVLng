<?php
/**
 *
 */
class YieldInverter extends AbstractYield
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
        'currentTotalWh'    => 0,
        'totalWh'            => 0,
        'powerAcW'            => array(),
        'strings'            => array(),
    );

    /**
     *
     */
    public function __construct($powerValues = array(), $currentTotalWattHours = 0, $dailyWattHours = 0)
    {
        $this->data['currentTotalWh'] = (int)$currentTotalWattHours;
        $this->data['totalWh'] = (int)$dailyWattHours;
        $this->data['powerAcW'] = $powerValues;
        $this->data['strings'] = array();
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
        // if we only got a single yield value in the inverter and no currentTotalWh set,
        // this single yield value is the currentTotalWh and we return it as that
        if (count($this->data['powerAcW']) == 1 && $this->data['currentTotalWh'] == 0) {
            return current($this->data['powerAcW']);
        }
        return $this->data['currentTotalWh'];
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
    public function setDailyWattHours($value)
    {
        $this->data['totalWh'] = (int)$value;
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
    public function setPowerValues($values)
    {
        $this->data['powerAcW'] = $values;
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
    public function addString(YieldString $string)
    {
        $this->data['strings'][count($this->data['strings']) + 1] = $string;
        return $this;
    }

    /**
     *
     */
    public function setStrings($strings)
    {
        $this->data['strings'] = $strings;
        return $this;
    }

    /**
     *
     */
    public function getStrings()
    {
        return $this->data['strings'];
    }

    /**
     *
     */
    public function asArray()
    {
        $return = $this->data;
        $currentTotalWattHours = 0;
        $dailyWattHours = 0;
        foreach ($return['strings'] as $id => $string) {
            if (is_object($string) && $string instanceof YieldString) {
                $return['strings'][$id] = $string->asArray();
                $currentTotalWattHours += $string->getCurrentTotalWattHours();
                $dailyWattHours += $string->getDailyWattHours();
            }
        }
        if ($return['currentTotalWh'] == 0) {
            $return['currentTotalWh'] = $currentTotalWattHours;
        }
        if ($return['totalWh'] == 0) {
            $return['totalWh'] = $dailyWattHours;
        }
        return $return;
    }
}
