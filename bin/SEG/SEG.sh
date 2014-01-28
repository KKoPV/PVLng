#!/bin/bash
##############################################################################
### @author      Knut Kohl <github@knutkohl.de>
### @copyright   2012-2014 Knut Kohl
### @license     GNU General Public License http://www.gnu.org/licenses/gpl.txt
### @version     1.0.0
##############################################################################

##############################################################################
### Init
##############################################################################
pwd=$(dirname $0)

. $pwd/../PVLng.conf
. $pwd/../PVLng.sh

while getopts "i:tvxh" OPTION; do
    case "$OPTION" in
        i) INTERVAL=$(int "$OPTARG") ;;
        t) TEST=y; VERBOSE=$((VERBOSE + 1)) ;;
        v) VERBOSE=$((VERBOSE + 1)) ;;
        x) TRACE=y ;;
        h) usage; exit ;;
        ?) usage; exit 1 ;;
    esac
done

read_config $pwd/SEG.conf

shift $((OPTIND-1))

read_config "$pwd/$1"

##############################################################################
### Start
##############################################################################
test "$TRACE" && set -x

test "$APIURL" || error_exit "SEG API URL is required, see SEG.conf.dist"
test "$SITE_TOKEN" || error_exit "SEG site name is required (SITE_TOKEN)"
test "$NODE_NAME" || error_exit "SEG node name is required (NODE_NAME)"

CHANNEL_N=$(int "$CHANNEL_N")
test $CHANNEL_N -gt 0 || error_exit "No channel sections defined (CHANNEL_N)"

##############################################################################
### Go
##############################################################################
curl="$(curl_cmd)"

LC_NUMERIC=en_US

if test -z "$INTERVAL"; then
    ifile=$(run_file SEG "$1" last)
    if test -f "$ifile"; then
        INTERVAL=$(echo "scale=0; ( "$(date +%s)" - "$(<$ifile)" ) / 60" | bc -l)
    else
        ### start with 10 minutes interval...
        INTERVAL=10
    fi
    date +%s >$ifile
fi

i=0

while test $i -lt $CHANNEL_N; do

    i=$((i + 1))

    log 1 "--- Channel $i ---"

    eval ENABLED=\$ENABLED_$i
    if test "$ENABLED" -a $(bool "$ENABLED") -eq 0; then
        ### Enabled was defined AND set to FALSE
        log 1 disabled
        continue
    fi

    ### required parameters
    eval GUID=\$GUID_$i
    log 2 "GUID     : $GUID"
    test "$GUID" || error_exit "Channel GUID is required (GUID_$i)"

    eval STREAM_NAME=\$STREAM_NAME_$i
    log 2 "STREAM   : $STREAM_NAME"
    test "$STREAM_NAME" || error_exit "SEG stream name is required (STREAM_NAME_$i)"

    fetch="data/$GUID.tsv?start=-${INTERVAL}minutes&period=${INTERVAL}minutes"
    log 2 "Fetch    : $fetch"

    ### read value, get last row
    row=$(PVLngGET $fetch | tail -n1)
    log 2 "Data:    : $row"

    ### No data for last $INTERVAL minutes
    test "$row" || continue

    if echo "$row" | egrep -q '[[:alpha:]]'; then
        error_exit "PVLng API readout error:\n\n$row"
    fi

    ### set "timestamp" and "data" to $1 and $2
    set $row
    value="$2"

    ### Factor for this channel
    if test $(int $(PVLngGET $GUID/numeric.txt)) -eq 1; then
        ### Only for numeric channels!
        eval FACTOR=\$FACTOR_$i
        log 2 "Factor   : $FACTOR"
        test "$FACTOR" && value=$(echo "scale=4; $value * $FACTOR" | bc -l)
    else
        ### URL encode spaces to +
        value="$(echo $value | sed -e 's~ ~+~g')"
    fi

    log 1 "Value    : $value"

    stream_data="$stream_data($STREAM_NAME $value)"

done

data="(site $SITE_TOKEN (node $NODE_NAME ? $stream_data))"

log 2 "Send     : $data"

test "$stream_data" || exit

test "$TEST" && exit

### Send
### http://api.smartenergygroups.com/api_streams/<stream_token>/add_point?value=47.2
rc=$($(curl_cmd) --request PUT --write-out %{http_code} --output $TMPFILE \
                 --data "$data" $APIURL)

log 2 @$TMPFILE

### Check result, ONLY 200 is ok
if test $rc -eq 200; then
    ### Ok, state added
    log 1 "Ok"
else
    ### log error
    save_log "SEG-$NODE_NAME" "Update failed [$rc] for $value"
    save_log "SEG-$NODE_NAME" @$TMPFILE
fi

set +x

exit

##############################################################################
# USAGE >>

Update Smart Energy Group streams for one device

Usage: $scriptname [options] config_file

Options:
    -i interval  Fix Average interval in minutes
    -t           Test mode, don't push to SEG
                 Sets verbosity to info level
    -v           Set verbosity level to info level
    -vv          Set verbosity level to debug level
    -h           Show this help

See device.conf.dist for reference.

# << USAGE
