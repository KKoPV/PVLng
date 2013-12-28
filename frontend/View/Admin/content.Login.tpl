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

<div class="push_3 grid_3" style="margin-top:5em;margin-bottom:5em">

    <p>{{WelcomeToAdministration}}</p>

    <br />

    <form method="POST">

    <div class="ui-widget">

    <table id="logintable">
        <tbody>
        <tr>
            <td><label for="user">{{Name}}</label>:</td>
            <td><input type="text" class="ui-corner-all" id="user" name="user" /></td>
        </tr>

        <tr>
            <td><label for="pass">{{Password}}</label>:</td>
            <td><input type="password" class="ui-corner-all" id="pass" name="pass" /></td>
        </tr>

        <tr>
            <td></td>
            <td>
                <div>
                    <input id="save" type="checkbox" name="save" class="iCheckLine" style="margin-right:0.5em" />
                    <label for="save">{{StayLoggedIn}}</label>
                </div>

            </td>
        </tr>

        <tr>
            <td></td>
            <td><input type="submit" value="{{Login}}" /></td>
        </tr>
        </tbody>
    </table>

    </div>

    </form>

</div>

<div class="clear"></div>
