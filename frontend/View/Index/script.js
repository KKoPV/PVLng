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

var ChartHeight = 564;

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

function View( slug ) {
    this.slug = slug;

    this.fetch = function( el, callback ) {
        if (el.length == 0) return;
        $.getJSON(
            PVLngAPI + '/views.json',
            { select: true },
            function(data) {
                el.empty();
                $(data).each(function(id, view) {
                    $('<option/>').val(view.slug).text(view.name).appendTo(el);
                });
                if (typeof callback != 'undefined') callback(el);
            }
        );
    };

    this.load = function( collapse ) {
        if (this._slug == '') return;

        oTable.fnProcessingIndicator(true);

        $.getJSON(
            PVLngAPI + '/view/' + this._slug + '.json',
            {},
            function(data) {
                var expanded = tree.expanded, preset;
                if (!expanded) tree.toggle(true);

                /* Uncheck all channels and ... */
                $('tr.channel').removeClass('checked');
                $('input.channel').iCheck('uncheck');
                /* ... re-check all channels in view */
                $.each(data, function (id, p) {
                    if (id == 'p') {
                        preset = p;
                    } else {
                        $('#c'+id).val(p).iCheck('check');
                    }
                });

                /* Re-arrange channels in collapsed tree */
                if (((typeof collapse != 'undefined') && collapse) || !expanded) {
                    tree.toggle(false);
                }

                $('#preset').val(preset).trigger('change');
            }
        ).fail(function () {
            oTable.fnProcessingIndicator(false);
        });
    };

};

Object.defineProperty( View.prototype, 'slug', {
    set: function( slug ) {
        if (typeof slug != 'string') return;
        this._slug = slug.trim();
        $('#loaddeleteview').val(slug);
        this.name = slug ? $('#loaddeleteview option:selected').text() : '';
        $('#saveview').val(this._name);
    },
    get: function() {
        return this._slug;
    }
});

Object.defineProperty( View.prototype, 'name', {
    set: function( name ) {
        this._name = name.trim();
    },
    get: function() {
        return this._name;
    }
});

/**
 *
 */
var
    qs, view,
    chart = false, timeout,

    options = {
        chart: {
            renderTo: 'chart',
            paddingRight: 15,
            alignTicks: false,
            zoomType: 'x',
            resetZoomButton: {
                position: { x: 0, y: -50 }
            },
            events: {
                selection: function(event) {
                    setTimeout(setExtremes, 100);
                }
            }
        },
        title: { text: '' },
        plotOptions: {
            series: {
                point: {
                    events: {
                        click: function() {
                            if (confirm('Do you really want delete this reading value?\n\n '+
                                (new Date(this.x).toLocaleString().replace(' 00:00', ''))+' : '+this.y)) {

                                var point = this,
                                    url = PVLngAPI + 'data/' +
                                          point.reading.guid + '/' +
                                          point.reading.timestamp + '.json';
                                _log(url);

                                $.ajax({
                                    dataType: 'json',
                                    url: url,
                                    type: 'DELETE',
                                    success: function(data) {
                                        point.remove();
                                    },
                                    error: function(data) {
                                        alert(data.responseJSON.message);
                                    }
                                });
                            }
                        }
                    }
                },
                turboThreshold: 0
            },
            line: {
                marker: { enabled: false }
            },
            spline: {
                marker: { enabled: false },
            },
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
            bar: {
                groupPadding: 0.1
            }
        },
        xAxis : {
            type: 'datetime'
        },
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
                       Highcharts.dateFormat('%a. ',this.x) +
                       (new Date(this.x).toLocaleString()).replace(' 00:00:00', '').replace(':00', '') +
                       '</th></tr></thead><tbody>' + body + '</tbody></table>'
                     : null;
            },
            borderWidth: 0,
            shadow: true,
            crosshairs: true,
            shared: true
        },
        loading: {
            labelStyle: {
                top: '40%',
                fontSize: '200%'
            }
        },
