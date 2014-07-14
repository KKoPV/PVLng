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

/**
 *
 */
$(function() {

    $('#entities').DataTable({
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
