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

##############################################################################
### Start
##############################################################################
test "$EMAIL" || error_exit "Email is required! (EMAIL)"
test "$SUBJECT" || error_exit "Subject is required! (SUBJECT)"

GUID_N=$(int "$GUID_N")
test $GUID_N -gt 0 || error_exit "No GUIDs defined (GUID_N)"

##############################################################################
### Go
##############################################################################
test "$TRACE" && set -x

if test "${BODY:0:1}" == @; then
    BODY="$pwd/${BODY:1}"
    test -r "$BODY" || error_exit "Missing mail template: $BODY"
    BODY=$(<$BODY)
fi

### If mail body was empty, just list all channels
test -z "$BODY" && empty=y

curl=$(curl_cmd)

i=0

while test $i -lt $GUID_N; do

    i=$((i+1))

    log 1 "--- Section $i ---"

    eval GUID=\$GUID_$i

    if test -z "$GUID"; then
        log 1 'Disabled, skip'
        continue
    fi

    name="$(PVLngGET $GUID/name.txt)"
    desc="$(PVLngGET $GUID/description.txt)"
    unit="$(PVLngGET $GUID/unit.txt)"

    ### Extract 2nd value == data
    value=$(PVLngGET data/$GUID.tsv?period=last | cut -f2)

	### Format for this channel defined?
	eval FORMAT=\$FORMAT_$i

	if test "$FORMAT"; then
		log 2 "Format   : $FORMAT"
	    printf -v value "$FORMAT" "$value"
	fi

    if test "$empty"; then
        test "$desc" && name="$name ($desc)"
        BODY="$BODY- $name: $value $unit\n"
    else
        BODY=$(
            echo "$BODY" | \
            sed -e "s~[{]NAME_$i[}]~$name~g" \
                -e "s~[{]DESCRIPTION_$i[}]~$desc~g" \
                -e "s~[{]VALUE_$i[}]~$value~g" \
                -e "s~[{]UNIT_$i[}]~$unit~g"
        )
    fi

done

log 1 "Send email to $EMAIL"
log 1 "Subject: $SUBJECT"
log 1 "Body:\n$BODY"

test "$TEST" || echo -e "$BODY" | mail -a "From: PVLng@$(hostname --long)" \
                                       -a "Content-Type: text/plain; charset=UTF-8" \
                                       -s "[PVLng] $SUBJECT" "$EMAIL" >/dev/null

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
