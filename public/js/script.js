/**
 *
 *
 * @author      Knut Kohl <github@knutkohl.de>
 * @copyright   2012-2013 Knut Kohl
 * @license     GNU General Public License http://www.gnu.org/licenses/gpl.txt
 * @version     1.0.0
 * @revision    $Rev$
 */

console.time('Duration');

$(function() {

    /**
     * http://paulgueller.com/2011/04/26/parse-the-querystring-with-jquery/
     */
    jQuery.extend({
        parseQueryString: function() {
            var qs = window.location.search.replace('?', '');
            if (qs == '') return {};
            result = {};
            $.each(qs.split('&'), function(i, v){
                var pair = v.split('=');
                /* decodeURI doesn't work for the date strings :-( */
                result[pair[0]] = (pair.length > 1) ? pair[1].replace(/%2F/g, '/') : '';
            });
            return result;
        }
    });

    /* Inititilize Pines Notify */
    $.pnotify.defaults.styling = 'jqueryui';
    $.pnotify.defaults.delay = 5000;
    $.pnotify.defaults.history = false;
    $.pnotify.defaults.stack.spacing1 = 5;
    $.pnotify.defaults.stack.spacing2 = 15;
    $.pnotify.defaults.labels.redisplay = pnotify_defaults_labels_redisplay;
    $.pnotify.defaults.labels.all = pnotify_defaults_labels_all;
    $.pnotify.defaults.labels.last = pnotify_defaults_labels_last;
    $.pnotify.defaults.labels.stick = pnotify_defaults_labels_stick;
    $.pnotify.defaults.labels.close = pnotify_defaults_labels_close;

    /* Inititilize Tooltips */
    $('.tip, .tipbtn').tipTip({
        attribute: 'tip',
        maxWidth: '400px',
        edgeOffset: 10
    });
    $('#tiptip_content').addClass('ui-state-default');

    $('button, a.button, input[type="submit"]').button();
    $('.toolbar').buttonset();
    $('.login').button('option', 'disabled', (user == ''));

    $('input[type=text], input[type=number], input[type=password], select, textarea').addClass('ui-corner-all');

    $(messages).each(function(id, msg) {
        if (msg.type == 'error') {
            msg.history = true;
            msg.hide = false;
        }
        $.pnotify(msg);
    });

    $('.numbersOnly').keyup(function () {
            if (this.value != this.value.replace(/[^0-9\.]/g, '')) {
                 this.value = this.value.replace(/[^0-9\.]/g, '');
            }
    });

    $('input.iCheck').iCheck({
        checkboxClass: 'icheckbox_flat',
        radioClass: 'iradio_flat'
    });

    $('input.iCheckLine').each(function(){
        var self = $(this),
        label = self.next(),
        label_text = label.text();
        label.remove();
        self.iCheck({
            checkboxClass: 'icheckbox_line',
            radioClass: 'iradio_line',
            insert: '<div class="icheck_line-icon"></div>' + label_text
        });
    });

    $('input').iCheck('update');

    /* Back to top */
    var fadeDuration = 500;
    $(window).scroll(function() {
        if ($(this).scrollTop()) {
            $('.back-to-top').fadeIn(fadeDuration);
        } else {
            $('.back-to-top').fadeOut(fadeDuration);
        }
    });

    $('.back-to-top').click(function(e) {
        e.preventDefault();
        jQuery('html, body').animate({scrollTop: 0}, fadeDuration);
        return false;
    });

    shortcut.add('Shift+F1', function() { window.location = '/'; });
    shortcut.add('Shift+F2', function() { window.location = '/dashboard'; });
    shortcut.add('Shift+F3', function() { window.location = '/list'; });
    shortcut.add('Shift+F4', function() { window.location = '/overview'; });
    shortcut.add('Shift+F5', function() { window.location = '/channel'; });
    shortcut.add('Shift+F6', function() { window.location = '/info'; });
    shortcut.add('Shift+F7', function() { window.location = '/description'; });
    shortcut.add('Alt+L',    function() { window.location = '/logout'; });

});

var timer;

/**
 *
 */
function _log() {
    console.timeEnd('Duration');
    console.time('Duration');
    if (arguments.length == 1) {
        console.log(arguments[0]);
    } else {
        $(arguments).each(function(id, data) {
            if (id == 0) {
                console.group(data);
            } else {
                if (Object.prototype.toString.call(data) === '[object Array]') {
                    console.table(data);
/*
                } else if (typeof data == 'object') {
                    console.dir(data);
*/
                } else {
                    console.log(data);
                }
            }
        });
        console.groupEnd();
    }
}

/**
 *
 */
String.prototype.repeat = function(count) {
    if (count < 1) return '';
    var result = '', pattern = this.valueOf();
    while (count > 0) {
        if (count & 1) result += pattern;
        count >>= 1, pattern += pattern;
    }
    return result;
};
