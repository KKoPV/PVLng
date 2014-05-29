<script>
/**
 *
 *
 * @author      Knut Kohl <github@knutkohl.de>
 * @copyright   2012-2013 Knut Kohl
 * @license     GNU General Public License http://www.gnu.org/licenses/gpl.txt
 * @version     1.0.0
 */

$(function() {

    $.ajaxSetup({
        beforeSend: function setHeader(xhr) {
            xhr.setRequestHeader('X-PVLng-Key', PVLngAPIkey);
        }
    });

    $('#table-info').DataTable({
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
        },
        fnInitComplete: function() {
            $('.last-reading').each(function(id, el) {
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
        }
    });
});
</script>
