<!--
/**
 *
 *
 * @author      Knut Kohl <github@knutkohl.de>
 * @copyright   2012-2014 Knut Kohl
 * @license     MIT License (MIT) http://opensource.org/licenses/MIT
 * @version     1.0.0
 */
-->

<script src="//maps.google.com/maps/api/js?sensor=false"></script>

<script>

/**
 *
 */
$(function() {

    var geocoder;

    $('#geoloc').button({
        icons: { primary: 'ui-icon-search' }, text: false
    }).click(function(){

        $('#map-wrapper').hide();

        var location = $('#text').val();

        if (!location) return;

        if (!geocoder) geocoder = new google.maps.Geocoder();

        geocoder.geocode(
            { address: location },
            function(data) {
                // console.log(data);
                if (!data.length) {
                    $.alert('No location found.', 'Sorry');
                    return;
                }

                $('#lat').val(data[0].geometry.location.lat().toFixed(4));
                $('#lon').val(data[0].geometry.location.lng().toFixed(4));

                /* http://moz.com/ugc/everything-you-never-wanted-to-know-about-google-maps-parameters */
                var url = 'https://maps.google.com/maps?t=m&source=s_q&ie=UTF8&hq=&z=14&output=embed' +
                          '&q=' + encodeURIComponent(data[0].formatted_address);
                // console.log(url);
                $('#map').prop('src', url);
                $('#map-wrapper').show();
            }
        );
    });

    $('#save-loc').button({
        icons: { primary: 'ui-icon-disk' }, text: false
    }).click(function(){
        this.form.submit();
    });

    shortcut.add('Enter', function() { $('#geoloc').click(); });
});

</script>
