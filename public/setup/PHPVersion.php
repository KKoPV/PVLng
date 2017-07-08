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
    public $title = 'Check required PHP Version';

    /**
     *
     */
    public function process($params)
    {
        // $params == min. version
        $this->info('Require at least PHP', $params);
        if (version_compare(PHP_VERSION, $params, 'ge')) {
            $this->success('Found PHP', PHP_VERSION);
        } else {
            $this->error('Found PHP', PHP_VERSION);
        }
    }
}
