<?php

// Included from frontend/Controller.php

// Send each 6 hours
if (!$this->config->SendStatistics || $this->db->LastStats + 6*60*60 > time()) return;

$args = array(
    // Unique installation id
    $this->db->queryOne('SELECT `pvlng_id`()'),
    // Real channels, writable and no childs allowed
    count((new ORM\ChannelView)->filterByChilds(0)->filterByWrite(1)->find()),
    // Row count in numeric and non-numeric readings tables
    (new ORM\ReadingNum)->rowCount() + (new ORM\ReadingStr)->rowCount()
);

// Send
$ch = curl_init('http://stats.pvlng.com/index.php');
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($args));
curl_exec($ch); // Fire and forget
curl_close($ch);

$this->db->LastStats = time();
