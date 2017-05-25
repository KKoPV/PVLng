<?php
/**
 *
 */
namespace ORM;

/**
 *
 */
class ChannelView extends ChannelViewBase
{

    /**
     *
     */
    public function __construct($id = null)
    {
        /* Build WITHOUT $id lookup, views have no primary key... */
        parent::__construct();
        if ($id) {
            $this->filterById($id)->findOne();
        }
    }

    /**
     *
     */
    public function getModelClass()
    {
        return 'Channel\\'.$this->model;
    }
}
