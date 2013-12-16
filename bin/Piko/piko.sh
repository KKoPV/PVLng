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
. $pwd/../PVLng.functions

PORT=80
ID=255

while getopts "n:p:i:tvxh" OPTION; do
	case "$OPTION" in
		n) HOST=$OPTARG ;;
		p) PORT=$OPTARG ;;
		i) ID=$OPTARG ;;
		t) TEST=y; VERBOSE=$(expr $VERBOSE + 1) ;;
		v) VERBOSE=$(expr $VERBOSE + 1) ;;
		x) TRACE=y ;;
		h) usage; exit ;;
		?) usage; exit 1 ;;
	esac
done

read_config "$pwd/alert.conf"

GUID_N=$(int "$GUID_N")
test $GUID_N -gt 0 || error_exit "No sections defined (GUID_N)"

##############################################################################
### Start
##############################################################################
test "$TRACE" && set -x

# --quiet Quiet mode, print only values
# --status Get inverter status
# --power Get inverter current power (W)
# --daily Get inverter daily index (Wh)
# --index Get inverter total index (Wh)
# --tech Get technical data

$pwd/contrib/Piko.py --host=$HOST --port=$PORT --id=$ID --quiet \
                     --status --power --daily --index --tech >$TMPFILE


### Inverter Status : 3 (Running)
### Total energy : 801296 Wh
### Today energy : 4841 Wh
### DC Power : 2613 W
### AC Power : 2414 W
### Efficiency : 92.4%
### DC String 1 : 640.4 V 3.24 A 2075 W T=a660 (41.21 C) S=4009
### DC String 2 : 599.6 V 0.89 A 538 W T=a680 (41.14 C) S=c00a
### DC String 3 : 0.0 V 0.00 A 0 W T=a660 (41.21 C) S=0003
### AC Phase 1 : 234.8 V 3.45 A 791 W T=9a20 (48.21 C)
### AC Phase 2 : 235.3 V 3.43 A 792 W T=9a20 (48.21 C)
### AC Phase 3 : 241.6 V 3.54 A 831 W T=9a20 (48.21 C)
### AC Status : 28 (001c ---L123)

### 3
### 801296
### 4841
### 2613 2414 92.4
### 640.4 3.24 2075 41.21 4009
### 599.6 0.89 538 41.14 c00a
### 0.0 0.00 0 41.21 0003
### 234.8 3.45 791 48.21
### 235.3 3.43 792 48.21
### 241.6 3.54 831 48.21



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

	### skip attributes row, extract 2nd value == data from last row, if exists
	value=$($(curl_cmd) $url | tail -n+2 | tail -n1 | cut -f2)

	log 2 "$url => $value"

	### Create unique hash
	flagfile=/var/run/pvlng-$(echo "$pwd/alert.conf-$i" | md5sum | cut -d' ' -f1).alert

	### Prepare condition
	CONDITION=$(echo "$CONDITION" | sed -e "s/[{]VALUE[}]/$value/g")

	### Skip if condition is not true
	if test $(test \$CONDITION) -eq 0; then
		### remove flag file
		test -f $flagfile && rm $flagfile
		continue
	fi

	### Condition was true, execute commands

	### Skip if flag file exists, condition was true before and ONCE is set to 1
	test -f $flagfile && continue

	eval ONCE=\$ONCE_$i

	if test $(int "$ONCE") -eq 1; then
		### Mark condition was true
		touch $flagfile
	fi

	### Get commands count
	eval COMMAND_N=$(int "\$COMMAND_$i_N")

	j=0

	while test $j -lt $COMMAND_N; do

		j=$(expr $j + 1)

		log 1 "--- COMMAND $j ---"

		eval COMMAND=\$COMMAND_$i_$j

		if test "${COMMAND:-log}" = "log"; then
			### Save data to PVLng log
			test "$TEST" && log 1 "log alert: $GUID - $value" || save_log 'Alert' "$GUID: $value"
		else
			### Prepare command
			COMMAND=$(echo "$COMMAND" | sed -e "s/[{]VALUE[}]/$value/g")
			### Execute command
			test "$TEST" && log 1 "$($COMMAND)" || $($COMMAND)
		fi

	done

done

set +x

exit

##############################################################################
# USAGE >>

Read live data from Kostal Piko inverter

Usage: $scriptname -n host [options]

Options:

	-n  Host name or IP address, required
	-p  Port, default 80
	-i  RS485 bus address, e.g. if you have more than one inverter connected
		default 255
	-t  Test mode, don't save to PVLng
	    Sets verbosity to info level
	-v  Set verbosity level to info level
	-vv Set verbosity level to debug level
	-h  Show this help

Requires a configuation file $pwd/alert.conf

See $pwd/alert.conf.dist for details.

# << USAGE
