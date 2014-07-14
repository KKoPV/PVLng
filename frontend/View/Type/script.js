<script>
/**
 *
 *
 * @author      Knut Kohl <github@knutkohl.de>
 * @copyright   2012-2014 Knut Kohl
 * @license     MIT License (MIT) http://opensource.org/licenses/MIT
 * @version     1.0.0
 */

$(function() {

    var oTable = $('.dataTable').dataTable({
        bFilter: true,
        bInfo: true,
        aoColumns: [
            { sClass: 'r', sWidth: '1%' },
            null,
            null,
            null,
            null,
            null,
            { bSortable: false, sWidth: '1%' },
            { sClass: 'details-control', bSortable: false }
        ],
    });

    /* Add event listener for opening and closing details */
    $('.dataTable tbody').on('click', 'td.details-control', function () {
        var nTr = $(this).parents('tr')[0];
        if ( oTable.fnIsOpen(nTr) ) {
            /* This row is already open - close it */
            $(nTr).removeClass('shown');
            oTable.fnClose(nTr);
        } else {
            /* Open this row */
            $(nTr).addClass('shown');
            oTable.fnOpen(nTr, $('<p/>').html($(this).find('span').html()), 'description');
        }
    });

});

</script>
