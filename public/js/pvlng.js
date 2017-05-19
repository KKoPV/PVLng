/**
 *
 * @author     Knut Kohl <github@knutkohl.de>
 * @copyright  2012-2014 Knut Kohl
 * @license    MIT License (MIT) http://opensource.org/licenses/MIT
 * @version    1.0.0
 */

/**
 *
 */
var qs;

/**
 *
 */
var dFrom, dTo;

/**
 *
 */
var pvlng = new function() {

    /**
     *
     */
    this.maxFutureDays = 2;

    /**
     * Public property
     */
    this.verbose = false;

    /**
     * Single date picker
     */
    this.dp;

    /**
     * Range date picker
     */
    this.dpFrom;
    this.dpTo;

    /**
     * Cookie handling with pure JS
     *
     * http://stackoverflow.com/a/1460174
     */
    this.cookie = new function() {

        this.set = function(name, value, days) {
            var expires = '';
            if (days) {
                var date = new Date();
                date.setTime(date.getTime() + days*8.64e7);
                expires = '; expires=' + date.toGMTString();
            }
            document.cookie = escape(name) + '=' + escape(value) + expires + '; path=/';
        },

        this.get = function(name) {
            var nameEQ = escape(name) + '=', ca = document.cookie.split(';'), c;
            for (var i=0; i<ca.length; i++) {
                c = ca[i];
                while (c.charAt(0) === ' ') c = c.substring(1, c.length);
                if (c.indexOf(nameEQ) === 0) return unescape(c.substring(nameEQ.length, c.length));
            }
            return null;
        },

        this.remove = function(name) {
            this.set(name, '', -1);
        }
    },

    /* Add functions to stack to execute after all other scripts finished */
    this.onFinished = new function() {
        var stack = [];

        this.add = function() {
            $.each(arguments, function(i, func) { stack.push(func) });
        }

        /* Run all buffered functions */
        this.run = function( func ) {
            $.each(stack, function(i, func) { func() });
        }
    },

    /**
     * Scroll to #top as top most visible element
     */
    this.scroll = function( top ) {
        $('html, body').stop().animate({ scrollTop: $(top).offset().top-3 }, 'fast');
    }

    /**
     * Title is optional
     */
    this.alert = function( msg, title ) {
        if (arguments.length == 1) title = '';

        $("<div/>").dialog({
            title: title,
            resizable: false,
            modal: true,
            width: 480,
            buttons: {
                'Ok': function() { $(this).dialog('close') }
            },
            close: function() { $(this).remove() },
        })
        .html(msg)
        .find('.ui-dialog-titlebar-close').remove();
    };

    /**
     * Add clear search button to table
     */
    this.addClearSearchButton = function( table, hint ) {
        $('<img />')
            .prop('src', '/images/ico/cross-script.png')
            .appendTo($('#'+table+'_filter'))
            .css({ marginLeft: '4px', cursor: 'pointer' })
            .prop('title', hint).tipTip()
            .click(function() {
                 /* Clear search, force re-search and refocus */
                 $(this).parent().find('input').val('').trigger('keyup').focus();
             });
    };

    /**
     *
     */
    this.log = function() {
        if (!this.verbose) return;

        if (arguments.length == 1) {
            console.log(arguments[0]);
        } else {
            $(arguments).each(function(id, data) {
                if (id == 0) {
                    console.group(data);
                } else {
                    if (Object.prototype.toString.call(data) === '[object Array]') {
                        console.table(data);
                    } else {
                        console.log(data);
                    }
                }
            });
            console.groupEnd();
        }
    };

    /**
     * Calculates the dates based on selected day and time range
     */
    this.calcDates = function(dir) {
        dir = dir || 0;

        var date = $.datepicker.parseDate('mm/dd/yy', $('#timerangedate').val()),
            timerange = $('input[name="timerange"]:checked').val(),
            from, to, dst, format, preset = presetPeriods.split(';');

        // Remember DST before
        dst  = date.dst();

        switch (timerange) {

            case 'd':
                from = new Date(date.getTime() + dir*8.64e7);
                to   = new Date(from);
                format = this.dp.data('dateFormat');
                preset = typeof views != 'undefined' && views.preset ? views.preset : preset[0];
                break;

            case 'w':
                var dow = date.getDay() - 1;
                if (dow < 0) dow = 7 + dow;
                from = new Date(date.getTime() + 7*dir*8.64e7 - dow*8.64e7);
                to   = new Date(from.getTime() + 6*8.64e7);
                format = this.dp.datepicker('option', 'weekHeader') + ' ' +
                         $.datepicker.iso8601Week(from) + '.yy';
                preset = preset[1];
                break;

            case 'm':
                var y = date.getFullYear(), m = date.getMonth() + dir;
                if (m < 0) { m = 12 + m; y -= 1; }
                else if (m > 11) { m = 12 - m; y += 1; }
                from = new Date(y, m,   1);
                to   = new Date(y, m+1, 0);
                format = 'MM yy';
                preset = preset[2];
                break;

            case 'y':
                var y = date.getFullYear() + dir;
                from = new Date(y,   0, 1);
                to   = new Date(y+1, 0, 0);
                format = 'yy';
                preset = preset[3];
                break;
        }

        // Max. date is today + pvlng.maxFutureDays
        to = new Date(Math.min(to, new Date(new Date().getTime() + this.maxFutureDays*8.64e7)));

        // Adjust daylight savings time
        if (dst != from.dst()) from.addTime(-dir * 3.6e6);
        if (dst != to.dst())   to.addTime(-dir * 3.6e6);

        this.dp.setDate(from, format);

        this.dpFrom.datepicker('option', 'maxDate', to).datepicker('setDate', from);
        this.dpTo.datepicker('option', 'minDate', from).datepicker('setDate', to);

        if ($('#dp1').is(':visible')) {
            if ($('#preset').length) {
                setTimeout(function() {
                    $('#preset').val(preset).trigger('change');
                }, 0);
            } else if (typeof afterDatesCalculated === 'function') {
                setTimeout(afterDatesCalculated, 0);
            }
        }
    };

    /**
     *
     */
    this.changeDate = function( dir ) {
        this.calcDates(dir);
    };

    /**
     *
     */
    this.changeDates = function( dir ) {
        var from = new Date(this.dpFrom.datepicker('getDate').getTime() + dir*8.64e7),
            to   = new Date(this.dpTo.datepicker('getDate').getTime() + dir*8.64e7);

        if (to > (new Date).getTime() + 8.64e7) {
            $.pnotify({ type: 'info', text: "Can't go beyond tomorrow." });
            return;
        }

        this.dpFrom.datepicker('option', 'maxDate', to).datepicker('setDate', from);
        this.dpTo.datepicker('option', 'minDate', from).datepicker('setDate', to);
        this.dp.setDate(from);

        updateOutput();
    };

    /**
     *
     */
    this.changePreset = function() {
        var preset = $('#preset').val().match(/(\d+)(\w+)/);

        if (!preset) {
            $('#periodcnt').val(1);
            $('#period').val('');
        } else {
            if ($('#dp2').is(':visible')) {
                // Adjust start date only in days select view
                var from = new Date(pvlng.dpFrom.datepicker('getDate'));
                switch (preset[2]) {
                    case 'd': /* day - set start to 1st day of month */
                    case 'w': /* week - set start to 1st day of month */
                        from.setDate(1);
                        break;
                    case 'm': /* month - set start to 1st day of year */
                        from.setDate(1);
                        from.setMonth(0);
                        break;
                }
                pvlng.dpFrom.datepicker('setDate', from);
            }
            $('#periodcnt').val(preset[1]);
            $('#period').val(preset[2]);
        }
        $('#period').trigger('change');
    };

    /**
     * Profile run time of callable
     */
    this.profile = function( callable ) {
        var start = new Date();
        callable();
        return new Date() - start;
    };

    /**
     * Rewrite console commands, verbose sensitive and
     * return elapsed time on end
     */
    var _timers = [];

    this.time = function( label ) {
        _timers.push({
            start: performance.now(),
            label: label
        });
    };

    this.timeEnd = function() {
        var t = _timers.pop();
        if (!t) return;
        var d = performance.now() - t.start;
        if (this.verbose) {
            console.log(t.label + ': ' + (d/1000).toFixed(3) + 's');
        }
        return d;
    };

    // Private Property
    // var internalVar = true;
    // var internalFunction = function() {};

    /**
     * Generate pseudo GUID
     * http://stackoverflow.com/a/2117523
     */
    this.guid = function(prefix) {
        prefix = prefix ? prefix + '-' : '';
        return prefix + 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function(c) {
            var r = Math.random()*16|0, v = c == 'x' ? r : (r&0x3|0x8);
            return v.toString(16);
        })
    }
};

