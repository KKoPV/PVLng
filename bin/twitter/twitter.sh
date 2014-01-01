#!/bin/bash
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
. $pwd/../PVLng.sh

test -f $pwd/.tokens || error_exit "Missing token file! Did you run setup.sh?"

. $pwd/twitter.items.sh
. $pwd/.pvlng
. $pwd/.tokens

##############################################################################
function listItems {
    printf '\nImplemented items:\n\n'
    typeset -F | grep ' twitter_' | sed -e 's/.*twitter_//'| \
    while read line; do
        eval help="\$twitter_${line}_help"
        printf '    - %-25s - %s\n' "$line" "$help"
    done
    printf "\nSee $pwd/twitter.items.sh for more details\n"
}

##############################################################################
while getopts "lftvxh" OPTION; do
    case "$OPTION" in
        l) listItems; exit ;;
        f) FORCE=y ;;
        t) TEST=y; VERBOSE=$((VERBOSE + 1)) ;;
        v) VERBOSE=$((VERBOSE + 1)) ;;
        x) TRACE=y ;;
        h) usage; exit ;;
        ?) usage; exit 1 ;;
    esac
done

shift $((OPTIND-1))

read_config "$1"

test "$STATUS" || error_exit "Missing status!"

ITEM_N=$(int "$ITEM_N")
test $ITEM_N -gt 0  || error_exit "No items defined"

##############################################################################
### Start
##############################################################################
test "$TRACE" && set -x

TWITTER_URL="https://api.twitter.com/1/statuses/update.json"

curl="$(curl_cmd)"

LC_NUMERIC=en_US

i=0

while test $i -lt $ITEM_N; do

    i=$((i + 1))

    log 1 "--- $i ---"

    eval ITEM=\$ITEM_$i
    log 1 "Item    : $ITEM"

    eval GUID=\$GUID_$i
    log 1 "GUID    : $GUID"

    value=$(twitter_$ITEM $GUID)
    log 1 "Value : $value"

    ### Exit if no value is found, e.g. no actual power outside daylight times
    test "$value" && test "$value" != "0" || test "$FORCE" || exit

    eval FACTOR=\$FACTOR_$i
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

if test -z "$TEST"; then

    test $VERBOSE -gt 0 && opts="--debug"

    $pwd/twitter.php $opts \
        --consumer_key=$CONSUMER_KEY \
        --consumer_secret=$CONSUMER_SECRET \
        --oauth_token=$OAUTH_TOKEN \
        --oauth_secret=$OAUTH_TOKEN_SECRET \
        --status="$STATUS" --location="$LAT_LON"
fi

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
