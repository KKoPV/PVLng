<script>
/**
 *
 *
 * @author      Knut Kohl <github@knutkohl.de>
 * @copyright   2012-2014 Knut Kohl
 * @license     MIT License (MIT) http://opensource.org/licenses/MIT
 * @version     1.0.0
 */

var Period      = '{MOBILE_PERIOD}',
    ChartHeight = '{MOBILE_CHARTHEIGHT}',

    views = {
        <!-- BEGIN VIEWS -->
        '{NAME}': { period: '{PERIOD}', data: '{DATA}' },
        <!-- END -->
    },

    options = {
        chart: {
            renderTo: 'chart',
            height: ChartHeight,
            spacingLeft: 5,
            spacingRight: 5,
            spacingTop: 5,
            spacingBottom: 5,
            alignTicks: false
        },
        credits: { enabled: false },
        title: { text: '' },
        plotOptions: {
            line:   { marker: { enabled: false } },
            spline: { marker: { enabled: false } },
            areaspline: {
                marker: { enabled: false },
                shadow: false,
                fillOpacity: 0.2
            },
            areasplinerange: {
                marker: { enabled: false },
                shadow: false,
                fillOpacity: 0.2
            },
            bar: {
                groupPadding: 0.1
            }
        },
        xAxis :  { type: 'datetime' },
        legend:  { enabled: false },
        tooltip: { enabled: false },
        loading: {
            hideDuration: 0,
            showDuration: 0,
            labelStyle: { top: '40%', fontSize: '150%', color: 'black' }
        }
    },

    lock = false,
    channels = [],
    chart;

/**
 *
 */
