/**
 * PVLng - PhotoVoltaic Logger new generation
 *
 * @link       https://github.com/KKoPV/PVLng
 * @link       https://pvlng.com/
 * @author     Knut Kohl <github@knutkohl.de>
 * @copyright  2012 Knut Kohl
 * @license    MIT License (MIT) http://opensource.org/licenses/MIT
 */

var UpdateRowStateActive = true;

/**
 * Calculate date row state icon
 */
function UpdateRowState() {
    /* Disabled during row clearance */
    if (!UpdateRowStateActive) return;

    var Class = ['fa-question-circle', 'fa-check-circle'],
        Color = ['DarkOrange',         'DarkGreen'];

    $('#table-times tbody tr').each(function(id, tr) {
        id = $(tr).data('id');
        var t = $('#time-'+id),
            i = $('#icon-'+id),
            p = $('#price-'+id),
            c = $('#comment-'+id),
            ck = false, valid, cls, color;

        /* At least one weekday must be checked */
        $('.weekday-'+id).each(function(id, el) {
            if ($(el).is(':checked')) ck = true;
        });

        valid = (t.val() != "" && ck && p.val() != "");

        /* Set row state icon and color */
        i.removeClass(Class.join(' ')).addClass(Class[+valid]).css('color', Color[+valid]);

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
            { sWidth:  '1%', aTargets: [ 0, 1, 3 ] },
            { sWidth: '35em', aTargets: [ 2 ] }
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
        yearRange: '-23:+1'
    }).datepicker('setDate', '{DATE}' ? new Date('{DATE}') : '');

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
    $('.time, .price').on('keyup', UpdateRowState);
    $('.weekday').on('ifToggled', UpdateRowState);

    /**
     *
     */
    $('.row-delete').on('click', function() {
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