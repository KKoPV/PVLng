<?php
/**
 * PVLng - PhotoVoltaic Logger new generation (https://pvlng.com/)
 *
 * @link       https://github.com/KKoPV/PVLng
 * @author     Knut Kohl <github@knutkohl.de>
 * @copyright  2012-2016 Knut Kohl
 * @license    MIT License (MIT) http://opensource.org/licenses/MIT
 */
namespace Api;

/**
 *
 */
use Slim\View as SlimView;
use Buffer;

/**
 *
 */
class View extends SlimView
{

    /**
     *
     */
    public function render($result, $data = null)
    {
        $this->app = Api::getInstance();
        $response  = $this->app->response;

        if ($filename = $this->get('filename')) {
            $response['Cache-Control'] = 'no-cache, must-revalidate';
            $response['Expires'] = 'Sat, 01 Jan 2000 00:00:00 GMT';
            $response['Content-Disposition'] = 'attachment; filename=' . $filename;
        }

        $contentType = $response['Content-Type'];

        /**
         * Adjust BEFORE rendering
         * e.g. JSON can switch for JSONP to text/javascript
         */
        $this->app->ContentType($contentType . '; charset=utf-8');

        ob_start();

        if ($this->app->formatter) {
            $formatter = $this->app->formatter;
        } else {
            $formatters = array(
                'application/csv' => 'CSV',
                'application/tsv' => 'TSV',
                'application/xml' => 'XML',
                'text/plain'      => 'TXT',
                'text/html'       => 'HTML',
            );

            if (isset($formatters[$contentType])) {
                $formatter = '\Formatter\\'.$formatters[$contentType];
            } else {
                $formatter = '\Formatter\\JSON';
            }
        }

        $formatter = new $formatter;
        $formatter->render($result);

        if ($result instanceof Buffer) {
            // Free memory
            $result->close();
        }

        return ob_get_clean();
    }

    // -----------------------------------------------------------------------
    // PROTECTED
    // -----------------------------------------------------------------------

    /**
     *
     */
    protected $app;
}
