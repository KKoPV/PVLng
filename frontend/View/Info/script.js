<!--
/**
 *
 *
 * @author      Knut Kohl <github@knutkohl.de>
 * @copyright   2012-2013 Knut Kohl
 * @license     GNU General Public License http://www.gnu.org/licenses/gpl.txt
 * @version     1.0.0
 */
-->

<script>

$(function() {

    $.ajaxSetup({
        beforeSend: function setHeader(xhr) {
            xhr.setRequestHeader('X-PVLng-Key', PVLngAPIkey);
        }
    });

    $('#table-info').DataTable({
        bSort: false,
        bLengthChange: false,
        bFilter: false,
        bInfo: false,
        bPaginate: false,
        bJQueryUI: true,
        oLanguage: { sUrl: '/resources/dataTables.'+language+'.json' }
    });

    $('#regenerate').click(function() {
        /* Replace text, make bold red and unbind this click handler */
        $(this).val("{{Sure}}?").css('fontWeight','bold').css('color','red').unbind();
        return false;
    });

    $('#table-stats').DataTable({
        bSort: true,
        bLengthChange: false,
        bFilter: false,
        bInfo: false,
        bPaginate: false,
        bJQueryUI: true,
        oLanguage: { sUrl: '/resources/dataTables.'+language+'.json' },
        aoColumns: [
            null,
            null,
            null,
            null,
            null,
            { asSorting: false },
            null
        ],
        fnFooterCallback: function( nFoot, aData, iStart, iEnd, aiDisplay ) {
            var th = nFoot.getElementsByTagName('th');
            var len = aData.length, re = new RegExp('['+ThousandSeparator+']', 'g'), cnt = 0;
            th[0].innerHTML = len + " {{Channels}}";
            while (len--) cnt += parseInt(aData[len][4].replace(re, ''));
            th[1].innerHTML = $.number(cnt, 0, '', ThousandSeparator);
        },
        fnInitComplete: function() {
            $('.last-reading').each(function(id, el){
                $.getJSON(
                    PVLngAPI + 'data/' + $(el).data('guid') + '.json',
                    {
                        attributes: true, /* need decimals for formating */
                        period:     'readlast'
                    },
                    function(data) {
                        if (data.length < 2) return;
                        var attr = data.shift();
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
