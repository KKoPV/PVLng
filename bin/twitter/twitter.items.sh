##############################################################################
### @author      Knut Kohl <github@knutkohl.de>
### @copyright   2012-2013 Knut Kohl
### @license     GNU General Public License http://www.gnu.org/licenses/gpl.txt
### @version     1.1.0
###
### 1.1.0
### Adjust functions, make more variable
###
### 1.0.0
### Initial creation
##############################################################################

##############################################################################
twitter_last_help='Actual/last value'
##############################################################################
function twitter_last {
    value=$(PVLngGET2 "data/$1.tsv?period=last" | cut -f2)
    log 1 "$url => $value"
    echo $value
}

##############################################################################
twitter_average_help='Average value since $1'
### $1 - Start time
### $2 - GUID
### Example params: midnight 24hours
### Start at today midnight and aggregate 24 hours > 1 row as result
##############################################################################
function twitter_average {
    value=$(PVLngGET2 "data/$2.tsv?start=$1&period=99y" | cut -f2)
    log 1 "$url => $value"
    echo $value
}

##############################################################################
twitter_maximum_help='Maximum value since $1'
### $1 - Start time
### $2 - GUID
### Example params: midnight | first%20day%20of%20this%20month
### Start at today midnight  | 1st of this month
##############################################################################
function twitter_maximum {
    ### Get all data rows
    PVLngGET2 "data/$2.tsv?start=$1" >$TMPFILE

    ### Loop all rows and find max. value
    imax=0; vmax=0
    while read line; do
        value=$(echo "$line" | cut -f2)
        ### Cut of decimals for testing
        int=$(printf "%.0f" $value)
        if test $int -gt $imax; then
            imax=$int
            vmax=$value
        fi
    done <$TMPFILE

    log 1 "$url => $vmax"
    echo $vmax
}

##############################################################################
twitter_production_help='Production in kWh since $1'
### $1 - Start time
### $2 - GUID
### Example params: midnight | first%20day%20of%20this%20month
### Start at today midnight  | 1st of this month
##############################################################################
function twitter_production {
    value=$(PVLngGET2 "data/$2.tsv?start=$1&period=last" | cut -f2)
    log 1 "$url => $value"
    echo $value
}

##############################################################################
twitter_overall_help='Overall production in MWh'
##############################################################################
function twitter_overall {
    value=$(PVLngGET2 "data/$1.tsv?period=readlast" | cut -f2)
    log 1 "$url => $value"
    echo $value
}

##############################################################################
twitter_today_working_hours_help='Today working hours in hours :-)'
##############################################################################
function twitter_today_working_hours {
    ### Get all data rows
    PVLngGET2 "data/$1.tsv" >$TMPFILE

    ### get first line, get 1st value
    min=$(cat $TMPFILE | head -n1 | cut -f1)
    ### get last line, get 1st value
    max=$(cat $TMPFILE | tail -n1 | cut -f1)
    log 1 "$url => $min - $max"

    ### to hours
    echo "scale=3; ($max - $min) / 3600" | bc -l
}