/*
        exporting: {
            buttons: {
                contextButton: {
                    menuItems: [{
                        text: 'Export to PNG (small)',
                        onclick: function() {
                            this.exportChart({
                                width: 250
                            });
                        }
                    }, {
                        separator: true
                    }, {
                        text: 'Export to PNG (large)',
                        onclick: function() {
                            this.exportChart();
                        },
                        separator: false
                    }]
                }
            }
        }
*/
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
function ChartDialog( id, name ) {
    /* get stringified settings */
    var p = new presentation($('#c'+id).val());
    /* set dialog properties */
    /* find the radio button with the axis value and check it */
    $('input[name="d-axis"][value="' + p.axis + '"]').prop('checked', true);
    $('#d-type').val(p.type);
    $('#d-cons').prop('checked', p.consumption);
    $('input[name="d-width"][value="' + p.width + '"]').prop('checked', true);
    $('#d-min').prop('checked', p.min);
    $('#d-max').prop('checked', p.max);
    $('#d-last').prop('checked', p.last);
    $('#d-style').val(p.style);
    $('#d-color').val(p.color);
    $('#d-color').spectrum('set', p.color);
    $('#d-color-use-neg').prop('checked', p.coloruseneg);
    $('#d-color-neg').val(p.colorneg);
    $('#d-color-neg').spectrum('set', p.colorneg);
    $('#d-color-neg').spectrum($('#d-color-use-neg').is(':checked') ? 'enable' : 'disable');
    $('#d-threshold').val(p.threshold);
    $('#d-threshold').prop('disabled', !$('#d-color-use-neg').is(':checked'));

    $('input').iCheck('update');
    $('#d-type').trigger('change');

    /* set the id into the dialog for onClose to write data back */
    $('#dialog-chart').data('id',id).dialog('option','title',name).dialog('open');
}

/**
 *
 */
var channels = [];

/**
 * Scale timestamps down to full minute, hour, day, week, month, quarter or year
 */
var xAxisResolution = {
    i: 60,
    h: 60 * 60,
    d: 60 * 60 * 24,
    w: 60 * 60 * 24 * 7,
    m: 60 * 60 * 24 * 30,
    q: 60 * 60 * 24 * 90,
    y: 60 * 60 * 24 * 360
};

var lastChanged = (new Date).getTime() / 1000 / 60,
    inUpdate = false;

/**
 *
 */
