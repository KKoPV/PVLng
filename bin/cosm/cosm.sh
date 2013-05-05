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
APIURL=
APIKEY=
FEED=
GUID_N=0

. $pwd/../PVLng.conf
. $pwd/../PVLng.functions

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
test "$APIURL" || error_exit "Cosm API URL is required (APIURL)!"
test "$APIKEY" || error_exit "Cosm ApiKey is required (APIKEY)!"

test "$FEED" || error_exit "Feed Id must be set (FEED)!"
FEED=$(int "$FEED")
test $FEED -gt 0 || error_exit "Feed Id must > 0 (FEED)!"

test "$INTERVAL" || error_exit "Update interval must be set (INTERVAL)!"
INTERVAL=$(int "$INTERVAL")
test $INTERVAL -gt 0 || error_exit "Update interval must > 0 (INTERVAL)!"

GUID_N=$(int "$GUID_N")
test $GUID_N -gt 0 || error_exit "No sections defined (GUID_N)"

##############################################################################
### Start
##############################################################################
test "$TRACE" && set -x

curl=$(curl_cmd)

LC_NUMERIC=en_US

i=0

while test $i -lt $GUID_N; do

	i=$(expr $i + 1)

	log 1 "--- $i ---"

	eval GUID=\$GUID_$i
	log 2 "GUID     : $GUID"
	test "$GUID" || error_exit "Sensor GUID is required (GUID_$i)"

	eval STREAM=\$STREAM_$i
	log 2 "Stream   : $STREAM"
	test "$STREAM" || error_exit "Cosm datastream Id is required (STREAM_$i)"

	eval FORMAT=\$FORMAT_$i
	log 2 "Format   : $FORMAT"

	### read value
	url="$PVLngURL1/$GUID.tsv?period=${INTERVAL}minutes"
	log 2 "Get-URL  : $url"

	### skip attributes row, get last row, set timestamp and data to $1 and $2
	set $($curl $url | tail -n+2 | tail -n1)
	timestamp=$1

	### Just after 0:00 no data for today yet
	test "$timestamp" || continue

	if test "$FORMAT"; then
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

	URL="${APIURL}/${FEED}/datastreams/${STREAM}.csv"
	log 1 "Feed-URL : $URL"

	test "$TEST" && continue

	### Send
	rc=$($curl --request PUT \
	           --header "X-ApiKey: $APIKEY" \
	           --write-out %{http_code} \
	           --output $TMPFILE \
	           --data-binary "$value" \
	           $URL)

	### Check result, ONLY 200 is ok
	if test $rc -eq 200; then
		### Ok, data added
		log 1 "Ok"
	else
		### log error
		save_log "Cosm" "Failed: $(cat $TMPFILE)"
	fi

done

set +x

exit

##############################################################################
# USAGE >>

Push channel data to datastreams on Cosm.com

Usage: $scriptname [options] config_file

Options:

	-t  Test mode, don't save to PVLng
	    Sets verbosity to info level
	-v  Set verbosity level to info level
	-vv Set verbosity level to debug level
	-h  Show this help

Requires a configuation file $pwd/cosm.conf

See $pwd/cosm.conf.dist for details.

# << USAGE
