<?php
/**
 *
 *
 * @author      Knut Kohl <github@knutkohl.de>
 * @copyright   2012-2013 Knut Kohl
 * @license     GNU General Public License http://www.gnu.org/licenses/gpl.txt
 * @version     1.0.0
 */
class View extends Slim\View {

    /**
     *
     */
    public function render( $result, $data=NULL )
    {

        $this->app = API::getInstance();
        $response = $this->app->response;

        if ($filename = $this->get('filename')) {
            $response['Cache-Control'] = 'no-cache, must-revalidate';
            $response['Expires'] = 'Sat, 01 Jan 2000 00:00:00 GMT';
            $response['Content-Disposition'] = 'attachment; filename=' . $filename;
        }

        ob_start();

        switch ($response['Content-Type']) {
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

        return ob_get_clean();
    }

    /**
     *
     */
    protected $app;

    /**
     *
     */
    protected function asJSON( $result )
    {
        if ($result instanceof Buffer) {
            echo '[';
            $count = count($result);
            $i = 0;
            foreach($result as $row) {
                if ($i && $i < $count) echo ',';
                echo json_encode($this->normalizeJSON($row));
                $i++;
            }
            echo ']';
        } else {
            if (is_scalar($result) && json_decode($result, true) !== false) {
                // Ok, is JSON
            } else {
                $result = json_encode($this->normalizeJSON($result));
            }
            $api = API::getInstance();
            $callback = $api->request->get('callback');
            if (!$callback) $callback = $api->request->get('jsonp');
            if ($callback) $result = $callback.'('.$result.')';
            echo $result;
        }
    }

    /**
     *
     */
    protected function asCSV( $result, $sep )
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
                if (strstr($value, $sep)) $value = '"' . $value . '"';
                $line .= $sep . $value;
            }
            // Trim leading separator
            echo substr($line, 1), PHP_EOL;
        }
    }

    /**
     *
     */
    protected function asXML( $result )
    {

        require_once LIB_DIR . DS . 'contrib' . DS . 'Array2XML.php';

        if ($result instanceof Buffer) {
            echo '<?xml version="1.0" encoding="UTF-8" ?'.'>' . PHP_EOL;
            echo '<data lastUpdated="'.date('c').'">' . PHP_EOL;

            $attr = $app->request->get('attributes');

            foreach ($result as $row) {
                $node = $attr ? 'attributes' : 'reading';
                $xml = '<' . $node . '>' . PHP_EOL;
                foreach ($row as $key=>$value) {
                    $xml .= '<' . $key . '>' . $value . '</' . $key . '>' . PHP_EOL;
                }
                $xml .= '</' . $node . '>' . PHP_EOL;
                echo $xml;
                $attr = FALSE;
            }
            echo '</data>' . PHP_EOL;

        } else {

            $result = array(
                '@attributes' => array('lastUpdated' => date('c')),
                'data' => (array) $result,
            );

            Array2XML::Init('1.0', 'UTF-8', $app->config('mode')=='development');

            echo Array2XML::createXML($node, $result)->saveXML();
        }
    }

    /**
     *
     */
    protected function asTXT( $result )
    {
        if ($result instanceof Buffer OR is_array($result)) {
            // Reformat only iterable content
            $cnt = count($result);
            $line = 0;

            foreach ($result as $key=>$value) {
                if (is_array($value)) $value = implode(' ', $value);
                $value = str_replace("\r", '', $value);
                $value = str_replace("\n", '\n', $value);
                if ($cnt > 1) {
                    echo trim($key . ': ' . $value);
                    if (++$line < $cnt) echo ' / ';
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
    protected function asHTML( $result )
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
    protected function normalizeJSON( $data )
    {
        if (is_array($data)) {
            foreach ($data as $key=>$value) {
                $data[$key] = $this->normalizeJSON($value);
            }
        } else {
            $data = str_replace("\r", '', $data);
            if ((string) $data == (string) +$data) $data = +$data;
        }
        return $data;
    }
}
