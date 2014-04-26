<script>
/**
 *
 *
 * @author      Knut Kohl <github@knutkohl.de>
 * @copyright   2012-2013 Knut Kohl
 * @license     GNU General Public License http://www.gnu.org/licenses/gpl.txt
 * @version     1.0.0
 */

/**
Chart canvas height
Width is 940px,
- Ratio  5 x  4 : 752
- Ratio  4 x  3 : 705
- Ratio 16 x 10 : 587
- Ratio 10 x  6 : 564
- Ratio 16 x  9 : 528
 */

var ChartHeight = 528;

/* refresh timeout in sec., set 0 for no automatic refresh */
var RefreshTimeout = 300;

/* ------------------------------------------------------------------------ */
</script>

<script src="/js/chart.js+jquery.treetable.js+spectrum.js"></script>
<!--
<script src="/js/palettes.js"></script>
-->

<!-- load Highcharts scripts direct from highcharts.com -- >
<script src="http://code.highcharts.com/highcharts.js"></script>
<script src="http://code.highcharts.com/highcharts-more.js"></script>
<script src="http://code.highcharts.com/modules/exporting.js"></script>
-->
<script src="/js/highcharts.js"></script>
<script src="/js/highcharts-more.js"></script>
<script src="/js/highcharts-exporting.js"></script>

<script>

var channels = {
    <!-- BEGIN DATA -->
    {ID}: { id: {ID}, guid: '{GUID}', name: '{NAME}', unit: '{UNIT}' },
    <!-- END -->
};

function Views() {
    this.views = {};
    this.actual = { slug: '', name: '', public: 0 };

    this.fetch = function( callback ) {
        this.views = {};
        var that = this;
        $.getJSON(
            PVLngAPI + '/views/'+language+'.json',
            { select: true },
            function(data) {
                $(data).each(function(id, view) {
                    that.views[view.slug] = view;
                });
            }
        ).always(function() {
            if (typeof callback != 'undefined') callback(that);
        });
    };

    this.buildSelect = function(el, selected) {
        var option, optgroups = [ [], [], [] ];

        el = $(el).empty();

        /* Build optgroups */
        $.each(this.views, function(id, view) {
            if (view.slug) {
                if (user) {
                    optgroups[view.public].push(view);
                } else if (view.public == 1) {
                    /* Only public views */
                    optgroups[1].push(view);
                }
            } else {
                /* --- Select --- append direct */
                $('<option/>').text(view.name).val('').appendTo(el);
            }
        });

        $.each(optgroups, function(id, optgroup) {
            if (optgroup.length == 0) return;

            var o = $('<optgroup/>');

            if (id == 0) {
                o.prop('label', '{{private}}');
            } else if (id == 1) {
                o.prop('label', '{{public}}');
            } else if (id == 2) {
                o.prop('label', '{{mobile}}');
            }

            $.each(optgroup, function(id, view) {
               $('<option/>').text(view.name).val(view.slug).appendTo(o);
            });

            o.appendTo(el);
        });

        el.val(selected);
    };

    this.load = function( slug, collapse, callback ) {
        if (typeof this.views[slug] == 'undefined') {
            $('#wrapper').show();
            return;
        }

        if (typeof collapse == 'undefined') collapse = false;

        $('#chart').addClass('wait');
        oTable.fnProcessingIndicator(true);

        var expanded = tree.expanded, preset;
        if (!expanded) tree.toggle(true);

        this.actual = this.views[slug];

        /* Uncheck all channels and ... */
        $('input.channel').iCheck('uncheck').val('');
        $('tr.channel').removeClass('checked');
        /* ... re-check all channels in view */
        $.each(JSON.parse(this.actual.data), function (id, p) {
            if (id == 'p') {
                preset = p;
            } else {
                $('#c'+id).val(p).iCheck('check');
            }
        });
        $('#modified').hide();

        /* Re-arrange channels in collapsed tree */
        if (collapse || !expanded) tree.toggle(false);

        $('#wrapper').show();
        $('#preset').val(preset).trigger('change');

        $('#loaddeleteview').val(this.actual.slug);
        $('#saveview').val(this.actual.name);
        $('#public').prop('checked', this.actual.public).iCheck('update');
        $('#visibility').val(this.actual.public);

        if (typeof callback != 'undefined') callback(this.actual);

        $('#chart').removeClass('wait');
        oTable.fnProcessingIndicator(false);
    };
}

/**
 *
 */
