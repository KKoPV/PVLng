<?php
/**
 *
 */
class YieldPlant extends AbstractYield
{

    /**
     *
     */
    protected $data = array(
        'recordTimestampStart' => 0,
        'recordTimestampEnd'   => 0,
        'intervallInSeconds'   => 0,
        'currentTotalWh'       => 0,
        'totalWh'              => 0,
        'powerAcW'             => array(),
        'inverter'             => array(),
    );

    /**
     *
     */
    public function __construct(
        $timestampStart = 0,
        $timestampEnd = 0,
        $intervallSeconds = 300,
        $powerValues = array(),
        $currentTotalWattHours = 0,
        $dailyWattHours = 0
    ) {

        $this->data['recordTimestampStart'] = (int)$timestampStart;
        $this->data['recordTimestampEnd'] = (int)$timestampEnd;
        $this->data['intervallInSeconds'] = (int)$intervallSeconds;
        $this->data['powerAcW'] = $powerValues;
        $this->data['currentTotalWh'] = (int)$currentTotalWattHours;
        $this->data['totalWh'] = (int)$dailyWattHours;
        $this->data['inverter'] = array();
    }

    /**
     *
     */
    public function setTimestampStart($timestamp)
    {
        $this->data['recordTimestampStart'] = (int)$timestamp;
        return $this;
    }

    /**
     *
     */
    public function getTimestampStart()
    {
        return $this->data['recordTimestampStart'];
    }

    /**
     *
     */
    public function setTimestampEnd($timestamp)
    {
        $this->data['recordTimestampEnd'] = (int)$timestamp;
        return $this;
    }

    /**
     *
     */
    public function getTimestampEnd()
    {
        return $this->data['recordTimestampEnd'];
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
    public function setIntervallInSeconds($intervallSeconds)
    {
        $this->data['intervallInSeconds'] = (int)$intervallSeconds;
        return $this;
    }

    /**
     *
     */
    public function getIntervallInSeconds()
    {
        return $this->data['intervallInSeconds'];
    }

    /**
     *
     */
    public function addInverter(YieldInverter $inverter)
    {
        $this->data['inverter'][count($this->data['inverter']) + 1] = $inverter;
        return $this;
    }

    /**
     *
     */
    public function asArray()
    {

        // Find lowest start and highest end
        $plantStart = PHP_INT_MAX;
        $plantEnd = 0;

        foreach ($this->data['inverter'] as $inverter) {
            $timestamp = $inverter->getTimestampStart();
            if ($timestamp < $plantStart) {
                $plantStart = $timestamp;
            }
            $timestamp = $inverter->getTimestampEnd();
            if ($timestamp > $plantEnd) {
                $plantEnd = $timestamp;
            }
            foreach ($inverter->getStrings() as $string) {
                $timestamp = $string->getTimestampStart();
                if ($timestamp < $plantStart) {
                    $plantStart = $timestamp;
                }
                $timestamp = $string->getTimestampEnd();
                if ($timestamp > $plantEnd) {
                    $plantEnd = $timestamp;
                }
            }
        }

        $currentTotalWattHours = 0;
        $dailyWattHours = 0;
        // overal pwoer array length
        $powersCnt = 0;

        // Fetch powers and fix array sizes
        foreach ($this->data['inverter'] as $idInverter => $inverter) {
            // inverter
            $start = $inverter->getTimestampStart();
            $end = $inverter->getTimestampEnd();
            $powers = $inverter->getPowerValues();

            if ($cnt = count($powers)) {
                $intervall = ($end - $start) / $cnt;
                // fill up missing values
                $powers = $this->leftPadPowers($powers, round(($start - $plantStart) / $intervall));
                $powers = $this->rightPadPowers($powers, round(($plantEnd - $end) / $intervall));
            }

            // set new values
            $inverter->setPowerValues($powers);

            // sum up current and daily watt hours for corresponding parent plant values
            $currentTotalWattHours += $inverter->getCurrentTotalWattHours();
            $dailyWattHours += $inverter->getDailyWattHours();

            // calc plant 'powerAcW'
            if (empty($this->data['powerAcW'])) {
                // init with 1st inverter ...
                $this->data['powerAcW'] = $powers;
                $powersCnt = count($powers);
            } else {
                $powers = $this->rightPadPowers($powers, $powersCnt - count($powers));
                // add further inverter
                foreach ($this->data['powerAcW'] as $id => $power) {
                    $this->data['powerAcW'][$id] += $powers[$id];
                }
            }

            $currentTotalWattHoursInverter = 0;
            $dailyWattHoursInverter = 0;

            // same for strings
            foreach ($inverter->getStrings() as $string) {
                // sum up current and daily watt hours for corresponding parent inverter values
                $currentTotalWattHoursInverter += $string->getCurrentTotalWattHours();
                $dailyWattHoursInverter += $string->getDailyWattHours();

                // inverter
                $start = $string->getTimestampStart();
                $end = $string->getTimestampEnd();
                $powers = $string->getPowerValues();

                if ($cnt = count($powers)) {
                    $intervall = ($end - $start) / $cnt;
                    // fill up missing values
                    $powers = $this->leftPadPowers($powers, round(($start - $plantStart) / $intervall));
                    $powers = $this->rightPadPowers($powers, $powersCnt - count($powers));
                }

                // set new values
                $string->setPowerValues($powers);
            }

            // set the summed up strings instead of inverter value as plant sum if the inverter has not
            // a given value already
            if ($currentTotalWattHoursInverter > 0 &&
                $this->data['inverter'][$idInverter]->getCurrentTotalWattHours() == 0) {
                $currentTotalWattHours = $currentTotalWattHoursInverter;
            }
            if ($dailyWattHoursInverter > 0 && $this->data['inverter'][$idInverter]->getDailyWattHours() == 0) {
                $dailyWattHours = $dailyWattHoursInverter;
            }

            // set the calculated string value into the inverter if it has not a given value
            if ($this->data['inverter'][$idInverter]->getCurrentTotalWattHours() == 0) {
                $this->data['inverter'][$idInverter]->setCurrentTotalWattHours($currentTotalWattHoursInverter);
            }
            if ($this->data['inverter'][$idInverter]->getDailyWattHours() == 0) {
                $this->data['inverter'][$idInverter]->setDailyWattHours($dailyWattHoursInverter);
            }
        }

        // set the calculated inverter value into the plant if it has not a given value
        if ($this->data['currentTotalWh'] == 0) {
            $this->data['currentTotalWh'] = $currentTotalWattHours;
        }
        if ($this->data['totalWh'] == 0) {
            $this->data['totalWh'] = $dailyWattHours;
        }

        $this->setTimestampStart($plantStart);
        $this->setTimestampEnd($plantEnd);
        if ($plantStart != $plantEnd || $this->getIntervallInSeconds() == 0) {
            if ($cnt = count($this->data['powerAcW'])) {
                $this->setIntervallInSeconds(ceil(($plantEnd - $plantStart) / $cnt));
            }
        }

        $return = $this->data;
        foreach ($return['inverter'] as $id => $inverter) {
            if (is_object($inverter) && $inverter instanceof YieldInverter) {
                $return['inverter'][$id] = $inverter->asArray();
            }
        }
        return $return;
    }
}
