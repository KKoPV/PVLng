#!/bin/bash
##############################################################################
### @author      Knut Kohl <github@knutkohl.de>
### @copyright   2012-2013 Knut Kohl
### @license     GNU General Public License http://www.gnu.org/licenses/gpl.txt
### @version     $Id$
##############################################################################

##############################################################################
### Init variables
##############################################################################
pwd=$(dirname $0)
WEBBOX="192.168.0.168:80"
GUID_N=0

. $pwd/../PVLng.conf
. $pwd/../PVLng.sh

while getopts "tvxh" OPTION; do
  case "$OPTION" in
    t) TEST=y; VERBOSE=$(expr $VERBOSE + 1) ;;
    v) VERBOSE=$(expr $VERBOSE + 1) ;;
    x) TRACE=y ;;
    h) usage; exit ;;
    ?) usage; exit 1 ;;
  esac
done

read_config "$pwd/Webbox.conf"

GUID_N=$(int "$GUID_N")
test $GUID_N -gt 0  || error_exit "No GUIDs defined"

##############################################################################
### Start
##############################################################################
test "$TRACE" && set -x

test "$WEBBOX" || error_exit "IP address is required!"

##############################################################################
### Go
##############################################################################
RESPONSEFILE=$(mktemp /tmp/pvlng.XXXXXX)

trap 'rm -f $TMPFILE $RESPONSEFILE >/dev/null 2>&1' 0

curl="$(curl_cmd)"

i=0

while test $i -lt $GUID_N; do

	i=$(expr $i + 1)

	log 1 "--- Section $i ---"

	eval GUID=\$GUID_$i
	test "$GUID" || error_exit "Equipment GUID is required (GUID_$i)"

	### request serial
	SERIAL=$($curl --header "Accept: plain/text" "$PVLngURL2/$GUID/attributes/serial")
	test "$SERIAL" || error_exit "No serial number found for GUID: $GUID"

	### Build RPC request, catch all channels from equipment
	cat >$TMPFILE <<EOT
{ "version": "1.0", "proc": "GetProcessData", "id": "$SERIAL", "format": "JSON",
  "params": { "devices": [ { "key": "$SERIAL", "channels": null } ] } }
EOT

	log 2 "$(cat $TMPFILE)"

	### Query webbox
	$curl --output $RESPONSEFILE --data-urlencode RPC@$TMPFILE http://$WEBBOX/rpc
	rc=$?

	if test $rc -ne 0; then
		error_exit "cUrl error for Webbox: $rc"
	fi

	### Test mode
	log 2 "Webbox response:"
	log 2 $(cat $RESPONSEFILE)

	### Check response for error object
	if grep -q '"error"' $RESPONSEFILE; then
		error_exit "$(printf "ERROR from Webbox:\n%s" "$(cat $RESPONSEFILE)")"
	fi

	### Save data
	test "$TEST" || PVLngPUT2 $GUID @$RESPONSEFILE

done

set +x

exit

##############################################################################
# USAGE >>

Read Inverter/Sensorbox data from Webbox

Usage:
    $0 [options]

Options:

    -t  Test mode, read only from Webbox and show the results, don't send data
    -v  Make execution verbose
    -h  This help

Uses Webbox.conf as config file.

See Webbox.conf.dist for reference.

# << USAGE
