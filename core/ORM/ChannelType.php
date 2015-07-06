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
    public function getModelClass() {
        return $this->model ? 'Channel\\'.$this->model : NULL;
    }

    /**
     *
     */
    public function calcAverageLine( $id, $child, $p ) {
        self::$db->query('CALL `pvlng_model_averageline`({1}, {2}, {3})', $id, $child, $p);
    }

}
