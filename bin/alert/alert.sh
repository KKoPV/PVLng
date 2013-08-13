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
CONFIG="$1"

read_config "$CONFIG"

GUID_N=$(int "$GUID_N")
test $GUID_N -gt 0 || error_exit "No sections defined (GUID_N)"

##############################################################################
### Start
##############################################################################
test "$TRACE" && set -x

### Prepare conditions
function replace_vars {
	local str="$1" ### save for looping
	local i=0
	local value=
	local name=
	local last=

	### On replacing in Condition, $EMPTY is not set, so it works on real data

	### max. 100 parameters :-)
	while test $i -lt 100; do
		i=$((i+1))

		eval value="\$value_$i"
		test -z "$value" && value=$EMPTY

		eval name="\$name_$i"
		eval last="\$last_$i"

		str=$(echo "$str" | sed -e "s~[{]VALUE_$i[}]~$value~g" \
		                        -e "s~[{]NAME_$i[}]~$name~g" \
		                        -e "s~[{]LAST_$i[}]~$last~g")
	done

	### If only 1 is used, VALUE, NAME and LAST are also allowed
	test -z "$value_1" && value_1=$EMPTY
	str=$(echo "$str" | sed -e "s~[{]VALUE[}]~$value_1~g" \
	                        -e "s~[{]NAME[}]~$name_1~g" \
	                        -e "s~[{]LAST[}]~$last_1~g")

	echo "$str"
}

### Reset run files
function reset {
	files=$(ls $(run_file alert $CONFIG '*'))
	log 1 Reset, delete $files ...
	rm $files
}

if test "$RESET"; then
	reset
	exit
fi

curl=$(curl_cmd)

i=0

while test $i -lt $GUID_N; do

	i=$((i+1))

	EMPTY=

	log 1 "--- $i ---"

	eval GUID_i_N=\$GUID_${i}_N
	GUID_i_N=$(int "$GUID_i_N")

	if test $GUID_i_N -eq 0; then
		continue
	fi

	j=0

	while test $j -lt $GUID_i_N; do

		j=$((j+1))

		eval url="\$PVLngURL2/\$GUID_${i}_${j}"
		log 2 "URL: $url"

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

		lastfile=$(run_file alert $CONFIG "$i.$j.last")
		test -f $lastfile && last=$(<$lastfile) || last=
		eval last_$j="\$last"

		echo -n "$value" >$lastfile

	done

	flagfile=$(run_file alert $CONFIG "$i.once")

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

		eval EMPTY=\$ACTION_${i}_${j}_EMPTY
		test "$EMPTY" || EMPTY="<empty>"

		case ${ACTION:-log} in

			log)
				### Save data to PVLng log
				if test "$TEST"; then
					log 1 "Log: $GUID - $value"
				else
					save_log 'Alert' "{NAME}: {VALUE}"
				fi
				;;

			logger)
				eval MESSAGE=\$ACTION_${i}_${j}_MESSAGE
				test "$MESSAGE" || MESSAGE="{NAME}: {VALUE}"
				MESSAGE=$(replace_vars "$MESSAGE")

			    log 1 "Logger: $MESSAGE"

				test "$TEST" || logger -t PVLng "$MESSAGE"
				;;

			mail)
				eval EMAIL=\$ACTION_${i}_${j}_EMAIL
				test "$EMAIL" || error_exit "Email is required! (ACTION_${i}_${j}_EMAIL)"

				eval SUBJECT=\$ACTION_${i}_${j}_SUBJECT
				test "$SUBJECT" || SUBJECT="{NAME}: {VALUE}"
				SUBJECT=$(replace_vars "$SUBJECT")

				eval BODY=\$ACTION_${i}_${j}_BODY
				BODY=$(replace_vars "$BODY")

			    log 1 "Send email to $EMAIL"
				log 1 "Subject: $SUBJECT"
				log 1 "Body:"
				log 1 "$BODY"

				test "$TEST" || echo -e "$BODY" | mail -s "[PVLng] $SUBJECT" $EMAIL >/dev/null
				;;

			file)
				eval DIR=\$ACTION_${i}_${j}_DIR
				test "$DIR" || error_exit "Directory is required! (ACTION_${i}_${j}_DIR)"

				eval PREFIX=\$ACTION_${i}_${j}_PREFIX
				test "$PREFIX" || PREFIX="alert"

				eval TEXT=\$ACTION_${i}_${j}_TEXT
				test "$TEXT" || TEXT="{NAME}: {VALUE}"
				TEXT=$(replace_vars "$TEXT")

				file=$(mktemp $DIR/$PREFIX.$(date +"%F.%X").XXXXXX)

				log 1 "Text: $TEXT"
				log 1 "File: $file"

				test "$TEST" || echo "$TEXT" >$file
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

See $pwd/alert.conf.dist for details.

# << USAGE
