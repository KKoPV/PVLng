/**
 * PVLng - PhotoVoltaic Logger new generation
 *
 * @link       https://github.com/KKoPV/PVLng
 * @link       https://pvlng.com/
 * @author     Knut Kohl <github@knutkohl.de>
 * @copyright  2012 Knut Kohl
 * @license    MIT License (MIT) http://opensource.org/licenses/MIT
 */

var defaults = {
        refresh: 30, // seconds
        color: {
            belowThreshold: 'red',
            aboveThreshold: 'green'
        },
        overshoot: 10
    },

    chartOptions = {
        exporting: false,
        plotOptions: {
           gauge: {
                dataLabels: {
                    borderWidth: 0,
                    y: 45,
                    useHTML: true
                },
                dial: {
                    backgroundColor: 'gray',
                    rearLength: '25%'
                },
                overshoot: defaults.overshoot,
                pivot: {
                    radius: 10,
                    borderWidth: 1,
                    borderColor: 'gray',
                    backgroundColor: {
                        linearGradient: { x1: 0, y1: 0, x2: 1, y2: 1 },
                        stops: [ [0, 'white'], [1, 'gray'] ]
                    }
                }
            }
        },

        pane: {
            startAngle: -135,
            endAngle: 135,
            background: [{
                backgroundColor: {
                    linearGradient: { x1: 0, y1: 0, x2: 0, y2: 1 },
                    stops: [ [0, '#FFF'], [1, '#333'] ]
                },
                borderWidth: 0,
                outerRadius: '109%'
            }, {
                backgroundColor: {
                    linearGradient: { x1: 0, y1: 0, x2: 0, y2: 1 },
                    stops: [ [0, '#333'], [1, '#FFF'] ]
                },
                borderWidth: 1,
                outerRadius: '107%'
            }, {
                /* default background */
            }, {
                backgroundColor: '#DDD',
                borderWidth: 0,
                outerRadius: '105%',
                innerRadius: '103%'
            }]
        },

        tooltip: { enabled: false },
        credits: { enabled: false }
    },

    charts = [],
    timeout;

/**
 *
 */
function updateCharts() {

    clearTimeout(timeout);

    var date = new Date;

    $('input.channel:checked').each(function(chart_id, el) {
        chart_id++;
        el = $(el);

        var t, url = PVLngAPI + 'data/' + el.data('guid') + '.json';
        pvlng.log('Fetch', url);


        $.getJSON(url, { attributes: true, full: true }, function(data) {
            /* pop out 1st row with attributes */
            var attr = data.shift();
/*
            pvlng.log('Attributes', attr);
            pvlng.log('Data', data);
*/
            if (!data[0] || (date.getTime()/1000 - data[0].timestamp) > 600) {
                /* NO data row found or data older than 10 minuts */
                charts[chart_id] = undefined;
                $('#chart-'+chart_id)
                    .empty()
                    .append($('<div/>').addClass('chart-title').html(attr.name))
                    .append($('<div/>').addClass('chart-subtitle').html(attr.description))
                    .append($('<p />').addClass('chart-subtitle').html('{{NoDataAvailable}}'));
            } else if (charts[chart_id] == undefined) {
                var options = $.extend({}, chartOptions, {
                    chart:    { renderTo: 'chart-'+chart_id+'-chart', type: 'gauge' },
                    title:    attr.unit, /* Suppress title */
                    yAxis: {
                        min: attr.valid_from,
                        max: attr.valid_to,
                        plotBands: []
                    },
                    series: [{
                        data: [null],
                        dataLabels: {
                            formatter: function() {
                                var color = this.series.options.threshold !== ''
                                    ? 'color:' + (this.y < this.series.options.threshold ? defaults.color.belowThreshold : defaults.color.aboveThreshold)
                                    : '';
                                return '<div style="text-align:center">' +
                                       '<span style="font-size:125%;'+color+'">' +
                                       Highcharts.numberFormat(this.y, attr.decimals, '{DSEP}', '{TSEP}') +
                                       '</span><br/>' +
                                       '<span style="font-size:110%;color:#A0A0A0">'+attr.unit+'</span>' +
                                       '</div>';
                            }
                        },
                        threshold: attr.threshold /* Store threshold into options for formatter */
                    }]
                });

                $('#chart-'+chart_id)
                    .empty()
                    .append($('<div/>').addClass('chart-title').html(attr.name))
                    .append($('<div/>').addClass('chart-subtitle').html(attr.description))
                    .append($('<div/>').prop('id', 'chart-'+chart_id+'-chart').addClass('chart-inner'));

                if (attr.extra) {
                    /* draw colored plot bands
                       start > end : color
                       > end : color
                       start > : color
                       missing <start> and <end> are replaced by valid_from and valid_to
                    */

                    /* split into bands */
                    $(attr.extra.split("\n")).each(function(id, band) {
                        /* split into from-to and color */
                        var fromto_color = band.trim().split(':');

                        /* split from and to */
                        var fromto = fromto_color[0].trim().split('>');

                        if (fromto[0] == '') {
                            fromto[0] = attr.valid_from;
                        } else if (fromto[0].indexOf('%') != -1) {
                            fromto[0] = fromto[0].replace('%', '');
                            fromto[0] = attr.valid_from + (attr.valid_to - attr.valid_from) * fromto[0] / 100;
                        }

                        if (fromto[1] == '') {
                            fromto[1] = attr.valid_to;
                        } else if (fromto[1].indexOf('%') != -1) {
                            fromto[1] = fromto[1].replace('%', '');
                            fromto[1] = attr.valid_from + (attr.valid_to - attr.valid_from) * fromto[1] / 100;
                        }

                        options.yAxis.plotBands.push({
                            from:  +fromto[0],
                            to:    +fromto[1],
                            color: fromto_color[1]
                        });
                    });
                }

                charts[chart_id] = new Highcharts.Chart(options);
            }

            charts[chart_id] && charts[chart_id].series[0].data[0].update(data[0].data);
        });
    });

    timeout = setTimeout(updateCharts, defaults.refresh * 1000);
}

