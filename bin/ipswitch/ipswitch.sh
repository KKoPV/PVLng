#!/bin/sh
##############################################################################
### @author      Patrick Feisthammel <patrick.feisthammel@citrin.ch>
### @copyright   2013 Patrick Feisthammel
### @license     GNU General Public License http://www.gnu.org/licenses/gpl.txt
### @version     1.1.0
##############################################################################

##############################################################################
### Init
##############################################################################
pwd=$(dirname $0)

. $pwd/../PVLng.conf
. $pwd/../PVLng.sh

CACHED=false

while getopts "tvxnh" OPTION; do
	case "$OPTION" in
		t) TEST=y; VERBOSE=$((VERBOSE + 1)) ;;
		v) VERBOSE=$((VERBOSE + 1)) ;;
		x) TRACE=y ;;
                n) NOPUSH=y ;;
		h) usage; exit ;;
		?) usage; exit 1 ;;
	esac
done

shift $((OPTIND-1))
CONFIG="$1"

read_config "$CONFIG"

##############################################################################
### Check config data
##############################################################################
test "$IPSWITCH" || error_exit "IP address of IPswitch is required"

GUID_N=$(int "$GUID_N")
test $GUID_N -gt 0 || error_exit "No sections defined"

##############################################################################
### Start
##############################################################################
test "$TRACE" && set -x

### read value, extract data, $(curl_cmd) respects verbose settings!
row=$($(curl_cmd) http://$IPSWITCH/csv.html | awk '/<body>/{print $0}' | sed 's/<body>//; s/<br>//;')
log 2 "Raw data : $row"

i=0
valuelist=""
guidlist=""
while test $i -lt $GUID_N; do

	i=$((i + 1))

	log 1 "--- Section $i ---"

	### required parameters
	eval GUID=\$GUID_$i
	log 2 "GUID     : $GUID"
	test "$GUID" || error_exit "Sensor GUID is required (GUID_$i)"

	eval CHANNEL=\$CHANNEL_$i
	log 2 "Channel  : $CHANNEL"
	test "$CHANNEL" || error_exit "IPswitch channel name is required (CHANNEL_$i)"

	value=$(echo $row | cut -d, -f $CHANNEL | sed 's/ //g' )
	log 1 "Value    : $value"

	if echo "$value" | egrep -v -q '^[0-9\.-]+$'; then
		error_exit "$GUID: $value not numeric, extracted from $row"
	fi

	if test "$TEST" ; then
		log 1 "Test-Mode - not sending value=$value of channel=$CHANNEL to $GUID"
	else
                if test "$NOPUSH" = "y" ; then
                        log 1 "Not pushing to server - value=$value, channel=$CHANNEL, GUID=$GUID"
                else
		        PVLngPUT2 $GUID $value
                fi
	fi
        valuelist="$valuelist,$value"
        guidlist="$guidlist,$GUID"
done

if test ! -z "$CSVFILENAME" ; then
        file="$CSVFILENAME$(date +%Y%m%d).csv"
        line="$(date +%s)$valuelist"
        if test "$TEST" ; then
                log 1 "Test-Mode - not writing to $file: $line"
        else
                if test ! -e $file ; then 
                        echo "#v1:time$guidlist" >>$file;
		fi
		echo $line >>$file || error_exit "Could not write to $file: $line"
        fi
fi

set +x

exit

##############################################################################
# USAGE >>

Read IPswitch data and push to PVLng channel

Usage: $scriptname [options] config_file

Options:

	-t  Test mode, don't put values 
	    Sets verbosity to info level
	-v  Set verbosity level to info level
	-vv Set verbosity level to debug level
        -n  Do not push the values to the server
	-h  Show this help

See $pwd/ipswitch.conf.dist for details.

# << USAGE