var
    qs = {}, views = {}, chart = false, channels_chart = [], updateTimeout,

    chartOptions = {
        chart: {
            renderTo: 'chart',
            paddingRight: 15,
            alignTicks: false,
            zoomType: 'x',
            resetZoomButton: { position: { x: 0, y: -50 } },
            events: {
                selection: function(event) { setTimeout(setExtremes, 100); }
            }
        },
        title: { text: '' },
        plotOptions: {
            series: {
                point: {
                    events: {
                        click: function() {
                            if (!PVLngAPIkey) return;
                            if (!this.series.userOptions.raw) {
                                alert('Can\'t delete from consolidated data.');
                                return;
                            }

                            $('#reading-serie').html(this.series.name);
                            $('#reading-timestamp').html((new Date(this.x)).toLocaleString());
                            $('#reading-value').html(this.y + ' ' + this.series.userOptions.unit);

                            $('#dialog-reading')
                                .data('guid', this.series.userOptions.guid)
                                .data('timestamp', (this.x/1000).toFixed(0))
                                .data('point', this)
                                .dialog('open');

                        }
                    }
                },
                turboThreshold: 0
            },
            line: { marker: { enabled: false } },
            spline: { marker: { enabled: false } },
            areaspline: {
                marker: { enabled: false },
                shadow: false,
                fillOpacity: 0.2,
            },
            areasplinerange: {
                marker: { enabled: false },
                shadow: false,
                fillOpacity: 0.2
            },
            bar: { groupPadding: 0.1 }
        },
        xAxis : { type: 'datetime' },
        tooltip: {
            useHTML: true,
            formatter: function() {
                var body = '';
                $.each(this.points, function(id, point) {
                    /* Show tooltip only for series with a unit */
                    if (point.series.tooltipOptions.valueSuffix == '') return;

                    if (point.point.low != undefined && point.point.high != undefined) {
                        var color = point.series.color;
                        var value = Highcharts.numberFormat(+point.point.low, point.series.options.decimals) + ' - ' +
                                    Highcharts.numberFormat(+point.point.high, point.series.options.decimals);
                    } else if (point.y != undefined) {
                        if (point.series.options.negativeColor && +point.y < point.series.options.threshold) {
                            var color = point.series.options.negativeColor;
                        } else {
                            var color = point.series.color;
                        }
                        var value = Highcharts.numberFormat(+point.y, point.series.options.decimals);
                    } else {
                        return;
                    }
                    var even = (id & 1) ? 'even' : ''; /* id starts by 0 */
                    body += '<tr style="color:' + color + '" class="' + even + '">'
                          + '<td class="name">' + point.series.name + '</td>'
                          + '<td class="value">' + value + '</td>'
                          + '<td class="unit">' + point.series.tooltipOptions.valueSuffix
                          + '</td></tr>';
                });
                return (body)
                     ? '<table id="chart-tooltip"><thead><tr><th colspan="3">' +
                       (new Date(this.x).toLocaleString()).replace(/:00$/, '') +
                       '</th></tr></thead><tbody>' + body + '</tbody></table>'
                     : null;
            },
            borderWidth: 0,
            shadow: true,
            crosshairs: true,
            shared: true
        },
        loading: {
            labelStyle: { top: '40%' },
            style: { opacity: 0.8 }
        }
    };

/**
 *
 */
function changeDates( dir ) {
    dir *= 24*60*60*1000;

    var from = new Date(Date.parse($('#from').datepicker('getDate')) + dir),
        to = new Date(Date.parse($('#to').datepicker('getDate')) + dir);

    if (to > new Date) {
        $.pnotify({ type: 'info', text: "Can't go beyond today." });
        return;
    }

    $('#from').datepicker('option', 'maxDate', to).datepicker('setDate', from);
    $('#to').datepicker('option', 'minDate', from).datepicker('setDate', to);

    updateChart();
}

/**
 *
 */
function Tree( expanded ) {

    this.expanded = expanded;

    this.toggle = function( force ) {
        this.expanded = (typeof force != 'undefined') ? force : !this.expanded;

        /* Redraw to react on this.expanded */
        oTable.fnDraw();

        $('span.indenter').toggle(this.expanded);
        if (this.expanded) {
            $('#treetoggle').prop('src','/images/ico/toggle.png').prop('alt','[-]');
            $('#tiptoggle').html('{{CollapseAll}} (F4)');
        } else {
            $('#treetoggle').prop('src','/images/ico/toggle_expand.png').prop('alt','[+]');
            $('#tiptoggle').html('{{ExpandAll}} (F4)');
        }
    }
}

tree = new Tree(!!user);

/**
 *
 */
function TimeStrToSec( str, _default ) {
    if (typeof str == 'undefined' || str == '') str = _default;
    /* Split into hours and minutes */
    var time = ((new String(str)).trim()+':0').split(':', 2);
    return time[0] * 3600 + time[1] * 60;
}

function SecToTimeStr( s ) {
    var h = Math.floor(s/60/60), m = Math.floor((s - h*60*60)/60);
    /* Make hours and minutes 2 characters long */
    return '0'.concat(h).slice(-2) + ':' + '0'.concat(m).slice(-2);
}

/**
 *
 */
