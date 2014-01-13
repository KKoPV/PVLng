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
    /* set defaults */
    this.axis = 1;
    this.type = defaults.line.type;
    this.consumption = false;
    this.style = 'Solid';
    this.width = defaults.line.width;
    this.color = defaults.line.color;
    this.coloruseneg = false;
    this.colorneg = defaults.line.color;
    this.threshold = 0;
    this.min = false;
    this.max = false;
    this.last = false;

    try { $.extend(this, JSON.parse(data));    } catch(e) {}

    this.toString = function() {
        return JSON.stringify(this);
    }
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
        max = { id: null, x: null, y: -Number.MAX_VALUE };

    var d, dataLabels = {
        enabled: true,
        formatter: function() {
            return Highcharts.numberFormat(+this.y, serie.decimals)
        },
        color: serie.color,
        style: { fontWeight: 'bold' },
        borderRadius: 3,
        backgroundColor: 'rgba(252, 255, 197, 0.7)',
        borderWidth: 1,
        borderColor: '#AAA'
    };

    /* search min. and max. values */
    $.each(serie.data, function(i, point) {
        ts.min = Math.min(ts.min, point[0]);
        if (channel.min && (point[1] < min.y)) min = { id: i, x: point[0], y: point[1] };
        ts.max = Math.max(ts.max, point[0]);
        if (channel.max && (point[1] > max.y)) max = { id: i, x: point[0], y: point[1] };
    });

    if (min.id != null) {

        d = $.extend({}, dataLabels, {
            align: ((ts.min + (ts.max - ts.min)/2 - min.x) > 0) ? 'left' : 'right',
            y: 26
        });

        serie.data[min.id] = {
            marker: {
                enabled: true,
                symbol: 'triangle',
                fillColor: serie.color
            },
            dataLabels: d,
            x: min.x,
            y: min.y
        };

        $('#min'+serie.id).html(Highcharts.numberFormat(min.y, serie.decimals) + ' ' + serie.unit);
    }

    if (max.id != null) {

        d = $.extend({}, dataLabels, {
            align: ((ts.min + (ts.max - ts.min)/2 - max.x) > 0) ? 'left' : 'right',
            y: -7
        });

        serie.data[max.id] = {
            marker: {
                enabled: true,
                symbol: 'triangle-down',
                fillColor: serie.color
            },
            dataLabels: d,
            x: max.x,
            y: max.y
        };

        $('#max'+serie.id).html(Highcharts.numberFormat(max.y, serie.decimals) + ' ' + serie.unit);
    }

    var last = serie.data.length-1;

    if (channel.last && (last >= 0)) {

        d = $.extend({}, dataLabels, { align: 'left' });

        serie.data[last] = {
            marker: {
                enabled: true,
                symbol: 'circle',
                fillColor: serie.color
            },
            dataLabels: d,
            x: serie.data[last][0],
            y: serie.data[last][1]
        };

    }

    return serie;
}
