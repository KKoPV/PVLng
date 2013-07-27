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

<form method="post">

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
			<label for="periodcnt" style="margin-left:1em" >{{Aggregation}}:</label>
			<input class="numbersOnly r" type="text" id="periodcnt" name="periodcnt"
						 value="1" size="2" />
			{PERIODSELECT} &nbsp; <button id="btn-refresh" onclick="updateChart(); return false">{{Refresh}}</button>
		</td>
	</tr>
	</table>
</div>

<div id="chart" style="margin-top: 1em">
	<div id="chart-placeholder">
		{{NoChannelsSelectedYet}} &nbsp;
		<a id="btn-go" href="#view">{{GoToVariants}}</a>
	</div>
</div>

<div class="alpha grid_4">
	<!-- IF {USER} -->
		<a id="togglewrapper" href="#">{{HideChannels}}</a>
	<!-- ENDIF -->
</div>

<div class="grid_6 omega" style="text-align:right">
	<input id="az" type="checkbox" />&nbsp;<label for="az">{{SetAxisMinZero}}</label>
</div>

<div class="clear"></div>

<div id="wrapper" style="padding-top:1em<!-- IF {USER} == "" -->;display:none<!-- ENDIF -->">

	<table id="tree" class="dataTable treeTable">
		<thead>
		<tr>
			<th>
				<img id="treetoggle" src="/images/ico/toggle.png"
				     class="tip" onclick="ToggleTree()" alt="[+]" title="#tiptoggle" />
				<div id="tiptoggle" style="display:none">{{CollapseAll}}</div>
			</th>
			<th style="width:99%;padding-left:0" class="l">
				<span class="indenter" style="padding-left: 0px;"></span>
				{{Channel}}
			</th>
			<th class="r">{{Amount}}</th>
			<th class="l">{{Unit}}</th>
			<th class="r">{{Cost}}</th>
			<th><img src="/images/ico/node_design.png" alt="" /></th>
		</tr>
		</thead>

		<tbody>

		<!-- BEGIN DATA -->

		<tr data-tt-id="{ID}" <!-- IF {PARENT} -->data-tt-parent-id="{PARENT}" <!-- ENDIF -->>
			<td>
				<!-- IF {GRAPH} -->
				<input id="c{ID}" class="channel iCheck" type="checkbox"	name="v[{ID}]"
				       data-id="{ID}" data-guid="{GUID}" data-unit="{UNIT}"
				       value='{PRESENTATION}'
							 <!-- IF {CHECKED} -->checked="checked"<!-- ENDIF --> />
				<!-- ENDIF -->
			</td>
			<td style="padding:0.4em 0">
				<img style="vertical-align:middle" class="tip"
				     src="/images/ico/{ICON}" alt="" title="{TYPE}" />
				<span class="tip" title="{GUID}">
					{NAME} <!-- IF {DESCRIPTION} -->({DESCRIPTION})<!-- ENDIF -->
				</span>
				<img id="s{ID}" src="/images/spinner.gif" style="float:right;display:none" />
			</td>
			<td id="cons{ID}" class="consumption r"></td>
			<td id="u{ID}">{UNIT}</td>
			<td id="costs{ID}" class="costs r"></td>
			<td>
				<!-- IF {GRAPH} -->
				<img src="/images/ico/chart.png" onclick="ChartDialog({ID}, '{NAME}')"
						 class="tip" title="{{ChartSettingsTip}}" style="cursor:pointer"/>
				<!-- ENDIF -->
			</td>
		</tr>

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

</div>

<!-- IF {USER} -->
<h3>
	<a name="view"></a>
		{{Variants}}
		<img src="/images/ico/information_frame.png" class="tip"
		     title="{{MobileVariantHint}}" />
</h3>
<p>
<!-- ELSE -->
<p>
	<span style="margin-right:1em">{{VariantsPublic}}:</span>
