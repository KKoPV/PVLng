#!/usr/bin/php
<?php
/**
 *
 *
 * @author      Knut Kohl <github@knutkohl.de>
 * @copyright   2012-2013 Knut Kohl
 * @license     GNU General Public License http://www.gnu.org/licenses/gpl.txt
 * @version     $Id$
 */

/**
 *
 * TwitterOAuth repository:
 *   https://github.com/abraham/twitteroauth
 *   git clone git://github.com/abraham/twitteroauth.git
 *
 * TwitterOAuth example:
 *   http://ditio.net/2010/06/07/twitter-php-oauth-update-status/
 *
 * API test console on twitter:
 *   https://dev.twitter.com/console
 *
 */

require_once __DIR__.'/contrib/argv.class.php';

$args = new argv($argv, 'Update Twitter with actual data');

$args->add('c', 'Twitter consumer key', 'consumer_key', TRUE)
     ->add('e', 'Twitter consumer secret', 'consumer_secret', TRUE)
     ->add('o', 'OAuth token', 'oauth_token', TRUE)
     ->add('u', 'OAuth secret', 'oauth_secret', TRUE)
     ->add('s', 'Status to post', 'status', TRUE)
     ->add('l', 'Location: latitude,longitude', 'location')
     ->add('t', 'Activate test mode', 'test')
     ->add('d', 'Activate debug mode', 'debug')
     ->add('h', 'This help', 'help')
     ->run();

if ($args->d) echo PHP_EOL, print_r($args->getAll(), TRUE);

$loc = explode(',', $args->l);

if (count($loc) == 2) {

  $status = array(
    'status' => $args->s,
    'lat'    => $loc[0],
    'long'   => $loc[1],
    'display_coordinates' => 'true',
  );

} else {

  $status = array(
    'status' => $args->s
  );

}

require_once __DIR__.'/contrib/twitteroauth.php';

$try = 6;

while ($try-- > 0) {

  if ($args->d) printf("Tries left: %d ...\n", $try);

  // Update status
  $conn = new TwitterOAuth($args->c, $args->e, $args->o, $args->u);

  $res = $conn->get('account/verify_credentials');

  $rc = ($conn->http_code == 200) ? 0 : 3;
  if ($args->d) printf("Verify credentials HTTP code: %d\n", $conn->http_code);

  ($rc == 0) && $try = 0;

  sleep(10);
}

if ($rc) {
  echo 'Twitter connect failed.', PHP_EOL, PHP_EOL;
} elseif ($args->t) {
  echo 'Would send: ', PHP_EOL, print_r($status, TRUE), PHP_EOL;
} else {
  $res = $conn->post('statuses/update', $status);

  $rc = ($conn->http_code == 200) ? 0 : 4;
  if ($rc) {
    echo 'Twitter update failed.', PHP_EOL;
    echo $conn->http_header['status'], PHP_EOL;
  }
}

if ($args->d) echo PHP_EOL, print_r($conn, TRUE), PHP_EOL;
