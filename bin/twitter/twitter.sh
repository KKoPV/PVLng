#!/bin/sh
##############################################################################
### @author      Knut Kohl <github@knutkohl.de>
### @copyright   2012-2013 Knut Kohl
### @license     GNU General Public License http://www.gnu.org/licenses/gpl.txt
### @version     $Id$
##############################################################################

##############################################################################
### Init
##############################################################################
pwd=$(dirname $0)

. $pwd/../PVLng.conf
. $pwd/../PVLng.functions
. $pwd/twitter.items

while getopts "lftvxh" OPTION; do
  case "$OPTION" in
    l) printf '\nImplemented items:\n\n'
       typeset -F | grep ' twitter_' | sed -e 's/.*twitter_/  - /'
       printf "\nSee $pwd/twitter.items for details\n"
       exit ;;
    f) FORCE=y ;;
    t) TEST=y; VERBOSE=$(expr $VERBOSE + 1) ;;
    v) VERBOSE=$(expr $VERBOSE + 1) ;;
    x) TRACE=y ;;
    h) usage; exit ;;
    ?) usage; exit 1 ;;
  esac
done

shift $((OPTIND-1))

read_config "$1"

ITEM_N=$(int "$ITEM_N")
test $ITEM_N -gt 0  || error_exit "No items defined"

##############################################################################
### Start
##############################################################################
test "$TRACE" && set -x

test "$OAUTH_TOKEN"        || error_exit "Missing OAuth token!"
test "$OAUTH_TOKEN_SECRET" || error_exit "Missing OAuth secret!"
test "$STATUS"             || error_exit "Missing status!"

### App tokens
CONSUMER_KEY="4Qs7FkTWVyJKfZKYSadAw"
CONSUMER_SECRET="baUNgkJxIbSiPau7VXBq1I1h4byWDNHRuqq2vmGA"

TWITTER_URL="https://api.twitter.com/1/statuses/update.json"

curl="$(curl_cmd)"

LC_NUMERIC=en_US

ITEMS=0

while test $ITEMS -lt $ITEM_N; do

  ITEMS=$(expr $ITEMS + 1)
  log 1 "--- $ITEMS ---"

  eval ITEM=\$ITEM_$ITEMS
  log 1 "Item  : $ITEM"

  eval GUID=\$GUID_$ITEMS
  log 1 "GUID  : $GUID"

  value=$(twitter_$ITEM $GUID)
  value=$(int $value)
  log 1 "Value : $value"

  ### Exit if no value is found, e.g. no actual power outside daylight times
  test "$value" != "0" || test "$FORCE" || exit

  eval FACTOR=\$FACTOR_$ITEMS
  log 1 "Factor: $FACTOR"

  if test "$FACTOR"; then
    value=$(echo "scale=3; $value * $FACTOR" | bc -l)
    log 1 "Value : $value"
  fi

  PARAMS="$PARAMS $value"

done

log 1 '--- Status ---'
log 1 "Status   : $STATUS"
log 1 "Parameter: $PARAMS"

STATUS=$(printf "$STATUS" $PARAMS)

##############################################################################
log 1 "Result   : $STATUS"
log 1 "Length   : $(echo $STATUS | wc -c)"

test "$TEST" && exit

$(dirname $0)/twitter.php \
  --consumer_key=$CONSUMER_KEY \
  --consumer_secret=$CONSUMER_SECRET \
  --oauth_token=$OAUTH_TOKEN \
  --oauth_secret=$OAUTH_TOKEN_SECRET \
  --status="$STATUS" \
  --lat=$LAT --long=$LONG

set +x

exit $?

##############################################################################
# USAGE >>

Post status to twitter

Usage: $scriptname [options] config_file

Options:

    -l   List implemented items
    -t   Test mode, don't post
         Sets verbosity to info level
    -v   Set verbosity level to info level
    -vv  Set verbosity level to debug level
    -h   Show this help

See $pwd/twitter.conf.dist for details.

# << USAGE