function ChartDialog( id ) {
    /* get stringified settings */
    var p = $('#c'+id).val();
    if (p == 'on') {
        /* initial, no presetaion set yet */
        p = new presentation();
        /* suggest scatter for channels without unit */
        if (!channels[id].unit) p.type = 'scatter';
    } else {
        p = new presentation(p);
    }

    /* set dialog properties */
    /* find the radio button with the axis value and check it */
    $('input[name="d-axis"][value="' + p.axis + '"]').prop('checked', true);
    $('#d-type').val(p.type);
    $('#d-cons').prop('checked', p.consumption);
    /* find the radio button with the line width and check it */
    $('input[name="d-width"][value="' + p.width + '"]').prop('checked', true);
    $('#d-min').prop('checked', p.min);
    $('#d-max').prop('checked', p.max);
    $('#d-last').prop('checked', p.last);
    $('#d-all').prop('checked', p.all);
    $('#d-style').val(p.style);
    $('#d-color').val(p.color);
    $('#d-color').spectrum('set', p.color);
    $('#d-color-use-neg').prop('checked', p.coloruseneg);
    $('#d-color-neg').val(p.colorneg);
    $('#d-color-neg').spectrum('set', p.colorneg);
    $('#d-color-neg').spectrum($('#d-color-use-neg').is(':checked') ? 'enable' : 'disable');
    $('#d-threshold').val(p.threshold);
    $('#d-threshold').prop('disabled', !$('#d-color-use-neg').is(':checked'))
                     .spinner('option', 'disabled', !$('#d-color-use-neg').is(':checked'));
    $('#d-time1').val(p.time1);
    $('#d-time2').val(p.time2);
    $('#d-time-slider').slider('values', [ TimeStrToSec(p.time1), TimeStrToSec(p.time2) ]);

    $('input').iCheck('update');
    $('#d-type').trigger('change');

    /* Set the id into the dialog for onClose to write data back */
    $('#dialog-chart').data('id', id).dialog('option', 'title', channels[id].name).dialog('open');
}

/**
 *
 */
/**
 * Scale timestamps down to full minute, hour, day, week, month, quarter or year
 */
var xResolution = {
    i: 60,
    h: 60 * 60,
    d: 60 * 60 * 24,
    w: 60 * 60 * 24 * 7,
    m: 60 * 60 * 24 * 30,
    q: 60 * 60 * 24 * 90,
    y: 60 * 60 * 24 * 360
};

var windowVisible = true,
    browserPrefix = null,
    lastChanged = (new Date).getTime() / 1000 / 60;

/**
 *
 */
