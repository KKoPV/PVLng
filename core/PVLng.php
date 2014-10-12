<?php
/**
 *
 *
 * @author      Knut Kohl <github@knutkohl.de>
 * @copyright   2012-2014 Knut Kohl
 * @license     MIT License (MIT) http://opensource.org/licenses/MIT
 * @version     1.0.0
 */
abstract class PVLng {

    /**
     *
     */
    const StatisticsURL = 'http://stats.pvlng.com/index.php';

    /**
     *
     */
    public static function getLoginToken() {
        $app = slimMVC\App::getInstance();
        return sha1(__FILE__ . "\x00" . sha1(
            $_SERVER['REMOTE_ADDR'] . "\x00" .

            strtolower($app->config->get('Admin.User')) . "\x00" .
            $app->config->get('Admin.Password')
        ));
    }

    /**
     * Send anonymous statistics about channel & readings count
     */
    public static function sendStatistics() {
        $db = slimMVC\MySQLi::getInstance();

        // Send at least each 6 hours
        if ($db->LastStats + 6*60*60 > time()) return;

        // This data will be send
        $args = array(
            // Unique installation id
            $db->queryOne('SELECT `pvlng_id`()'),
            // Real channels, writable and no childs allowed
            (new ORM\ChannelView)->filterByChilds(0)->filterByWrite(1)->find()->count(),
            // Row count in numeric and non-numeric readings tables
            (new ORM\ReadingNum)->rowCount() + (new ORM\ReadingStr)->rowCount()
        );

        $ch = curl_init(self::StatisticsURL);

        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($args));
        curl_exec($ch);

        // On error, make next try in 1 hour
        $db->LastStats = curl_errno($ch) ? time()-5*60*60 : time();

        curl_close($ch);
    }
}
