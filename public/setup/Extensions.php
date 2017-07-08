<?php
/**
 *
 */
namespace Setup;

/**
 *
 */
class Extensions extends SetupTask
{
    /**
     *
     */
    public $title = 'Check required PHP Extensions';

    /**
     *
     */
    public function process($params)
    {
        $opt = ['(optional)', '(required)'];
        $extensions = get_loaded_extensions();

        foreach ($params as $ext => $data) {
            $data[] = true; // Required by default
            if (in_array($ext, $extensions)) {
                $this->success($data[0], $opt[+$data[1]]);
            } elseif ($data[1]) {
                $this->error(sprintf($this->PHPLink, $ext, $data[0]), $opt[+$data[1]], ' - Please install extension');
            } else {
                $this->info(sprintf($this->PHPLink, $ext, $data[0]), $opt[+$data[1]], ' - <i>not installed</i>');
            }
        }
    }

    /**
     *
     */
    protected $PHPLink = '<a href="http://php.net/manual/book.%s.php" target="_blank">%s</a>';
}
