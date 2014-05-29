/**
 *
 */

window.alert = function( msg, title ) {
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
    .html(msg);
};

var pvlng = new function() {

    this.menu = new function() {
        var that = this;
        this.Timer = null;

        this.setTimer = function() {
            if (that.Timer) return;

            that.Timer = setTimeout(
                function() {
                    $('#submenu').parent().hide();
                    that.unsetTimer();
                },
                3000
            );
            /* console.log('Set MenuTimer: '+that.Timer); */
        };

        this.unsetTimer = function() {
            if (!that.Timer) return;

            /* console.log('Unset MenuTimer: '+that.Timer); */
            that.Timer = clearTimeout(that.Timer);
        };

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

    // Private Property
    var internalVar = true;
    var internalFunction = function() {};

    /* Public */
    this.verbose = false;

    this.Overlay = function() {
        this.show = function() {
            /*$('#container').hide();*/
            this.overlay.show();
        };

        this.hide = function() {
            this.overlay.hide();
            $('#container').show();
        };

        /* Prepare overlay ... */
        this.overlay = $('<div/>')
            .height($(document).height())
            .css({
                display: 'none',
                position: 'fixed',
                top: 0,
                left: 0,
                width: '100%',
                backgroundColor: '#FFF',
                zIndex: 1000
            })
            .append(
                /* ... and preload image */
                $('<img/>')
                    .prop('src', '/images/loading_dots.gif')
                  /*  .prop('width', 64).prop('height', 21) */
                    .css({
                        width: '64px',
                        height: '21px',
                        position: 'fixed',
                        top: (window.innerHeight/2-15)+'px',
                        left: (window.innerWidth/2-32)+'px'
                    })
            )
            .appendTo('body');
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
                    } else {
                        console.log(data);
                    }
                }
            });
            console.groupEnd();
        }
    };

    /**
     *
     */
    this.changeDates = function ( dir ) {
        dir *= 24*60*60*1000;

        var from = new Date(Date.parse($('#from').datepicker('getDate')) + dir),
            to = new Date(Date.parse($('#to').datepicker('getDate')) + dir);

        if (to > new Date) {
            $.pnotify({ type: 'info', text: "Can't go beyond today." });
            return;
        }

        $('#from').datepicker('option', 'maxDate', to).datepicker('setDate', from);
        $('#to').datepicker('option', 'minDate', from).datepicker('setDate', to);

        updateOutput();
    };

    /**
     *
     */
    this.changePreset = function () {
        var preset = $('#preset').val().match(/(\d+)(\w+)/);

        if (!preset) {
            $('#periodcnt').val(1);
            $('#period').val('');
        } else {
            var from = new Date($("#from").datepicker('getDate'));
            switch (preset[2]) {
                case 'd': /* day - set start to 1st day of month */
                    from.setDate(1);
                    break;
                case 'w': /* week - set start to 1st day of month */
                    from.setDate(1);
                    break;
                case 'm': /* month - set start to 1st day of year */
                    from.setDate(1);
                    from.setMonth(0);
                    break;
            }
            $("#from").datepicker('setDate', from);
            $('#periodcnt').val(preset[1]);
            $('#period').val(preset[2]);
        }
        $('#period').trigger('change');
    };

};

$(function() {

    /**
     *
     */
    $('a.module').hover(
        function() {
            var submenu = submenus[$(this).data('module')];
            if (!submenu) return;

            pvlng.menu.unsetTimer();

            var menu = $('#submenu').empty();
            $.each(submenu, function(id, el) {
                $('<a/>').prop('href', el.ROUTE).text(el.LABEL)
                    .prop('title', el.HINT).tipTip()
                    .appendTo(menu)
                    .hover(
                        pvlng.menu.unsetTimer,
                        pvlng.menu.setTimer
                    );
            });
            menu.buttonset().parent().show();
        },
        pvlng.menu.setTimer
    );

    /**
     *
     */
    $('#btn-reset').button({
        icons: { primary: 'ui-icon-calendar' },
        label: '&nbsp;',
        text: false
    }).click(function(e) {
        e.preventDefault();
        var d = new Date;
        /* Set date ranges */
        $('#from').datepicker('option', 'maxDate', d);
        $('#to').datepicker('option', 'minDate', d);
        /* Set date today */
        $('#from').datepicker('setDate', d);
        $('#to').datepicker('setDate', d);
        updateOutput();
    });

    $('#preset').change(function() {
        pvlng.changePreset();
        updateOutput();
    });

});
