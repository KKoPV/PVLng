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

if test "$TEST" && test -z "$(which owread)"; then
	error_exit "Missing owread binary from OWFS. Is OWFS is properly installed?"
fi

read_config "$pwd/owfs.conf"

test $SERVER || SERVER=4304

GUID_N=$(int "$GUID_N")
test $GUID_N -gt 0 || error_exit "No sections defined (GUID_N)"

##############################################################################
### Start
##############################################################################
test "$TRACE" && set -x

test $(bool "$CACHED") -eq 0 && CACHED='/uncached' || CACHED=
test -z "$CACHED" && log 1 "Use cached channel values"

i=0

while test $i -lt $GUID_N; do

	i=$(expr $i + 1)

	log 1 "--- GUID $i ---"

	eval GUID=\$GUID_$i
	test "$GUID" || error_exit "Sensor GUID is required (GUID_$i)"

	SERIAL=$($(curl_cmd) "$PVLngURL1/$GUID.csv?attributes=serial")
	CHANNEL=$($(curl_cmd) "$PVLngURL1/$GUID.csv?attributes=channel")

	### read value
	value=$(owread -s $SERVER ${CACHED}/${SERIAL}/$CHANNEL)
	log 1 "Read : owread -s $SERVER ${CACHED}/${SERIAL}/$CHANNEL => $value"

	### Save data
	test "$TEST" || PVLngPUT1 $GUID $value

done

set +x

exit

##############################################################################
# USAGE >>

Fetch 1-wire sensor data

Usage: $scriptname [options]

Options:

	-t  Test mode, don't save to PVLng
	    Sets verbosity to info level
	-v  Set verbosity level to info level
	-vv Set verbosity level to debug level
	-h  Show this help

Requires a configuation file $pwd/owfs.conf

See $pwd/owfs.conf.dist for details.

# << USAGE
