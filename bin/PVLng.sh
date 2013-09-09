##############################################################################
### @author      Knut Kohl <github@knutkohl.de>
### @copyright   2012-2013 Knut Kohl
### @license     GNU General Public License http://www.gnu.org/licenses/gpl.txt
### @version     $Id$
##############################################################################

PVLngURL1="$PVLngHost/api/r1"
PVLngURL2="$PVLngHost/api/r2"

CURL="$(which curl)"
test -z "$CURL" && echo "Missing curl executable!" && exit 1

CURL="$CURL $CURLCONNECT"

### Create temp. file e.g. for curl --output
TMPFILE=$(mktemp /tmp/pvlng.XXXXXX)

### Define some variables
scriptname=${0##*/}
pwd=$(dirname $0)

TEST=
VERBOSE=0
TRACE=

##############################################################################
### show message depending of verbosity level on stderr
##############################################################################
function log {
	test $VERBOSE -ge $1 || return

	shift

	{	### Detect if $1 is a "@filename"
		if test "${1:0:1}" == '@'; then
			echo $(date +"[%d-%b %H:%M:%S]") File: ${1:1}
			cat ${1:1}
		else
			echo -e $(date +"[%d-%b %H:%M:%S]") "$*"
		fi
	} >&2
}

##############################################################################
### show usage
### requires a section of text enclosed by
### # USAGE >>
### ...
### # << USAGE
##############################################################################
function usage {
	s=$(cat "$0" | \
	    awk '{if($0~/^#+ +USAGE +>+/){while(getline>0){if($0~/^#+ *<+ *USAGE/)exit;print $0}}}')
	eval s="$(echo \""$s"\")"
	echo "$s" >&2
}

##############################################################################
### read config file
##############################################################################
function read_config {
	test "$1"    || error_exit 'Missing config file!'
	test -r "$1" || error_exit 'Configuration file is not readable!'

	log 2 "--- $1 ---"

	while read var value; do
		test -n "$var" -a "${var:0:1}" != '#' || continue
		value=$(echo -e "$value" | sed -e 's/^"[ \t]*//g' -e 's/[ \t]*"$//g')
		log 2 "$(printf '%-20s = %s' $var "$value")"
		eval "$var=\$value"
	done <"$1"
}

##############################################################################
### analyse paramter $1 as boolean
##############################################################################
function bool {
	case $(echo "$1" | tr '[A-Z]' '[a-z]') in
		1|x|on|yes|true) echo 1 ;;
		*)               echo 0 ;;
	esac
}

##############################################################################
### force paramter $1 as integer
##############################################################################
function int {
	test -n "$1" && t=$(expr "$1" \* 1 2>/dev/null)
	test -z "$t" && echo 0 || echo $t
}

##############################################################################
### build md5sum of file
##############################################################################
function hash {
	md5sum "$1" | cut -d' ' -f1
}

##############################################################################
### build run file name
##############################################################################
function run_file {
	echo $pwd/../../run/$1.$(echo $(basename "$2") | sed -e 's~[.].*$~~g' -e 's~[^A-Za-z0-9-]~_~g').$3
}

##############################################################################
function curl_cmd {
	v=$(int "$VERBOSE")
	test $v -le 2 && cmd="$CURL --silent" || cmd="$CURL --verbose"
	echo $cmd $CurlOpts
}

##############################################################################
### Quote data for JSON requests
### $1 = data string
##############################################################################
function JSON_quote {
	### Quote " to \\"
	echo "$1" | sed -e 's~"~\\"~g'
}

##############################################################################
### Save a log message to PVLng
### $1 = scope
### $2 = message
##############################################################################
function save_log {

	local scope=$(JSON_quote "$1")
	local message=

	### detect @filename or "normal string" to post
	if test "${2:0:1}" == '@'; then
		message=$(JSON_quote "$(<${2:1})")
	else
		message=$(JSON_quote "$2")
	fi

	log 1 "Scope   : $scope"
	log 1 "Message : $message"

	$(curl_cmd) --request PUT \
                --header "X-PVLng-key: $PVLngAPIkey" \
                --header "Content-Type: application/json" \
                --data "{\"scope\":\"$scope\",\"message\":\"$message\"}" \
                $PVLngHost/api/r2/log >/dev/null
}

##############################################################################
### Save data to PVLng
### $1 = GUID
### $2 = date
##############################################################################
function PVLngPUT1 {

	log 2 "GUID	 : $1"
	log 2 "Data	 : $2"

	local data=

	test "${2:0:1}" != "@" && data="data=\"$2\"" || data="data$2"

	cmd=$(curl_cmd)

	rc=$($cmd --header "X-PVLng-key: $PVLngAPIkey" --request PUT \
						--write-out %{http_code} --output $TMPFILE \
						--data-urlencode $data $PVLngURL1/$1)

	if echo "$rc" | grep -qe '^20[012]'; then
		### 200/201/202 ok
		log 1 HTTP code : $rc
		log 1 "$(cat $TMPFILE)"
	else
		### errors
		log -1 HTTP code : $rc
		log -1 "$(cat $TMPFILE)"
		save_log "$1" @$TMPFILE
	fi

}

##############################################################################
### Save data to PVLng
### $1 = GUID
### $2 = value or @file_name with JSON data
##############################################################################
function PVLngPUT2 {

	local GUID="$1"
	local raw="$2"
	local data="$2"

	log 2 "GUID	 : $GUID"
	log 2 "Data	 : $data"

	if test "${data:0:1}" != "@"; then
	    ### No file
		data="{\"data\":\"$(JSON_quote "$data")\"}"
	fi

	log 2 "Send	 : $data"

	### Clear temp. file before
	rm $TMPFILE >/dev/null 2>&1

	set $($(curl_cmd) --request PUT \
	                  --header "X-PVLng-key: $PVLngAPIkey" \
	                  --header "Content-Type: application/json" \
	                  --write-out %{http_code} \
	                  --output $TMPFILE \
	                  --data-binary $data \
	                  $PVLngURL2/data/$GUID.txt)

	if echo "$1" | grep -qe '^20[012]'; then
		### 200/201/202 ok
		log 1 "HTTP code : $1"
		test -f $TMPFILE && log 2 @$TMPFILE
	else
		### errors
		log -1 "HTTP code : $1"
		test -f $TMPFILE && log -1 @$TMPFILE
		save_log "$GUID" "raw: $raw"
		save_log "$GUID" @$TMPFILE
	fi

}

##############################################################################
### trap function to clean up
##############################################################################
function clean_up {
	### Clean up on program exit, accepts an exit status
	rm -f "$TMPFILE" >/dev/null 2>&1
	exit $1
}

##############################################################################
### exit with error message and return code 1
##############################################################################
function error_exit {
	### Display error message and exit
	echo
	echo "$scriptname: ${1:-"Unknown Error"}" 1>&2
	echo
	clean_up 1
}

trap clean_up 0
