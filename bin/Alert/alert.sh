#!/bin/bash
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

test -d $pwd/run || mkdir -p $pwd/run
if test ! -d $pwd/run; then
	error_exit "Can't create run dir: $pwd/run"
fi

. $pwd/../PVLng.conf
. $pwd/../PVLng.functions

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

SYSTEMID=

read_config "$1"

GUID_N=$(int "$GUID_N")
test $GUID_N -gt 0 || error_exit "No sections defined (GUID_N)"

##############################################################################
### Start
##############################################################################
function replace_vars {
	echo "$1" | sed -e "s/[{]VALUE[}]/$value/g" \
	                -e "s/[{]NAME[}]/$name/g" -e "s/[{]LAST[}]/$last/g"
}

test "$TRACE" && set -x

i=0

while test $i -lt $GUID_N; do

	i=$(expr $i + 1)

	log 1 "=== GUID $i ==="

	eval GUID=\$GUID_$i
	if test -z "$GUID"; then
		log 1 "GUID is empty, skip"
		continue
	fi

	eval CONDITION=\$CONDITION_$i
	test "$CONDITION" || error_exit "Condition is required (CONDITION_$i)"

	url="$PVLngURL1/$GUID.tsv?period=last"

	$(curl_cmd) $url >$TMPFILE

	### detect numeric channel
	numeric=$(cat $TMPFILE | head -n1 | cut -f8)

	### Check, if data was received
	test -n "$numeric" || continue

	### get attributes row, extract 2nd value == channel name
	name=$(cat $TMPFILE | head -n1 | cut -f2)
	name="$name ($(cat $TMPFILE | head -n1 | cut -f5))"
	### skip attributes row, extract 2nd value == data from last row, if exists
	value=$(cat $TMPFILE | tail -n+2 | tail -n1 | cut -f2)

	log 2 "URL    : $url"
	log 2 "Result : $name, $value"

	### Create unique hash
	filename=$(echo $(basename "$1" '.conf') | sed -e 's/[^A-Za-z0-9-]/_/g').$i
	lastfile=$pwd/run/$filename.last
	flagfile=$pwd/run/$filename.sem

	test -f $lastfile && last=$(<$lastfile)

	echo "$value" >$lastfile

	### Prepare condition
	CONDITION=$(replace_vars "$CONDITION")

	if test $numeric -eq 1; then
		result=$(echo "$CONDITION" | bc -l)
	else
		test $CONDITION
		test $? -eq 0 && result=1 || result=0
		test -z "$value" && value='<empty>'
	fi

	### Skip if condition is not true
	if test $result -eq 0; then
		log 1 "Skip, condition '$CONDITION' not apply"
		### remove flag file
		test -f $flagfile && rm $flagfile
		continue
	fi

	### Condition was true

	### Skip if flag file exists, condition was true before && ONCE is set
	if test -f $flagfile; then
		log 1 "Skip, report condition '$CONDITION' only once"
		continue
	fi

	eval ONCE=\$ONCE_$i

	if test $(int "$ONCE") -eq 1; then
		### Mark condition was true
		touch $flagfile
	fi

	### Get actions count
	eval ACTION_N=\$ACTION_${i}_N
	ACTION_N=$(int $ACTION_N)

	j=0

	while test $j -lt $ACTION_N; do

		j=$(expr $j + 1)

		log 1 "--- ACTION $j ---"

		eval ACTION=\$ACTION_${i}_${j}

		if test "${ACTION:-log}" = "log"; then
			### Save data to PVLng log
			test "$TEST" && log 1 "log alert: $GUID - $value" || save_log 'Alert' "$name ($GUID): $value"
		else
			### Prepare command
			ACTION=$(replace_vars "$ACTION")
			### Execute command
			log 1 "$ACTION"
			test "$TEST" || eval "$ACTION"
		fi

	done

done

set +x

exit

##############################################################################
# USAGE >>

Alert on channels conditions

Usage: $scriptname [options] config_file

Options:

	-t  Test mode, don't save to PVLng
	    Sets verbosity to info level
	-v  Set verbosity level to info level
	-vv Set verbosity level to debug level
	-h  Show this help

Requires a writable directory: $pwd/run

See $pwd/alert.conf.dist for details.

# << USAGE
