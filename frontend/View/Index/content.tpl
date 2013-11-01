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

<div id="nav" style="margin-top:1em">
	<input type="hidden" id="fromdate" name="fromdate" />
	<input type="hidden" id="todate" name="todate" />

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
		<td style="width:99%;text-align:right">
			<label for="periodcnt" style="margin-right:1em" >{{Aggregation}}:</label>
			<input class="numbersOnly r" style="margin-right:.5em" type="text"
			       id="periodcnt" name="periodcnt" value="1" size="2" />
			{PERIODSELECT} &nbsp;
			<button id="btn-refresh" onclick="updateChart(); return false">{{Refresh}}</button>
		</td>
	</tr>
	</table>
</div>

<div id="chart" style="margin-top: 1em">
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
						{NAME} <!-- IF {PUBLIC} -->({{public}})<!-- ENDIF -->
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

<div class="alpha grid_4">
	<!-- IF {USER} -->
		<a id="togglewrapper" href="#">{{ToggleChannels}}</a>
	<!-- ENDIF -->
</div>

<div class="grid_6 omega" style="text-align:right">
	<input id="az" type="checkbox" />&nbsp;<label for="az">{{SetAxisMinZero}}</label>
</div>

<div class="clear"></div>

<div id="wrapper" style="padding-top:1em">

<!-- IF {USER} == "" -->

	<!--
	Show only channels in public charts and NOT all channel GUIDs
	-->

	<table id="tree" class="dataTable">

		<thead>
		<tr>
			<th style="width:99%">{{Channel}}</th>
			<th>{{Amount}}</th>
			<th>{{Unit}}</th>
		</tr>
		</thead>

		<tbody>

		<!-- BEGIN DATA -->
		<!-- IF {CHECKED} -->  <!-- MUST have also {GRAPH} before :-) -->
			<!-- INCLUDE content.rows.nouser.inc.tpl -->
		<!-- ENDIF -->
		<!-- END -->

		</tbody>

	</table>

<!-- ELSE -->

	<table id="tree" class="dataTable treeTable">
		<thead>
		<tr>
			<th>
				<img id="treetoggle" src="/images/ico/toggle.png"
					 style="width:16px;height:16px"
				     class="tip" onclick="ToggleTree()" alt="[+]"
					 title="#tiptoggle" width="16" height="16" />
				<div id="tiptoggle" style="display:none">{{CollapseAll}}</div>
			</th>
			<th style="width:99%;padding-left:0" class="l">
				<span class="indenter" style="padding-left: 0px;"></span>
				{{Channel}}
			</th>
			<th class="r">{{Amount}}</th>
			<th class="l">{{Unit}}</th>
			<th class="r">{{Earning}}&nbsp;/ {{Cost}}</th>
			<th>
				<img style="width:16px;height:16px" src="/images/ico/node_design.png"
				     alt="" width="16" height="16" />
			</th>
		</tr>
		</thead>

		<tbody>

		<!-- BEGIN DATA -->
			<!-- INCLUDE content.rows.inc.tpl -->
		<!-- END -->

		</tbody>

		<tfoot>
			<tr>
				<th colspan="2">&nbsp;</th>
				<th colspan="2" class="l">{{Total}}</th>
				<th id="costs" style="padding-right:10px" class="r"></th>
				<th></th>
			</tr>
		<tfoot>
	</table>

<!-- ENDIF -->

</div> <!-- wrapper -->

<!-- IF {USER} -->
<h3>
	{{Variants}}
	<img style="margin-left:.5em;width:16px;height:16px" class="tip"
	     src="/images/ico/information_frame.png" width="16" height="16"
	     title="{{MobileVariantHint}}" />
</h3>
<p>
<!-- ELSE -->
<p>
	<span style="margin-right:1em">{{VariantsPublic}}:</span>
<!-- ENDIF -->

	<!-- IF {USER} -->
	<input id="saveview" type="text" name="saveview" value="{VIEW}"/>
	<input style="margin-left:.5em" id="public" type="checkbox" name="public" value="1"
		<!-- IF {VIEWPUBLIC} -->checked="checked"<!-- ENDIF -->
	/>
	<label for="public">{{public}}</label>
	<img style="margin-left:.5em;width:16px;height:16px" class="tip"
	     src="/images/ico/information_frame.png" width="16" height="16"
	     title="{{publicHint}}" />
	<input type="submit" name="save" value="{{Save}}" style="margin:0 3em 0 .5em" />
	<!-- ENDIF -->

	<select id="loaddeleteview" name="loaddeleteview">
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
	<input type="submit" name="load" value="{{Load}}" style="margin-left:.5em" />
	<!-- IF {USER} -->
	<input type="submit" name="delete" value="{{Delete}}" style="margin-left:.5em" />
	<a id="btn-bookmark" class="fr tip" title="{{DragBookmark}}" data-url="/chart/">
		PVLng | {VIEW}
	<!-- ENDIF -->
	</a>
</p>

</form>

<!-- INCLUDE dialog.chart.tpl -->
