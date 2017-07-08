<?php
/**
 *
 */
namespace Setup;

/**
 *
 */
class Composer extends SetupTask
{
    /**
     *
     */
    public $title = 'Check Composer dependencies';

    /**
     *
     */
    public function process($params)
    {
        $dir  = realpath($params[0]);
        $conf = $this->path($dir, 'composer.json');

        if (!file_exists($conf)) {
            $this->info('Composer is not required');
            return;
        }

        $lock = $this->path($dir, 'composer.lock');
        $cmd  = 'composer --working-dir='.$dir.' --no-dev update 2>&1';

        $this->success('<tt>composer.json</tt> exists');

        if (!file_exists($lock)) {
            $this->info('<tt>composer.lock</tt> missing');
            if (function_exists('exec')) {
                $this->info('Run Composer');
                exec($cmd);
            }
        }

        if (file_exists($lock)) {
            $this->success('<tt>composer.lock</tt> exists');
            $this->success('Composer dependencies installed');
            include $this->path($dir, 'vendor', 'autoload.php');
        } else {
            $this->error('Can\'t update Composer dependencies');
            $this->error('Composer needs to be installed system wide');
            $this->info('Then run:', $cmd);
        }
    }
}
