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
        $response = $this->app->response;

        if ($filename = $this->get('filename')) {
            $response['Cache-Control'] = 'no-cache, must-revalidate';
            $response['Expires'] = 'Sat, 01 Jan 2000 00:00:00 GMT';
            $response['Content-Disposition'] = 'attachment; filename=' . $filename;
        }

        $ContentType = $response['Content-Type'];

        /**
         * Adjust before real rendering
         * e.g. JSON can switch for JSONP to text/javascript
         */
        $this->app->ContentType($ContentType . '; charset=utf-8');

        ob_start();

        switch ($ContentType) {
            default:
                $this->asJSON($result);
                break;
            case 'application/csv':
                $this->asCSV($result, ';');
                break;
            case 'application/tsv':
                $this->asCSV($result, "\t");
                break;
            case 'application/xml':
                $this->asXML($result);
                break;
            case 'text/plain':
                $this->asTXT($result);
                break;
            case 'text/html':
                $this->asHTML($result);
                break;
        }

        if ($result instanceof Buffer) {
            // Free memory
            $result->close();
        }
        return ob_get_clean();
    }

    /**
     *
     */
    protected $app;

    /**
     *
     */
    protected function asJSON($result)
    {
        $callback = $this->app->request->get('callback');
        if (!$callback) {
            $callback = $this->app->request->get('jsonp');
        }

        if ($callback) {
            $this->app->ContentType('text/javascript;charset=utf-8');
            echo $callback, '(';
        }

        if ($result instanceof Buffer) {
            echo '[';
            $count = count($result);
            $i = 0;
            foreach ($result as $row) {
                if ($i && $i < $count) {
                    echo ',';
                }
                echo json_encode($this->normalizeJSON($row));
                $i++;
            }
            echo ']';
        } else {
            if (is_scalar($result) && json_decode($result) && json_last_error() === JSON_ERROR_NONE) {
                // Ok, is JSON
                echo $result;
            } else {
                echo json_encode($this->normalizeJSON($result));
            }
        }

        if ($callback) {
            echo ')';
        }
    }

    /**
     *
     */
    protected function asCSV($result, $sep)
    {
        if (!$result instanceof Buffer && !is_array($result)) {
            $result = array($result);
        }

        foreach ($result as $row) {
            $line = '';
            foreach ((array) $row as $value) {
                // Mask line breaks
                $value = str_replace("\r", '', $value);
                $value = str_replace(PHP_EOL, '\n', $value);
                if (strstr($value, $sep)) {
                    $value = '"' . $value . '"';
                }
                $line .= $sep . $value;
            }
            // Trim leading separator
            echo substr($line, 1), PHP_EOL;
        }
    }

    /**
     *
     */
    protected function asXML($result)
    {
        if ($result instanceof Buffer) {
            echo '<?xml version="1.0" encoding="UTF-8" ?'.'>' . PHP_EOL;
            echo '<data lastUpdated="'.date('c').'">' . PHP_EOL;

            $attr = $this->app->request->get('attributes');

            foreach ($result as $row) {
                $node = $attr ? 'attributes' : 'reading';
                $xml = '<' . $node . '>' . PHP_EOL;
                foreach ($row as $key => $value) {
                    $xml .= '<' . $key . '>' . $value . '</' . $key . '>' . PHP_EOL;
                }
                $xml .= '</' . $node . '>' . PHP_EOL;
                echo $xml;
                $attr = false;
            }
            echo '</data>' . PHP_EOL;
        } else {
            $result = array(
                '@attributes' => array('lastUpdated' => date('c')),
                'data' => (array) $result,
            );

            Array2XML::Init('1.0', 'UTF-8', $this->app->config('mode')=='development');

            echo Array2XML::createXML('data', $result)->saveXML();
        }
    }

    /**
     *
     */
    protected function asTXT($result)
    {
        if ($result instanceof Buffer || is_array($result)) {
            // Reformat only iterable content
            $cnt = count($result);
            $line = 0;

            foreach ($result as $key => $value) {
                if (is_array($value)) {
                    $value = implode(' ', $value);
                }
                $value = str_replace("\r", '', $value);
                $value = str_replace("\n", '\n', $value);
                if ($cnt > 1) {
                    echo trim($key . ': ' . $value);
                    if (++$line < $cnt) {
                        echo ' / ';
                    }
                } else {
                    echo $value, PHP_EOL;
                }
            }
        } else {
            echo $result;
        }
    }

    /**
     *
     */
    protected function asHTML($result)
    {
        echo '<html>
<head>
    <title>'.$result['title'].'</title>
    <style>
        body { font-family: Verdana,Arial,sans-serif }
        tt { font-size: 120% }
    </style>
</head>
<body>
';
        echo $result['body'];
        echo '</body></html>';
    }

    // -----------------------------------------------------------------------
    // PROTECTED
    // -----------------------------------------------------------------------

    /**
     *
     */
    protected function normalizeJSON($data)
    {
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                $data[$key] = $this->normalizeJSON($value);
            }
        } else {
            $data = str_replace("\r", '', $data);
            if ((string) $data == (string) +$data) {
                $data = +$data;
            }
        }
        return $data;
    }
}
