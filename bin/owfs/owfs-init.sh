#!/bin/sh
### BEGIN INIT INFO
# Provides:	         owfs
# Required-Start:    $remote_fs
# Required-Stop:     $remote_fs
# Should-Start:
# Default-Start:     2 3 4 5
# Default-Stop:      0 1 6
# Short-Description: Start owserver, owfs file system and owhttpd server
# Description:
### END INIT INFO

MOUNT=/owfs

SERVERPORT=4304

HTTPDPORT=3001

. /lib/lsb/init-functions

start() {

  # This will start owserver that will access 1-wire controller on 
  # all USB ports and owserver will listen on port 4304
  /usr/local/bin/owserver -uall -p $SERVERPORT

  # If you want to still have access to 1-Wire via filesystem (owfs),
  # you can start it this way, "-s localhost:4304" is important here,
  # since instead of direct access to hardwire, owfs is instructed
  # to access 1-Wire via owserver that we started on port 4304 before:
  /usr/local/bin/owfs --allow_other --mountpoint=$MOUNT -s localhost:$SERVERPORT

  # It may be convinient to start owhttpd on port 3001 that allows you to browse
  # your 1-wire network in web browser:
  /usr/local/bin/owhttpd -s localhost:$SERVERPORT -p $HTTPDPORT

}

stop() {

  killall /usr/local/bin/owhttpd
  killall /usr/local/bin/owfs
  killall /usr/local/bin/owserver

}

case "$1" in
  start)
    start
    ;;
  stop)
    stop
    ;;
  restart)
    $0 stop
    $0 start
    ;;
  *)
    echo "Usage: $0 {start|stop|restart}" >&2
    exit 1
    ;;
esac

exit 0
