<?php
/**
 * Send anonymous statistics, included from frontend/Controller.php
 *
 * @author      Knut Kohl <github@knutkohl.de>
 * @copyright   2012-2014 Knut Kohl
 * @license     MIT License (MIT) http://opensource.org/licenses/MIT
 * @version     1.0.0
 */

// Send each 6 hours if activated
if (!$this->config->SendStatistics || $this->db->LastStats + 6*60*60 > time()) return;

// This data will be send
$args = array(
    // Unique installation id
    $this->db->queryOne('SELECT `pvlng_id`()'),
    // Real channels, writable and no childs allowed
    (new ORM\ChannelView)->filterByChilds(0)->filterByWrite(1)->find()->count(),
    // Row count in numeric and non-numeric readings tables
    (new ORM\ReadingNum)->rowCount() + (new ORM\ReadingStr)->rowCount()
);

$ch = curl_init('http://stats.pvlng.com/index.php');

curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($args));
curl_exec($ch);

// On error, make next try in 1 hour
$this->db->LastStats = curl_errno($ch) ? time()-5*60*60 : time();

curl_close($ch);