<!-- ENDIF -->

	<!-- IF {USER} -->
	<input id="saveview" type="text" name="saveview" value="{VIEW}"/>
	<input id="public" type="checkbox" name="public" value="1" 
		<!-- IF {VIEWPUBLIC} -->checked="checked"<!-- ENDIF -->
	/>
	<label for="public">{{public}}</label>
	<img src="/images/ico/information_frame.png" class="tip"
	     title="{{publicHint}}" />
	<input type="submit" name="save" value="{{Save}}" style="margin-right:2em" />
	<!-- ENDIF -->

	<select id="loadview" name="loadview">
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
	<input type="submit" name="load" value="{{Load}}" style="margin-left:.5em" />
	<!-- IF {USER} -->
	<input type="submit" name="delete" value="{{Delete}}" style="margin-left:.5em" />
	<!-- ENDIF -->
	<a id="btn-bookmark" class="fr tip" title="{{DragBookmark}}" data-url="/chart/">
		PVLng | {VIEW}
	</a>
</p>

</form>

<div id="dialog-chart" style="display:none" title="{{ChartSettings}}">
	<table id="d-table">
		<tbody>
		<tr>
			<td>
				{{Axis}}
			</td>
			<td id="td-axis">
				<input type="radio" name="d-axis" value="9" />
				<input type="radio" name="d-axis" value="7" />
				<input type="radio" name="d-axis" value="5" />
				<input type="radio" name="d-axis" value="3" />
				<input type="radio" name="d-axis" value="1" />
				<img style="margin:0 0.5em 0.7em;vertical-align:top" src="/images/chart.png" width="35" height="18" />
				<input type="radio" name="d-axis" value="2" />
				<input type="radio" name="d-axis" value="4" />
				<input type="radio" name="d-axis" value="6" />
				<input type="radio" name="d-axis" value="8" />
				<input type="radio" name="d-axis" value="10" />
			</td>
		</tr>
		<tr>
			<td>
				<label for="d-type">{{SeriesType}}</label>
			</td>
			<td>
				<select id="d-type">
					<option value="line">Line</option>
					<option value="spline">Spline</option>
					<option value="areasplinerange">Spline min/max</option>
					<option value="areaspline">Spline with area</option>
					<option value="bar">Bar</option>
					<option value="scatter">Scatter</option>
				</select>
			</td>
		</tr>
		<tr>
			<td>
				<label for="d-cons">{{Presentation}}</label>
			</td>
			<td>
				<div class="fl">
					<input type="checkbox" id="d-cons" class="iCheckLine tip" />
					<label for="d-cons">{{ShowConsumption}}</label>
				</div>
				<img src="/images/ico/information_frame.png" class="tip"
				     style="margin-left:.5em" title="{{ShowConsumptionHint}}" />
			</td>
		</tr>
		<tr>
			<td>
				<label for="d-style">{{dashStyle}}</label>
			</td>
			<td>
				<select id="d-style">
					<option value="">None</option>
					<option value="Solid">Solid</option>
					<option value="LongDash">Long Dash</option>
					<option value="Dash">Dash</option>
					<option value="Dot">Dot</option>
					<option value="DashDot">Dash-Dot</option>
					<optgroup label="Long">
						<option value="LongDashDot">Dash-Dot</option>
						<option value="LongDashDotDot">Dash-Dot-Dot</option>
					</optgroup>
					<optgroup label="Short">
						<option value="ShortDash">Dash</option>
						<option value="ShortDot">Dot</option>
						<option value="ShortDashDot">Dash-Dot</option>
						<option value="ShortDashDotDot">Dash-Dot-Dot</option>
					</optgroup>
				</select>
			</td>
		</tr>
		<tr>
			<td>{{LineWidth}}</td>
			<td>
				<input type="checkbox" id="d-bold" class="iCheckLine" />
				<label for="d-bold">{{LineBold}}</label>
			</td>
		</tr>
		<tr>
			<td>
				{{MarkExtremes}}
			</td>
			<td>
				<div style="float:left;margin-right:2em">
					<input type="checkbox" id="d-min" class="iCheckLine" />
					<label for="d-min">{{min}}</label>
				</div>
				<div style="float:left">
					<input type="checkbox" id="d-max" class="iCheckLine" />
					<label for="d-max">{{max}}</label>
				</div>
			</td>
		</tr>
		<tr>
			<td>
				<label for="d-color">{{Color}}</label>
			</td>
			<td>
				<input id="spectrum" type="color" id="d-color" />
			</td>
		</tr>
		</tbody>
	</table>
</div>
