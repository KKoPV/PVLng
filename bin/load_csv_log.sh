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

. $pwd/PVLng.conf
. $pwd/PVLng.sh

while getopts "tvxh" OPTION; do
    case "$OPTION" in
        t) TEST=y; VERBOSE=$((VERBOSE + 1)) ;;
        v) VERBOSE=$((VERBOSE + 1)) ;;
        x) TRACE=y ;;
        h) usage; exit ;;
        ?) usage; exit 1 ;;
    esac
done

shift $((OPTIND-1))

##############################################################################
### Start
##############################################################################
test "$TRACE" && set -x

while test "$1"; do

    ### Ubuntu/Debian don't have the same awk as openSUSE, so the GUID match
    ### didn't work for me. Because of that I changed awk to sed.
    ### https://github.com/K-Ko/PVLng/pull/18
    GUID=$(echo "$1" | sed -n 's/.*\(\([a-z0-9]\{4\}-\)\{7\}[a-z0-9]\{4\}\).*/\1/p')
    test "$GUID" || error_exit "No sensor GUID in filename detected"

    test "$TEST" || PVLngPUT2CSV $GUID "@$1"

    shift
done

set +x

exit

##############################################################################
# USAGE >>

Load logged CSV files via API r2

Usage: $scriptname [options] log_files
       $scriptname [options] data/<GUID>/*.csv

GUID will be extracted from file name

Options:

    -t  Test mode, don't save to PVLng
        Sets verbosity to info level
    -v  Set verbosity level to info level
    -vv Set verbosity level to debug level
    -h  Show this help

Log file format, Semicolon separated lines of:

    <date time>;<value>

# << USAGE
