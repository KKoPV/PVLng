<script>
/**
 *
 *
 * @author      Knut Kohl <github@knutkohl.de>
 * @copyright   2012-2014 Knut Kohl
 * @license     MIT License (MIT) http://opensource.org/licenses/MIT
 * @version     1.0.0
 */
</script>

<!--
<script src="/js/palettes.js"></script>
-->

<script>

var ChartHeight = {INDEX_CHARTHEIGHT},
    RefreshTimeout = {INDEX_REFRESH},
    aborted = false,
    qs = {},
    chart = false,
    chartLoading = '<img src="/images/loading_dots.gif" width="64" height="21">',
    channels_chart = [],
    updateTimeout,
    updateActive = false,
    windowVisible = true,
    browserPrefix = null,
    lastChanged = (new Date).getTime() / 1000 / 60,
    oTable,
    channels = {
        <!-- BEGIN DATA -->
        {ID}: { id: {ID}, guid: '{GUID}', name: '{NAME}', unit: '{UNIT}', entity: {ENTITY}, n: {NUMERIC} },
        <!-- END -->
    },

    /**
     * Scale timestamps down to full minute, hour, day, week, month, quarter or year
     */
    xResolution = {
        i: 60,
        h: 60 * 60,
        d: 60 * 60 * 24,
        w: 60 * 60 * 24 * 7,
        m: 60 * 60 * 24 * 30,
        q: 60 * 60 * 24 * 90,
        y: 60 * 60 * 24 * 360
    },

    chartOptions = {
        chart: {
            renderTo: 'chart',
            style: { fontFamily: 'inherit' },
            alignTicks: false,
            zoomType: 'x',
            resetZoomButton: {
                relativeTo: 'chart',
                position: { x: -40 }
            },
            panning: true,
            panKey: 'shift',
            events: {
                load: function () {
                    this.renderer
                        .label('PVLng v'+PVLngVersion)
                        .attr('padding', 1)
                        .css({ color: 'lightgray', fontSize: '11px' })
                        .add();
                },
                redraw: function () {
                    /* Date and time, but without seconds */
                    this.renderer
                        .label((new Date).toLocaleString().slice(0,-3), 0, 16)
                        .attr({ fill: 'white', padding: 1 })
                        .css({ color: 'lightgray', fontSize: '9px' })
                        .add();
                }
            }
        },
        title: { text: '' },
        /* legend: { borderRadius: 5, borderWidth: 1 }, */
        credits: false,
        plotOptions: {
            series: {
                point: { events: { click: PVLngAPIkey ? deleteReading : null } },
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
var views = new function Views() {
    this.views = {};
    this.actual = { slug: '', name: '', public: 0 };

    this.fetch = function( callback ) {
        this.views = {};
        var that = this;
        $.getJSON(
            PVLngAPI + 'views.json',
            { sort_by_visibilty: true, no_data: true },
            function(data) {
                var l = data.length;
                for (var i = 0; i<l; i++) {
                    that.views[data[i].slug] = data[i];
                }
                if (l) $('.ui-tabs').tabs('option', 'active', 1) /* Tabs are zero based */
            }
        ).always(function() {
            if (typeof callback != 'undefined') callback(that);
        });
    };

    this.buildSelect = function(el, selected) {
        var optgroups = [ [], [], [] ],
            labels    = [ '{{private}}', '{{public}}', '{{mobile}}' ],
            l, i, j;

        el = $(el).empty();

        /* Build optgroups */
        $.each(this.views, function(id, view) {
            /* Collect in groups */
            optgroups[view.public].push(view);
        });

        $('<option/>').appendTo(el);

        for (i=0; i<=2; i++) {
            l = optgroups[i].length;
            if (!l) continue;

            var o = $('<optgroup/>').prop('label', labels[i]);
            for (j=0; j<l; j++) {
               $('<option/>').text(optgroups[i][j].name).val(optgroups[i][j].slug).appendTo(o);
            }
            o.appendTo(el);
        }
        if (selected) el.val(selected);
        el.trigger('change');
    };

    this.load = function( slug, collapse, callback ) {

        if (typeof this.views[slug] == 'undefined') return;

        $.getJSON(
            PVLngAPI + 'view/'+slug+'.json',
            function(data) {
                if (typeof collapse == 'undefined') collapse = false;

                var expanded = tree.expanded, preset;
                if (!expanded) tree.toggle(true);

                views.views[slug].data = data;
                views.actual = views.views[slug];

                /* Uncheck all channels and ... */
                $('input.channel').iCheck('uncheck').val('');
                $('tr.channel').removeClass('checked');
                /* ... re-check all channels in view */
                $.each(views.actual.data, function (id, p) {
                    if (id == 'p') {
                        preset = p;
                    } else {
                        $('#c'+id).val(p).iCheck('check');
                    }
                });

                /* Re-arrange channels in collapsed tree */
                if (collapse || !expanded) tree.toggle(false);

                /* Scroll to navigation as top most visible element */
                pvlng.scroll('#nav');

                $('#preset').val(preset).trigger('change'); /* Realods chart */

                $('#load-delete-view').val(views.actual.slug).trigger('change');
                $('#saveview').val(views.actual.name);
                $('#visibility').val(views.actual.public).trigger('change');
                $('#modified').hide();

                if (typeof callback !== 'undefined') callback(views.actual);
            }
        ).fail(function( jqXHR, textStatus ) {
            alert( "Request failed: " + textStatus );
        });
    };
};

/**
 *
 */
var tree = new function Tree() {

    this.expanded = true;

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
};

tree.expanded = !!user;

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
    $('#scatter-candidate').hide();

    /* Get stringified settings */
    var p = $('#c'+id).val();

    if (p == '' || p == 'on') {
        /* Initial, not checked or no presetaion set yet */
        p = new presentation();
        /* Suggest scatter for channels without unit  or non-numeric */
        if (!channels[id].unit || !channels[id].n) {
            $('#scatter-candidate').show();
            p.type = 'scatter';
        }
    } else {
        /* Init presetation */
        p = new presentation(p);
    }

    /* Set dialog properties */
    /* Find the radio button with the axis value and check it */
    $('input[name="d-axis"][value="' + p.axis + '"]').prop('checked', true);
    $('#d-type').val(p.type);
    $('#d-cons').prop('checked', p.consumption);
    /* Find the radio button with the line width and check it */
    $('input[name="d-width"][value="' + p.width + '"]').prop('checked', true);
    $('#d-min').prop('checked', p.min);
    $('#d-max').prop('checked', p.max);
    $('#d-last').prop('checked', p.last);
    $('#d-all').prop('checked', p.all);
    $('#d-style').val(p.style);
    $('#d-color').val(p.color).spectrum('set', p.color);
    /* Different colors above/below threshold */
    $('input:radio[name="color-pos-neg"][value="'+p.colorusediff+'"]').prop('checked', true);
    $('#d-color-diff').val(p.colordiff).spectrum('set', p.colordiff);
    $('#d-color-threshold').val(p.threshold);
    /* Display times in chart */
    $('#d-time1').val(p.time1);
    $('#d-time2').val(p.time2);
    $('#d-daylight').prop('checked', p.daylight);
    $('#d-daylight-grace').val(p.daylight_grace);
    $('#d-time-slider').slider('values', [ TimeStrToSec(p.time1), TimeStrToSec(p.time2) ]);
    $('#d-legend').prop('checked', p.legend);
    $('#d-hidden').prop('checked', p.hidden);
    $('#d-position').text(p.position);
    $('#d-position-slider').slider('value', p.position);

    /* Update only in context of dialog chart */
    $('input.iCheck', '#dialog-chart').iCheck('update').trigger('ifToggled');
    $('select', '#dialog-chart').trigger('change');

    /* Set the id into the dialog for onClose to write data back */
    $('#dialog-chart').data('id', id).dialog('option', 'title', channels[id].name).dialog('open');
}

/**
 *
 */
function updateChart( forceUpdate, scroll ) {

    clearTimeout(updateTimeout);
    updateTimeout = null;

    if (updateActive || !windowVisible) return;

    updateActive = true;

    $('.spinner').hide();

    var fromDate = $('#fromdate').val(),
        toDate = $('#todate').val(),
        period_count = +$('#periodcnt').val(),
        period = $('#period').val(),
        ts = (new Date).getTime(),
        channels_new = [], yAxisMap = [], yAxis = [],
        channel, channel_clone, buffer = [],
        res = xResolution[period] ? xResolution[period] : 1,
        expanded = tree.expanded;

    /* If any outstanding AJAX reqeust was killed, force rebuild of chart */
    if ($.ajaxQ.abortAll() != 0) forceUpdate = true;

    if (user && views.actual) {
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

    /* Show all rows to reset consumption and cost columns */
    if (!expanded) tree.toggle(true);

    /* Reset consumption and costs data */
    $('.minmax, .consumption, .costs, #costs').html('');

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
        /* Remember channel in correct order */
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

        /* Remember original axis for change detection */
        channel.axis_org = channel.axis;
        /* Axis from chart point of view */
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

        /* Prepare axis */
        if (!yAxis[channel.axis]) {
            yAxis[channel.axis] = {
                lineColor: channel.color,
                showEmpty: false,
                minPadding: 0,
                maxPadding: 0,
                opposite: is_right
            };
            /* Only 1st left axis shows grid lines */
            if (channel.axis != 0) {
                yAxis[channel.axis].gridLineWidth = 0;
            }
        }

        /* Use 1st non-empty channel unit as axis title */
        if (!yAxis[channel.axis].title && channel.unit) {
            yAxis[channel.axis].title = { text: channel.unit };
        }
    });

    if (yAxis.length > 1) {
        $(yAxis).each(function(id) {
            yAxis[id].startOnTick = false;
            yAxis[id].endOnTick = false;
        });
    }

    /* Any channels checked for drawing? */
    if (channels_new.length == 0) {
        updateActive = FALSE;
        return;
    }

    $.wait();

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
                break;
            }
        }
    }

    if (changed) {
        /* Use UTC for timestamps with a period >= day to avoid wrong hours in hint */
        Highcharts.setOptions({ global: { useUTC: (res > xResolution.h) } });

        /* Happens also on 1st call! */
        chartOptions.yAxis = yAxis;
        chartOptions.exporting = { filename: views.actual.slug };

        /* (Re)Create chart */
        chart = new Highcharts.Chart(chartOptions);
        /* Help chart with fluid layout to find its correct size... */
        chart.reflow();
        /* Show zoom and pan help */
        $('#zoom-hint').show();
    }

    var f = $('#from').val(), t = $('#to').val();
    if (f != t) f += ' - ' + t;
    chart.setTitle({ text: $('<div/>').html(views.actual.name).text() }, { text: f });

    chart.showLoading(chartLoading);

    var series = [], costs = 0, date = new Date(), AJAXcount = 0,
        today = ('0'+(date.getMonth()+1)).slice(-2) + '/' +
                ('0'+date.getDate()).slice(-2) + '/' + date.getFullYear();

    /* get data */
    $(channels_chart).each(function(id, channel) {

        channel._ts = date.getTime();

        /* Count channel AJAX calls */
        AJAXcount++;

        $('#s'+channel.id).show();

        var url = PVLngAPI + 'data/' + channel.guid + '.json',
            start = fromDate,
            end = toDate + '+1day';

        if (channel.daylight && today == toDate) {
            start = 'sunrise;'+channel.daylight_grace;
            end = 'sunset;'+channel.daylight_grace;
        }

        _log('Fetch', url);

        $.getJSON(
            url,
            {
                attributes: true,
                full:       true,
                start:      start,
                end:        end,
                period:     (channel.type != 'scatter') ? period_count + period : '',
                _canAbort:  true,
                _ts:        channel._ts
            },
            function(data) {

                if (aborted) return;

                try {
                    /* pop out 1st row with attributes */
                    attr = data.shift();
                } catch(err) {
                    console.error(data);
                    /* Set pseudo channel */
                    series[id] = {};
                    return;
                }

                _log('Attributes', attr);
                _log('Data', data);

                if (attr.consumption) {
                    $('#cons'+channel.id).html(Highcharts.numberFormat(attr.consumption, attr.decimals));
                }

                if (attr.costs) {
                    costs += +attr.costs.toFixed(CurrencyDecimals);
                    $('#costs'+channel.id).html(CurrencyFormat.replace('{}', Highcharts.numberFormat(attr.costs, CurrencyDecimals)));
                }

                /* Add channel description if chart name NOT still contains it */
                t = (String(views.actual.name).toLowerCase().indexOf(String(attr.description).toLowerCase()) == -1)
                  ? ' (' + attr.description + ')'
                  : '';

                var serie = {
                    data: [],
                    color: channel.color,
                    id: channel.id,
                    /* HTML decode channel name */
                    name: $('<div/>').html(attr.name + t).text(),
                    showInLegend: channel.legend,
                    type: channel.type,
                    visible: !channel.hidden,
                    yAxis: channel.axis,
                    /* Own properties */
                    colorDiff: channel.colorusediff,
                    decimals: attr.decimals,
                    guid: channel.guid,
                    legendColor: channel.color, /* Force legend color */
                    unit: attr.unit,
                    raw: (period == '')
                };

                if (channel.colorusediff !== 0) {
                    if (channel.colorusediff === 1) {
                        /* Other color for values above threshold > switch colors! */
                        serie.color         = channel.colordiff;
                        serie.negativeColor = channel.color;
                    } else {
                        /* Other color for values below threshold */
                        serie.negativeColor = channel.colordiff;
                    }
                    serie.threshold = channel.threshold;
                }

                if (channel.linkedTo != undefined) serie.linkedTo = channel.linkedTo;
                serie.tooltip = { valueSuffix: attr.unit ? attr.unit : '' };

                if (channel.type == 'scatter') {
                    if (attr.marker) serie.marker = { symbol: 'url('+attr.marker+')' };
                    serie.dataLabels = {
                        enabled: true,
                        formatter: function() {
                            /* Switch for non-numeric / numeric channels */
                            return typeof this.point.name != 'undefined'
                                 ? this.point.name
                                 : Highcharts.numberFormat(this.point.y, this.point.series.options.decimals);
                        }
                    };
                    if (attr.unit.trim() == '') {
                        /* Mostly non-numeric channels */
                        serie.dataLabels.align = 'left';
                        serie.dataLabels.rotation = 270;
                        serie.dataLabels.style = { textShadow: 0 };
                        /* Move a bit */
                        serie.dataLabels.y = -8;
                    }
                } else if (channel.type != 'bar') {
                    if (channel.style != 'Solid') serie.dashStyle = channel.style;
                    serie.lineWidth = channel.width;
                }

                $(data).each(function(id, row) {
                    if (channel.type == 'scatter') {
                        /* Show scatters at their real timestamps, ALSO for consolidated data */
                        var point = { x: row.timestamp * 1000 };
                    } else {
                        var point = { x: Math.round(row.timestamp / res) * res * 1000 };
                    }

                    /* Check time range channels, only if not full day 00:00 .. 24:00 */
                    if (channel.time2-channel.time1 < 86400 && fromDate == toDate) {
                        /* Get todays seconds from timestamp */
                        date.setTime(row.timestamp * 1000);
                        var time = date.getHours() * 3600 + date.getMinutes() * 60 + date.getSeconds();
                        /* Skip data outside display time range */
                        if (time < channel.time1 || time > channel.time2) return;
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
                        var n = (row.data+'|').split('|');
                        point.name = n[0];
                        if (n[1]) point.marker = { symbol: 'url('+n[1]+')' };
                    }

                    serie.data.push(point);
                });

                if (!channel.all && (channel.min || channel.max || channel.last)) {
                    serie = setMinMax(serie, channel);
                }

                _log('Serie', serie);

                if (!changed) {
                    var s = chart.get(serie.id);
                    /* Replce data direct in existing chart data */
                    s.setData(serie.data, false);
                    /* Do we have raw data? Only then deletion of reading value is possible */
                    s.options.raw = (period == '');
                    /* Add dummy serie for completed check */
                    series[id] = {};
                } else {
                    series[id] = serie;
                }

                if (+'{INDEX_NOTIFYEACH}') $.pnotify({
                    type: 'success',
                    text: attr.name + ' loaded ' +
                          '(' + (((new Date).getTime() - ts)/1000).toFixed(1) + 's)'
                });
            }
        ).fail(function(jqXHR, textStatus, error) {
            if (textStatus == 'abort' || (jqXHR.status == 404 && textStatus == 'error')) {
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

            /* Another AJAX call is done */
            --AJAXcount;

            if (aborted) {
                updateActive = false;
                return;
            }

            $('#s'+channel.id).hide();

            $('#cons'+channel.id).prop('title', 'loaded in ' + ((new Date).getTime() - channel._ts) + ' ms').tipTip();

            /* All calls are done on AJAXcount == 0 */
            if (AJAXcount > 0) return;

            if (+'{INDEX_NOTIFYALL}') $.pnotify({
                type: 'success',
                text: series.length + ' {{ChannelsLoaded}} ' +
                      '(' + (((new Date).getTime() - ts)/1000).toFixed(1) + 's)'
            });

            $('#costs').html(costs ? CurrencyFormat.replace('{}', Highcharts.numberFormat(costs, CurrencyDecimals)) : '');

            _log('Apply series');

            if (changed) {
                /* Remove all existing series */
                for (var i=chart.series.length-1; i>=0; i--) {
                    chart.series[i].remove(false);
                }
                /* Add new series */
                $.each(series, function(i, serie) {
                    /* Valid channel with id */
                    if (serie.id) chart.addSeries(serie, false);
                });
            }

            if ($('#cb-autorefresh').is(':checked') && RefreshTimeout > 0) {
                updateTimeout = setTimeout(updateChart, RefreshTimeout*1000);
            }

            /* Redraw independent from other DOM changes via setTimeout */
            setTimeout(function() {
                chart.hideLoading();
                chart.redraw();
                $.wait(false);
                updateActive = false;
            }, 0);

        });
    });
}

