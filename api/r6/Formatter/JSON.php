<?php
/**
 * PVLng - PhotoVoltaic Logger new generation
 *
 * @link       https://github.com/KKoPV/PVLng
 * @link       https://pvlng.com/
 * @author     Knut Kohl <github@knutkohl.de>
 * @copyright  2012 Knut Kohl
 * @license    MIT License (MIT) http://opensource.org/licenses/MIT
 */
namespace Formatter;

/**
 *
 */
use Buffer;

/**
 *
 */
class JSON extends Formatter
{
    /**
     *
     */
    public function render($result)
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
                if ($i > 0 && $i < $count) {
                    echo ',';
                }
                echo json_encode($this->normalizeJSON($row));
                $i++;
            }
            echo ']';
        } else {
            if (is_scalar($result) && json_decode($result) && json_last_error() === JSON_ERROR_NONE) {
                // Ok, is still JSON
                echo $result;
            } else {
                echo json_encode($this->normalizeJSON($result));
            }
        }

        if ($callback) {
            echo ')';
        }
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
