/**
 * PVLng - PhotoVoltaic Logger new generation
 *
 * @link       https://github.com/KKoPV/PVLng
 * @link       https://pvlng.com/
 * @author     Knut Kohl <github@knutkohl.de>
 * @copyright  2012 Knut Kohl
 * @license    MIT License (MIT) http://opensource.org/licenses/MIT
 */

var resetTimer;

/**
 *
 */
function resetDeleteButton() {
    var b = $('#empty-database'), s = $('span', b);
    b.removeClass('confirm');
    s.text(s.data('text'));
    $.wait(false);
}

/**
 *
 */
$(function() {

    $('#empty-database').on('click', function(event) {

        var b = $(this), s = $('span', b);

        if (!b.hasClass('confirm')) {

            s.data('text', s.text()).text('Click again if you are ABSOLUTELY sure ...');
            b.addClass('confirm');
            resetTimer = setTimeout(resetDeleteButton, 8000);

        } else {

            clearTimeout(resetTimer);

            $.wait();

            $.ajax({
                type: 'DELETE',
                url: PVLngAPI + 'data',
                dataType: 'json',
            }).done(function(data, textStatus, jqXHR) {
                $('[name="d[core--EmptyDatabaseAllowed]"]').val(0).change();
                b.replaceWith('<strong>'+data.message+'</strong>');
            }).fail(function(jqXHR, textStatus, errorThrown) {
                $.pnotify({
                    type: textStatus, hide: false, text: jqXHR.responseJSON.message
                });
            }).always(function() {
                resetDeleteButton();
            });

        }

        event.preventDefault();
    });

});
