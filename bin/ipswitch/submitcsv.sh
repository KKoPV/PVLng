#!/bin/sh
##############################################################################
### @author      Patrick Feisthammel <patrick.feisthammel@citrin.ch>
### @copyright   2013 Patrick Feisthammel
### @license     GNU General Public License http://www.gnu.org/licenses/gpl.txt
### @version     2.0.0
###
### - 1.0.0
###   Initial creation
##############################################################################

### Reads CSV file as created by ipswitch.sh, like
### "1388529435","60070","59261","58812","7274","7208","6962","0","0","3","0","0","3"
###
### You have to use the same configuration file you used to write the csv file
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

CSVFILE="$2"

test "$CSVFILE"    || error_exit 'Missing csv file! Try -h'
test -r "$CSVFILE" || error_exit 'CSV file "'$CSVFILE'" is not readable!'

SENDFILE=$(mktemp /tmp/pvlng.submitcsv.XXXXXX)
ERRORFILE=$(mktemp /tmp/pvlng.submitcsv.error.XXXXXX)

##############################################################################
### Check config data
##############################################################################
GUID_N=$(int "$GUID_N")
test $GUID_N -gt 0 || error_exit "No sections defined"

##############################################################################
### Start
##############################################################################
test "$TRACE" && set -x

# check file
i=0
guidlist=""
while test $i -lt $GUID_N; do
	i=$((i + 1))
	eval GUID=\$GUID_$i
	guidlist="$guidlist,$GUID"
done
should="#v1:time$guidlist"
is=$(head -n 1 $CSVFILE)

if test "$should" != "$is" ; then
  error_exit "First Line of the file should be $should, but it is $is"
fi
i=0
while test $i -lt $GUID_N; do
	i=$((i + 1))
	eval GUID=\$GUID_$i
	### read csv file and create batch file to send
	perl -ne 'if ( m/^([0-9]+,.*)$/) { 
	    ($time, @list)= split(/,/, $1);
	    $val= $list['$i'-1];
	    printf "%s,%s;", $time, $val;
	} else {
	    if (! /^#/) {
		print STDERR $_;
	    }
	}' <$CSVFILE >>$SENDFILE.$GUID 2>>$ERRORFILE
done

if test -s $ERRORFILE ; then
    log 1 @$ERRORFILE

    rm $ERRORFILE >/dev/null 2>&1
    rm $SENDFILE.* >/dev/null 2>&1
    rm $SENDFILE >/dev/null 2>&1

    error_exit "CSV File has errors, see log"
else 
    i=0
    while test $i -lt $GUID_N; do
	i=$((i + 1))
	eval GUID=\$GUID_$i
	log 2 "GUID     : $GUID"

	if test "$TEST" ; then
	    log 1 "Test-Mode - not sending to $GUID"
	    log 1 @$SENDFILE.$GUID
	else
	    PVLngPUT2Batch $GUID @$SENDFILE.$GUID
	fi
	rm $SENDFILE.$GUID >/dev/null 2>&1
    done
    rm $ERRORFILE >/dev/null 2>&1
    rm $SENDFILE >/dev/null 2>&1
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