$(function() {

    $.extend({
        alert: function( msg, title ) {
            if (arguments.length == 1) title = '';

            $('<div/>').dialog({
                modal: true,
                resizable: false,
                title: title,
                width: 480,
                open: function() { $('.ui-dialog-titlebar-close').hide() },
                buttons: {
                    'Ok': function() { $(this).dialog('close') }
                },
                close: function() { $(this).remove() },
            })
            .html(msg);
        },

        confirm: function( msg, title, OkText, CancelText ) {
            if (!title) title = 'Confirm';
            if (!OkText) OkText = 'Ok';
            if (!CancelText) CancelText = 'Cancel';

            var d = $.Deferred(),
                options = {
                    modal: true,
                    resizable: false,
                    title: title,
                    width: 480,
                    open: function() { $('.ui-dialog-titlebar-close').hide() },
                    buttons: {},
                    close: function() { $(this).remove() }
                };

            /* Use given texts for buttons */
            options.buttons[OkText] = function() {
                $(this).dialog('close');
                d.resolve(true);
                return true;
            };
            options.buttons[CancelText] = function() {
                $(this).dialog('close');
                d.resolve(false);
                return false;
            };

            $('<div/>').html(msg).dialog(options);

            return d.promise();
        }
    });

    qs = $.parseQueryString();

    /* Refesh timeout */
    if (qs.refresh) {
        RefreshTimeout = qs.refresh;
    }

    /**
     * Anylyse query string for date(s)
     */
    if (qs.from && qs.to) {
        dFrom = new Date(qs.from.substr(0,1) != '-' ? qs.from : (new Date()).getTime() + qs.from * 8.64e7);
        dTo   = new Date(qs.to.substr(0,1)   != '+' ? qs.to   : (new Date()).getTime() + qs.to   * 8.64e7);
    } else if (qs.date) {
        dFrom = dTo = new Date(qs.date);
    } else {
        dFrom = dTo = new Date();
    }

    /**
     *
     */
    pvlng.dp = $('#timerange').datepicker({
        altField: '#timerangedate',
        altFormat: 'mm/dd/yy',
        maxDate: pvlng.maxFutureDays,
        showButtonPanel: true,
        showWeek: true,
        changeMonth: true,
        changeYear: true,
        showOn: null,
        onClose: function(dateText, inst) {
            if (dateText != inst.lastVal) {
                $(this).datepicker('setDate', new Date(inst.selectedYear, inst.selectedMonth, inst.selectedDay));
                pvlng.calcDates();
            }
        }
    }).datepicker('setDate', dTo);

    pvlng.dp.setDate = function(date, format) {
        format && this.datepicker('option', 'dateFormat', format);
        /* Set date and recalc new max. date */
        this.datepicker('setDate', date).datepicker('option', 'maxDate', pvlng.maxFutureDays);
    };

    // Remember local day date format
    pvlng.dp.data('dateFormat', pvlng.dp.datepicker('option', 'dateFormat'));

    // Show datepicker
    $('#dpCalendar').on('click', function() {
        $($(this).data('input')).datepicker('show');
    });

    // day, week, month, year
    $('input[name="timerange"]').on('change', function() {
        if ($(this).val() == 'd') $('#dpCalendar').show(); else $('#dpCalendar').hide();
        pvlng.calcDates();
    });

    pvlng.dpFrom = $('#from').datepicker({
        altField: '#fromdate',
        altFormat: 'mm/dd/yy',
        autoSize: true,
        maxDate: 0,
        showButtonPanel: true,
        showWeek: true,
        changeMonth: true,
        changeYear: true,
        onClose: function(selectedDate) {
            pvlng.dpTo.datepicker('option', 'minDate', selectedDate);
        }
    }).datepicker('setDate', dFrom);

    pvlng.dpTo = $('#to').datepicker({
        altField: '#todate',
        altFormat: 'mm/dd/yy',
        autoSize: true,
        maxDate: pvlng.maxFutureDays,
        showButtonPanel: true,
        showWeek: true,
        changeMonth: true,
        changeYear: true,
        onClose: function(selectedDate) {
            pvlng.dpFrom.datepicker('option', 'maxDate', selectedDate);
        }
    }).datepicker('setDate', dTo);

    /**
     *
     */
    $('#btn-reset').on('click', function(e) {
        var d = new Date;
        /* Set date ranges */
        pvlng.dpFrom.datepicker('option', 'maxDate', d);
        pvlng.dpTo.datepicker('option', 'minDate', d);
        /* Reset zoom */
        if (chart) chart.zoomOut();
        /* Set date today */
        pvlng.dp.setDate(d);
        $('#timerange-day').prop('checked', true).trigger('change');
        if ($('#dp2').is(':visible')) {
            setTimeout(function() {
                $('#preset').trigger('change');
            }, 0);
        }
    });

    $('#preset').on('change', function() {
        pvlng.changePreset();
        updateOutput();
    });

});