function updateChart( forceUpdate ) {

    if (inUpdate) return;

    inUpdate = true;

    clearTimeout(timeout);

    if (view.slug) {
        /* Provide permanent link only for logged in user and not embedded view level 2 */
        var from = $('#from').val(), to = $('#to').val(),
            date = (from == to)
                 ? 'date=' + $('#fromdate').val()
                 : 'from=' + $('#fromdate').val() + '&to=' + $('#todate').val();

        $('#btn-permanent').button({
            label: view.name + ' ' + from + ' | {strip_tags:TITLE}',
            disabled: (view.slug == '')
        }).prop('href', '/chart/' + view.slug + encodeURI('?' + date));

        $('#btn-bookmark').button({
            label: view.name + ' | {strip_tags:TITLE}',
            disabled: (view.slug == '')
        }).prop('href', '/chart/' + view.slug);
    }

    var ts = (new Date).getTime(),
        channels_new = [], yAxisMap = [], yAxis = [],
        channel, channel_clone, buffer = [],
        period_count = +$('#periodcnt').val(),
        period = $('#period').val(),
        res;

    /* reset consumption and costs data */
    $('.minmax, .consumption, .costs, #costs').each(function(id, el) {
        $(el).html('');
    });

    /* find active channels, map and sort axis */
    $('input.channel:checked').each(function(id, el) {
        channel = new presentation($(el).val());
        channel.id = $(el).data('id');
        channel.name = $('<div/>').html($(el).data('name')).text();
        channel.guid = $(el).data('guid');
        channel.unit = $(el).data('unit');
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
    if (channels_new.length == 0) {
        inUpdate = false;
        return;
    }

    _log('Channels:', channels_new);
    _log('yAxis:', yAxis);

    /* check for changed channels */
    var changed = false, now = (new Date).getTime() / 1000 / 60;

    /* renew chart at least each half hour to auto adjust axis ranges by Highcharts */
    if (forceUpdate || channels_new.length != channels.length || now - lastChanged > 30) {
        changed = true;
        channels = channels_new;
        lastChanged = now;
    } else {
        for (var i=0, l=channels_new.length; i<l; i++) {
            if (JSON.stringify(channels_new[i]) != JSON.stringify(channels[i])) {
                changed = true;
                channels = channels_new;
            }
        }
    }

    switch(period) {
        case 'd':  res = xAxisResolution['h'];  break;
        case 'w':  res = xAxisResolution['d'];  break;
        case 'm':  res = xAxisResolution['w'];  break;
        case 'q':  res = xAxisResolution['m'];  break;
        case 'y':  res = xAxisResolution['q'];  break;
        default:   res = xAxisResolution['i'];
    }

    if (changed) {
        /* use UTC for timestamps with a period >= day to avoid wrong hours in hint */
        Highcharts.setOptions({ global: { useUTC: (res >= xAxisResolution.d) } });

        /* happens also on 1st call! */
        options.yAxis = yAxis;

        /* (re)create chart */
        options.exporting = { filename: view.slug };
        chart = new Highcharts.Chart(options);
    }

    var f = $('#from').val(), t = $('#to').val();
    if (f != t) f += ' - ' + t;
    chart.setTitle({ text: $('<div/>').html(view.name).text() }, { text: f });

    var loading = channels.length;
    chart.showLoading('- ' + loading + ' -');

    var series = [], costs = 0;

    /* get data */
    $(channels).each(function(id, channel) {

        $('#s'+channel.id).show();

        var t, url = PVLngAPI + 'data/' + channel.guid + '.json';
        _log('Fetch', url);

        $.getJSON(
            url,
            {
                attributes: true,
                full:       true,
                start:      $('#fromdate').val(),
                end:        $('#todate').val() + '+1day',
                period:     (channel.type != 'scatter') ? period_count + period : '',
                _ts:        (new Date).getTime()
            },
            function(data) {
                /* pop out 1st row with attributes */
                attr = data.shift();
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
                    id: channel.id,
                    decimals: attr.decimals,
                    unit: attr.unit,
                    color: channel.color,
                    type: channel.type,
                    yAxis: channel.axis,
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
                    var ts = Math.round(row.timestamp / res) * res * 1000;
                    var reading = { guid: attr.guid, timestamp: row.timestamp };
                    if ($.isNumeric(row.data)) {
                        if (channel.type == 'areasplinerange') {
                            serie.data.push({ x: ts, low: row.min, high: row.max, reading: reading });
                        } else if (channel.consumption) {
                            serie.data.push({ x: ts, y: row.consumption, reading: reading });
                        } else {
                            serie.data.push({ x: ts, y: row.data, reading: reading });
                        }
                    } else {
                        serie.data.push({
                            x: ts,
                            y: 0,
                            name: row.data,
                            reading: reading
                        });
                    }
                });

                if (channel.type != 'areasplinerange' && (channel.min || channel.max)) {
                    serie = setMinMax(serie, channel);
                }

                _log('Serie', serie);

                series[id] = serie;

                if ('{INDEX_NOTIFYLOAD}') $.pnotify({
                    type: 'success',
                    text: attr.name + ' loaded'
                });
            }
        ).fail(function(jqXHR, textStatus, error) {
            $.pnotify({
                type: textStatus,
                text: error + "\n" + (jqXHR.responseJSON.message ? jqXHR.responseJSON.message : jqXHR.responseText),
                hide: false,
                sticker: false
            });

            /* Set pseudo channel */
            series[id] = {};

        }).always(function(data, status) {

            $('#s'+channel.id).hide();

            /* Force redraw */
            chart.hideLoading();
            if (--loading) chart.showLoading('- ' + (loading) + ' -');

            /* check real count of elements in series array! */
            var completed = series.filter(function(a){ return a !== undefined }).length;
            _log(completed+' series completed');

            /* check if all getJSON() calls finished */
            if (completed != channels.length) return;

            $.pnotify({
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
                        chart.series[sid++].setData(serie.data, false);
                    }
                });
            }

            chart.hideLoading();
            chart.redraw();

            setExtremes();
            resizeChart();

            oTable.fnProcessingIndicator(false);
            inUpdate = false;

            if (RefreshTimeout > 0) {
                timeout = setTimeout(updateChart, RefreshTimeout*1000);
            }

        });
    });
}

