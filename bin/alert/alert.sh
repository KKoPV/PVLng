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

read_config "$1"

GUID_N=$(int "$GUID_N")
test $GUID_N -gt 0 || error_exit "No sections defined (GUID_N)"

##############################################################################
### Start
##############################################################################
test "$TRACE" && set -x

### Prepare conditions
function replace_vars {
	echo "$1" | sed -e "s~[{]VALUE_1[}]~$value_1~g" -e "s~[{]VALUE_2[}]~$value_2~g" \
	                -e "s~[{]NAME_1[}]~$name_1~g" -e "s~[{]NAME_2[}]~$name_2~g" \
	                -e "s~[{]LAST[}]~$last~g"
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

	log 1 "--- $i ---"

	eval GUID_i_N=\$GUID_${i}_N
	GUID_i_N=$(int "$GUID_i_N")

	if test $GUID_i_N -eq 0; then
		continue
	fi

	j=0

	while test $j -lt $GUID_i_N; do

		j=$((j+1))

		eval GUID=\$GUID_${i}_${j}

		url="$PVLngURL2/$GUID"

		numeric=$($curl "$url/attributes/numeric")

		if echo "$numeric" | grep -qe '[[:alpha:]]'; then
		    ### An error occured
		    error_exit "$numeric"
		fi

		### Check, if data was received
		test -n "$numeric" || continue

		name="$($curl "$url/attributes/name") ($($curl "$url/attributes/description"))"
		eval name_$j="\$name"

		### extract 2nd value == data from last row, if exists
		data=$($curl --header "Accept: application/tsv" "$url/data?period=last")
		value=$(echo "$data" | cut -f2)

		log 2 "Result : $name - $value"

		eval value_$j="\$value"

	done

	lastfile=$pwd/run/$hash$i.last
	flagfile=$pwd/run/$hash$i.once

	test -f $lastfile && last=$(<$lastfile)

	echo -n "$value" >$lastfile

	### Prepare condition
	eval CONDITION=\$CONDITION_$i
	test "$CONDITION" || error_exit "Condition is required (CONDITION_$i)"

	CONDITION=$(replace_vars "$CONDITION")
	log 1 "Condition: $CONDITION"

	if test $numeric -eq 1; then
		result=$(echo "scale=4; $CONDITION" | bc -l)
	else
		test $CONDITION
		test $? -eq 0 && result=1 || result=0
		test -z "$value_1" && value_1='<empty>'
		test -z "$value_2" && value_2='<empty>'
	fi

	### Skip if condition is not true
	if test $result -eq 0; then
		log 1 "Skip, condition not apply."
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

		case ${ACTION:-log} in

			log)
				### Save data to PVLng log
				if test "$TEST"; then
					log 1 "Log: $GUID - $value"
				else
					save_log 'Alert' "{NAME_1}: {VALUE_1}"
				fi
				;;

			logger)
				eval MESSAGE=\$ACTION_${i}_${j}_MESSAGE
				test "$MESSAGE" || MESSAGE="{NAME_1}: {VALUE_1}"
				MESSAGE=$(replace_vars "$MESSAGE")

				if test "$TEST"; then
				    log 1 "Logger: $MESSAGE"
				else
					logger -t PVLng "$MESSAGE"
				fi
				;;

			mail)
				eval EMAIL=\$ACTION_${i}_${j}_EMAIL
				test "$EMAIL" || error_exit "Email is required! (ACTION_${i}_${j}_EMAIL)"

				eval SUBJECT=\$ACTION_${i}_${j}_SUBJECT
				test "$SUBJECT" || SUBJECT="{NAME_1}: {VALUE_1}"
				SUBJECT=$(replace_vars "$SUBJECT")

				eval BODY=\$ACTION_${i}_${j}_BODY
				BODY=$(replace_vars "$BODY")

				if test "$TEST"; then
				    log 1 "Send email to $EMAIL"
					log 1 "Subject: $SUBJECT"
					log 1 "Body:"
					log 1 "$BODY"
				else
				    echo -e "$BODY" | mail -s "[PVLng] $SUBJECT" $EMAIL >/dev/null
				fi
				;;

			*)
				### Prepare command
				ACTION=$(replace_vars "$ACTION")
				### Execute command
				log 1 "$ACTION"
				test "$TEST" || eval "$ACTION"
				;;
		esac

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
