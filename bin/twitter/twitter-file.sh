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

test -f $pwd/.tokens || error_exit "Missing token file! Did you run setup.sh?"

. $pwd/.pvlng
. $pwd/.tokens

while getopts "dtvxh" OPTION; do
	case "$OPTION" in
		d) DELETE=y ;;
		t) TEST=y; VERBOSE=$((VERBOSE + 1)) ;;
		v) VERBOSE=$((VERBOSE + 1)) ;;
		x) TRACE=y ;;
		h) usage; exit ;;
		?) usage; exit 1 ;;
	esac
done

shift $((OPTIND-1))

read_config "$1"

PATTERN_N=$(int "$PATTERN_N")
test $PATTERN_N -gt 0  || error_exit "No file patterns defined (\$PATTERN_N)"

##############################################################################
### Start
##############################################################################
test "$TRACE" && set -x

i=0

while test $i -lt $PATTERN_N; do

	i=$((i+1))

	log 1 "--- $i ---"

	eval PATTERN=\$PATTERN_$i
	log 1 "Pattern : $PATTERN"

	files="$(ls $PATTERN 2>/dev/null)"

	if test -z "$files"; then
		log 1 "No files."
		continue
	fi

	if test "$DELETE"; then
		log 1 "Delete: $files"
		test "$TEST" || rm $files
		continue
	fi

	for file in $files; do

		log 1 "--- $file ---"

		### Trim status
		STATUS=$(cat $file | sed -e 's~^ ~~' -e 's~ $~~')

		log 1 "Status  : $STATUS"
		log 1 "Length  : $(echo $STATUS | wc -c)"

		if test -z "$STATUS"; then
			continue
		fi

		if test -z "$TEST"; then
			$(dirname $0)/twitter.php \
			  --consumer_key=$CONSUMER_KEY \
			  --consumer_secret=$CONSUMER_SECRET \
			  --oauth_token=$OAUTH_TOKEN \
			  --oauth_secret=$OAUTH_TOKEN_SECRET \
			  --status="$STATUS" --location="$LAT_LON"

			eval move="\$FILE_${i}_MOVE"

			if test -z "$move"; then
				rm "$file"
			else
				test -d "$move" || mkdir -p "$move"
				mv "$file" "$move" 2>/dev/null
			fi
		fi
	done

done

set +x

exit

##############################################################################
# USAGE >>

Post status from file content to twitter

Usage: $scriptname [options] config_file

Options:
	-t   Test mode, don't post
	     Sets verbosity to info level
	-v   Set verbosity level to info level
	-vv  Set verbosity level to debug level
	-h   Show this help

See $pwd/twitter-file.conf.dist for details.

# << USAGE
