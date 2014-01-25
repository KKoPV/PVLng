##############################################################################
### Process the request
###
### $1 - Request
### $2 - DataCollection
##############################################################################
function requestComCard {

    url="$APIURL/$1.cgi?Scope=Device&DeviceId=$DEVICEID&DataCollection=$2"
    log 2 "$url"

    $curl --output $RESPONSEFILE $url
    rc=$?

    if test $rc -ne 0; then
        curl_error_exit $rc "$1/$2"
    fi

    ### Test mode
    log 2 "$1 response:"
    log 2 @$RESPONSEFILE

    ### Save data
    test "$TEST" || PVLngPUT2 $GUID @$RESPONSEFILE

}
