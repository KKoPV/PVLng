/**
 * Use this file to inject your own scripts
 *
 * Copy this to custom/hook.script.js and insert your coding
 *
 * @author     Knut Kohl <github@knutkohl.de>
 * @copyright  2012-2014 Knut Kohl
 * @license    MIT License (MIT) http://opensource.org/licenses/MIT
 * @version    1.0.0
 */

/**
 * DON'T insert "script" tags here!
 */

/**
 * Example to display current system load in header or footer
 * /

$(function() {

    if (user) {

        // API call works only for logged in user

        // Adjust if needed
        var loadWarning = 2;
        var loadAlert   = 5;

        // Color the load
        function colorLoad(load) {
            var el = $('<span/>').css('fontWeight', 'bold').text(load.toFixed(2)),
                color = 'green';
            if (load > loadAlert) color = 'red'; else
            if (load > loadWarning) color = 'orange';
            return el.css('color', color);
        }

        // Insert below title
        $('#title1').parent().append('<br>').append(
            $('<span/>').addClass('load')
        );

        // Append to footer
        $('.extra', '#footer').append(
            $('<span/>').addClass('load')
        );

        function showLoad() {
            $.getJSON(PVLngAPI + 'status', function(status) {
                // Insert below title ...
                var el = $('.load', '#header').empty();
                // OR append to footer
                var el = $('.load', '#footer').empty().append('/ System load : ');

                el.append(colorLoad(status.load.minutes_1))
                  .append(', ')
                  .append(colorLoad(status.load.minutes_5))
                  .append(', ')
                  .append(colorLoad(status.load.minutes_15));

                setTimeout(showLoad, 5 * 60 * 1000);
            });
        }

        showLoad();
    }

});

/* end system load */
