<?php

namespace QueueWorker;

interface WorkerInterface {

    public function process( $data );

}
