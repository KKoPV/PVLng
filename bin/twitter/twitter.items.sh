##############################################################################
### @author      Knut Kohl <github@knutkohl.de>
### @copyright   2012-2013 Knut Kohl
### @license     GNU General Public License http://www.gnu.org/licenses/gpl.txt
### @version     $Id$
##############################################################################

##############################################################################
### Actual power
##############################################################################
function twitter_power {
	url="$PVLngURL2/data/$1.tsv?period=last"
	value=$($curl $url | cut -f2)
	log 1 "$url => $value"
	echo $value
}

##############################################################################
### Average power over the last $1 minutes (e.g. 60 for last hour)
##############################################################################
function twitter_power_avg {
	url="$PVLngURL2/data/$2.tsv?start=-${1}minutes&period=${1}minutes"
	value=$($curl $url | cut -f2)
	log 1 "$url => $value"
	echo $value
}

##############################################################################
### Max. power of today
##############################################################################
function twitter_power_max {
	url="$PVLngURL2/data/$1.tsv"
	$curl $url >$TMPFILE

	max=0
	while read line; do
		value=$(echo "$line" | cut -f2)
		test $value -gt $max && max=$value
	done <$TMPFILE

	log 1 "$url => $max"
	echo $max
}

##############################################################################
### Production today in kWh
##############################################################################
function twitter_today {
	url="$PVLngURL2/data/$1.tsv?period=last"
	value=$($curl $url | cut -f2)
	log 1 "$url => $value"
	echo $value
}

##############################################################################
### Overall production in MWh
##############################################################################
function twitter_overall {
	url="$PVLngURL2/data/$1.tsv?start=0&period=99y"
	value=$($curl $url | cut -f2)
	log 1 "$url => $value"
	echo $value
}

##############################################################################
### Today working hours in hours :-)
##############################################################################
function twitter_today_working_hours {
	url="$PVLngURL2/data/$1.tsv"
	$curl $url >$TMPFILE

	### get first line, get 1st value
	min=$(cat $TMPFILE | head -n1 | cut -f1)
	### get last line, get 1st value
	max=$(cat $TMPFILE | tail -n1 | cut -f1)
	log 1 "$url => $min - $max"

	### to hours
	echo "scale=3; ($max - $min) / 3600" | bc -l
}
