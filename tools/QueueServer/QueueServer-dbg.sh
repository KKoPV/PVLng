#!/bin/bash
###
### Wrapper script to start/stop SMQ Server and Worker daemons
###
### @author     Knut Kohl <github@knutkohl.de>
### @copyright  2012-2015 Knut Kohl
### @license    MIT License (MIT) http://opensource.org/licenses/MIT
### @version    1.0.0
###

#set -x

pwd=$(dirname $(readlink -f $0))
#pwd=$(cd $(dirname ${BASH_SOURCE[0]}) && pwd)

conf=$pwd/QueueServer.conf

[ -f "$conf" ] || ( echo 'Missing configuration file!'; exit 127 )

case $1 in
    Server)
        prog=Server
        ;;
    Worker)
        prog=Worker
        ;;
    *)
        echo "Usage: $0 (Server|Worker)"
        exit 1
esac

. $conf

### Defaults
[ "$PORT" ] || PORT=7777
[ "$MEMCACHE" ] || MEMCACHE=localhost:11211

php $pwd/$prog.php -vvm $MEMCACHE -p $PORT
