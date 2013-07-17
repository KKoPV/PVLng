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
APIENDPOINT=
APIKEY=
GUID_N=0

. $pwd/../PVLng.conf
. $pwd/../PVLng.sh

CACHED=false

while getopts "tvxh" OPTION; do
	case "$OPTION" in
		t) TEST=y; VERBOSE=$(expr $VERBOSE + 1) ;;
		v) VERBOSE=$(expr $VERBOSE + 1) ;;
		x) TRACE=y ;;
		h) usage; exit ;;
		?) usage; exit 1 ;;
	esac
done

shift $((OPTIND-1))

read_config "$1"

##############################################################################
### Check config data
##############################################################################
test "$APIENDPOINT" || error_exit "Xively API Endpoint is required (APIENDPOINT)!"
test "$APIKEY" || error_exit "Xively API key is required (APIKEY)!"

test "$INTERVAL" || error_exit "Update interval must be set (INTERVAL)!"
INTERVAL=$(int "$INTERVAL")
test $INTERVAL -gt 0 || error_exit "Update interval must > 0 (INTERVAL)!"

GUID_N=$(int "$GUID_N")
test $GUID_N -gt 0 || error_exit "No sections defined (GUID_N)"

##############################################################################
### Start
##############################################################################
test "$TRACE" && set -x

LC_NUMERIC=en_US

curl=$(curl_cmd)
i=0
found=

while test $i -lt $GUID_N; do

	i=$(expr $i + 1)

	log 1 "--- $i ---"

	### required parameters
	eval GUID=\$GUID_$i
	log 2 "GUID     : $GUID"
	test "$GUID" || error_exit "Sensor GUID is required (GUID_$i)"

	eval CHANNEL=\$CHANNEL_$i
	log 2 "Channel  : $CHANNEL"
	test "$CHANNEL" || error_exit "Xively channel name is required (CHANNEL_$i)"

	### read value
	url="$PVLngURL2/$GUID/data?period=${INTERVAL}minutes"
	log 2 "Get-URL  : $url"

	### get last row
	row=$($curl --header "Accept: application/tsv"  $url | tail -n1)
	log 2 "$row"

	### Just after 0:00 no data for today yet
	test "$row" || continue

	if echo "$row" | egrep -q '[[:alpha:]]'; then
		error_exit "$row"
	fi

	### set timestamp and data to $1 and $2
	set $row
	timestamp=$1

	### Format for this channel defined?
	eval FORMAT=\$FORMAT_$i

	if test "$FORMAT"; then
		log 2 "Format   : $FORMAT"
	    value=$(printf "$FORMAT" "$2")
	else
        value=$2
	fi

	age=$(echo "scale=0;($(date +%s)-$timestamp)/60" | bc -l)
	log 2 "Last     : $(date -d @$timestamp)"
	log 2 "Age      : $age min."

	### test for valid timestamp
	### last readed timestamp must be greater or equal $valid
	if test $age -gt $INTERVAL; then
		log 1 "Skip timestamp outside update interval."
		continue
	fi

	log 1 "Value    : $value"

	echo $CHANNEL,$value >>$TMPFILE

	found=y

done

### found at least one "active" channel
test "$found" || exit

log 2 "Send data:"
log 2 "$(cat $TMPFILE)"

test "$TEST" && exit

### Send
set $($curl --request PUT \
            --header "X-ApiKey: $APIKEY" \
            --write-out %{http_code} \
            --data-binary @$TMPFILE \
            --output $TMPFILE \
            $APIENDPOINT.csv)

### Check result, ONLY 200 is ok
if test $1 -eq 200; then
	### Ok, data added
	log 1 "Ok"
else
	### log error
	save_log "Xively" "Failed: $(cat $TMPFILE)"
fi

set +x

exit

##############################################################################
# USAGE >>

Push PVLng channel data to device channels on Xively.com

Usage: $scriptname [options] config_file

Options:

	-t  Test mode, don't put to Xively
	    Sets verbosity to info level
	-v  Set verbosity level to info level
	-vv Set verbosity level to debug level
	-h  Show this help

See $pwd/device.conf.dist for details.

# << USAGE
