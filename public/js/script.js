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

/**
 * Idea from http://pastebin.com/jYqm9ZcQ
 */
$.fn.autoWidth = function(options) {
    var settings = {
        marginRight: 20 /* Extra margin right pixels */
    };
    if (options) $.extend(settings, options);

    var maxWidth = 0;
    this.each(function() {
        maxWidth = Math.max($(this).width(), maxWidth);
    });
    this.css('display', 'inline-block').width(maxWidth + settings.marginRight);
};

/**
 * http://paulgueller.com/2011/04/26/parse-the-querystring-with-jquery/
 */
$.extend({
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

/**
 *
 */
var lastModule, hideMenuTimer;

/**
 *
 * /
function setMenuHideTimer() {
    if (!hideMenuTimer) {
        /* console.log('Set MenuTimer'); * /
        hideMenuTimer = setTimeout(function() { $('#submenu').parent().hide() }, 3000 );
    }
}

function unsetMenuHideTimer() {
    /* console.log('Unset MenuTimer'); */
    /* Unset also hideMenuTimer variable ... * /
    hideMenuTimer = clearTimeout(hideMenuTimer);
}

/**
 *
 */
$(function() {

    /* Inititilize Pines Notify */
    $.pnotify.defaults.styling = 'jqueryui';
    $.pnotify.defaults.delay = 5000;
    $.pnotify.defaults.history = false;
    $.pnotify.defaults.sticker = false;
    $.pnotify.defaults.stack.spacing1 = 5;
    $.pnotify.defaults.stack.spacing2 = 15;
    $.pnotify.defaults.labels.stick = pnotify_defaults_labels_stick;
    $.pnotify.defaults.labels.close = pnotify_defaults_labels_close;

    $(messages).each(function(id, msg) {
        if (msg.type == 'error') {
            msg.history = true;
            msg.hide = false;
        }
        $.pnotify(msg);
    });

    /* Inititilize Tooltips */
    $('.tip, .tipbtn').tipTip({
        attribute: 'tip',
        maxWidth: '400px',
        edgeOffset: 10
    });
    $('#tiptip_content').addClass('ui-state-default');

    $('button, a.button, input[type=submit], input[type=checkbox].button, input[type=radio].button').each(function(id, el) {
        var options = { icons: { primary: null, secondary: null } };
        el = $(el);
        if (el.data('primary')) {
            options.icons.primary = el.data('primary');
        }
        if (el.data('secondary')) {
            options.icons.secondary = el.data('secondary');
        }
        if (!el.data('text')) {
            options.text = false;
        }
        el.button(options);
        el.prop('title', '');
    });

    $('.toolbar').buttonset();

    $('input[type=text], input[type=number], input[type=password], select, textarea').addClass('ui-corner-all');

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

    $('input[type=number]').prop('type', 'text').addClass('number-spinner').spinner();

    /* Back to top */
    var fadeDuration = 500;
    $(window).scroll(function() {
        if ($(this).scrollTop()) {
            $('.back-to-top').fadeIn(fadeDuration);
        } else {
            $('.back-to-top').fadeOut(fadeDuration);
        }
    });

    $('.language').click(function(e) {
        e.preventDefault();
        /* Detect if there is already parameters in URL */
        var sep = (window.location.search == '') ? '?' : '&';
        window.location = window.location + sep + 'lang=' + $(this).data('lang');
        return false;
    });

    $('.back-to-top').click(function(e) {
        e.preventDefault();
        jQuery('html, body').animate({scrollTop: 0}, fadeDuration);
        return false;
    });

    shortcut.add('Shift+F1', function() { window.location = '/'; });
    shortcut.add('Shift+F2', function() { window.location = '/dashboard'; });
    shortcut.add('Shift+F3', function() { window.location = '/list'; });
    shortcut.add('Shift+F4', function() { window.location = '/channel'; });
    shortcut.add('Shift+F5', function() { window.location = '/info'; });
    shortcut.add('Shift+F6', function() { window.location = '/description'; });
    shortcut.add('Alt+L',    function() { window.location = '/logout'; });

});

var timer, verbose = false;

/**
 *
 */
function _log() {
    if (!verbose) return;

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
 * /
String.prototype.repeat = function(count) {
    if (count < 1) return '';
    var result = '', pattern = this.valueOf();
    while (count > 0) {
        if (count & 1) result += pattern;
        count >>= 1, pattern += pattern;
    }
    return result;
};
*/
