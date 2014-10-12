<script>
/**
 *
 *
 * @author      Knut Kohl <github@knutkohl.de>
 * @copyright   2012-2014 Knut Kohl
 * @license     MIT License (MIT) http://opensource.org/licenses/MIT
 * @version     1.0.0
 */

function formatResult( option ) {
    return formatSelection(option) + option.text;
}

function formatSelection( option ) {
    return "<img class='channel-icon' src='" + option.id + "'>";
}

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

    /* Run last to overwrite default select2 */
    pvlng.onFinished.add(function() {
        $("#icon-select").select2({
            formatResult: formatResult,
            formatSelection: formatSelection
        });
    });

});

</script>
