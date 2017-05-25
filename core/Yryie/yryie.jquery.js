/**
 *
 */
function YryieToggle()
{
    $('#Yryie table .hide').toggle();
}

/**
 * Switch rows of given type on/off
 */
function YryieSwitch( _type, _checked )
{
    var v = 0;

    $('#Yryie table tbody tr').each(function (id, row) {
        row = $(row);

        if (row.hasClass(_type)) {
            row.toggle(_checked);
        }

        // re-color rows
        if (row.is(':visible')) {
            if (v++ % 2) {
                row.removeClass('odd').addClass('even');
            } else {
                row.removeClass('even').addClass('odd');
            }
        }
    });
}
