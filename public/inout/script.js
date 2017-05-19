/**
 * API URL, relative on this host
 */
var api = '/api/latest/data/';

/**
 *
 */
var timer;

/**
 * Show clock by default
 */
var clock = 1;

/**
 * HTML5 Page Visibility API
 *
 * http://www.sitepoint.com/introduction-to-page-visibility-api/
 * http://www.w3.org/TR/page-visibility
 */
var browserPrefix = null;

if (typeof document.hidden != 'undefined') {
    browserPrefix = '';
} else {
    var browserPrefixes = ['webkit', 'moz', 'ms', 'o'], l = browserPrefixes.length;
    // Test all vendor prefixes
    for (var i=0; i<l; i++) {
        if (typeof document[browserPrefixes[i] + 'Hidden'] != 'undefined') {
            browserPrefix = browserPrefixes[i];
            break;
        }
    }
}

if (browserPrefix !== null) {
    document.addEventListener(browserPrefix + 'visibilitychange', function() {
        if (document.hidden === false || document[browserPrefix + 'Hidden'] === false) {
            // The page is in foreground and visible
            update();
        } else {
            // The page went into background, clear timer
            clearTimeout(timer);
        }
        return false;
    });
}

/**
 * Functions for styling
 */
$.fn.extend({
    gray: function() {
        return this.each(function() {
            $(this).switchClass('green red', 'gray', InOut.fade);
        });
    },
    green: function() {
        return this.each(function() {
            $(this).switchClass('gray red', 'green', InOut.fade);
        });
    },
    red: function() {
        return this.each(function() {
            $(this).switchClass('gray green', 'red', InOut.fade);
        });
    }
});

/**
 *
 */
function kWh(val) {
    var abs = Math.abs(val);
    if (abs ==  0) return val.toFixed(0);
    if (abs <  10) return val.toFixed(2);
    if (abs < 100) return val.toFixed(1);
    return val.toFixed(0);
}

/**
 *
 */
function setText(cls, val) {
    var e0 = $('.inactive', '.'+cls), e1 = $('.active', '.'+cls);

    if (e1.val() == val) return;

    e1.fadeOut(InOut.fade, function() {
        e1.toggleClass('active').toggleClass('inactive');
    });

    e0.text(val).fadeIn(InOut.fade, function() {
        e0.toggleClass('active').toggleClass('inactive');
    });
}

/**
 * Fetch data for sensors and meters independent and style output
 */
function update(callback) {

    var paramsP = { start: '-'+InOut.PowerAvg+'minutes', period: '1d', short: 1 },
        paramsE = { short: 1 };

    $.when(
        // Average over last ? minutes for sensor channels
        $.getJSON(api + InOut.Pgen.GUID + '.json', paramsP),
        $.getJSON(api + InOut.Puse.GUID + '.json', paramsP),
        // Last value for meter channels
        $.getJSON(api + 'last/' + InOut.Egen.GUID + '.json', paramsE),
        $.getJSON(api + 'last/' + InOut.Euse.GUID + '.json', paramsE)
    ).done(function(dataPgen, dataPuse, dataEgen, dataEuse) {
        // Each data* argument is an array [ data, statusText, jqXHR ]

        // Powers
        var Pgen = dataPgen[0].length ? dataPgen[0][0][1] * (InOut.Pgen.Factor || 1) : 0,
            Puse = dataPuse[0].length ? dataPuse[0][0][1] * (InOut.Puse.Factor || 1) : 0;

        if (Pgen > 0) {
            $('.c1').green();
            $('#arrow1').switchClass('fa-angle-double-left', 'fa-angle-double-right', InOut.fade);
        } else {
            $('.c1').gray();
            $('#arrow1').removeClass('fa-angle-double-left fa-angle-double-right');
        }

        if (Pgen > Puse) {
            $('.c2, .c3').green();
            $('#arrow2').switchClass('fa-angle-double-left', 'fa-angle-double-right', InOut.fade);
        } else {
            $('.c2, .c3').red();
            $('#arrow2').switchClass('fa-angle-double-right', 'fa-angle-double-left', InOut.fade);
        }

        setText('Pgen', Pgen.toFixed(0));
        setText('Puse', Puse.toFixed(0));
        setText('Pbal', (Pgen - Puse).toFixed(0));

        // Energies
        var Egen = dataEgen[0].length ? dataEgen[0][0][1] * (InOut.Egen.Factor || 1) : 0,
            Euse = dataEuse[0].length ? dataEuse[0][0][1] * (InOut.Euse.Factor || 1) : 0;

        if (Egen > Euse) {
            $('.c4').green();
        } else {
            $('.c4').red();
        }

        setText('Egen', kWh(Egen));
        setText('Euse', kWh(Euse));
        setText('Ebal', kWh(Egen - Euse));

    }).fail(function() {
        console.log(arguments);
    }).always(function() {
        if (clock) {
            var d = new Date;
            $('#clock').text(('0' + d.getHours()).substr(-2) + ':' + ('0' + d.getMinutes()).substr(-2));
        }

        if (typeof callback === 'function') {
            // Wait until initial setText() is finished
            setTimeout(callback, 1000);
        }

        // Set auto refresh
        timer = setTimeout(update, InOut.Timeout * 1000);
    });
}

/**
 *
 */
$(function($) {

    var zoom = 1;

    // Prepare AJAX with API key
    $.ajaxSetup({
        beforeSend: function setHeader(XHR) {
            XHR.setRequestHeader('X-PVLng-Key', InOut.PVLngAPIkey)
        }
    });

    $.each(window.location.search.replace('?', '').split('&'), function(i, v) {
        var pair = v.split('=');
        if (pair[0] == 'resfresh' && pair[1] >= 30) {
            // If a timeout is provided as URL parameter "refresh", use this
            // but only if at least 30 sec. :-)
            InOut.Timeout = +pair[1];
        } else if (pair[0] == 'zoom' && pair[1] > 0) {
            zoom = +pair[1];
        } else if (pair[0] == 'clock') {
            clock = +pair[1];
            if (!clock) $('#clock').hide();
        }
    });

    // Size of 62.5% is equal 100% "page scale"
    $('table').css('font-size', (zoom*62.5)+'%');

    // A combo of zoom for webkit/ie and transform:scale for Firefox(-moz-)
    // and Opera(-o-) for cross-browser desktop & mobile
    // http://stackoverflow.com/a/16080995
    // Images are 4 times size, so zoom down initially by 0.25
    // multiplied with zoom factor
    zoom *= .25;

    $('.icons, .arrows').css({
        'zoom': (zoom*100)+'%',
        '-moz-transform': 'scale('+(zoom)+')',
        '-moz-transform-origin': '0 0'
    });

    // Update data and styles and finally show content
    update(function() { $('body').animate({ opacity: 1 }); });
});
