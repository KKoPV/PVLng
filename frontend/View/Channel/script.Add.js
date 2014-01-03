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

    $('#typeTable').DataTable({
        bSort: true,
        bLengthChange: false,
        bFilter: true,
        bInfo: false,
        bPaginate: false,
        bJQueryUI: true,
        oLanguage: { sUrl: '/resources/dataTables.'+language+'.json' },
        aoColumns: [
            { 'asSorting': false },
            null,
            null,
            { 'asSorting': false },
            { 'asSorting': false },
            { 'asSorting': false }
        ],
        aaSorting: [[ 1, "asc" ]]
    });

    $('#tplTable').DataTable({
        bSort: false,
        bLengthChange: false,
        bFilter: false,
        bInfo: false,
        bPaginate: false,
        bJQueryUI: true,
        oLanguage: { sUrl: '/resources/dataTables.'+language+'.json' }
    });

});

</script>
