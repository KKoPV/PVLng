#!/bin/bash
##############################################################################
### @author      Knut Kohl <github@knutkohl.de>
### @copyright   2012-2013 Knut Kohl
### @license     GNU General Public License http://www.gnu.org/licenses/gpl.txt
### @version     $Id$
##############################################################################

### How many parameters are supported
vMax=12

##############################################################################
### Init
##############################################################################
pwd=$(dirname $0)

. $pwd/../PVLng.conf
. $pwd/../PVLng.sh

while getopts "tvxh" OPTION; do
	case "$OPTION" in
		t) TEST=y; VERBOSE=$((VERBOSE + 1)) ;;
		v) VERBOSE=$((VERBOSE + 1)) ;;
		x) TRACE=y ;;
		h) usage; exit ;;
		?) usage; exit 1 ;;
	esac
done

read_config $pwd/pvoutput.conf

shift $((OPTIND-1))

read_config "$1"

##############################################################################
### Start
##############################################################################
test "$TRACE" && set -x

test "$APIURL"	 || error_exit "pvoutput.org API URL is required, see pvoutput.conf.dist"
test "$APIKEY"	 || error_exit "pvoutput.org API key is required, see pvoutput.conf.dist"
test "$SYSTEMID" || error_exit "pvoutput.org Plant Id is required"

NOW=$(date +%s)
LASTFILE=/tmp/$(hash "$1")
test -f "$LASTFILE" && LAST=$(<$LASTFILE) || LAST=$NOW
INTERVAL=$(echo "scale=0; ( $NOW - $LAST ) / 60" | bc -l)
echo $NOW >$LASTFILE

curl="$(curl_cmd)"

DATA=
i=0
check=

while test $i -lt $vMax; do

	i=$((i + 1))

	log 1 "--- $i ---"

	eval GUID=\$GUID_$i

	if test "$GUID"; then
	
		log 1 "$(printf 'GUID    %2d: %s' $i $GUID)"

		eval FACTOR=\$FACTOR_$i
		test "$FACTOR" || FACTOR=1
		log 1 "$(printf 'FACTOR  %2d: %s' $i $FACTOR)"

		url="$PVLngURL2/$GUID/data?period=${INTERVAL}minutes"
		log 2 "$url"

		### empty temp. file
		echo -n >$TMPFILE

		### extract 2nd value == data from last row, if exists
		value=$($curl --header "Accept: application/tsv" $url | tail -n1 | cut -f2)

		### unset only zero values for v1 .. v4
		if test $i -le 4; then
			test "$value" = "0" && value=
		fi

		if test "$value"; then
			value=$(echo "scale=3; $value * $FACTOR" | bc -l)
			DATA="$DATA -d v$i=$value"
		fi
		log 1 "$(printf 'VALUE   %2d: %s' $i $value)"

		check="$check$value"
	fi

	### Check if at least one of v1...v4 is set
	if test $i -eq 4; then
		if test "$check"; then
			log 1 "OK        : At least one of v1 .. v4 is filled ..."
		else
			### skip further processing
			log 1 "SKIP      : All of v1 .. v4 are empty!"
			exit
		fi
	fi

done

DATA="-d d="$(date "+%Y%m%d")" -d t="$(date "+%H:%M")"$DATA"

log 1 "Data      : $DATA"

test -z "$TEST" || exit

#save_log "PVOutput" "$DATA"

### Send
$curl --header "X-Pvoutput-Apikey: $APIKEY" \
      --header "X-Pvoutput-SystemId: $SYSTEMID" \
      --output $TMPFILE $DATA $APIURL
rc=$?

log 1 $(cat $TMPFILE)

### Check curl exit code
if test $rc -ne 0; then
	. $pwd/../curl-errors
	save_log "PVOutput" "Curl error ($rc): ${curl_rc[$rc]}"
fi

### Check result, ONLY 200 is ok
if cat $TMPFILE | grep -q '200:'; then
	### Ok, state added
	log 1 "Ok"
else
	### log error
	save_log "PVOutput / $SYSTEMID" "Update plant failed: $(cat $TMPFILE)"
fi

set +x

exit

##############################################################################
# USAGE >>

Update PVOutput.org system

Usage: $scriptname [options] config_file

Options:
	-t   Test mode, don't push to PVOutput
	     Sets verbosity to info level
	-v   Set verbosity level to info level
	-vv  Set verbosity level to debug level
	-h   Show this help

See system.conf.dist for reference.

# << USAGE
