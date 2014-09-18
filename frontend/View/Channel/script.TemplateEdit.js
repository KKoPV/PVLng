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
 *
 */
$(function() {

    var oTable = $('#channels').dataTable({
        bPaginate: false,
        bLengthChange: false,
        bFilter: false,
        bSort: false,
        bInfo: false,
        bJQueryUI: true,
        bAutoWidth: false,
        aoColumns: [
            null,
            null,
            null,
            null,
            { sWidth: '99%' },
            null,
            null,
            { sWidth: '1%', sClass: 'details-control' }
        ]
    });

    /* Add event listener for opening and closing details */
    $('#channels tbody').on('click', 'td.details-control', function () {
        var nTr = $(this).parents('tr')[0];
        if ( oTable.fnIsOpen(nTr) ) {
            /* This row is already open - close it */
            /* Get content of displayed row and move back into cell */
            $(this).html($('span', $(nTr).next()).hide());
            $(nTr).removeClass('shown');
            oTable.fnClose(nTr);
        } else {
            /* Open this row */
            $(nTr).addClass('shown');
            oTable.fnOpen(nTr, $(this).find('span').show(), 'details');
        }
    });
});

</script>
