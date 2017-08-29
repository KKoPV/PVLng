<?php
/**
 *
 */
namespace Setup;

/**
 *
 */
class Permissions extends SetupTask
{
    /**
     *
     */
    public $title = 'File / Directory permissions';

    /**
     *
     */
    public function process($params)
    {
        foreach ($params as $test => $func) {
            $msg = ucfirst(str_replace('_', ' ', $func));
            if ($func($test)) {
                $this->success($msg, '<tt>', $test, '</tt>');
            } else {
                $this->error($msg, '<tt>', $test, '</tt>');
            }
        }
    }
}