/**
 * Idea from http://stackoverflow.com/a/11612641
 */
$.ajaxQ = (function() {

    var id = 0, queue = {};

    $(document).ajaxSend(function(e, jqXHR, settings) {
        if (settings.url.indexOf('_canAbort') != -1) {
            /* Queue only channel data requests! */
            jqXHR._id = ++id;
            queue[jqXHR._id] = jqXHR;
        }
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
            if (cnt) aborted = true;
            return cnt;
        }
    };

})();

/**
 *
 */
function updateOutput() {
    updateActive = false;
    updateChart();
}

/**
 *
 */
function deleteReading() {
    if (!this.series.options.raw) return $.alert('Can\'t delete from consolidated data.', 'Error');

    var point = this, /* Remember point to remove afterwards from chart */
        url = PVLngAPI + 'data/' + this.series.options.guid + '/' + (this.x/1000).toFixed(0) + '.json',
        msg = $('<p/>').html('{{DeleteReadingConfirm}}'),
        ul = $('<ul/>')
             .append($('<li/>').html(this.series.name))
             .append($('<li/>').html((new Date(this.x)).toLocaleString()))
             .append($('<li/>').html('{{Reading}} : ' + this.y + ' ' + this.series.options.unit));

    $.confirm($('<p/>').append(msg).append(ul), '{{DeleteReading}}', '{{Yes}}', '{{No}}')
    .then(function(ok) {
        if (!ok) return;

        chart.showLoading(chartLoading);
        $.wait();

        $.ajax(
            { type: 'DELETE', url: url, dataType: 'json' }
        ).done(function(data, textStatus, jqXHR) {
            point.remove();
            $.pnotify({ type: 'success', text: '{{ReadingDeleted}}' });
        }).fail(function(jqXHR, textStatus, errorThrown) {
            $.pnotify({
                type: textStatus, hide: false,
                text: jqXHR.responseJSON.message ? jqXHR.responseJSON.message : jqXHR.responseText
            });
        }).always(function() {
            chart.hideLoading();
            $.wait(false);
        });
    })
}
/**
 *
 */
