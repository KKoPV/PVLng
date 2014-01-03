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

<div class="grid_10" style="margin-top:4em;margin-bottom:4em">

<!-- IF {ADMINPASS} -->

<p>
    Please update your
    <tt style="font-size:120%"><strong>config/config.php</strong></tt>
    with
</p>

<pre id="code" style="padding:1em 0.5em; border:dashed 1px #AAA; background-color:#EEE">
    'Admin' => array(
        'User'     => '{ADMINUSER}',
        'Password' => '{ADMINPASS}'
    ),
</pre>

<!-- ELSE -->

<div class="push_3 grid_4">

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

<!-- ENDIF -->

</div>
