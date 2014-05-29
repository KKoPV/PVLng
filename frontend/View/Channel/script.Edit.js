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

    $('#dataTable').DataTable({
        bPaginate: false,
        bLengthChange: false,
        bFilter: false,
        bSort: false,
        bInfo: false,
        bJQueryUI: true,
        bAutoWidth: false
    });

    $('#add2tree').on('ifChanged', function(){
        $('#tree').prop('disabled', !this.checked)
    });

});

</script>