var clock;

/**
 *
 */
$(function() {

    $('.chart, .chart-big').css({ textAlign: 'center' }).html('<small>{{JustAMoment}}</small>');

    var oTable = $('#tree').dataTable({
        aoColumns: [
            { bVisible: false, asSorting: [ 'asc' ] },
            { bSortable: false, sWidth: '1%' },
            { bSortable: false },
            { bSortable: false, sWidth: '1%' }
        ],
        aaSorting: [[ 0, 'asc' ]]
    });
    oTable.rowReordering();

    Highcharts.setOptions({
        lang: {
            thousandsSep: '{TSEP}',
            decimalPoint: '{DSEP}'
        }
    });

    updateCharts();

    clock = $('#clock');

    setInterval(function() {
        clock.html((new Date).toLocaleString());
    }, 1000);

    $('#togglewrapper').button({
        icons: { primary: 'ui-icon-carat-2-n-s' },
        label: '&nbsp;',
        text: false
    }).click(function() {
        var wrapper = $('#wrapper');
        if (wrapper.animate({ height: 'toggle', opacity: 'toggle' }).is(':visible')) {
            $('html, body').animate({ scrollTop: wrapper.offset().top-3 }, 'slow');
        }
    });

    if ({CHANNELCOUNT} > 0) $('#togglewrapper').trigger('click');

    $('.with-id').button('{ID}' ? 'enable' : 'disable');

    var pressedButton;

    $('input[type=submit]').click(function() {
        /* Remember pressed button */
        pressedButton = $(this);
    });

    $('#form-dashboard').submit(function(e) {
        if (pressedButton.attr('name') != 'delete' || pressedButton.data('confirmed')) {
            /* Not delete button or not confirmed, proceed */
            return;
        }

        e.preventDefault();

        /* Replace text, make red and mark confirmed for next click */
        pressedButton
            .button({ label: '{{Sure}}?' })
            .css({ color: 'red', fontWeight: 'bold' })
            .data('confirmed', 1);

        /* Reset after 5s */
        setTimeout(function() {
            pressedButton
                .button({ label: '{{Delete}}' })
                .css({ color: 'black', fontWeight: 'normal' })
                .data('confirmed', null);
        }, 5000);
    });

});
