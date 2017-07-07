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
use Array2XML;

/**
 *
 */
class XML extends Formatter
{
    /**
     *
     */
    public function render($result)
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
}
