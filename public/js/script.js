/**
 *
 *
 * @author      Knut Kohl <github@knutkohl.de>
 * @copyright   2012-2013 Knut Kohl
 * @license     GNU General Public License http://www.gnu.org/licenses/gpl.txt
 * @version     $Id$
 * @revision    $Rev$
 */

/**
 * http://paulgueller.com/2011/04/26/parse-the-querystring-with-jquery/
 */
jQuery.extend({
	parseQueryString: function() {
		var nvpair = {};
		var qs = window.location.search.replace('?', '');
		var pairs = qs.split('&');
		$.each(pairs, function(i, v){
			var pair = v.split('=');
			nvpair[pair[0]] = pair[1];
		});
		return nvpair;
	}
});

$(function() {

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
	$('input[type=text], input[type=password], select, textarea').addClass('ui-corner-all');

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

	$('#overlay').fadeOut();

});

/**
 *
 */
var verbose = true;

/**
 *
 */
function _log() {
	if (!verbose) return;
	var d = new Date;
	console.log(d.toLocaleString()+'.'+d.getMilliseconds());
	$(arguments).each(function(id, data) {
		console.log(data);
	});
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