function updateChart() {

    if (lock) return;
    lock = true;

    var view = $('#page-home').data('view'), period = views[view].period;

    $('#view').html(view);

    view = views[view].data;

    try {
        view = JSON.parse(view);
    } catch (e) {
        lock = false;
        return;
    }

    if (view == '') {
        lock = false;
        return;
    }

    var loading = view.length;
    chart.showLoading('- ' + loading + ' -');

    $('#table-cons tbody tr').remove();

    var channels_new = [], yAxisMap = [], yAxis = [],
        channel, buffer = [], channel_clone;

    /* Find active channels, map and sort axis */
    $(view).each(function(id, view) {
        /* Ignore private channels */
        if (!view.public) return;
        channel = new presentation(view.presentation);
        channel.id   = view.id;
        channel.guid = view.guid;
        channel.unit = view.unit;
        /* Remember channel */
        buffer.push(channel);
        /* Channel axis still registered? */
        if (yAxisMap.indexOf(channel.axis) == -1) yAxisMap.push(channel.axis);
    });

    /* Sort channels */
    buffer.sort(function(a, b) {
        return (a.position - b.position) /* Causes an array to be sorted numerically and ascending */
    });

    /* Sort axis to make correct order for Highcharts */
    yAxisMap.sort();

    /* Build channels */
    $(buffer).each(function(id, channel) {
        /* Axis on right side */
        var is_right = !(channel.axis % 2);

        /* Axis from chart point of view */
        channel.axis = yAxisMap.indexOf(channel.axis);

        if (channel.type == 'areasplinerange') {
            channel.type = 'spline';
        }
        channels_new.push(channel);

        /* Prepare axis */
        if (!yAxis[channel.axis]) {
            yAxis[channel.axis] = {
                title: null, /*{ text: channel.unit },*/
                lineColor: channel.color,
                showEmpty: false,
                minPadding: 0,
                maxPadding: 0,
                opposite: is_right
            };
            /* Only 1st left axis shows grid lines */
            if (channel.axis != 0) yAxis[channel.axis].gridLineWidth = 0;
        }
    });

    if (yAxis.length > 1) {
        /* Hide axes & labels when more than 1 axis is defined */
        $.each(yAxis, function(i) {
            /* yAxis[i].title = false; */
            yAxis[i].labels = { enabled: false };
            yAxis[i].lineWidth = 0;
        });
    }

    _log('Channels:', channels_new);
    _log('yAxis:', yAxis);

    /* Check for changed channels */
    var changed = false;

    if (channels_new.length != channels.length) {
        changed = true;
        channels = channels_new;
    } else {
        for (var i=0, l=channels_new.length; i<l; i++) {
            if (JSON.stringify(channels_new[i]) != JSON.stringify(channels[i])) {
                changed = true;
                channels = channels_new;
            }
        }
    }

    if (changed) {
        /* Happens also on 1st call! */
        options.yAxis = yAxis;
        /* (Re)Create chart */
        chart = new Highcharts.Chart(options);
    }

    var series = [], costs = 0;
    $('#table-cons').hide();

    /* Get data */
    $(channels).each(function(id, channel) {

        var url = PVLngAPI + 'data/' + channel.guid + '.json';
        _log('Fetch: ' + url);

        $.getJSON(
            /* Fetch channel data with attributes */
            url,
            {
                attributes: true,
                full:       true,
                short:      true,
                period:     (channel.type != 'scatter') ? period : '',
                _ts:        (new Date).getTime() /* force reload */
            },
            function(data) {
                var attr = data.shift(), t;
/*
                _log('Attributes:', attr);
                _log('Data:', data);
*/
                var serie = {     /* A trick to HTML-decode channel name */
                        name:     $('<div/>').html(attr.name).text(),
                        id:       channel.id,
                        decimals: attr.decimals,
                        unit:     attr.unit,
                        color:    channel.color,
                        type:     channel.type,
                        yAxis:    channel.axis,
                        data:     []
                    },
                    tr, td;

                $(data).each(function(id, row) {
                    var point = { x: Math.round(row[1] / 60) * 60 * 1000 };
                    if ($.isNumeric(row[2])) {
                        if (channel.type == 'areasplinerange') {
                            point.low  = row[3];
                            point.high = row[4];
                        } else {
                            point.y = row[2];
                        }
                    } else {
                        point.y = 0;
                        point.name = row[2];
                    }
                    serie.data.push(point);
                });

                if (attr.consumption) {
                    tr = $('<tr/>');
                    t = (attr.description) ? ' (' + attr.description + ')' : '';
                    $('<th/>').html(attr.name + t).appendTo(tr);
                    $('<td/>')
                        .addClass('r')
                        .html(Highcharts.numberFormat(attr.consumption, attr.decimals) + ' ' + attr.unit)
                        .appendTo(tr);

                    td = $('<td/>');
                    if (attr.costs) {
                        costs += +attr.costs.toFixed(2);
                        td.addClass('cost')
                          .html(Highcharts.numberFormat(attr.costs, 2))
                          .appendTo(tr);
                    }
                    td.appendTo(tr);
                    tr.appendTo('#table-cons tbody');
                }

                if (channel.linkedTo != undefined) serie.linkedTo = channel.linkedTo;

                if (channel.type == 'scatter') {
                    serie.dataLabels = {
                        enabled: true,
                        align: 'left',
                        rotation: 270,
                        align: 'left',
                        x: 4,
                        y: -7,
                        formatter: function() { return this.point.name }
                    };
                } else if (channel.type != 'bar') {
                    if (channel.style != 'Solid') serie.dashStyle = channel.style;
                    serie.lineWidth = channel.width;
                }

                if (channel.type != 'areasplinerange' && (channel.min || channel.max || channel.last)) {
                    serie = setMinMax(serie, channel);
                }

                _log('Serie: ', serie);

                series[id] = serie;
            }
        ).always(function() {
            /* Force redraw */
            chart.hideLoading();
            if (--loading > 0) chart.showLoading('- ' + (loading) + ' -');

            /* Check real count of elements in series array! */
            var completed = series.filter(function(a){ return a !== undefined }).length;
            _log(completed + ' completed');

            /* Check if all getJSON() calls finished, exit if not */
            if (completed !== channels.length) return;

            if (changed) {
                /* Remove all existing series */
                for (var i=chart.series.length-1; i>=0; i--) chart.series[i].remove();
                /* Add new series */
                $.each(series, function(i, serie) { chart.addSeries(serie, false) });
            } else {
                /* Replace series data */
                $.each(series, function(i, serie) { chart.series[i].setData(serie.data, false) });
            }

            chart.redraw();

            if (costs) {
                $('<tr/>').append(
                    $('<td/>')
                        .attr('colspan', 3)
                        .addClass('costs')
                        .html(Highcharts.numberFormat(costs, 2))
                ).appendTo('#table-cons tbody');
            }
            $('#table-cons').toggle(!!$('#table-cons tbody').children().length);

            /* Release as last the redraw lock */
            lock = false;
        });
    });
}

