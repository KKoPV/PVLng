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
    public function render( $result ) {

        $app = Slim\Slim::getInstance();
        $response = $app->response;

        if ($filename = $this->get('filename')) {
            $response['Cache-Control'] = 'no-cache, must-revalidate';
            $response['Expires'] = 'Sat, 01 Jan 2000 00:00:00 GMT';
            $response['Content-Disposition'] = 'attachment; filename=' . $filename;
        }

        ob_start();

        switch ($response['Content-Type']) {
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
            default:
                $this->asJSON($result);
                break;
        }

        return ob_get_clean();
    }

    /**
     *
     */
    protected function asCSV( $result, $sep ) {
        if (!$result instanceof Buffer AND !is_array($result)) {
            $result = array($result);
        }

        foreach ($result as $row) {
            $line = '';
            foreach ((array) $row as $value) {
                // Mask line breaks
                $value = str_replace("\r", '', $value);
                $value = str_replace("\n", '\n', $value);
                if (strstr($value, $sep)) {
                    $value = '"' . $value . '"';
                }
                $line .= $sep . $value;
            }
            // Trim leading separator
            echo substr($line, 1), "\n";
        }
    }

    /**
     *
     */
    protected function asXML( $result ) {

        require_once LIB_DIR . DS . 'contrib' . DS . 'Array2XML.php';

        $config = slimMVC\Config::getInstance();
        $app = slimMVC\App::getInstance();

        $data = $config->get('View.XML.Data', 'data');
        $node = $config->get('View.XML.Node', 'reading');

        if ($result instanceof Buffer) {
            echo '<?xml version="1.0" encoding="UTF-8" ?'.'>' . "\n";
            echo '<'.$data.' lastUpdated="'.date('c').'">' . "\n";

            $attr = $app->request->get('attributes');

            foreach ($result as $row) {
                $xml = '<' . ($attr ? 'attributes' : $node) . '>' . "\n";
                foreach ($row as $key=>$value) {
                    $xml .= '<' . $key . '>' . $value . '</' . $key . '>' . "\n";
                }
                $xml .= '</' . ($attr ? 'attributes' : $node) . '>' . "\n";
                echo $xml;
                $attr = FALSE;
            }
            echo '</data>' . "\n";

        } else {

            $result = array(
                '@attributes' => array('lastUpdated' => date('c')),
                $data => (array) $result,
            );

            Array2XML::Init('1.0', 'UTF-8', $config->get('View.Verbose'));

            echo Array2XML::createXML($node, $result)->saveXML();
        }
    }

    /**
     *
     */
    protected function asTXT( $result ) {
        if (!$result instanceof Buffer AND !is_array($result)) {
            $result = array($result);
        }

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
                echo $value;
            }
        }
    }

    /**
     *
     */
    protected function asHTML( $result ) {
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

    /**
     *
     */
    protected function asJSON( $result ) {
        if ($result instanceof Buffer) {
            echo '[';
            $count = count($result);
            $i = 0;
            foreach($result as $row) {
                if ($i AND $i < $count) echo ',';
                echo json_encode($this->normalizeJSON($row));
                $i++;
            }
            echo ']';
        } else {
            echo json_encode($this->normalizeJSON($result));
        }
    }

    // -----------------------------------------------------------------------
    // PROTECTED
    // -----------------------------------------------------------------------

    /**
     *
     */
    protected function normalizeJSON( $data ) {
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
