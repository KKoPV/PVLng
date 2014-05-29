<script>
/**
 *
 *
 * @author      Knut Kohl <github@knutkohl.de>
 * @copyright   2012-2014 Knut Kohl
 * @license     MIT License (MIT) http://opensource.org/licenses/MIT
 * @version     1.0.0
 */

var UpdateRowStateActive = true;

function UpdateRowState() {
    if (!UpdateRowStateActive) return;

    $('#table-times tbody tr').each(function(id, tr) {
        id = $(tr).data('id');
        var t = $('#time-'+id),
            i = $('#icon-'+id),
            p = $('#price-'+id),
            c = $('#comment-'+id),
            ck = false;

        /* At least one weekday must be checked */
        $('.weekday-'+id).each(function(id, el) {
            if ($(el).is(':checked')) { ck = true; return }
        });

        i.prop('src', '/images/ico/' + ((t.val() && ck && p.val()) ? 'tick' : 'minus') + '_shield.png');
        t.prop('required', (ck || p.val()) ? 'required' : '');
        p.prop('required', (t.val() || ck) ? 'required' : '');
        p.prop('disabled', !(t.val() || ck || p.val()));
        c.prop('disabled', !(t.val() || ck || p.val()));
    });
}

/**
 *
 */
$(function() {

    /**
     * Adjust checkboxes and weekday names
     */
    $('.icheckbox_flat').css({ float: 'left', marginRight: '.5em' });
    $('#table-times label').css({ float: 'left', marginRight: '1.5em' });

    UpdateRowState();

    $('#table-times').DataTable({
        bSort: false,
        aoColumnDefs: [
            /* Adjust columns with icons */
            { sWidth: "1%", aTargets: [ 0, 1, 3 ] }
        ]
    }).show();

    if ($.datepicker.regional[language]) {
        $.datepicker.setDefaults($.datepicker.regional[language]);
    } else {
        $.datepicker.setDefaults($.datepicker.regional['']);
    }

    $('#date-dp').datepicker({
        altField: '#date',
        altFormat: 'yy-mm-dd',
        showButtonPanel: true,
        showWeek: true,
        changeMonth: true,
        changeYear: true,
        yearRange: '-23:+1',
        showOn: 'button',
        buttonImage: '/images/ico/calendar_select.png'
    }).datepicker('setDate', '{DATE}' ? new Date('{DATE}') : '');

    /* Adjust calendar button */
    $('button.ui-datepicker-trigger').css({ padding: '2px' });

    /**
     *
     */
    $('.time').focusout(function() {
        var val = $(this).val();
        if (!val) return;

        var t = (val+'::').split(':');
        if (t[0] > 23) t[0] = 23;
        if (t[1] > 59) t[1] = 59;
        if (t[2] > 59) t[2] = 59;
        /* Format to ##:##:## */
        $(this).val(('00'+t[0]).slice(-2) + ':' + ('00'+t[1]).slice(-2) + ':' + ('00'+t[2]).slice(-2));
    });

    /**
     * Update row states
     */
    $('.time, .price').keyup(function() {
        UpdateRowState();
    });

    $('.weekday').on('ifToggled', function(){
        UpdateRowState();
    });

    /**
     *
     */
    $('.row-delete').click(function() {
        var id = $(this).parents('tr').data('id');

        /* Disable row update during removing data */
        UpdateRowStateActive = false;

        $('#time-'+id).val('');
        $('.weekday-'+id).iCheck('uncheck');
        $('#price-'+id).val('');
        $('#comment-'+id).val('');

        UpdateRowStateActive = true;
        UpdateRowState();
    });

});

</script>
