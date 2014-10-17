/**
 *
 *
 * @author      Knut Kohl <github@knutkohl.de>
 * @copyright   2012-2014 Knut Kohl
 * @license     MIT License (MIT) http://opensource.org/licenses/MIT
 * @version     1.0.0
 */
var PVLngVersion = '{VERSION}',
    PVLngAPI = 'http://{SERVERNAME}/api/latest/',
    PVLngAPIkey = '{APIKEY}',

    /* Inititilize Pines Notify labels here with I18N */
    pnotify_defaults_labels_stick = '{{Stick}}',
    pnotify_defaults_labels_close = '{{Close}}',

    DecimalSeparator = '{DSEP}',
    ThousandSeparator = '{TSEP}',

    CurrencyISO = '{CURRENCY}',
    CurrencySymbol = '{CURRENCYSYMBOL}',
    CurrencyDecimals = '{CURRENCYDECIMALS}',
    CurrencyFormat = '{CURRENCYFORMAT}',

    language  = '{LANGUAGE}',
    latitude  = +'{raw:LATITUDE}', /* Handles empty strings */
    longitude = +'{raw:LONGITUDE}',

    verbose = '{VERBOSE}',
    user = '{USER}';

if (user) {
    $(function($) {
        $.ajaxSetup({
            beforeSend: function setHeader(XHR) { XHR.setRequestHeader('X-PVLng-Key', PVLngAPIkey) }
        });
    });
}
