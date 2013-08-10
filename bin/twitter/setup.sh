#!/bin/sh
##############################################################################
### @author      Knut Kohl <github@knutkohl.de>
### @copyright   2012-2013 Knut Kohl
### @license     GNU General Public License http://www.gnu.org/licenses/gpl.txt
### @version     $Id$
##############################################################################

pwd=$(dirname $0)

. $pwd/.pvlng

request_token_url='https://api.twitter.com/oauth/request_token' \
authorize_url='https://api.twitter.com/oauth/authorize?oauth_token=$oauth_token' \
access_token_url='https://api.twitter.com/oauth/access_token' \

consumer_tmp=$(mktemp consumer.XXXXXX)
request_token_tmp=$(mktemp request_token.XXXXXX)
access_token_tmp=$(mktemp access_token.XXXXXX)

trap 'rm $consumer_tmp $request_token_tmp $access_token_tmp' 0

curlicue=$(dirname $0)/contrib/curlicue

echo "oauth_consumer_key=$CONSUMER_KEY&oauth_consumer_secret=$CONSUMER_SECRET" > $consumer_tmp

$curlicue -f $consumer_tmp -p 'oauth_callback=oob' -- \
          -s -d '' "$request_token_url" > $request_token_tmp

echo
echo 'Setup authorization for your twitter account'
echo
echo '1. Load this URL in your browser:'
echo
echo '   '$($curlicue -f $consumer_tmp -f $request_token_tmp -e "$authorize_url")
echo
read -p '2. Copy PIN you got there and paste here: ' pin
echo

$curlicue -f $consumer_tmp -f $request_token_tmp \
          ${pin:+-p "oauth_verifier=$pin"} -- \
          -s -d '' "$access_token_url" > $access_token_tmp

if cat $access_token_tmp | grep -vq 'oauth_token='; then
  echo 'Something went wrong (correct PIN?)'
  echo
  echo Response:
  cat $access_token_tmp
  echo
  echo 'Please try again.'
  exit
fi

IFS='&'
a=($(<$access_token_tmp))

( IFS='='
  for t in "${a[@]}"; do
    set $t
    if echo $1 | grep -q 'oauth'; then
      printf '%s="%s"\n' $(echo $1 | tr a-z A-Z) "$2"
    fi
  done
) >$pwd/.tokens

cat $pwd/.tokens
echo

echo '3. Wrote your tokens to .tokens'
echo
