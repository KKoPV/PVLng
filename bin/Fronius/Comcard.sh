#!/bin/bash
##############################################################################
### @author      Knut Kohl <github@knutkohl.de>
### @copyright   2012-2013 Knut Kohl
### @license     GNU General Public License http://www.gnu.org/licenses/gpl.txt
### @version     1.0.0
##############################################################################

##############################################################################
### Init variables
##############################################################################
pwd=$(dirname $0)

. $pwd/../PVLng.conf
. $pwd/../PVLng.sh

while getopts "stvxh" OPTION; do
    case "$OPTION" in
        s) SAVEDATA=y ;;
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

test "$APIURL" || error_exit "Solar Net API URL is required!"

GUID_N=$(int "$GUID_N")
test $GUID_N -gt 0  || error_exit "No GUIDs defined (GUID_N)"

if test "$LOCATION"; then
    ### Location given, test for daylight time
    loc=$(echo $LOCATION | sed -e 's/,/\//g')
    daylight=$(PVLngGET "daylight/$loc/60.txt")
    log 2 "Daylight: $daylight"
    test $daylight -eq 1 || exit 127
fi

##############################################################################
### Go
##############################################################################
. $pwd/func.sh

RESPONSEFILE=$(mktemp /tmp/pvlng.XXXXXX)

trap 'rm -f $TMPFILE $RESPONSEFILE >/dev/null 2>&1' 0

curl="$(curl_cmd --header 'Content-Type=application/json')"

i=0

while test $i -lt $GUID_N; do

    i=$((i + 1))

    log 1 "--- $i ---"

    eval GUID=\$GUID_$i
    test "$GUID" || error_exit "Inverter GUID is required (GUID_$i)"

    ### request serial and type, required fields
    DEVICEID=$(PVLngGET $GUID/serial.txt)
    TYPE=$(int $(PVLngGET $GUID/channel.txt))

    if test $TYPE -eq 1 -o $TYPE -eq 2; then
        requestComCard GetInverterRealtimeData CommonInverterData
    fi

    if test $TYPE -eq 2; then
        requestComCard GetStringRealtimeData NowStringControlData
    fi

    if test $TYPE -eq 3; then
        requestComCard GetSensorRealtimeData NowSensorData
    fi

done

set +x

exit

##############################################################################
# USAGE >>

Read data from Fronius inverters/SensorCards

Usage: $scriptname [options] config_file

Options:
    -s  Save data also into log file
    -t  Test mode, read only from ComCard and show the results, don't save to PVLng
        Sets verbosity to info level
    -v  Set verbosity level to info level
    -vv Set verbosity level to debug level
    -h  Show this help

See $pwd/System.conf.dist for reference.

# << USAGE
