/**
 * Helper functions for charting
 *
 * @author     Knut Kohl <github@knutkohl.de>
 * @copyright  2012-2015 Knut Kohl
 * @license    MIT License (MIT) http://opensource.org/licenses/MIT
 * @version    1.1.0
 *
 * 1.1.0
 * - Since HighCharts 4.1.1 have labels by default a text shadow,
 *   disable for min/max/last labels
 *
 * 1.0.0
 * - Initial creation
 */

/**
 *
 */
var presentation_defaults = {
    /* Chart hint decimals */
    HintDecimals: 2,

    line: {
        /* Line type */
        type: 'spline',
        /* Line width */
        width: 2,
        /* Line color */
        color: '#404040'
    }
};

/**
 *
 */
function presentation( data ) {
    /* Set defaults */
    this.v              = 3;
    this.axis           = 1;
    this.type           = presentation_defaults.line.type;
    this.style          = 'Solid';
    this.width          = presentation_defaults.line.width;
    this.color          = presentation_defaults.line.color;
    /* Removed in v2
    this.coloruseneg = false;
    this.colorneg = presentation_defaults.line.color;
    */
    /* Added in v2 */
    this.colorusediff   = 0;
    this.colordiff      = presentation_defaults.line.color;
    /* --- */
    this.consumption    = false;
    this.threshold      = 0;
    this.min            = false;
    this.max            = false;
    this.last           = false;
    this.all            = false;
    this.time1          = '00:00';
    this.time2          = '24:00';
    this.daylight       = false;
    this.daylight_grace = 0;
    this.legend         = true;
    this.position       = 0;
    this.hidden         = false;
    this.outline        = false;
    this.stack          = '';
    /* Removed in v3 */
    /*
    this.decimals       = '';
    */

    try {
        data = JSON.parse(data);
        if (typeof data.v == 'undefined') {
            /* prior v2 */
            if (data.coloruseneg === '1') {
                data.colorusediff = -1;
            } else if (data.coloruseneg === '0') {
                data.colorusediff = 0;
            }
            data.colordiff = data.colorneg;
        } else if (data.v == 3) {
            delete data.decimals;
        }
        delete data.coloruseneg;
        delete data.colorneg;
        $.extend(this, data);
    } catch(e) {}

    this.toString = function() { return JSON.stringify(this) }
}

/**
 *
 */
function setMinMax( serie, channel ) {

    if (serie.data.length == 0) return serie;

    var ts  = { min: serie.data[0].x, max: serie.data[serie.data.length-1].x },
        min = { id: null, x: null, y:  Number.MAX_VALUE },
        max = { id: null, x: null, y: -Number.MAX_VALUE },
        /* Use negativeColor as "original" color if serie shows data ABOVE threshold different */
        color = (serie.colorDiff == 1) ? serie.negativeColor : serie.color;

    /* Search min. and max. values */
    $.each(serie.data, function(i, point) {
        if (channel.min && (point.y < min.y)) {
            min = { id: i, x: point.x, y: point.y };
        }
        if (channel.max && (point.y > max.y)) {
            max = { id: i, x: point.x, y: point.y };
        }
    });

    if (min.id != null) {

        serie.data[min.id].marker = {
            enabled: true,
            symbol: 'triangle-down',
            fillColor: color
        };
        serie.data[min.id].dataLabels = {
            enabled: true,
            formatter: function() {
                return Highcharts.numberFormat(+this.y, serie.decimals)
            },
            color: color,
            style: { fontWeight: 'bold', textShadow: null },
            borderRadius: 3,
            backgroundColor: 'rgba(252, 255, 197, 0.7)',
            borderWidth: 1,
            borderColor: '#AAA',
            align: ((ts.min + (ts.max - ts.min)/2 - min.x) > 0) ? 'left' : 'right',
            y: 26
        };

        $('#min'+serie.id).html(Highcharts.numberFormat(min.y, serie.decimals) + ' ' + serie.unit);
    }

    if (max.id != null) {

        serie.data[max.id].marker = {
            enabled: true,
            symbol: 'triangle',
            fillColor: color
        };
        serie.data[max.id].dataLabels = {
            enabled: true,
            formatter: function() {
                return Highcharts.numberFormat(+this.y, serie.decimals)
            },
            color: color,
            style: { fontWeight: 'bold', textShadow: null },
            borderRadius: 3,
            backgroundColor: 'rgba(252, 255, 197, 0.7)',
            borderWidth: 1,
            borderColor: '#AAA',
            align: ((ts.min + (ts.max - ts.min)/2 - max.x) > 0) ? 'left' : 'right',
            y: -7
        };

        $('#max'+serie.id).html(Highcharts.numberFormat(max.y, serie.decimals) + ' ' + serie.unit);
    }

    var last = serie.data.length-1;

    /* Only if not still marked as min or max */
    if (channel.last && (last != max.id) && (last != min.id)) {

        serie.data[last].marker = {
            enabled: true,
            symbol: 'circle',
            fillColor: color
        };
        serie.data[last].dataLabels = {
            enabled: true,
            formatter: function() {
                return Highcharts.numberFormat(+this.y, serie.decimals)
            },
            color: color,
            style: { fontWeight: 'bold', textShadow: null },
            borderRadius: 3,
            backgroundColor: 'rgba(252, 255, 197, 0.7)',
            borderWidth: 1,
            borderColor: '#AAA',
            align: 'right',
            overflow: true,
            crop: false,
            y: -7
        };
    }

    return serie;
}
