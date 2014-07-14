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

<div class="grid_10">

<!-- IF ! {ADMINPASS} -->

<div class="push_3 grid_4">

<h2>Admin user</h2>

<form method="post">

    <table id="adminpass">
    <thead>
        <tr>
            <th></th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td><label for="u">Admin user:</label></td>
            <td><input id="u" type="text" name="u" value="{ADMINUSER}"/></td>
        </tr>
        <tr>
            <td><label for="p1">Password:</label></td>
            <td><input id="p1" type="password" name="p1" /></td>
        </tr>
        <tr>
            <td><label for="p2">Repeat password:</label></td>
            <td><input id="p2" type="password" name="p2" /></td>
        </tr>
    </tbody>
    </table>

    <p><input type="submit" value="Send" /></p>
    &nbsp;

</form>

</div>

<!-- ELSE -->

<div class="push_1 grid_8">

<h2>Admin user</h2>

<p>
    Please update your
    <tt style="font-size:120%"><strong>config/config.php</strong></tt>
    with
</p>

<!-- MUST be pre to avoid compression -->
<pre>
    'Admin' => array(
        'User'     => '{ADMINUSER}',
        'Password' => '{ADMINPASS}'
    ),

</pre>

<h2>Location</h2>

<p>
    For use of all daylight related functions (sunrise, sunset etc.), you need to configure your location.
</p>

<p>
    Just type in your street and city and let Google Maps API find your coordinates :-)
</p>

<p>
    <input id="text" type="text" size="50" placeholder="Street, City, Country" />
    <button id="geoloc" style="margin-left:.5em" class="tipbtn" title="Serach by Google Maps API">Search</button>
</p>

<div id="locresult" style="display:none">

<p>
    Please update your
    <tt style="font-size:120%"><strong>config/config.php</strong></tt>
    with
</p>

<pre id="location"></pre>

<iframe id="map" class="map" width="100%" height="350" frameborder="0"
        scrolling="no" marginheight="0" marginwidth="0"></iframe>

</div>

</div>

<!-- ENDIF -->

</div>

<!-- MUST be pre to avoid compression -->
<pre id="pre" style="display:none">
    'Location' => array(
        'Latitude'  => $lat,
        'Longitude' => $lon
    ),

</pre>
