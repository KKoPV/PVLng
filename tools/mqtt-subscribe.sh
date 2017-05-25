#!/bin/bash

#set -x

pwd=$(dirname $0)
pidfile=/run/mqtt-subscribe.pid

### --------------------------------------------------------------------------
doStart () {
    [ "$(getPid)" ] && return

    ### Defaults
    local host=localhost
    local port=1883
    local qos=1
    local log=

    ## Config file exists?
    [ -f $pwd/mqtt-subscribe.conf ] && . $pwd/mqtt-subscribe.conf

    if [ "$log" ]; then
        v='-v'
    else
        log=/dev/null
    fi

    if [ "$deamon" ]; then
        php $pwd/mqtt-subscribe.php -s $host -p $port -q $qos $v >>$log 2>/dev/null &
        echo $! >$pidfile
    else
        php $pwd/mqtt-subscribe.php -s $host -p $port -q $qos -v
    fi
}

### --------------------------------------------------------------------------
doStop () {
    local pid=$(getPid)
    if [ "$pid" ]; then
        kill $pid 2>/dev/null
        rm $pidfile
    fi
}

### --------------------------------------------------------------------------
showStatus () {
    local pid=$(getPid)
    if [ "$pid" ]; then
        echo Running with pid $pid
    else
        echo Not running yet
    fi
}

### --------------------------------------------------------------------------
getPid () {
    if [ -f $pidfile ]; then
        local pid=$(<$pidfile)
        if ps -p $pid >/dev/null; then
            echo $pid
        else
            ### Orphan pid file
            rm $pidfile
        fi
    fi
}

### --------------------------------------------------------------------------
[ "$2" == -f ] || deamon=y

case "$1" in
   start)    doStart; showStatus  ;;
   stop)     doStop; showStatus  ;;
   restart)  doStop; doStart; showStatus  ;;
   status)   showStatus ;;
   *)        echo -e "\nUsage: $0 (start|stop|restart|status)"  ;;
esac
