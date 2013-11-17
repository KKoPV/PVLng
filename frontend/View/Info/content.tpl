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

<div class="grid_10">

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
				http://{SERVERNAME}/api/r2/\{GUID\}/\{action\}
			</code>
			<p>{{SeeAPIReference}}</p>
		</td>
	</tr>
	<tr>
		<td style="vertical-align:top">{{YourAPIcode}}</td>
		<td>
			<code class="b">X-PVLng-Key: {APIKEY}</code>
			<form method="post" style="float:right">
				<input type="hidden" name="regenerate" value="1" />
				<input id="regenerate" type="submit" value="{{Regenerate}}" />
			</form>
			<div class="clear"/></div>
			<p>{{DontForgetUpdateAPIKey}}</p>
		</td>
	</tr>
	</tbody>
</table>

<h3>{{Statistics}}</h3>

<table id="table-stats">
	<thead>
	<tr>
		<th class="l">{{ChannelName}}</th>
		<th class="l">{{Description}}</th>
		<th class="l">{{Serial}}</th>
		<th class="l">{{Channel}}</th>
		<th class="r">{{Readings}}</th>
	</tr>
	</thead>

	<tbody>

	<!-- BEGIN STATS -->

	<tr>
		<td>
			<img src="/images/ico/{ICON}" style="width:16px;height:16px;margin-right:8px"
			     width="16" height="16" alt="" />
			{NAME}
		</td>
		<td>{DESCRIPTION}</td>
		<td>{SERIAL}</td>
		<td>{CHANNEL}</td>
		<td class="r">{numf:READINGS}</td>
	</tr>

	<!-- END -->

	</tbody>

	<tfoot>
	<tr>
		<th colspan="3"></th>
		<th class="l">{{Total}}</th>
		<th class="r">{numf:READINGS}</th>
	</tr>
	</tfoot>

</table>

</div>

<div class="clear"></div>
