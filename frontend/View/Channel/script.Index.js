<script>
/**
 *
 *
 * @author      Knut Kohl <github@knutkohl.de>
 * @copyright   2012-2013 Knut Kohl
 * @license     GNU General Public License http://www.gnu.org/licenses/gpl.txt
 * @version     1.0.0
 */
</script>

<script src="/js/jquery.treetable.js"></script>

<script>
(function($) {
/**
 * http://legacy.datatables.net/release-datatables/examples/api/multi_filter_select.html
 *
 * Function: fnGetColumnData
 * Purpose:  Return an array of table values from a particular column.
 * Returns:  array string: 1d data array
 * Inputs:   object:oSettings - dataTable settings object. This is always the last argument past to the function
 *           int:iColumn - the id of the column to extract the data from
 * Author:   Benedikt Forchhammer <b.forchhammer /AT\ mind2.de>
 */
$.fn.dataTableExt.oApi.fnGetColumnData = function( oSettings, iColumn ) {
    // set up data array
    var asResultData = new Array();

    // check that we have a column id
    if (typeof iColumn != 'undefined') {

        // list of rows which we're going to loop through, use only filtered rows
        var aiRows = oSettings.aiDisplayMaster, aData, sValue;

        for (var i=0, c=aiRows.length; i<c; i++) {
            aData = this.fnGetData(aiRows[i]);
            sValue = aData[iColumn];

            // ignore empty values
            if (sValue.length == 0) continue;

            // ignore unique values
            else if (jQuery.inArray(sValue, asResultData) > -1) continue;

            // else push the value onto the result data array
            else asResultData.push(sValue);
        }
    }

    return asResultData.sort();
}}(jQuery));

function fnCreateSelect( aData ) {
    var r='<select><option value=""></option>', i, iLen=aData.length;
    for ( i=0 ; i<iLen ; i++ ) {
        r += '<option value="'+aData[i]+'">'+aData[i]+'</option>';
    }
    return r+'</select>';
}

/**
 *
 */
$(function() {

    var oTable = $('#entities').dataTable({
        bLengthChange: true,
        aLengthMenu: [ [20, 50, 100, -1], [20, 50, 100, '{{All}}'] ],
        iDisplayLength: 20,
        bFilter: true,
        bInfo: true,
        bPaginate: true,
        sPaginationType: 'full_numbers',
        aoColumnDefs: [
            /* Adjust columns with icons */
            { bSortable: false, aTargets: [ 6 ] },
            { sWidth: "1%", aTargets: [ 5, 6 ] }
        ],
        aaSorting: [[ 0, 'asc' ]],
        fnInitComplete: function() {
            pvlng.addClearSearchButton('entities', '{{ClearSearch}}');
            $('select', '#entities_wrapper').select2();
        }
    });

    /* Add a select menu for 2nd to 5th TH element in the table footer */
    $('#entities tfoot th').each(function(i) {
        if (i>=1 && i<=4) {
            this.innerHTML = fnCreateSelect(oTable.fnGetColumnData(i));
            $('select', this).change(function() {
                oTable.fnFilter($(this).val(), i);
            });
        }
    } );

    $("#dialog-confirm").dialog({
        autoOpen: false,
        resizable: false,
        width: 480,
        modal: true,
        buttons: {
            '{{Delete}}': function() { $(this).data('form').submit(); },
            '{{Cancel}}': function() { $(this).dialog('close'); }
        }
    });

    $('.delete-form').submit(function(){
        currentForm = this;
        $('#dialog-confirm').data('form', this).dialog('open');
        return false;
    });

});

</script>
