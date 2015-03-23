<?php
/**
 *
 */
namespace SMQ;

/**
 * Shortcut class on localhost with default port 11211
 */
class SMQLocal extends SMQ {

    /**
     *
     */
    public function __construct( $id='SMQ' ) {
        parent::__construct($id);
        $this->addServer('localhost', 11211);
    }

}
