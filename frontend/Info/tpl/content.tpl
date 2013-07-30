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

<h3>{{SystemInformation}}</h3>

<table id="table-info">
	<thead>
	<tr>
		<th></th>
		<th></th>
	</tr>
	</thead>

	<tbody>
	<tr>
		<td style="vertical-align:top">{{APIURL}}</td>
		<td>
			<code class="b">
				http://{SERVERNAME}/api/r1/\{GUID\}.\{format\}<br />
				http://{SERVERNAME}/api/r2/\{GUID\}/\{action\}
			</code>
			<br /><br />
			{{SeeAPIReference}}
		</td>
	</tr>
	<tr>
		<td style="vertical-align:top">{{YourAPIcode}}</td>
		<td>
			<code class="b">{APIKEY}</code>
			<form method="post" style="float:right">
				<input type="hidden" name="regenerate" value="1" />
				<input id="regenerate" type="submit" value="{{Regenerate}}" />
			</form>
			<div class="clear"/></div>
			<p>
				{{DontForgetUpdateAPIKey}}
			</p>
		</td>
	</tr>
	</tbody>
</table>

<h3>{{Statistics}}</h3>

<table id="table-stats">
	<thead>
	<tr>
		<th></th>
		<th class="l">{{Name}}</th>
		<th class="l">{{Description}}</th>
		<th class="l">{{Serial}}</th>
		<th class="l">{{Channel}}</th>
		<th class="r">{{Readings}}</th>
	</tr>
	</thead>

	<tbody>

	<!-- BEGIN STATS -->

	<tr>
		<td><img src="/images/ico/{ICON}" width="16" height="16" alt="" /></td>
		<td>{NAME}</td>
		<td>{DESCRIPTION}</td>
		<td>{SERIAL}</td>
		<td>{CHANNEL}</td>
		<td class="r" style="padding-right:18px">{numf:READINGS}</td>
	</tr>

	<!-- END -->

	</tbody>

	<tfoot>
	<tr>
		<th colspan="4"></th>
		<th class="l">{{Total}}</th>
		<th class="r">{numf:READINGS}</th>
	</tr>
	</tfoot>

</table>
