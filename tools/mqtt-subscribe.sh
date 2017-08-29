#!/bin/bash
###
### PVLng - PhotoVoltaic Logger new generation
###
### @link       https://github.com/KKoPV/PVLng
### @author     Knut Kohl <github@knutkohl.de>
### @copyright  2017 Knut Kohl
### @license    MIT License (MIT) http://opensource.org/licenses/MIT
###
pwd=$(dirname $(readlink -f ${BASH_SOURCE[0]}))

DESC="MQTT subscriber daemon"
NAME="mqtt-sub"
DAEMON="$pwd/mqtt/mqtt-subscribe.php"

# Define LSB log_* functions.
# Depend on lsb-base (>= 3.0-6) to ensure that this file is present.
. /lib/lsb/init-functions

### Defaults
host=localhost
port=1883
log=

## Config file exists?
cfg=$pwd/../config/mqtt-subscribe.conf
[ -r $cfg ] && . $cfg

DAEMON_ARGS="-s $host -p $port"

if [ "$log" ]; then
    DAEMON_ARGS="$DAEMON_ARGS -v" # verbose
else
    log=/dev/null
fi

### --------------------------------------------------------------------------
do_start () {
    ### Return
    ###   0 if daemon has been started
    ###   1 if daemon was already running
    ###   2 if daemon could not be started

    if [ "$1" ]; then
        ### Run in foreground
        if [ "$(get_pid)" ]; then
            log_failure_msg "$DESC is running, please stop beforehand"
            return 1
        fi

        echo
        echo ':::::::::::::::::::  RUN IN FOREGROUND, STOP WITH CTRL+C  :::::::::::::::::::'
        echo

        $DAEMON $DAEMON_ARGS -v
    else
        [ "$(get_pid)" ] && return 1 # was already running

        $DAEMON $DAEMON_ARGS &>$log &

        ### Check if daemon was started
        sleep 1
        [ "$(get_pid)" ] || return 2 # could not be started
    fi

    return 0 # has been started
}

### --------------------------------------------------------------------------
do_stop () {
    ### Return
    ###   0 if daemon has been stopped
    ###   1 if daemon was already stopped
    ###   2 if daemon could not be stopped

    [ "$(get_pid)" ] || return 1 # not running

    pkill -f "$DAEMON"

    ### Check if daemon was stopped
    sleep 1
    [ "$(get_pid)" ] && return 2

    return 0 # has been stopped
}

### --------------------------------------------------------------------------
check_status () {
    if [ "$(get_pid)" ]; then
        log_success_msg "$DESC is running"
        return 0
    else
        log_failure_msg "$DESC is not running"
        return 1
    fi
}

### --------------------------------------------------------------------------
get_pid () {
    pgrep -f "$DAEMON"
}

### --------------------------------------------------------------------------
case "$1" in
    start)
        log_daemon_msg "Starting $DESC" "$NAME"
        do_start
        case "$?" in
            0|1) log_end_msg 0 ;;
            2)   log_end_msg 1 ;;
        esac
        ;;
    stop)
        log_daemon_msg "Stopping $DESC" "$NAME"
        do_stop
       case "$?" in
          0|1) log_end_msg 0 ;;
          2)   log_end_msg 1 ;;
       esac
        ;;
    restart)
        log_daemon_msg "Restarting $DESC" "$NAME"
        do_stop
        case "$?" in
            0|1)
                do_start
                case "$?" in
                    0) log_end_msg 0 ;;
                    1) log_end_msg 1 ;; # Old process is still running
                    *) log_end_msg 1 ;; # Failed to start
                esac
                ;;
            *)
                log_end_msg 1 # Failed to stop
                ;;
        esac
        ;;
   foreground)
        do_start foreground
        exit $?
        ;;
   status)
        check_status "$DAEMON" "$DESC" && exit 0 || exit $?
        ;;
   *)
        log_action_msg "Usage: $0 (start|stop|restart|status|foreground)" >&2
        exit 1
        ;;
esac
