<!--
/**
 *
 *
 * @author      Knut Kohl <github@knutkohl.de>
 * @copyright   2012-2013 Knut Kohl
 * @license     GNU General Public License http://www.gnu.org/licenses/gpl.txt
 * @version     1.0.0
 */
-->

<script src="http://maps.google.com/maps/api/js?sensor=false"></script>

<script>

/**
 *
 */
jQuery.fn.selectText = function(){
    var doc = document, element = this[0], range, selection;

    if (doc.body.createTextRange) {
        range = document.body.createTextRange();
        range.moveToElementText(element);
        range.select();
    } else if (window.getSelection) {
        selection = window.getSelection();
        range = document.createRange();
        range.selectNodeContents(element);
        selection.removeAllRanges();
        selection.addRange(range);
    }
};

/**
 *
 */
var GoogleMapsGeocoder;

/**
 *
 */
$(function() {

    $('#adminpass').DataTable({
        bPaginate: false,
        bLengthChange: false,
        bFilter: false,
        bSort: false,
        bInfo: false,
        bJQueryUI: true
    });

    $('.pre').click(function() {
        /* select all, make ready for copy */
        $(this).select();
    }).mouseup(function(e) {
        e.preventDefault();
    });

    if ('{ADMINPASS}') {

        $('#geoloc').button({
            icons: {
                primary: 'ui-icon-search'
            },
            text: false
        }).click(function(){

            var location = $('#text').val();

            GoogleMapsGeocoder = new google.maps.Geocoder();

            GoogleMapsGeocoder.geocode(
                {
                    address: location
                },
                function(data) {
                    _log('Geo data', data);

                    $("#location").html(
                        $('#pre').html()
                        .replace(   /* Round to 3 decimals */
                            '$lat', Math.round(data[0].geometry.location.lat() * 1000) / 1000
                        ).replace(
                            '$lon', Math.round(data[0].geometry.location.lng() * 1000) / 1000
                        )
                    );

                    /* http://moz.com/ugc/everything-you-never-wanted-to-know-about-google-maps-parameters */
                    var url = 'https://maps.google.com/maps?t=m&source=s_q&ie=UTF8&hq=&z=14&output=embed'+
                              '&q='+encodeURIComponent(location)+
                              '&hnear='+encodeURIComponent(data[0].formatted_address);
                    _log('Map URL', url);

                    $('#map').prop('src', url);
                    $('#locresult').show();
                }
            );

            return false;
        });

        shortcut.add('Enter', function() { $('#geoloc').click(); });
    }
});

</script>
