/**
 *
 */
var defaults = {
    /* Chart hint decimals */
    HintDecimals: 2,

    line: {
        /* line type */
        type: 'spline',
        /* line width */
        width: 2,
        /* line color */
        color: '#404040'
    }
};

/**
 *
 */
function presentation( data ) {
    /* Set defaults */
    this.v = 2;
    this.axis = 1;
    this.type = defaults.line.type;
    this.consumption = false;
    this.style = 'Solid';
    this.width = defaults.line.width;
    this.color = defaults.line.color;
    /* Removed in v2
    this.coloruseneg = false;
    this.colorneg = defaults.line.color;
    */
    /* Added in v2 */
    this.colorusediff = 0;
    this.colordiff = defaults.line.color;
    /* --- */
    this.threshold = 0;
    this.min = false;
    this.max = false;
    this.last = false;
    this.all = false;
    this.time1 = '00:00';
    this.time2 = '24:00';
    this.legend = true;
    this.position = 0;

    try {
        data = JSON.parse(data);
        if (typeof data.v == 'undefined') {
            /* prior v3.0.0 */
            if (data.coloruseneg === '1') {
                data.colorusediff = -1;
            } else if (data.coloruseneg === '0') {
                data.colorusediff = 0;
            }
            data.colordiff = data.colorneg;
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
function setExtremes() {

    var i, e, extremes=[], p, pos=[], min=100, max=-100;

    for (i=0; i<chart.yAxis.length; i++) {

        /* Reset extremes */
        chart.yAxis[i].setExtremes(null, null);

        /* Get extremes */
        e = chart.yAxis[i].getExtremes();

        /* Calc rel. position of 0 value; 0 - top, 1 - bottom*/
        p = e.max / (e.max-e.min);

        /* Remember min/max positions */
        min = Math.min(min, p);
        max = Math.max(max, p);

        /* Remember extremes and positions */
        extremes.push( { min: e.min, max: e.max, height: e.max-e.min } );
        pos.push(p);
    }

    /* Average to align to */
    var center = (max+min)/2;

    for (i=0; i<chart.yAxis.length; i++) {

        if (max <= 1) {
            /* With neg. values */
            if (pos[i] < center) {
                /* Add offset on top */
                extremes[i].max += (center-pos[i])*2 * extremes[i].height;
            } else {
                /* Add offset at bottom */
                extremes[i].min -= (pos[i]-center)*2 * extremes[i].height;
            }
        } else if (min >= 1) {
            /* Add offset at bottom */
            extremes[i].min -= (pos[i]-min) * extremes[i].height;
        } else {
            /* ??? */
        }

        extremes[i].min = Math.round(extremes[i].min * 100) / 100;
        extremes[i].max = Math.round(extremes[i].max * 100) / 100;

        /* Add 5% min padding only for "real" neg. data */
        if (extremes[i].min < -0.001) {
            extremes[i].min -= (extremes[i].max-extremes[i].min) * 0.05;
        }

        /* Add 5% max padding only for "real" pos. data */
        if (extremes[i].max > 0.001) {
            extremes[i].max += (extremes[i].max-extremes[i].min) * 0.05;
        }

        chart.yAxis[i].setExtremes(extremes[i].min, extremes[i].max, false);
    }

    chart.redraw();
}

/**
 *
 */
function setMinMax( serie, channel ) {

    var
        ts  = { min: Number.MAX_VALUE, max: -Number.MAX_VALUE },
        min = { id: null, x: null, y:  Number.MAX_VALUE },
        max = { id: null, x: null, y: -Number.MAX_VALUE },
        /* Use negativeColor as "original" color if serie shows data above threshold different */
        color = (serie.colorDiff == 1) ? serie.negativeColor : serie.color;

    /* search min. and max. values */
    $.each(serie.data, function(i, point) {
        ts.min = Math.min(ts.min, point.x);
        if (channel.min && (point.y < min.y)) min = { id: i, x: point.x, y: point.y };
        ts.max = Math.max(ts.max, point.x);
        if (channel.max && (point.y > max.y)) max = { id: i, x: point.x, y: point.y };
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
            style: { fontWeight: 'bold' },
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
            style: { fontWeight: 'bold' },
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
    if (channel.last && (last != max.id) && (last != min.id) && (last >= 0)) {

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
            style: { fontWeight: 'bold' },
            borderRadius: 3,
            backgroundColor: 'rgba(252, 255, 197, 0.7)',
            borderWidth: 1,
            borderColor: '#AAA',
            align: 'left',
            overflow: true,
            crop: false,
            y: -7
        };
    }

    return serie;
}
