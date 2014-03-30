<?php
/**
 *
 * @author     Knut Kohl <github@knutkohl.de>
 * @copyright  2012-2014 Knut Kohl
 * @license    MIT License (MIT) http://opensource.org/licenses/MIT
 * @version    1.0.0
 */
namespace Hook;

/**
 *
 */
class XivelyStatus extends Base {

    /**
     *
     */
    public static function data_save_after( &$channel, $config ) {

        // Default format, if still set ignored
        $config[] = '%s';

        // Xively CSV: "Channel,Value"
        $value = $config[2] . ',' . sprintf($config[3], $channel->value);

        // Write to memory stream for PUT request
        $fh = fopen('php://memory', 'rw');
        fwrite($fh, $value);
        rewind($fh);

        $ch = curl_init($config[0].'.csv');

        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('X-ApiKey: '.$config[1]));
        curl_setopt($ch, CURLINFO_HEADER_OUT, TRUE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_PUT, TRUE);
        curl_setopt($ch, CURLOPT_INFILE, $fh);
        curl_setopt($ch, CURLOPT_INFILESIZE, strlen($value));

        $response = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if ($response === FALSE || $code >= 400) {
            echo $response ? $response : sprintf('[%d] %s', $code, curl_error($ch));
            #print_r(curl_getinfo($ch));
        }

        curl_close($ch);
        fclose($fh);
    }

}
