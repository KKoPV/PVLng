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

<div class="push_1 grid_8">

    <p>
        For use of all daylight related functions (sunrise, sunset etc.), you need to configure your location.
    </p>

    <p>
        Just type in your street and city and let Google Maps API find your coordinates :-)
    </p>

    <p>
        <input id="text" type="text" size="60" placeholder="Street, City, Country">
        <button id="geoloc" style="margin-left:.5em" class="tipbtn" title="Serach by Google Maps API">Search</button>
    </p>

    <div id="map-wrapper" style="display:none">
        <iframe id="map" class="map" width="100%" height="350" frameborder="0"
                scrolling="no" marginheight="0" marginwidth="0"></iframe>

        <p>
            <form id="loc-form" action="/location" method="post">
            Latitude:<input id="lat" type="text" class="loc" name="loc[Latitude]" readonly="readonly">
            Longitude:<input id="lon" type="text" class="loc" name="loc[Longitude]" readonly="readonly">
            <button id="save-loc">Save</button>
            </form>
        </p>
    </div>

</div>
