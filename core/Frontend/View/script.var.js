/**
 * JS config variables
 *
 * @author      Knut Kohl <github@knutkohl.de>
 * @copyright   2012-2014 Knut Kohl
 * @license     MIT License (MIT) http://opensource.org/licenses/MIT
 */
var PVLngVersion = '{VERSION}',
    PVLngAPI     = '{APIURL}',
    PVLngAPIkey  = '{APIKEY}',

    /* Inititilize Pines Notify labels here with I18N */
    pnotify_defaults_labels_stick = '{{Stick}}',
    pnotify_defaults_labels_close = '{{Close}}',

    DecimalSeparator  = '{DSEP}',
    ThousandSeparator = '{TSEP}',

    CurrencyISO      = '{CURRENCY}',
    CurrencySymbol   = '{CURRENCYSYMBOL}',
    CurrencyDecimals = +'{CURRENCYDECIMALS}',
    CurrencyFormat   = '{CURRENCYFORMAT}',

    language = '{LANGUAGE}',

    /* May be empty on 1st start */
    latitude  = +'{raw:LATITUDE}',
    longitude = +'{raw:LONGITUDE}',

    /* Make boolean */
    debug       = !!+'{DEBUG}',
    development = !!+'{DEVELOPMENT}',
    user        = !!+'{USER}';
