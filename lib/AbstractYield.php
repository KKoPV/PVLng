<?php
/**
 * Used as base for Yield, Plant, Inverter and String
 */
abstract class AbstractYield
{

    /**
     *
     */
    protected $data = array();

    /**
     *
     */
    public function asArray()
    {
        return $this->data;
    }

    /**
     *
     */
    public function asJson()
    {
        return json_encode($this->asArray());
    }

    /**
     *
     */
    public function isValid()
    {
        return true;
    }

    /**
     * Put 0 in front
     *
     * @param &$powers array Transfer by reference to avoid full copy
     * @param $count integer Count of 0 to pad
     */
    protected function leftPadPowers($powers, $count)
    {
        for ($i = 0; $i < $count; ++$i) {
            array_unshift($powers, 0);
        }
        return $powers;
    }

    /**
     * Push 0 at the end
     *
     * @param &$powers array Transfer by reference to avoid full copy
     * @param $count integer Count of 0 to pad
     */
    protected function rightPadPowers($powers, $count)
    {
        for ($i = 0; $i < $count; ++$i) {
            $powers[] = 0;
        }
        return $powers;
    }
}
