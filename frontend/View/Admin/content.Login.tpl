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

<div class="push_3 grid_7" style="margin-top:5em;margin-bottom:5em">

    <p>{{WelcomeToAdministration}}</p>

    <br />

    <form method="POST">

    <div class="ui-widget">

    <table id="logintable">
        <tbody>
        <tr>
            <td><label for="pass">{{Password}}</label>:</td>
            <td><input type="password" class="ui-corner-all" id="pass" name="pass"></td>
        </tr>

        <tr>
            <td></td>
            <td>
                <div class="fl" style="margin-right:0.5em">
                    <input id="save" type="checkbox" name="save" class="iCheck">
                </div>
                <label for="save">{{StayLoggedIn}}</label>
            </td>
        </tr>

        <tr>
            <td></td>
            <td><input type="submit" value="{{Login}}"></td>
        </tr>
        </tbody>
    </table>

    </div>

    </form>

</div>

<div class="clear"></div>
