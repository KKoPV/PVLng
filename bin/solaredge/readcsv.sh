#!/bin/sh
##############################################################################
### @author      Patrick Feisthammel <patrick.feisthammel@citrin.ch>
### @copyright   2013 Patrick Feisthammel
### @license     GNU General Public License http://www.gnu.org/licenses/gpl.txt
### @version     1.0.1
##############################################################################

### Reads CSV file as delivered from Solaredge, like
### 25/12/2013 14:30,"168,1667"
### 25/12/2013 14:45,"906,8333"
### 27/11/2013 00:00,"11.372,823"
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
CONFIG="$1"

read_config "$CONFIG"

test "$2"    || error_exit 'Missing csv file! Try -h'
test -r "$2" || error_exit 'csv file is not readable!'

CSVFILE="$2"
SENDFILE=$(mktemp /tmp/pvlng.data.XXXXXX)
ERRORFILE=$(mktemp /tmp/pvlng.data.error.XXXXXX)

##############################################################################
### Check config data
##############################################################################
GUID_N=$(int "$GUID_N")
test $GUID_N -gt 0 || error_exit "No sections defined"
test $GUID_N -eq 1 || error_exit "Exactly one section has to be defined"

##############################################################################
### Start
##############################################################################
test "$TRACE" && set -x

### read csv file and create batch file to send
perl -ne 'if ( m/^([0-9\/]{10}) ([0-2][0-9]:[0-5][0-9]),\"([0-9\.,]+)\"/) { 
            $date=$1; $time=$2; $c=$3; 
            $c=~ s/\.//g; $c=~ s/,/./;
            $date=~ s#/#.#g;
            printf "%s,%s,%s;", $date, $time, $c;
          } else {
            if (! /^Time,/) {
              print STDERR $_;
            }
          }' <$CSVFILE >>$SENDFILE 2>$ERRORFILE

if test -s $ERRORFILE ; then
        log 1 @$ERRORFILE
	rm $ERRORFILE >/dev/null 2>&1
	rm $SENDFILE >/dev/null 2>&1
        error_exit "CSV File has errors, see log"
else 
        i=1
	### required parameters
	eval GUID=\$GUID_$i
	log 2 "GUID     : $GUID"
	test "$GUID" || error_exit "Sensor GUID is required (GUID_$i)"

	if test "$TEST" ; then
		log 1 "Test-Mode - not sending to $GUID"
                log 1 @$SENDFILE
	else
		PVLngPUT2Batch $GUID @$SENDFILE
	fi
	rm $SENDFILE >/dev/null 2>&1
	rm $ERRORFILE >/dev/null 2>&1
fi

set +x

exit

##############################################################################
# USAGE >>

Read Solaredge CSV and push to PVLng channel

Usage: $scriptname [options] config_file csv_file

Options:

	-t  Test mode, don't put values 
	    Sets verbosity to info level
	-v  Set verbosity level to info level
	-vv Set verbosity level to debug level
	-h  Show this help

See $pwd/solaredge.conf.dist for details.

# << USAGE