function updateChart( forceUpdate ) {

    clearTimeout(updateTimeout);
    updateTimeout = null;

    if (!windowVisible) return;

    var fromDate = $('#fromdate').val(), toDate = $('#todate').val();

    /* If any outstanding AJAX reqeust was killed, force rebuild of chart */
    if ($.ajaxQ.abortAll() != 0) forceUpdate = true;

    if (views.actual) {
        /* Provide permanent link only for logged in user and not embedded view level 2 */
        var from = $('#from').val(), to = $('#to').val(),
            date = (from == to)
                 ? 'date=' + fromDate
                 : 'from=' + fromDate + '&to=' + toDate;

        $('#btn-permanent').button({
            label: views.actual.name + ' ' + from + ' | {strip_tags:TITLE}',
            disabled: (views.actual.slug == '')
        }).prop('href', '/chart/' + views.actual.slug + encodeURI('?' + date));

        $('#btn-bookmark').button({
            label: views.actual.name + ' | {strip_tags:TITLE}',
            disabled: (views.actual.slug == '')
        }).prop('href', '/chart/' + views.actual.slug);
    }

    var ts = (new Date).getTime(),
        channels_new = [], yAxisMap = [], yAxis = [],
        channel, channel_clone, buffer = [],
        aborted = false,
        period_count = +$('#periodcnt').val(),
        period = $('#period').val(),
        res = xResolution[period] ? xResolution[period] : 1,
        expanded = tree.expanded;

    /* Show all rows to reset consumption and cost columns */
    if (!expanded) tree.toggle(true);

    /* Reset consumption and costs data */
    $('.minmax, .consumption, .costs, #costs').each(function(id, el) {
        $(el).html('');
    });

    /* Re-collapse if needed */
    if (!expanded) tree.toggle(false);

    /* find active channels, map and sort axis */
    $('input.channel:checked').each(function(id, el) {
        var ch = channels[$(el).data('id')],
            channel = new presentation($(el).val());
        channel.id = ch.id;
        channel.name = $('<div/>').html(ch.name).text();
        channel.guid = ch.guid;
        channel.unit = ch.unit;
        channel.time1 = TimeStrToSec(channel.time1,  0);
        channel.time2 = TimeStrToSec(channel.time2, 24);
        /* remember channel */
        buffer.push(channel);
        /* still mapped? */
        if (yAxisMap.indexOf(channel.axis) == -1) yAxisMap.push(channel.axis);
    });

    /* sort axis to make correct order for Highcharts */
    yAxisMap.sort();

    /* build channels */
    $(buffer).each(function(id, channel) {
        /* axis on right side */
        var is_right = !(channel.axis % 2);

        /* axis from chart point of view */
        channel.axis = yAxisMap.indexOf(channel.axis);

        if (channel.type == 'areasplinerange') {
            /* handling area splines */
            if (period == '') {
                /* no period => show spline */
                channel.type = 'spline';
                channels_new.push(channel);
            } else {
                /* period, add channel and ... */
                channels_new.push(channel);
                /* ... add 2nd spline channel! */
                var channel_clone = $.extend({}, channel);
                channel_clone.linkedTo = ':previous';
                channel_clone.type = 'spline';
                channels_new.push(channel_clone);
            }
        } else {
            channels_new.push(channel);
        }

        /* prepare axis */
        if (!yAxis[channel.axis]) {
            yAxis[channel.axis] = {
                /* unit as axis title */
                title: { text: channel.unit },
                lineColor: channel.color,
                showEmpty: false,
                minPadding: 0,
                maxPadding: 0,
                opposite: is_right
            };
            /* only 1st left axis shows grid lines */
            if (channel.axis != 0) {
                yAxis[channel.axis].gridLineWidth = 0;
            }
        }
    });

    if (yAxis.length > 1) {
        $(yAxis).each(function(id) {
            yAxis[id].startOnTick = false;
            yAxis[id].endOnTick = false;
        });
    }

    /* Any channels checked for drawing? */
    if (channels_new.length == 0) return;

    $('#chart').addClass('wait');
    oTable.fnProcessingIndicator(true);

    _log('Channels:', channels_new);
    _log('yAxis:', yAxis);

    /* check for changed channels */
    var changed = false, now = (new Date).getTime() / 1000 / 60;

    /* renew chart at least each half hour to auto adjust axis ranges by Highcharts */
    if (forceUpdate || channels_new.length != channels_chart.length || now - lastChanged > 30) {
        changed = true;
        channels_chart = channels_new;
        lastChanged = now;
    } else {
        for (var i=0, l=channels_new.length; i<l; i++) {
            if (JSON.stringify(channels_new[i]) != JSON.stringify(channels_chart[i])) {
                changed = true;
                channels_chart = channels_new;
            }
        }
    }

    if (changed) {
        /* use UTC for timestamps with a period >= day to avoid wrong hours in hint */
        Highcharts.setOptions({ global: { useUTC: (res > xResolution.h) } });

        /* happens also on 1st call! */
        chartOptions.yAxis = yAxis;
        chartOptions.exporting = { filename: views.actual.slug };

        /* (re)create chart */
        chart = new Highcharts.Chart(chartOptions);
        /* Help chart with fluid layout to find its correct size... */
        chart.reflow();
    }

    var f = $('#from').val(), t = $('#to').val();
    if (f != t) f += ' - ' + t;
    chart.setTitle({ text: $('<div/>').html(views.actual.name).text() }, { text: f });

    chart.showLoading('<img src="/images/loading_bar.gif" width="192" height="12" />');

    var series = [], costs = 0, date = new Date();

    /* get data */
    $(channels_chart).each(function(id, channel) {

        $('#s'+channel.id).show();

        var url = PVLngAPI + 'data/' + channel.guid + '.json';

        _log('Fetch', url);

        $.getJSON(
            url,
            {
                attributes: true,
                full:       true,
                start:      fromDate,
                end:        toDate + '+1day',
                period:     (channel.type != 'scatter') ? period_count + period : '',
                _ts:        date.getTime()
            },
            function(data) {
                try {
                    /* pop out 1st row with attributes */
                    attr = data.shift();
                } catch(err) {
                    console.error(data);
                    /* Set pseudo channel */
                    series[id] = {};
                    return;
                }
/*
                _log('Attributes', attr);
                _log('Data', data);
*/
                if (attr.consumption) {
                    $('#cons'+channel.id).html(Highcharts.numberFormat(attr.consumption, attr.decimals));
                }

                if (attr.costs) {
                    costs += +attr.costs.toFixed({CURRENCYDECIMALS});
                    $('#costs'+channel.id).html(Highcharts.numberFormat(attr.costs, {CURRENCYDECIMALS}));
                }

                t = (attr.description) ? ' (' + attr.description + ')' : '';

                var serie = { /* HTML decode channel name */
                    name: $('<div/>').html(attr.name + t).text(),
                    guid: channel.guid,
                    id: channel.id,
                    decimals: attr.decimals,
                    unit: attr.unit,
                    color: channel.color,
                    type: channel.type,
                    yAxis: channel.axis,
                    raw: (period == ''),
                    data: []
                };

                if (channel.coloruseneg) {
                    serie.threshold = channel.threshold;
                    serie.negativeColor = channel.colorneg;
                }

                if (channel.linkedTo != undefined) serie.linkedTo = channel.linkedTo;
                serie.tooltip = { valueSuffix: attr.unit ? attr.unit : '' };

                if (channel.type == 'scatter') {
                    serie.dataLabels = {
                        enabled: true,
                        formatter: function() {
                            /* Switch for non-numeric / numeric channels */
                            return this.point.name
                                 ? this.point.name
                                 : Highcharts.numberFormat(this.point.y, this.point.series.options.decimals);
                        }
                    };
                    if (attr.unit.trim() == '') {
                        /* mostly non-numeric channels */
                        serie.dataLabels.align = 'left';
                        serie.dataLabels.rotation = 270;
                        serie.dataLabels.x = 3;
                        serie.dataLabels.y = -8;
                    }
                } else if (channel.type != 'bar') {
                    serie.dashStyle = channel.style;
                    serie.lineWidth = channel.width;
                }


                $(data).each(function(id, row) {
                    var time;

                    /* Check time range channels, only if not full day 00:00 .. 24:00 */
                    if (channel.time2-channel.time1 < 86400 && fromDate == toDate) {
                        /* Get todays seconds from timestamp */
                        date.setTime(row.timestamp * 1000);
                        time = date.getHours() * 3600 + date.getMinutes() * 60 + date.getSeconds();
                        /* Skip data outside display time range */
                        if (time < channel.time1 || time > channel.time2) return;
                    }

                    var point = {};

                    if (channel.type == 'scatter') {
                        /* Show scatters at their real timestamps, ALSO for consolidated data */
                        point.x = row.timestamp * 1000;
                    } else {
                        point.x = Math.round(row.timestamp / res) * res * 1000;
                    }

                    if ($.isNumeric(row.data)) {
                        if (channel.type == 'areasplinerange') {
                            point.low  = row.min;
                            point.high = row.max;
                        } else {
                            if (!channel.all) {
                                point.y = channel.consumption ? row.consumption : row.data;
                            } else {
                                /* Format data label */
                                point.y = +(channel.consumption ? row.consumption : row.data).toFixed(attr.decimals);
                                point.dataLabels = { enabled: true };
                            }
                        }
                    } else {
                        point.y = 0;
                        point.name = row.data;
                    }

                    serie.data.push(point);
                });

                if (!channel.all && (channel.min || channel.max || channel.last)) {
                    serie = setMinMax(serie, channel);
                }

                _log('Serie', serie);

                series[id] = serie;

                if ('{INDEX_NOTIFYLOADEACH}') $.pnotify({
                    type: 'success',
                    text: attr.name + ' loaded ' +
                          '(' + (((new Date).getTime() - ts)/1000).toFixed(1) + 's)'
                });
            }
        ).fail(function(jqXHR, textStatus, error) {
            if (textStatus == 'abort') {
                /* Aborted during loading */
                aborted = true;
            } else {
                $.pnotify({
                    type: textStatus,
                    text: error + "\n" + (jqXHR.responseJSON ? jqXHR.responseJSON.message : jqXHR.responseText),
                    hide: false
                });
                /* Set pseudo channel */
                series[id] = {};
            }
        }).always(function(data, status) {

            if (aborted) return;

            $('#s'+channel.id).hide();

            /* check real count of elements in series array! */
            var completed = series.filter(function(a){ return a !== undefined }).length;
            _log(completed+' series completed');

            /* check if all getJSON() calls finished */
            if (completed != channels_chart.length) return;

            if ('{INDEX_NOTIFYLOADALL}') $.pnotify({
                type: 'success',
                text: completed + ' channels loaded ' +
                      '(' + (((new Date).getTime() - ts)/1000).toFixed(1) + 's)'
            });
            $('#costs').html(costs ? Highcharts.numberFormat(costs, {CURRENCYDECIMALS}) : '');

            _log('Apply series');

            if (changed) {
                /* remove all existing series */
                while (chart.series.length) {
                    chart.series[0].remove();
                }
                /* add new series */
                $.each(series, function(i, serie) {
                    if (serie.id) {
                        /* Valid channel with id */
                        chart.addSeries(serie, false);
                    }
                });
            } else {
                /* replace series data */
                var sid = 0;
                $.each(series, function(i, serie) {
                    if (serie.id) {
                        /* Valid channel with id */
                        chart.series[sid].setData(serie.data, false);
                        /* Do we have raw data? Only then deletion of reading value is possible */
                        chart.series[sid].userOptions.raw = serie.raw;
                        sid++;
                    }
                });
            }

            chart.hideLoading();
            chart.redraw();

            setExtremes();

            $('#chart').removeClass('wait');
            oTable.fnProcessingIndicator(false);

            if (RefreshTimeout > 0) {
                updateTimeout = setTimeout(updateChart, RefreshTimeout*1000);
            }

        });
    });
}

