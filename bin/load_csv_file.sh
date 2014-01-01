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

GUID=$1
test "$GUID" || error_exit "Sensor GUID is required (1st parameter)"

shift

##############################################################################
### Start
##############################################################################
test "$TRACE" && set -x

while test "$1"; do

    test "$TEST" || PVLngPUT2CSV $GUID "@$1"

    shift
done

set +x

exit

##############################################################################
# USAGE >>

Load CSV files via API r2

Usage: $scriptname [options] GUID files
       $scriptname [options] GUID data/<GUID>/*.csv

Options:

    -t  Test mode, don't save to PVLng
        Sets verbosity to info level
    -v  Set verbosity level to info level
    -vv Set verbosity level to debug level
    -h  Show this help

File format, Semicolon separated lines of:

    <timestamp>;<value>
    <date time>;<value>
    <date>;<time>;<value>

# << USAGE
