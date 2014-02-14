#!/bin/bash
##############################################################################
### @author      Knut Kohl <github@knutkohl.de>
### @copyright   2012-2013 Knut Kohl
### @license     GNU General Public License http://www.gnu.org/licenses/gpl.txt
### @version     1.0.0
##############################################################################

##############################################################################
### Init
##############################################################################
pwd=$(dirname $0)

. $pwd/../PVLng.conf
. $pwd/../PVLng.sh

while getopts "tvxh" OPTION; do
    case "$OPTION" in
        t) TEST=y; VERBOSE=$((VERBOSE + 1)) ;;
        v) VERBOSE=$((VERBOSE + 1)) ;;
        x) TRACE=y ;;
        h) usage; exit ;;
        ?) usage; exit 1 ;;
    esac
done

shift $((OPTIND-1))

read_config "$1"

##############################################################################
### Start
##############################################################################
test "$TRACE" && set -x

test "$LAT_LON" || error_exit "Missing loaction coordinates (LAT_LON)!"

loc="$(echo "$LAT_LON" | sed -e 's~,~\t~')"
set $loc
LATITUDE=$1
LONGITUDE=$2

test "$LATITUDE" -a "$LONGITUDE" || error_exit "Invalid loaction definition (LOCATION)!"

test "$GUID" || error_exit "Missing OpenWeatherMap channel GUID (GUID)!"

##############################################################################
### Go
##############################################################################
RESPONSEFILE=$(mktemp /tmp/pvlng.XXXXXX)

trap 'rm -f $TMPFILE $RESPONSEFILE >/dev/null 2>&1' 0

# http://api.openweathermap.org/data/2.5/weather?lat=51.54805&lon=12.13125&units=metric&APPID=edfbe28d77cd456ae014bbc030730ed
API_URL="http://api.openweathermap.org/data/2.5/weather?lat=$LATITUDE&lon=$LONGITUDE&units=metric"

test "$APPID" && API_URL="$API_URL&APPID=$APPID"
test "$LANGUAGE" && API_URL="$API_URL&lang=$LANGUAGE"

log 2 "$API_URL"

curl="$(curl_cmd)"

### Query OpenWeatherMap API
$curl --output $RESPONSEFILE $API_URL
rc=$?

log 2 @$RESPONSEFILE

if test $rc -ne 0; then
     error_exit "cUrl error for OpenWeatherMap API: $rc"
fi

code=$($curl --request POST --header "Content-Type: application/json" \
             --data-binary @$RESPONSEFILE $PVLngURL/json/cod.txt)

if test "$code" != "200"; then
    echo "API URL: $API_URL"
    cat $RESPONSEFILE
    error_exit "$code"
fi

### Test mode
log 2 "OpenWeatherMap API response:"
log 2 @$RESPONSEFILE

test "$TEST" || PVLngPUT $GUID @$RESPONSEFILE

exit

##############################################################################
# USAGE >>

Fetch data from OpenWeatherMap API

Usage: $scriptname [options] config_file

Options:
    -t   Test mode, don't post
         Sets verbosity to info level
    -v   Set verbosity level to info level
    -vv  Set verbosity level to debug level
    -h   Show this help

See $pwd/station.conf.dist for details.

# << USAGE