/**
 *
 */
var oTable, xhrPool = [];

/**
 * Idea from http://stackoverflow.com/a/11612641
 */
$.ajaxQ = (function() {

    var id = 0, queue = {};

    $(document).ajaxSend(function(e, jqXHR) {
        jqXHR._id = ++id;
        queue[jqXHR._id] = jqXHR;
    });

    $(document).ajaxComplete(function(e, jqXHR) {
        delete queue[jqXHR._id];
    });

    return {
        abortAll: function() {
            var cnt = 0;
            $.each(queue, function(i, jqXHR) {
                jqXHR.abort();
                cnt++;
            });
            return cnt;
        }
    };

})();

/**
 *
 */
function resetDeleteButton() {
    $('#btn-delete').button({ label: '&nbsp;', text: false }).data('confirmed', 0);
}

/**
 *
 */
$(function() {

    $.fn.dataTableExt.afnFiltering.push(
        function( oSettings, aData, iDataIndex ) {
            return tree.expanded ? true : $(oTable.fnGetNodes()[iDataIndex]).hasClass('checked');
        }
    );

    $.fn.dataTableExt.oApi.fnProcessingIndicator = function( oSettings, onoff ) {
        this.oApi._fnProcessingDisplay( oSettings, onoff );
    };

    $.ajaxSetup({
        beforeSend: function setHeader(xhr) {
            xhr.setRequestHeader('X-PVLng-Key', PVLngAPIkey);
        }
    });

    /**
     *
     */
    qs = $.parseQueryString();

    chartOptions.chart.height = qs.height || ChartHeight;

    if ($.datepicker.regional[language]) {
        $.datepicker.setDefaults($.datepicker.regional[language]);
    } else {
        $.datepicker.setDefaults($.datepicker.regional['']);
    }

    if (qs.date) {
        var d = new Date(qs.date);
    } else {
        var d = new Date();
    }

    $("#from").datepicker({
        altField: '#fromdate',
        altFormat: 'mm/dd/yy',
        maxDate: 0,
        showButtonPanel: true,
        showWeek: true,
        changeMonth: true,
        changeYear: true,
        onClose: function( selectedDate ) {
            $("#to").datepicker( "option", "minDate", selectedDate );
        }
    }).datepicker('setDate', d);

    $("#to").datepicker({
        altField: '#todate',
        altFormat: 'mm/dd/yy',
        maxDate: 0,
        showButtonPanel: true,
        showWeek: true,
        changeMonth: true,
        changeYear: true,
        onClose: function( selectedDate ) {
            $("#from").datepicker( "option", "maxDate", selectedDate );
        }
    }).datepicker('setDate', d);

    Highcharts.setOptions({
        global: { alignTicks: false },
        lang: {
            thousandsSep: '{TSEP}',
            decimalPoint: '{DSEP}',
            resetZoom: '{{resetZoom}}',
            resetZoomTitle: '{{resetZoomTitle}}'
        }
    });

    if (user) {
        var aoColumnDefs = [
            { sWidth: '1%', aTargets: [ 0, 2, 3, 4, 5, 6 ] }
        ];
    } else {
        var aoColumnDefs = [
            { sWidth: '1%', aTargets: [ 1, 2, 3 ] }
        ];
    }

    /**
     *
     */
    oTable = $('#data-table').DataTable({
        bSort: false,
        bInfo: false,
        bLengthChange: false,
        bPaginate: false,
        bProcessing: true,
        bAutoWidth: false,
        bJQueryUI: true,
        sDom: '<"H"r>t<"F">',
        oLanguage: { sUrl: '/resources/dataTables.'+language+'.json' },
        aoColumnDefs: user
            ? [ { sWidth: '1%', aTargets: [ 0, 2, 3, 4, 5, 6 ] } ]
            : [ { sWidth: '1%', aTargets: [ 1, 2, 3 ] } ],
        fnInitComplete: function() {
            /* Init treetable AFTER databale is ready */
            $('.treeTable').treetable({
                initialState: 'expanded',
                indent: 24,
                column: 1
            });
        }
    });

    if (user) {
        $('.chartdialog').addClass('clickable').click(function() {
            ChartDialog($(this).data('id'));
        });

        $('.showlist').addClass('clickable').click(function() {
            window.location.href = '/list/' + channels[$(this).data('id')].guid;
        });
    }

    /**
     *
     */
    $('#preset').change(function() {
        var preset = ($('#preset').val() || '').match(/(\d+)(\w+)/),
            pcount = $('#periodcnt'), period = $('#period'),
            before = pcount.val() + period.val();

        if (!preset) {
            pcount.val(1);
            period.val('');
        } else {
            var from = new Date($("#from").datepicker('getDate'));
            switch (preset[2]) {
                case 'd': /* day, week - set start to 1st day of month */
                case 'w':
                    from.setDate(1);  break;
                case 'm': /* month, quarter - set start to 1st day of year */
                case 'q':
                    from.setDate(1);  from.setMonth(0);  break;
            }
            $("#from").datepicker('setDate', from);
            pcount.val(preset[1]);
            period.val(preset[2]);
        }
        /* Force chart reload */
        updateChart();
    });

    /**
     *
     */
    $("#dialog-chart").dialog({
        autoOpen: false,
        position: [ null, 20 ],
        width: 750,
        modal: true,
        buttons: {
            '{{Ok}}': function() {
                $(this).dialog('close');
                var chk = $('#c'+$(this).data('id')), old = chk.val(),
                    p = new presentation();
                p.axis = +$('input[name="d-axis"]:checked').val();
                p.type = $('#d-type').val();
                p.consumption = $('#d-cons').is(':checked');
                p.style = $('#d-style').val();
                p.width = +$('input[name="d-width"]:checked').val();
                p.min = $('#d-min').is(':checked');
                p.max = $('#d-max').is(':checked');
                p.last = $('#d-last').is(':checked');
                p.all = $('#d-all').is(':checked');
                p.color = $('#d-color').spectrum('get').toHexString();
                p.coloruseneg = $('#d-color-use-neg').is(':checked');
                p.colorneg = $('#d-color-neg').spectrum('get').toHexString();
                p.threshold = +$('#d-threshold').val().replace(',', '.');

                p.time1 = SecToTimeStr(TimeStrToSec($('#d-time1').val(), 0));
                p.time2 = SecToTimeStr(TimeStrToSec($('#d-time2').val(), 24));

                p = p.toString();

                if (p != old) {
                    chk.val(p);
                    $('#modified').show();
                }
            },
            '{{Cancel}}': function() {
                $(this).dialog('close');
            }
        }
    });

    $('.spectrum').spectrum({
        showPalette: true,
/*
        showPaletteOnly: true,
        localStorageKey: 'pvlng.channel.color',
*/
        palette: [
            ['#404040', '#4572A7'],
            ['#AA4643', '#89A54E'],
            ['#80699B', '#3D96AE'],
            ['#DB843D', '#92A8CD'],
            ['#A47D7C', '#B5CA92']
        ],
        showInitial: true,
        showButtons: false,
        preferredFormat: 'hex',
        hide: function(color) { color.toHexString(); }
    });

    /**
     *
     */
    $('#d-type').change(function() {
        var notBar = $('.not-bar'), notScatter = $('.not-scatter');

        /* Reset all */
        notBar.removeClass('disabled');
        notBar.find('input, select').prop('disabled', false);

        notScatter.removeClass('disabled');
        notScatter.find('input, select').prop('disabled', false);

        /* Disable all invalid options for given type */
        if (this.value == 'bar') {
            notBar.addClass('disabled');
            notBar.find('input, select').prop('disabled', true);
        } else if (this.value == 'scatter') {
            $('#d-color-use-neg').iCheck('uncheck').trigger('ifToggled');
            notScatter.addClass('disabled');
            notScatter.find('input, select').prop('disabled', true);
        }
        $('input').iCheck('update');
    });

    $('#d-color-use-neg').on('ifToggled', function(e) {
        var checked = $(this).is(':checked');
        $('#d-threshold').prop('disabled', !checked).spinner('option', 'disabled', !checked );
        $('#d-color-neg').spectrum(checked ? 'enable' : 'disable');
    });

    $('#d-time-slider').slider({
        range: true,
        min: 0, /* 00:00 */ max: 86400, /* 24:00 */
        values: [ 0, 86400 ], /* 00:00 - 24:00 */
        slide: function( e, ui ) {
            /* Simulate hour stepping, but allow fine grained set from input */
            var step = 3600;
            /* Calc hour and minutes parts */
            $('#d-time1').val(SecToTimeStr(Math.floor(ui.values[0]/step)*step));
            $('#d-time2').val(SecToTimeStr(Math.floor(ui.values[1]/step)*step));
        }
    });

    $('input').iCheck('update');

    $('input.channel').on('ifToggled', function() {
        $('#r'+this.id).toggleClass('checked', this.checked);
        $('#modified').show();
    });

    $('#btn-reset').button({
        icons: { primary: 'ui-icon-calendar' },
        label: '&nbsp;',
        text: false
    }).click(function(event) {
        event.preventDefault();
        var d = new Date;
        /* Set date ranges */
        $('#from').datepicker('option', 'maxDate', d);
        $('#to').datepicker('option', 'minDate', d);
        /* Set date today */
        $('#from').datepicker('setDate', d);
        $('#to').datepicker('setDate', d);
        updateChart();
    });

    $('#btn-refresh').button({
        icons: { primary: 'ui-icon-refresh' },
        label: '&nbsp;',
        text: false
    }).click(function(event) {
        event.preventDefault();
        updateChart(event.shiftKey);
    });

    $('#btn-permanent').button({
        icons: { primary: 'ui-icon-image' },
        text: false
    });

    $('#btn-bookmark').button({
        icons: { primary: 'ui-icon-bookmark' },
        text: false
    });

    $('#treetoggle').click(function() { tree.toggle() });

    $('#togglewrapper').button({
        icons: { primary: 'ui-icon-carat-2-n-s' },
        label: '&nbsp;',
        text: false
    }).click(function() {
        $('#wrapper').animate( { height: 'toggle', opacity: 'toggle' } );
    });

    $('#top-load-view').change(function() {
        views.load($('#top-load-view option:selected').val(), true, function(view) {
            $('#loaddeleteview').val(view.slug);
            $('#saveview').val(view.name);
        });
    });

    $('#btn-load').button({
        icons: { primary: 'ui-icon-folder-open' },
        label: '&nbsp;',
        text: false
    }).click(function(event) {
        views.load($('#loaddeleteview option:selected').val(), true);
    });

    $('#btn-delete').button({
        icons: { primary: 'ui-icon-trash' },
        label: '&nbsp;',
        text: false
    }).click(function(event) {
        var option = $('#loaddeleteview option:selected'), btn = $(this);

        if (option.val() == '') return;

        if (btn.data('confirmed') == 0) {
            /* Replace text, make red and mark confirmed for next click */
            btn.button({ label: '{{Sure}}?', text: true }).data('confirmed', 1)
               .find('.ui-button-text').css('color', 'red');
            /* Reset after 5s */
            setTimeout(resetDeleteButton, 5000);
        } else {
            resetDeleteButton();
            btn.button('disable');
            $('#chart').addClass('wait');

            $.ajax({
                type: 'DELETE',
                dataType: 'json',
                url: PVLngAPI + '/view/'+option.val()+'.json'
            }).done(function(data, textStatus, jqXHR) {
                $.pnotify({ type: 'success', text: option.text() + ' {{deleted}}' });
                /* Just delete selected option and clear save name input */
                option.remove();
                $('#saveview').val('');
            }).fail(function(jqXHR, textStatus, errorThrown) {
                $.pnotify({
                    type: textStatus, hide: false,
                    text: jqXHR.responseJSON.message ? jqXHR.responseJSON.message : jqXHR.responseText
                });
            }).always(function() {
                $('#chart').removeClass('wait');
                btn.button({ label: '&nbsp;', text: false }).data('confirmed', 0).button('enable');
            });
        }
    }).css('font-weight', 'bold');

    $('#btn-save').button({
        icons: { primary: 'ui-icon-disk' },
        label: '&nbsp;',
        text: false
    }).click(function() {
        $(this).button('disable');
        $('#chart').addClass('wait');

        /* Save view */
        var btn = this,
            data = {
                name: $('#saveview').val(),
                data: { p: $('#preset').val() },
                public: $('#visibility option:selected').val()
            };

        $('input.channel:checked').each(function(id, el) {
            data.data[$(el).data('id')] = $(el).val();
        });

        $.ajax({
            type: 'PUT',
            dataType: 'json',
            url: PVLngAPI + '/view.json',
            contentType: 'application/json',
            processData: false, /* Send prepared JSON in body */
            data: JSON.stringify(data)
        }).done(function (data, textStatus, jqXHR) {
            $.pnotify({ type: textStatus, text: data.name + ' saved' });
            views.fetch(function(views) {
                /* Rebuild select */
                views.buildSelect('#loaddeleteview', data.slug);
                views.load(data.slug);
                /* Adjust chart title */
                if (chart) chart.setTitle(views.actual.name);
            });
            $('#modified').hide();
        }).fail(function(jqXHR, textStatus, errorThrown) {
            $.pnotify({
                type: textStatus, hide: false, text: jqXHR.responseText
            });
        }).always(function() {
            $('#chart').removeClass('wait');
            $(btn).button('enable');
        });
    });

    views = new Views();

    views.fetch(function(views) {
        if (user) {
            views.buildSelect('#top-load-view');
            $('#top-select').show();
            $('#top-load-view').children('option').clone().appendTo('#loaddeleteview');
            $('#top-load-view').children('optgroup').clone().appendTo('#loaddeleteview');
        } else {
            views.buildSelect('#loaddeleteview');
            $('#public-select').show();
        }
        $('#loading').hide();
        /* Chart slug provided by URL, load and collapse tree */
        views.load(qs.chart, true);
    });

    $("#dialog-reading").dialog({
        modal: true,
        resizable: false,
        bgiframe: true,
        width: 500,
        autoOpen: false,
        buttons: {
            '{{Yes}}': function() {
                var self = $(this);

                self.dialog('close');
                chart.showLoading('{{JustAMoment}}');
                $('#chart').addClass('wait');

                var url = PVLngAPI + 'data/' + self.data('guid') + '/' + self.data('timestamp') + '.json';

                $.ajax(
                    { type: 'DELETE', url: url, dataType: 'json' }
                ).done(function(data, textStatus, jqXHR) {
                    self.data('point').remove();
                    $.pnotify({ type: 'success', text: '{{ReadingDeleted}}' });
                }).fail(function(jqXHR, textStatus, errorThrown) {
                    $.pnotify({
                        type: textStatus, hide: false,
                        text: jqXHR.responseJSON.message ? jqXHR.responseJSON.message : jqXHR.responseText
                    });
                }).always(function() {
                    chart.hideLoading();
                    $('#chart').removeClass('wait');
                });

            },
            '{{No}}': function() {
                $(this).dialog('close');
            }
        }
    });

    /**
     * HTML5 Page Visibility API
     *
     * http://www.sitepoint.com/introduction-to-page-visibility-api/
     * http://www.w3.org/TR/page-visibility
     */
    if (document.hidden !== undefined) {
        browserPrefix = '';
    } else {
        var browserPrefixes = ['webkit', 'moz', 'ms', 'o'];
        /* Test all vendor prefixes */
        for (var i=0; i<browserPrefixes.length; i++) {
            if (document[browserPrefixes[i] + 'Hidden'] != undefined) {
                browserPrefix = browserPrefixes[i];
                break;
            }
        }
    }

    if (browserPrefix !== null) {
        document.addEventListener(browserPrefix + 'visibilitychange', function() {
            if (document.hidden === false || document[browserPrefix + 'Hidden'] === false) {
                /* The page is in foreground and visible */
                windowVisible = true;
                /* Was longer in background, so the updateTimeout was cleared */
                if (!updateTimeout) setTimeout(updateChart, 1000);
            } else {
                windowVisible = false;
            }
        });
    }

    shortcut.add('Alt+P', function() { changeDates(-1); });
    shortcut.add('Alt+N', function() { changeDates(1); });
    shortcut.add('F6',    function() { updateChart(); });
    shortcut.add('F7',    function() { updateChart(true); });
    if (user) {
        shortcut.add('F3', function() { $('#togglewrapper').click(); });
        shortcut.add('F4', function() { tree.toggle(); });
    }
});

</script>
