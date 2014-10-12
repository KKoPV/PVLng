<script>
/**
 *
 *
 * @author      Knut Kohl <github@knutkohl.de>
 * @copyright   2012-2014 Knut Kohl
 * @license     MIT License (MIT) http://opensource.org/licenses/MIT
 * @version     1.0.0
 */

/**
 * http://wublast.wunderground.com/cgi-bin/WUBLAST?lat=51.5&lon=12.1&radius=75&width=480&height=320&key=sat_ir4_thumb&gtt=0&extension=png&proj=me&num=1&delay=25&timelabel=0&basemap=1&borders=1&theme=WUBLAST_WORLD&rand=1408602840&api_key=87aedb10be423d15
 * http://wublast.wunderground.com/cgi-bin/WUBLAST?lat=51.5&lon=12.1&radius=75&width=480&height=320&key=sat_ir4_thumb&gtt=0&extension=png&timelabel=1&basemap=0&borders=1&api_key=87aedb10be423d15
 */

function htmlhelper( tbody ) {

    var tbody = tbody;
    var rows = [];

    this.init = function(force) {
        if (rows.length > 0 && !force) return;

        rows = [ $('<tr/>'), $('<tr/>'), $('<tr/>'), $('<tr/>') ];

        rows[0].append(this.td().prop('colspan', 2));
        rows[1].append(this.td().prop('colspan', 2));
        rows[2].append(this.td('{{Temperature}}').css({ fontWeight: 'bold', textAlign: 'left' }))
               .append(this.td('°C'));
        rows[3].append(this.td('{{Clouds}}').css({ fontWeight: 'bold', textAlign: 'left' }))
               .append(this.td('%'));
    };

    this.td = function( html ) {
        return $('<td/>').html(html);
    };

    this.add = function() {
        this.init();
        if ($('td', rows[0]).length == 13) {
            this.show();
            this.init(true);
        }
        $.each(arguments, function(id, td) { rows[id].append(td) });
    };

    this.show = function() {
        if ($('td', rows[0]).length > 1) {
            $.each(rows, function(id, tr) { tbody.append(tr) });
            tbody.append($('<tr/>').append($('<td/>').html('&nbsp;')));
        }
    };
}

/**
 *
 */
$(function() {

    var APIkey = '{WEATHER_APIKEY}', l = { en: 'EN', de: 'DL' }[language];

    if (!APIkey) {
        $('h3', '#content')
        .html('Missing Wunderground API key please <a href="/settings#controller-Weather-APIkey">configure</a> before');
    } else {

    $.ajax({
        /* Combine geo lookup, hourly and 3 day forcast into one request */
        url: 'http://api.wunderground.com/api/'+APIkey+'/geolookup/hourly/forecast/lang:' + l +
             '/q/' + latitude + ',' + longitude + '.json',
        dataType: 'jsonp',
        success: function(response) {

            $('h3 a', '#content')
                .prop('href', response.location.wuiurl)
                .html(response.location.city + ' / ' + response.location.country_name);

            var html = new htmlhelper($('table tbody', '#content'));

            /* Hourly forecast */
            $.each(response.hourly_forecast, function(id, data) {
                /* https://www.utexas.edu/depts/grg/kimmel/nwsforecasts.html
                   "sky" is the amount expected to be covered by opaque clouds, the type that
                   do not allow other clouds, or blue sky to be visible through or above them.
                   Cloudy                       90-100%
                   Mostly cloudy                70-80%
                   Partly Cloudy/Partly Sunny   30-60%
                   Mostly Clear/Mostly Sunny    10-30%
                   Clear/Sunny                  0-10%
                   Fair                         Less than 40% cloud cover, no
                                                precipitation and no extreme weather */
                var rgb = 255 - (data.sky * 255 / 100).toFixed(0);

                if (data.FCTTIME.hour == 0) {
                    html.add(
                        html.td('<strong>'+data.FCTTIME.weekday_name_abbrev+'<strong>')
                            .prop('rowspan', 4)
                            .css({ backgroundColor: '#F0F0F0' })
                    );
                }

                html.add(
                    html.td($('<img/>').prop('src', data.icon_url).css({ width: '50px', height: '50px' })),
                    html.td(data.FCTTIME.hour + ':00'),
                    html.td(data.temp.metric, true),
                    html.td(data.sky)
                        .css('background-color', 'rgb('+rgb+', '+rgb+', '+rgb+')')
                        .css('color', data.sky < 30 ? 'black' : 'white')
                );

            });

            /* 3 day forecast, EXCEPT today and tomorrow */
            $.each(response.forecast.simpleforecast.forecastday.slice(2), function(id, data) {
                html.add(
                    html.td($('<img/>').prop('src', data.icon_url)).css({ padding: '0 5px' }),
                    html.td(data.date.weekday_short),
                    html.td(data.low.celsius+'-'+data.high.celsius, true),
                    html.td()
                );
            });

            html.show();

            $('#content').find('div').append(
                $('<img/>').prop('src', 'http://wublast.wunderground.com/cgi-bin/WUBLAST?lat='+latitude+'&lon='+longitude+
                                        '&api_key='+APIkey+'&radius=50&width=480&height=320&key=sat_ir4_thumb&gtt=0'+
                                        '&extension=png&timelabel=1&basemap=0&borders=1&_='+(new Date).getTime())
                           .css({ width: '480px', height: '320px' })
            );
        }
    });

    }
});

</script>
