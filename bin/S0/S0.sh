#!/bin/bash
##############################################################################
### @author      Knut Kohl <github@knutkohl.de>
### @copyright   2012-2013 Knut Kohl
### @license     GNU General Public License http://www.gnu.org/licenses/gpl.txt
### @version     1.0.0
##############################################################################

##############################################################################
### Init
##############################################################################
pwd=$(dirname $0)

. $pwd/../PVLng.conf
. $pwd/../PVLng.sh

S0=$(which S0)
test "$S0" || error_exit 'Missing "S0" binary!'

while getopts "sltvxh" OPTION; do
    case "$OPTION" in
        s) STOP=y ;;
        l) SAVEDATA=y ;;
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

GUID_N=$(int "$GUID_N")
test $GUID_N -gt 0    || error_exit "No sections defined"

##############################################################################
### Start
##############################################################################
test "$TRACE" && set -x

i=0

while test $i -lt $GUID_N; do

    i=$((i + 1))

    log 1 "--- Section $i ---"

    eval GUID=\$GUID_$i
    test "$GUID" || error_exit "Sensor GUID is required (GUID_$i)"
    log 1 "GUID    : $GUID"

    DEVICE=$(PVLngGET2 $GUID/channel.txt)
    test "$DEVICE" || error_exit "Device is required (DEVICE_$i)"
    log 1 "Device  : $DEVICE"

    if test ! -r "$DEVICE"; then
        echo
        echo Device $DEVICE is not readable for script running user!
        echo
        ls -l "$DEVICE"
        echo
        echo Please make sure the user is at least added to the group which ownes the device.
        exit 2
    fi

    eval RESOLUTION=\$RESOLUTION_$i
    test "$RESOLUTION" || \
        error_exit "Sensor resolution is required (RESOLUTION_$i)"
    RESOLUTION=$(int $RESOLUTION)
    test "$RESOLUTION" -gt 0 || \
        error_exit "Sensor resolution must be a positive integer!"

    eval IMPULSES=\$IMPULSES_$i
    IMPULSES=$(int $IMPULSES)

    ##############################################################################
    ### Go
    ##############################################################################
    ### log file for measuring data
    LOG=$(run_file S0 $CONFIG $i.log)

    log 1 "Log     : $LOG"

    ### Identify S0 process by device attached to!
    pid=$(ps ax | grep -e "[ /]S0" | grep "$DEVICE" | sed -e 's/^ *//' | cut -d' ' -f1)

    if test "$pid"; then

        ############################################################################
        ### Fine, S0 is running
        ############################################################################

        if test "$STOP"; then
            log 0 "Stop listening"
            log 0 "Kill process $pid ..."
            kill $pid
            log 0 "Remove log $LOG ..."
            rm $LOG
            log 0 "Done."
            exit
        fi

        ### log exists?
        test -f "$LOG" || continue

        mv $LOG $TMPFILE

        ### number of readings
        impulse=$(wc -l $TMPFILE | cut -d' ' -f1)
        log 1 "impulse : $impulse"

        ### test for NOT empty file
        test $impulse -gt 0 || continue

        if test $IMPULSES -eq 0; then
            ### calculate average power
            power=0
            while read p; do
                log 1 "power   : $p"
                power=$(echo "scale=4; $power + $p" | bc -l)
            done <$TMPFILE

            power=$(echo "scale=4; $power / $impulse" | bc -l)

            log 1 "avg.    : $power"
        fi

        if test "$TEST"; then
            ### Restore data file
            cat $TMPFILE >>$LOG
        else
            if test $IMPULSES -eq 0; then
                # log average power
                PVLngPUT2 $GUID $power
            else
                # log impulses
                PVLngPUT2 $GUID $impulse
            fi
        fi

    else

        ##########################################################################
        ### mostly 1st run, start s0
        ##########################################################################
        if test "$TEST"; then
            log 1 "TEST: $S0 -d $DEVICE -r $RESOLUTION -l $LOG"
        else
            ### Start read of device in watt mode!
            $S0 -d $DEVICE -r $RESOLUTION -l $LOG
        fi

    fi

done

set +x

exit

##############################################################################
# USAGE >>

Read S0 impulses

Usage: $scriptname [options] config_file

Options:
    -s    Stop listening, kill all running S0 processes
    -l  Save data also into log file
    -t    Test mode
    -v    Make processing verbose
    -vv    Make processing more verbose
    -h    Show this help

# << USAGE
