<?php
/**
 *
 */
namespace Setup;

/**
 *
 */
class Configuration extends SetupTask
{
    /**
     *
     */
    public $title = 'Check configuration file';

    /**
     *
     */
    public function process($params)
    {
        if (!file_exists($params['config'])) {
            $this->info('<tt>', $params['config'], '</tt> missing');
            $this->info('Try to create from <tt>', $params['default'], '</tt>');
            copy($params['default'], $params['config']);
        }

        if (file_exists($params['config'])) {
            $this->success('<tt>', basename($params['config']), '</tt> exists');
        } else {
            $this->error('Can\'t create <tt>', basename($params['config']), '</tt>');
            $this->error(
                'Please copy <tt>',
                basename($params['default']),
                '</tt> to <tt>',
                basename($params['config']),
                '</tt>'
            );
        }
    }
}
