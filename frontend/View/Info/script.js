<script>
/**
 *
 *
 * @author      Knut Kohl <github@knutkohl.de>
 * @copyright   2012-2013 Knut Kohl
 * @license     GNU General Public License http://www.gnu.org/licenses/gpl.txt
 * @version     1.0.0
 */

var ChartOptions = {
    chart: { type: 'pie' },
    title: false,
    exporting: false,
    credits: false,
    tooltip: false,
    plotOptions: { pie: { dataLabels: { enabled: true } } },
    /* Init empty dataset */
    series: [{ data: null }]
};

$(function() {

    $.ajaxSetup({
        beforeSend: function setHeader(xhr) {
            xhr.setRequestHeader('X-PVLng-Key', PVLngAPIkey);
        }
    });

    $('#table-info, #table-cache').DataTable({
        bSort: false
    });

    $('#regenerate').click(function() {
        /* Replace text, make bold red and unbind this click handler */
        $(this).val("{{Sure}}?").css('fontWeight','bold').css('color','red').unbind();
        return false;
    });

    $.extend( jQuery.fn.dataTableExt.oSort, {
        "numeric-span-pre": function ( a ) {
            return +a.match(/>(.*?)</)[1];
        },

        "numeric-span-asc": function( a, b ) {
            return ((a < b) ? -1 : ((a > b) ? 1 : 0));
        },

        "numeric-span-desc": function(a,b) {
            return ((a < b) ? 1 : ((a > b) ? -1 : 0));
        }
    });

    $('#table-stats').DataTable({
        bLengthChange: false,
        bFilter: false,
        bInfo: false,
        bPaginate: false,
        aoColumns: [
            null,
            null,
            { sType: 'numeric-span', sWidth: '1%' },
            { bSortable: false },
            { sWidth: '1%' }
        ],

        fnFooterCallback: function( nFoot, aData, iStart, iEnd, aiDisplay ) {
            var th = nFoot.getElementsByTagName('th'), len = aData.length, cnt = 0;
            th[0].innerHTML = len + " {{Channels}}";
            while (len--) cnt += +aData[len][2].match(/>(.*?)</)[1];
            th[1].innerHTML = $.number(cnt, 0, '', ThousandSeparator);
        }
    });

    /* Load charts on Tab activation, not before */
    var tabs2 = tabs3 = tabs4 = false;

    $('.ui-tabs').on('tabsactivate', function(e, ui) {
        if (ui.newPanel.selector == '#tabs-2' && !tabs2) {
            tabs2 = true;
            var options = ChartOptions;
            options.series[0].data = [
                <!-- BEGIN STATS --><!-- IF {READINGS} -->
                ['{NAME}<!-- IF {DESCRIPTION} --> ({DESCRIPTION})<!-- ENDIF -->', {raw:READINGS}],
                <!-- ENDIF --><!-- END -->
            ];

            if (options.series[0].data.length == 0) {
                $('#stats-chart').hide();
                return;
            }

            options.plotOptions.pie.dataLabels.formatter = function() {
                return '<b>'+this.point.name+'</b>: '
                     + Highcharts.numberFormat(this.point.y, 0, DecimalSeparator, ThousandSeparator)
                     + ' ('
                     + Highcharts.numberFormat(this.point.percentage, 2, DecimalSeparator, ThousandSeparator)
                     + ' %)'
            };

            /* Use width of 1st visible tab container (#tab-1) to set chart width */
            $('#stats-chart').width($('#tabs-1').width()).highcharts(options);

            $('.last-reading', '#table-stats').each(function(id, el) {
                $.getJSON(
                    PVLngAPI + 'data/' + $(el).data('guid') + '.json',
                    {
                        attributes: true, /* need decimals for formating */
                        period:     'readlast'
                    },
                    function(data) {
                        if (data.length < 2) return;
                        var attr = data.shift(), val;
                        if (attr.numeric) {
                            $(el).number(data[0].data, attr.decimals, DecimalSeparator, ThousandSeparator);
                        } else {
                            $(el).html(data[0].data != "" ? data[0].data : '<empty>');
                        }
                        $(el).addClass('ok');
                    }
                ).fail(function(jqXHR) {
                    $(el).html('<small>'+jqXHR.responseJSON.message+'</small>').addClass('fail');
                });
            });
        } else if (ui.newPanel.selector == '#tabs-3' && !tabs3) {
            tabs3 = true;
            var options = ChartOptions;
            options.series[0].data = [
                ['{{DatabaseSize}}', {DATABASESIZE}],
                ['{{DatabaseFree}}', {DATABASEFREE}]
            ];
            options.plotOptions.pie.dataLabels.formatter = function() {
                return '<b>'+this.point.name+'</b>: '
                     + Highcharts.numberFormat(this.point.y, 1, DecimalSeparator, ThousandSeparator)
                     + ' MB ('
                     + Highcharts.numberFormat(this.point.percentage, 2, DecimalSeparator, ThousandSeparator)
                     + ' %)'
            };

            /* Use width of 1st visible tab container (#tab-1) to set chart width */
            $('#db-chart').width($('#tabs-1').width()).highcharts(options);
        } else if (ui.newPanel.selector == '#tabs-4' && !tabs4) {
            tabs4 = true;
            var hits = {raw:CACHEHITS}, misses = {raw:CACHEMISSES};

            if (!hits || !misses) {
                $('#cache-chart').hide();
                return;
            }

            var options = ChartOptions;
            options.series[0].data = [
                ['{{CacheHits}}',   hits],
                ['{{CacheMisses}}', misses]
            ];
            options.plotOptions.pie.dataLabels.formatter = function() {
                return '<b>'+this.point.name+'</b>: '
                     + Highcharts.numberFormat(this.point.y, 0, DecimalSeparator, ThousandSeparator)
                     + ' ('
                     + Highcharts.numberFormat(this.point.percentage, 2, DecimalSeparator, ThousandSeparator)
                     + ' %)'
            };

            /* Use width of 1st visible tab container (#tab-1) to set chart width */
            $('#cache-chart').width($('#tabs-1').width()).show().highcharts(options);
        }
    });
});
</script>
