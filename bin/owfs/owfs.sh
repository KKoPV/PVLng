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
GUID_N=0

. $pwd/../PVLng.conf
. $pwd/../PVLng.sh

owread=$(which owread)
test "$owread" || error_exit "Missing owread binary!"

CACHED=false

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

if test "$TEST" && test -z "$(which owread)"; then
    error_exit "Missing owread binary from OWFS. Is OWFS is properly installed?"
fi

shift $((OPTIND-1))

read_config "$1"

test $SERVER || SERVER="localhost:4304"

GUID_N=$(int "$GUID_N")
test $GUID_N -gt 0 || error_exit "No sections defined (GUID_N)"

##############################################################################
### Start
##############################################################################
test "$TRACE" && set -x

test $(bool "$CACHED") -eq 0 && CACHED='/uncached' || CACHED=
test -z "$CACHED" && log 1 "Use cached channel values"

curl=$(curl_cmd)
i=0

while test $i -lt $GUID_N; do

    i=$(expr $i + 1)

    log 1 "--- GUID $i ---"

    eval GUID=\$GUID_$i
    test "$GUID" || error_exit "Sensor GUID is required (GUID_$i)"

    SERIAL=$(PVLngGET2 $GUID/serial.txt)
    CHANNEL=$(PVLngGET2 $GUID/channel.txt)

#     SERIAL=$(PVLngNC "$GUID,serial")
#     CHANNEL=$(PVLngNC "$GUID,channel")

    ### read value
    cmd="$owread -s $SERVER ${CACHED}/${SERIAL}/${CHANNEL}"
    log 2 $cmd
    value=$($cmd)
    log 1 "Value        = $value"

    ### Save data
    test "$TEST" || PVLngPUT2 $GUID $value

done

set +x

exit

##############################################################################
# USAGE >>

Fetch 1-wire sensor data

Usage: $scriptname [options]

Options:

    -s  Save data also into log file
    -t  Test mode, don't save to PVLng
        Sets verbosity to info level
    -v  Set verbosity level to info level
    -vv Set verbosity level to debug level
    -h  Show this help

Requires a configuation file $pwd/owfs.conf

See $pwd/owfs.conf.dist for details.

# << USAGE
