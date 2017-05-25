<?php
/**
 *
 */
namespace ORM;

/**
 *
 */
class ChannelType extends ChannelTypeBase
{
    /**
     *
     */
    public function getModelClass()
    {
        return $this->model ? 'Channel\\'.$this->model : null;
    }
}