var resizeTimeout;

/**
 *
 */
function resizeChart() {
    if (!chart) return;
    clearTimeout(resizeTimeout);
    /* Resize chart correct into parent container */
    var c = $('#chart')[0];
    chart.setSize(c.offsetWidth, c.offsetHeight);
}

/**
 *
 */
var oTable;

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

    options.chart.height = qs.ChartHeight || ChartHeight;

    $(window).resize(function() {
        resizeTimeout = setTimeout(resizeChart, 500);
    });

    if (language != "en") {
        $.datepicker.setDefaults($.datepicker.regional[language]);
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
        global: {
            alignTicks: false
        },
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
        bInfo: false,
        bLengthChange: false,
        bPaginate: false,
        bProcessing: true,
        bJQueryUI: true,
        sDom: ' <"H"r>t<"F">',
        oLanguage: { sUrl: '/resources/dataTables.'+language+'.json' },
        fnInitComplete: function() {
            /* Init treetable AFTER databale is ready */
            var tt = $('.treeTable').treetable({
                initialState: 'expanded',
                indent: 24,
                column: 1,
                onInitialized: function() {
                    if (view.slug) {
                        view.load();
                        updateChart();
                        tree.toggle(false);
                    }
                }
            });
            /* Public view builds no treetable! */
            if (tt.length == 0) updateChart();
        }
    });

    /**
     *
     */
    $('#preset').change(function() {
        var preset = ($('#preset').val() || '').match(/(\d+)(\w+)/);

        if (!preset) {
            $('#periodcnt').val(1);
            $('#period').val('');
        } else {
            var from = new Date($("#from").datepicker('getDate'));
            switch (preset[2]) {
                case 'd': /* day - set start to 1st day of month */
                    from.setDate(1);
                    break;
                case 'w': /* week - set start to 1st day of month */
                    from.setDate(1);
                    break;
                case 'm': /* month - set start to 1st day of year */
                    from.setDate(1);
                    from.setMonth(0);
                    break;
            }
            $("#from").datepicker('setDate', from);
            $('#periodcnt').val(preset[1]);
            $('#period').val(preset[2]);
        }

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
                p = new presentation();
                p.axis = +$('input[name="d-axis"]:checked').val();
                p.type = $('#d-type').val();
                p.consumption = $('#d-cons').is(':checked');
                p.style = $('#d-style').val();
                p.width = +$('input[name="d-width"]:checked').val();
                p.min = $('#d-min').is(':checked');
                p.max = $('#d-max').is(':checked');
                p.last = $('#d-last').is(':checked');
                p.color = $('#d-color').spectrum('get').toHexString();
                p.coloruseneg = $('#d-color-use-neg').is(':checked');
                p.colorneg = $('#d-color-neg').spectrum('get').toHexString();
                p.threshold = +$('#d-threshold').val().replace(',', '.');
                $('#c'+$(this).data('id')).val(p.toString());
                $(this).dialog('close');
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
        $('.not-bar, .not-scatter').show();
        if (this.value == 'bar') {
            $('.not-bar').hide();
        } else if (this.value == 'scatter') {
            $('.not-scatter').hide();
        }
    });

    $('#d-color-use-neg').on('ifToggled', function(e) {
        var checked = $(this).is(':checked');
        $('#d-threshold').prop('disabled', !checked);
        $('#d-color-neg').spectrum(checked ? 'enable' : 'disable');
    });

    $('input').iCheck('update');

    $('input.channel').on('ifToggled', function() {
        $('#r'+this.id).toggleClass('checked', this.checked);
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
        icons: { primary: 'ui-icon-carat-1-n' },
        label: '&nbsp;',
        text: false
    }).click(function(event) {
        event.preventDefault();
        $(this).button({
            icons: { primary: 'ui-icon-carat-1-' + ($('#wrapper').is(':visible')?'s':'n') },
            text: false
        });
        $('#wrapper').animate( { height: 'toggle', opacity: 'toggle' } );
    });

    $('#top-load-view').change(function() {
        view.slug = $('#top-load-view option:selected').val();
        view.load(true);
    });

    $('#btn-load').button({
        icons: { primary: 'ui-icon-folder-open' },
        label: '&nbsp;',
        text: false
    }).click(function(event) {
        view.slug = $('#loaddeleteview option:selected').val();
        view.load();
        $('#wrapper').show();
    });

    $('#btn-delete').button({
        icons: { primary: 'ui-icon-trash' },
        label: '&nbsp;',
        text: false
    }).click(function(event) {
        if ($(this).data('confirmed') == 0) {
            /* Replace text, make bold and mark confirmed for next click */
            $(this).button({ label: '{{Sure}}?', text: true }).data('confirmed', 1);
            /* Reset after 5s */
            setTimeout(
                function() {
                    $('#btn-delete').button({ label: '&nbsp;', text: false }).data('confirmed', 0);
                },
                5000
            );
        } else {
            $(this).button({ label: '&nbsp;', text: false }).data('confirmed', 0);
            var option = $('#loaddeleteview option:selected');
            if (option.val() == '') return;

            var btn = $(this);
            view.slug = option.val();

            btn.button('disable');
            $(document.body).addClass('wait');

            $.ajax({
                type: 'DELETE',
                dataType: 'json',
                url: PVLngAPI + '/view/'+view.slug+'.json'
            }).done(function(data, textStatus, jqXHR) {
                $.pnotify({ type: 'success', text: view.name + ' deleted' });
                view.slug = '';
                /* Delete select option */
                option.remove();
            }).fail(function(jqXHR, textStatus, errorThrown) {
                $.pnotify({
                    type: textStatus,
                    text: jqXHR.responseText,
                    hide: false,
                    sticker: false
                });
            }).always(function() {
                $(document.body).removeClass('wait');
                btn.button('enable');
            });
        }
    }).css('font-weight', 'bold').css('color', 'red');

    $('#btn-save').button({
        icons: { primary: 'ui-icon-disk' },
        label: '&nbsp;',
        text: false
    }).click(function(event) {
        event.preventDefault();
        /* Save view */
        var btn = this,
            data = {
            name: $('#saveview').val(),
            data: { p: $('#preset').val() },
            public: $('#public').is('checked') ? 1 : 0
        };

        $('input.channel:checked').each(function(id, el) {
            data.data[$(el).data('id')] = $(el).val();
        });

        $(this).button('disable');
        $(document.body).addClass('wait');

        $.ajax({
            type: 'PUT',
            dataType: 'json',
            url: PVLngAPI + '/view.json',
            processData: false,
            contentType: 'application/json',
            data: JSON.stringify(data)
        }).done(function (data, textStatus, jqXHR) {
            $.pnotify({ type: 'success', text: data.name + ' saved' });
            view.fetch($('#loaddeleteview'), data.slug);
        }).fail(function(jqxhr, textStatus, errorThrown) {
            $.pnotify({
                type: textStatus,
                text: jqxhr.responseText,
                hide: false,
                sticker: false
            });
        }).always(function() {
            $(document.body).removeClass('wait');
            $(btn).button('enable');
        });
    });

    shortcut.add('Alt+P', function() { changeDates(-1); });
    shortcut.add('Alt+N', function() { changeDates(1); });
    shortcut.add('F3',    function() { $('#togglewrapper').click(); });
    shortcut.add('F4',    function() { tree.toggle(); });
    shortcut.add('F6',    function() { updateChart(); });
    shortcut.add('F7',    function() { updateChart(true); });

    view = new View();

    view.fetch($('#top-load-view'), function(el) {
        el.find('option').clone().appendTo('#loaddeleteview');
        $('#top-select').toggle(!!user);
        /* Chart slug provided by URL, load and collapse tree */
        view.slug = qs.chart;
        view.load(true);
    });

});

</script>
