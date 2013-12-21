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
. $pwd/../PVLng.sh

CACHED=false

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
### Check config data
##############################################################################
GUID_N=$(int "$GUID_N")
test $GUID_N -gt 0 || error_exit "No sections defined (GUID_N)"

##############################################################################
### Start
##############################################################################
test "$TRACE" && set -x

LC_NUMERIC=en_US

curl=$(curl_cmd)

### read value, extract data
row=$(curl --silent --output - http://$IPSWITCH/csv.html | awk '/<body>/{print $0}' | sed 's/<body>//; s/<br>//;')
log 2 "Data:    : $row"

i=0
while test $i -lt $GUID_N; do

	i=$((i + 1))

	log 1 "--- $i ---"

	### required parameters
	eval GUID=\$GUID_$i
	log 2 "GUID     : $GUID"
	test "$GUID" || error_exit "Sensor GUID is required (GUID_$i)"

	eval CHANNEL=\$CHANNEL_$i
	log 2 "Channel  : $CHANNEL"
	test "$CHANNEL" || error_exit "IPswitch channel name is required (CHANNEL_$i)"


        value=$(echo $row | cut -d, -f $CHANNEL)
	log 1 "Value    : $value"

	if echo "$value" | egrep -v -q '^[0-9\.]+$'; then
		error_exit "$value not numeric"
	fi

        if test "$TEST" ; then
          log 1 "Test-Mode - not sending value=$value of channel=$CHANNEL to $GUID"
        else 
          PVLngPUT2 "$GUID" "$value" "$value"
        fi        

done

set +x

exit

##############################################################################
# USAGE >>

Push PVLng channel data to device channels on Xively.com

Usage: $scriptname [options] config_file

Options:

	-t  Test mode, don't put values 
	    Sets verbosity to info level
	-v  Set verbosity level to info level
	-vv Set verbosity level to debug level
	-h  Show this help

See $pwd/device.conf.dist for details.

# << USAGE