$(function() {

    pvlng.onFinished.add( function() {
        $('#tabs').on('tabsactivate', function(e, ui) {
            /* Scroll to tabs as top most visible element */
            if (ui.newPanel.length && ui.newPanel.prop('id') == 'tabs-1') pvlng.scroll('#tabs');
        });
    });

    /**
     * Modify legend color for pos./neg. splitted series
     * Idea from
     * http://highcharts.uservoice.com/forums/55896-general/suggestions/4575779-make-the-legend-icon-colors-modifiable
     * http://jsfiddle.net/stephanevanraes/CZSzT/
     */
    Highcharts.wrap(Highcharts.Legend.prototype, 'colorizeItem', function (item) {
        /**
         * Switch color for a legend item
         * Digg into Highcharts code, you can find the property "legendColor" is used BEFORE "color",
         * but never defined by Highcharts itself before...
         */
        arguments[1].legendColor = arguments[1].options.legendColor;
        /* Render legend item via wrapped function */
        item.apply(this, [].slice.call(arguments, 1));
    });

    $.fn.dataTableExt.afnFiltering.push(
        function( oSettings, aData, iDataIndex ) {
            return tree.expanded ? true : $(oTable.fnGetNodes()[iDataIndex]).hasClass('checked');
        }
    );

    /**
     * If a chart height is provided as URL parameter "height", remember in cookie
     */
    qs = $.parseQueryString();

    if (qs.height) {
        /* A value of 0 will reset height and remove cookie */
        if (qs.height > 0) {
            pvlng.cookie.set('ChartHeight', ChartHeight = qs.height);
        } else {
            pvlng.cookie.remove('ChartHeight');
        }
    } else {
        var h = pvlng.cookie.get('ChartHeight');
        if (h) ChartHeight = h;
    }
    chartOptions.chart.height = ChartHeight;

    var dFrom, dTo;
    if (qs.from && qs.to) {
        dFrom = new Date(qs.from);
        dTo   = new Date(qs.to);
    } else if (qs.date) {
        dFrom = dTo = new Date(qs.date);
    } else {
        dFrom = dTo = new Date();
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
    }).datepicker('setDate', dFrom);

    $("#to").datepicker({
        altField: '#todate',
        altFormat: 'mm/dd/yy',
        maxDate: 1,
        showButtonPanel: true,
        showWeek: true,
        changeMonth: true,
        changeYear: true,
        onClose: function( selectedDate ) {
            $("#from").datepicker( "option", "maxDate", selectedDate );
        }
    }).datepicker('setDate', dTo);

    Highcharts.setOptions({
        global: { alignTicks: false },
        lang: {
            thousandsSep: '{TSEP}',
            decimalPoint: '{DSEP}',
            resetZoom: '{{resetZoom}}',
            resetZoomTitle: '{{resetZoomTitle}}'
        }
    });

    /**
     *
     */
    oTable = $('#data-table').DataTable({
        bSort: false,
        bFilter: true,        /* Allow filter by coding, but   */
        sDom: '<"H"r>t<"F">', /* remove filter input from DOM. */
        bAutoWidth: false,
        aoColumnDefs: user
            ? [ { sWidth: '1%', aTargets: [ 0, 2, 3, 4, 5 ] } ]
            : [ { sWidth: '1%', aTargets: [ 1, 2 ] } ]
    });

    if (user) {
        $('.treeTable').treetable({
            initialState: 'expanded',
            indent: 24,
            column: 1
        });
        $('.chartdialog').addClass('clickable').click(function() {
            ChartDialog($(this).parents('tr').data('tt-id'));
        });

        $('.showlist').each(function() {
            $(this).wrap('<a></a>').parent()
            .prop('href', '/list/' + channels[$(this).parents('tr').data('tt-id')].guid);
        });

        $('.editentity')
        .each(function() {
            /* For "Open link in new tab" ... */
            $(this).wrap('<a></a>').parent()
            .prop('href', '/channel/edit/' + channels[$(this).parents('tr').data('tt-id')].entity);
        })
        .click(function(e) {
            e.preventDefault();
            window.location.href = '/channel/edit/' + channels[$(this).parents('tr').data('tt-id')].entity +
                                   '?returnto=' + (views.actual.slug ? '/chart/' + views.actual.slug : '/');
        });
    }

    /**
     *
     */
    $("#d-table > tbody > tr").each(function(id, tr) {
        $(tr).addClass(id % 2 ? 'even' : 'odd');
    });

    /**
     *
     */
    $("#dialog-chart").dialog({
        autoOpen: false,
        width: 750,
        modal: true,
        buttons: {
            '{{Ok}}': function() {
                $(this).dialog('close');
                var chk = $('#c'+$(this).data('id')),
                    old = chk.val(),
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
                p.colorusediff = +$('input:radio[name="color-pos-neg"]:checked').val();
                p.colordiff = $('#d-color-diff').spectrum('get').toHexString();
                p.threshold = +$('#d-color-threshold').val().replace(',', '.');
                p.legend = $('#d-legend').is(':checked');
                p.hidden = $('#d-hidden').is(':checked');
                p.position = +$('#d-position').text();

                p.time1 = SecToTimeStr(TimeStrToSec($('#d-time1').val(), 0));
                p.time2 = SecToTimeStr(TimeStrToSec($('#d-time2').val(), 24));
                p.daylight = $('#d-daylight').is(':checked');
                p.daylight_grace = +$('#d-daylight-grace').val();

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
            notScatter.addClass('disabled');
            notScatter.find('input, select').prop('disabled', true);
        }
        $('input').iCheck('update');
    });

    $('#d-color-use-diff').on('ifToggled', function(e) {
        var checked = $(this).is(':checked');
        $('#d-color-threshold').prop('disabled', checked).spinner('option', 'disabled', checked);
        $('#d-color-diff').spectrum(checked ? 'disable' : 'enable');
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

    $('#d-position-slider').slider({
        min: -100, max: 100,
        slide: function( e, ui ) {
            $('#d-position').text(ui.value);
        }
    });

    $('#d-position-slider .ui-slider-handle')
        /* Style slider handle to acceppt position text */
        .css({ width: '2em', marginLeft: '-1em', textDecoration: 'none', textAlign: 'center' })
        /* Insert separate span to style text */
        .append($('<span/>').prop('id', 'd-position').css({ fontSize: 'xx-small', color: 'gray' }));

    $('#d-daylight').on('ifToggled', function(e) {
        var checked = !$(this).is(':checked');
        $('#d-daylight-grace').prop('disabled', checked).spinner('option', 'disabled', checked);
    });

    /* Inject checkbox for chart auto refresh */
    var cb_refresh = $('<input/>').prop('id', 'cb-autorefresh').prop('type', 'checkbox')
        .prop('checked', !(lscache.get('chart-autorefresh') === false));
    /* Wrapper div for iChecked checkbox */
    $('<div/>').addClass('fr').css('margin', '6px 0 0 6px')
        .append(cb_refresh).appendTo('#preset-wrapper');
    /* iCheck after inserted into wrapper DIV */
    cb_refresh.iCheck({ checkboxClass: 'icheckbox_flat' })
        .on('ifToggled', function(e) {
            /* Remember current state */
            lscache.set('chart-autorefresh', $(this).is(':checked'))
        })
        /* Put the hint onto parent div injected by iCheck around the checkbox */
        .parent().prop('title', '{{ChartAutoRefresh}}');

    $('input.iCheck').iCheck('update');

    $('input.channel').on('ifToggled', function() {
        $('#r'+this.id).toggleClass('checked', this.checked);
        $('#modified').show();
    });

    $('#btn-refresh').button({
        icons: { primary: 'ui-icon-refresh' }, text: false
    }).click(function(event) {
        event.preventDefault();
        updateChart(event.shiftKey);
    });

    $('#btn-permanent').button({
        icons: { primary: 'ui-icon-image' }, text: false
    });

    $('#btn-bookmark').button({
        icons: { primary: 'ui-icon-bookmark' },
        text: false
    });

    $('#treetoggle').click(function() { tree.toggle() });

    $('#btn-load').button({
        icons: { primary: 'ui-icon-folder-open' }, text: false
    }).click(function(event) {
        if (event.shiftKey) {
            /* Shift-Click sets display date to today AND reloads chart */
            $('#btn-reset').trigger('click');
        } else {
            views.load($('#load-delete-view option:selected').val(), true);
        }
    });

    $('#btn-delete').button({
        icons: { primary: 'ui-icon-trash' }, text: false
    }).click(function(event) {
        var option = $('#load-delete-view option:selected'), btn = $(this);

        if (option.val() == '') return;

        if (btn.hasClass('confirmed') == 0) {
            /* Replace text, make red and mark confirmed for next click */
            btn.button({ label: '{{Sure}}?', text: true }).addClass('confirmed');
            /* Reset after 5s */
            setTimeout(function() {
                $('#btn-delete').button({ label: '&nbsp;', text: false }).removeClass('confirmed');
            }, 5000);
        } else {
            btn.button({ label: '&nbsp;', text: false, }).button('disable').removeClass('confirmed');
            $.wait();

            $.ajax({
                type: 'DELETE',
                dataType: 'json',
                url: PVLngAPI + '/view/'+option.val()+'.json'
            }).done(function(data, textStatus, jqXHR) {
                $.pnotify({ type: 'success', text: option.text() + ' {{deleted}}' });
                /* Just delete selected option and clear save name input */
                option.remove();
                $('#load-delete-view').val('').trigger('change');
                $('#saveview').val('');
            }).fail(function(jqXHR, textStatus, errorThrown) {
                $.pnotify({
                    type: textStatus, hide: false,
                    text: jqXHR.responseJSON.message ? jqXHR.responseJSON.message : jqXHR.responseText
                });
            }).always(function() {
                $.wait(false);
                btn.button({ label: '&nbsp;', text: false }).data('confirmed', 0).button('enable');
            });
        }
    }).css('font-weight', 'bold');

    $('#btn-save').button({
        icons: { primary: 'ui-icon-disk' }, text: false
    }).click(function() {
        $(this).button('disable');
        $.wait();

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
        }).done(function (data) {
            views.fetch(function(views) {
                /* Rebuild select */
                views.buildSelect('#load-delete-view', data.slug);
                views.load(data.slug);
                /* Adjust chart title */
                if (chart) chart.setTitle(views.actual.name);
            });
            $('#modified').hide();
            $.pnotify({ type: 'success', text: data.name + ' saved' });
        }).fail(function(jqXHR, textStatus, errorThrown) {
            $.pnotify({
                type: textStatus, hide: false, text: jqXHR.responseText
            });
        }).always(function() {
            $.wait(false);
            $(btn).button('enable');
        });
    });

    views.fetch(function(views) {
        views.buildSelect('#load-delete-view');
        if (!user) {
            $('#public-select').show();
            $('#wrapper').show();
        }
        /* Chart slug provided by URL?, load and collapse tree */
        if (!qs.chart) $('#top-select').show();
        views.load(qs.chart, true);
    });

    /**
     * HTML5 Page Visibility API
     *
     * http://www.sitepoint.com/introduction-to-page-visibility-api/
     * http://www.w3.org/TR/page-visibility
     */
    if (typeof document.hidden != 'undefined') {
        browserPrefix = '';
    } else {
        var browserPrefixes = ['webkit', 'moz', 'ms', 'o'], l = browserPrefixes.length;
        /* Test all vendor prefixes */
        for (var i=0; i<l; i++) {
            if (typeof document[browserPrefixes[i] + 'Hidden'] != 'undefined') {
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
                /* Was longer in background, so the updateTimeout is not set anymore */
                if (!updateTimeout) {
                    /* Check if toDate in chart is today and not in the past */
                    var d = new Date(),
                        today = ('0'+(d.getMonth()+1)).slice(-2) + '/' +
                                ('0'+d.getDate()).slice(-2) + '/' +
                                d.getFullYear();
                    if ($('#cb-autorefresh').is(':checked') && $('#todate').val() >= today) {
                        setTimeout(function() {
                            /* Scroll to navigation as top most visible element */
                            if (chart) pvlng.scroll('#nav');
                            updateChart();
                        },
                        1000);
                    }
                }
            } else {
                windowVisible = false;
            }
            return false;
        });
    }

    shortcut.add('Alt+P', function() { pvlng.changeDates(-1) });
    shortcut.add('Alt+N', function() { pvlng.changeDates(1) });
    shortcut.add('F6',    function() { updateChart() });
    shortcut.add('F7',    function() { updateChart(true) });

    if (user) {
        shortcut.add('F4', function() { tree.toggle() });
    }
});

</script>
