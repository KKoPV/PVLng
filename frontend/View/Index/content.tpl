<!--
/**
 *
 *
 * @author      Knut Kohl <github@knutkohl.de>
 * @copyright   2012-2013 Knut Kohl
 * @license     GNU General Public License http://www.gnu.org/licenses/gpl.txt
 * @version     $Id: v1.0.0.2-19-gf67765b 2013-05-05 22:03:31 +0200 Knut Kohl $
 */
-->

<form method="post" action="/index">

<input type="hidden" id="fromdate" name="fromdate" />
<input type="hidden" id="todate" name="todate" />

<div id="nav" class="grid_10" style="margin-top:1em">

	<table style="width:100%">
	<tr>
		<td>
			<span class="ui-icon ui-icon-triangle-1-w tip"
			      title="{{PrevDay}}" onclick="changeDates(-1)"></span>
		</td>
		<td>
			<input class="c" type="text" id="from" name="from" size="10" />
		</td>
		<td style="padding:0 .75em;font-weight:bold">
			&mdash;
		</td>
		<td>
			<input class="c" type="text" id="to" name="to" size="10" />
		</td>
		<td>
			<span class="ui-icon ui-icon-triangle-1-e tip"
			      title="{{NextDay}}" onclick="changeDates(1)"></span>
		</td>
		<td>
			<button id="btn-reset" style="margin-left:1em">{{Today}}</button>
		</td>
		<td style="width:99%;text-align:right">
			<label for="periodcnt" style="margin-right:1em" >{{Aggregation}}:</label>
			<input class="numbersOnly r" style="margin-right:.5em" type="text"
			       id="periodcnt" name="periodcnt" value="1" size="2" />
			{PERIODSELECT} &nbsp;
			<button id="btn-refresh">{{Refresh}}</button>
		</td>
	</tr>
	</table>
</div>

<div class="clear"></div>

<div id="chart" class="grid_10" style="margin-top: 1em">
	<div id="chart-placeholder">
	<!-- IF {VIEW} -->
		<p>
			<img style="width:48px;height47px" src="/images/loading.gif"
			     alt="{{JustAMoment}}" width="48" height="47" />
		</p>
	<!-- ELSE -->
		<!-- IF {USER} -->
			{{NoChannelsSelectedYet}}
		<!-- ELSE -->
			{{NoViewSelectedYet}}
		<!-- ENDIF -->
		<br /><br />

		<span style="margin-right:1em">{{Variants}}:</span>

		<select name="loadview" onChange="this.form.submit()">
			<option value="">--- {{Select}} ---</option>
			<!-- BEGIN VIEWS -->
				<!-- IF {__USER} -->
					<!-- show all charts and mark public charts -->
					<option value="{NAME}" <!-- IF {SELECTED} -->selected="selected"<!-- ENDIF -->>
						{NAME} <!-- IF {PUBLIC} --> ({{public}})<!-- ENDIF -->
					</option>
				<!-- ELSEIF {PUBLIC} -->
					<!-- show only public charts -->
					<option value="{NAME}" <!-- IF {SELECTED} -->selected="selected"<!-- ENDIF -->>
						{NAME}
					</option>
				<!-- ENDIF -->
			<!-- END -->
		</select>
		<noscript>
			<input type="submit" name="load" value="{{Load}}" style="margin-left:.5em" />
		</noscript>
	<!-- ENDIF -->
	</div>
</div>

<div class="clear"></div>

<div class="grid_4">
	<a id="togglewrapper" href="#">{{ToggleChannels}}</a>
</div>

<div class="grid_6" style="text-align:right">
	<input id="az" type="checkbox" />&nbsp;<label for="az">{{SetAxisMinZero}}</label>
</div>

<div class="clear"></div>

<div id="wrapper" class="grid_10" style="padding-top:1em">
	<!-- IF {USER} -->
		<!-- INCLUDE datatable.inc.tpl -->
	<!-- ELSE -->
		<!-- INCLUDE datatable.nouser.inc.tpl -->
	<!-- ENDIF -->
</div>

<div class="clear"></div>

<!-- IF {USER} -->
	<!-- INCLUDE variants.inc.tpl -->
<!-- ELSE -->
	<!-- INCLUDE variants.nouser.inc.tpl -->
<!-- ENDIF -->

</form>

<!-- INCLUDE dialog.chart.tpl -->
