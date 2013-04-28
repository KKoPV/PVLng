<!--
/**
 *
 *
 * @author      Knut Kohl <github@knutkohl.de>
 * @copyright   2012-2013 Knut Kohl
 * @license     GNU General Public License http://www.gnu.org/licenses/gpl.txt
 * @version     $Id$
 */
-->

<div class="prefix_3" style="margin-top:3em;margin-bottom:7em">

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
				<input id="save" type="checkbox" name="save" />
				<label for="save">{{StayLoggedIn}}</label>
			</td>
		</tr>

		<tr>
			<td></td>
			<td><input class="ui-button ui-widget ui-state-default ui-corner-all ui-priority-primary" type="submit" value="Login" /></td>
		</tr>
		</tbody>
	</table>

	</div>

	</form>

</div>
