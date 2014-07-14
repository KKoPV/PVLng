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

/**
 *
 */
$(function() {

    $('#typeTable').DataTable({
        bLengthChange: true,
        aLengthMenu: [ [20, 50, 100, -1], [20, 50, 100, '{{All}}'] ],
        iDisplayLength: 20,
        bFilter: true,
        bInfo: true,
        bPaginate: true,
        sPaginationType: 'full_numbers',
        aoColumns: [
            { bSortable: false, sWidth: '1%' },
            null,
            null,
            null,
            null,
            { bSortable: false }
        ],
        aaSorting: [[ 1, "asc" ]],
        fnInitComplete: function() {
            pvlng.addClearSearchButton('typeTable', '{{ClearSearch}}');
            $('select', '#typeTable_wrapper').select2();
        }
    });

    $('#tplTable').DataTable({
        bFilter: true,
        bInfo: true,
        aoColumns: [
            { asSorting: false, sWidth: '1%' },
            null,
            { asSorting: false, sClass: 'p50' }
        ],
        aaSorting: [[ 1, "asc" ]],
        fnInitComplete: function() {
            pvlng.addClearSearchButton('tplTable', '{{ClearSearch}}');
        }
    });

});

</script>
