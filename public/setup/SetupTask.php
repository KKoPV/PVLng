<?php
/**
 *
 */
namespace Setup;

/**
 *
 */
abstract class SetupTask
{
    /**
     *
     */
    public $title = 'Setup task ...';

    /**
     * Prepare result array
     */
    abstract public function process($params);

    /**
     *
     */
    public function isError()
    {
        return ($this->error === true);
    }

    /**
     *
     */
    public function getMessages()
    {
        return $this->messages;
    }

    /**
     *
     */
    public function path()
    {
        return implode(DIRECTORY_SEPARATOR, func_get_args());
    }

    /**
     *
     */
    protected $error;

    /**
     *
     */
    protected $messages = array();

    /**
     *
     */
    protected function code($code)
    {
        return '<pre>' . $code . '</pre>';
    }

    /**
     *
     */
    protected function info()
    {
        $this->messages[] = implode(' ', func_get_args());
    }

    /**
     *
     */
    protected function success()
    {
        $this->messages[] =
            '<span style="color:green">' .
            implode(' ', func_get_args()) .
            ' - <strong>OK</strong></span>';
    }

    /**
     *
     */
    protected function error()
    {
        $this->error = true;
        $this->messages[] =
            '<span style="color:red">' .
            implode(' ', func_get_args()) .
            ' - <strong>FAILED</strong></span>';
    }

    /**
     *
     */
    protected function arrayPath2Key($array, $key)
    {
        $p = &$array;
        $path = explode('.', $key);
        while ($key = array_shift($path)) {
            $p = &$p[$key];
        }
        return $p;
    }
}
