/**
 *
 *
 * @author      Knut Kohl <github@knutkohl.de>
 * @copyright   2012-2014 Knut Kohl
 * @license     MIT License (MIT) http://opensource.org/licenses/MIT
 * @version     1.0.0
 */
var geocoder;

/**
 *
 */
function geoCodeLocation() {
    var location = $('#text').val();

    if (!location) return;

    /* Remove PVLng-Key from request */
    $.ajaxSetup({ beforeSend: function(){} });

    $.getJSON(
        'http://maps.googleapis.com/maps/api/geocode/json',
        { address: location },
        function(data) {
            console.log(data);
            if (!data.results.length) {
                alert('Sorry, no location found.');
                return;
            }

            var res = data.results[0], loc = res.geometry.location;

            $('#lat').val(loc.lat.toFixed(4)*1);
            $('#lon').val(loc.lng.toFixed(4)*1);

            /* http://moz.com/ugc/everything-you-never-wanted-to-know-about-google-maps-parameters */
            var url = 'https://maps.google.com/maps?' +
                      't=m&source=s_q&ie=UTF8&hq=&z=14&output=embed' +
                      '&q=' + encodeURIComponent(res.formatted_address);

            // console.log(url);
            $('#map').prop('src', url);
            $('#map-wrapper').show();
        }
    );
}


/**
 *
 */
$(function() {

    $('#text').keypress(function(e) { (e.keyCode == 13) && geoCodeLocation() });
    $('#geoloc').button().click(geoCodeLocation);
/*
    $('#save-loc').button().click(function(){ this.form.submit();  });
*/
    shortcut.add('Enter', function() { $('#geoloc').click(); });
});
