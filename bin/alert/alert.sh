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

. $pwd/../PVLng.conf
. $pwd/../PVLng.sh

while getopts "tvrxh" OPTION; do
	case "$OPTION" in
		t) TEST=y; VERBOSE=$((VERBOSE+1)) ;;
		v) VERBOSE=$((VERBOSE+1)) ;;
		r) RESET=y ;;
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
test "$TRACE" && set -x

### Prepare conditions
function replace_vars {
	echo "$1" | sed -e "s~[{]VALUE[}]~$value~g" \
	                -e "s~[{]NAME[}]~$name~g" -e "s~[{]LAST[}]~$last~g"
}

### Reset run files
function reset {
	files=$pwd/run/$hash*
	log 1 Reset, delete $files ...
	rm $files
}

### Create unique hash
hash=$(echo $(basename "$1") | sed -e 's~[.].*$~~g' -e 's~[^A-Za-z0-9-]~_~g').$i

if test "$RESET"; then
	reset
	exit
fi

curl=$(curl_cmd)

i=0

while test $i -lt $GUID_N; do

	i=$((i+1))

	log 1 "=== GUID $i ==="

	eval GUID=\$GUID_$i
	if test -z "$GUID"; then
		log 1 "GUID is empty, skip"
		continue
	fi

	eval CONDITION=\$CONDITION_$i
	test "$CONDITION" || error_exit "Condition is required (CONDITION_$i)"

	### Use last readings for the same GUID
	if test "$GUID" != "$LASTGUID"; then

		url="$PVLngURL2/$GUID"

		numeric=$($curl "$url/attributes/numeric")

		if echo "$numeric" | grep -qe '[[:alpha:]]'; then
		    ### An error occured
		    error_exit "$numeric"
		fi

		### Check, if data was received
		test -n "$numeric" || continue

		name="$($curl "$url/attributes/name") ($($curl "$url/attributes/description"))"

		### extract 2nd value == data from last row, if exists
		data=$($curl --header "Accept: application/tsv" "$url/data?period=last")
		value=$(echo "$data" | cut -f2)

		LASTGUID=$GUID

	fi

	log 2 "Result : $name - $value"

	lastfile=$pwd/run/$hash$i.last
	flagfile=$pwd/run/$hash$i.once

	test -f $lastfile && last=$(<$lastfile)

	echo -n "$value" >$lastfile

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

		j=$((j+1))

		log 1 "--- Action $j ---"

		eval ACTION=\$ACTION_${i}_${j}

		if test "${ACTION:-log}" = "log"; then
			### Save data to PVLng log
			test "$TEST" && log 1 "Log alert: $GUID - $value" || save_log 'Alert' "[$GUID] $name: $value"
		else
			### Prepare command
			ACTION=$(replace_vars "$ACTION")
			### Execute command
			log 1 "$ACTION"
			test "$TEST" || eval "$ACTION"
		fi

	done

done

test "$TEST" && reset

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
