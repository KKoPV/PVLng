<?php
/**
 *
 */
namespace Setup;

/**
 *
 */
class PHPVersion extends SetupTask
{
    /**
     *
     */
    public $title = 'PHP Version';

    /**
     *
     */
    public function process($params)
    {
        // $params == min. version
        $this->info('Require at least: <strong>PHP', $params, '</strong>');
        if (version_compare(PHP_VERSION, $params, 'ge')) {
            $this->success('PHP', PHP_VERSION);
        } else {
            $this->error('PHP', PHP_VERSION);
        }
    }
}