/**
 *
 */
function updateWeather() {
    var chart = $('#weather-chart').html($('#weather-wait').clone().show()),
        container = $('#weather').empty(),
        temperatures = [],
        l = { en: 'EN', de: 'DL' }[language];

    $.ajax({
        url: 'http://api.wunderground.com/api/'+APIkey+'/hourly/forecast/lang:' + l +
             '/q/' + latitude + ',' + longitude + '.json',
        dataType: 'jsonp',
        success: function(response) {
            container.empty();
            $.each(response.hourly_forecast, function(id, data) {
                if (data.FCTTIME.hour == 0) {
                    $('<div/>')
                    .html($('<span/>').html(data.FCTTIME.weekday_name_abbrev.split('').join('<br />')))
                    .addClass('mark')
                    .appendTo(container);
                }
                var div = $('<div/>')
                          .append($('<img/>').prop('src', 'http://icons.wxug.com/i/c/h/'+data.icon+'.gif'))
                          .append('<br />' + data.FCTTIME.hour + '<small>:'+data.FCTTIME.min+'</small>');
                container.append(div);
                /* Mark each 6 hours (6, 12 18) */
                if (data.FCTTIME.hour > 0 && data.FCTTIME.hour % 6 == 0) div.addClass('mark');
                /* Make timestamp and temperature numeric */
                temperatures.push({ x: +data.FCTTIME.epoch*1000, y: +data.temp.metric });
            });
            $.each(response.forecast.simpleforecast.forecastday.slice(-2), function(id, data) {
                $('<div/>')
                .append($('<img/>').prop('src', 'http://icons.wxug.com/i/c/h/'+data.icon+'.gif'))
                .append('<br />' + data.date.weekday_short)
                .appendTo(container);
            });

            /* Mark temperature Min/Max value */
            ts  = { min: temperatures[0].x, max: temperatures[temperatures.length-1].x },
            min = { id: null, x: null, y:  100 },
            max = { id: null, x: null, y: -100 },

            $.each(temperatures, function(i, point) {
                if (point.y < min.y) min = { id: i, x: point.x, y: point.y };
                if (point.y > max.y) max = { id: i, x: point.x, y: point.y };
            });

            temperatures[min.id].marker     = { enabled: true };
            temperatures[max.id].marker     = { enabled: true };
            temperatures[min.id].dataLabels = { enabled: true };
            temperatures[max.id].dataLabels = { enabled: true };

            /* Create temperatures chart */
            chart.highcharts({
                chart: { type: 'spline', margin: [ 5, 5, 25, 5 ] },
                title: false,
                exporting: { enabled: false },
                credits: { enabled: false },
                /*tooltip: { enabled: false },*/
                legend: { enabled: false },
                plotOptions: { spline: { marker: { enabled: false } } },
                xAxis: [ { type: 'datetime', title: false } ],
                yAxis : [ { title: false, labels: { enabled: false } } ],
                series: [ { name: '{{Temperature}}', data: temperatures } ]
            });
        }
    });
}

var APIkey = '{WEATHER_APIKEY}';

/**
 *
 */
$(function() {

    Highcharts.setOptions({
        global: {
            useUTC: false,
            alignTicks: false
        },
        lang: {
            thousandsSep: '{TSEP}',
            decimalPoint: '{DSEP}',
            resetZoom: '{{resetZoom}}',
            resetZoomTitle: '{{resetZoomTitle}}'
        }
    });

    chart = new Highcharts.Chart(options);

    updateChart();

    $('#page-home').on('pageshow', function( event, ui ) {
        updateChart();
    });

    $('#btn-home').on('click', function(e) {
        e.preventDefault();
        for (var v in views) if (views.hasOwnProperty(v)) break;
        $('#page-home').data('view', v);
        updateChart();
    });

    $('#btn-chart-refresh').on('click', function(e) {
        e.preventDefault();
        updateChart();
    });

    if (!APIkey) {
        $('#btn-weather').hide();
    } else {
        $('#page-weather').on('pageshow', function( event, ui ) {
            updateWeather();
        });

        $('#btn-weather-refresh').on('click', function(e) {
            e.preventDefault();
            updateWeather();
        });

    }
});

</script>
