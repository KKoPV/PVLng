<?php
/**
 *
 */
class YieldOverall extends AbstractYield
{

    /**
     *
     */
    protected $data = array(
        'creator'               => 'www.pv-log.com',
        'version'               => '1.0',
        'filecontent'           => 'Minutes',
        'deleteDayBeforeImport' => 0,
        'utc_offset'            => null,
        'plant'                 => null,
    );

    /**
     *
     */
    public function __construct(
        $creator = 'www.pv-log.com',
        $type = 'Minutes',
        $deleteDayBeforeImport = 0,
        $utcOffset = null
    ) {
        $this->data['creator'] = $creator;
        $this->data['version'] = '1.0';
        $this->data['filecontent'] = $type;
        $this->data['deleteDayBeforeImport'] = (int)$deleteDayBeforeImport;
        $this->data['utc_offset'] = $utcOffset;
        $this->data['plant'] = new YieldPlant();
    }

    /**
     *
     */
    public function setType($type)
    {
        $this->data['filecontent'] = $type;
        return $this;
    }

    /**
     *
     */
    public function getType()
    {
        return $this->data['filecontent'];
    }

    /**
     *
     */
    public function setUtcOffset($utcOffset)
    {
        $this->data['utc_offset'] = (int)$utcOffset;
        return $this;
    }

    /**
     *
     */
    public function getUtcOffset()
    {
        return $this->data['utc_offset'];
    }

    /**
     *
     */
    public function setDeleteDayBeforeImport($deleteDayBeforeImport)
    {
        $this->data['deleteDayBeforeImport'] = (int)$deleteDayBeforeImport;
        return $this;
    }

    /**
     *
     */
    public function getDeleteDayBeforeImport()
    {
        return $this->data['deleteDayBeforeImport'];
    }

    /**
     *
     */
    public function setCreator($creator)
    {
        $this->data['creator'] = $creator;
        return $this;
    }

    /**
     *
     */
    public function getCreator()
    {
        return $this->data['creator'];
    }

    /**
     *
     */
    public function setPlant(YieldPlant $plant)
    {
        $this->data['plant'] = $plant;
        return $this;
    }

    /**
     *
     */
    public function getPlant()
    {
        return $this->data['plant'];
    }

    /**
     *
     */
    public function asArray()
    {
        $return = $this->data;
/*
        // some debugging
        $return['dbg'] = array(
            'Start' => date('r', $return['plant']->getTimestampStart()),
            'End'   => date('r', $return['plant']->getTimestampEnd())
        );
*/
        $return['plant'] = $return['plant']->asArray();
        return $return;
    }
}
