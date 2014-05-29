<?php
/**
 *
 */
namespace ORM;

/**
 *
 */
class ChannelType extends ChannelTypeBase {

    /**
     *
     */
    public function ModelClass() {
        return $this->model ? 'Channel\\'.$this->model : NULL;
    }

}
