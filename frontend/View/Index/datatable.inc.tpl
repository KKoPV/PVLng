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

<table id="tree" class="dataTable treeTable">
	<thead>
	<tr>
		<th>
			<img id="treetoggle" src="/images/ico/toggle.png"
				 style="width:16px;height:16px" width="16" height="16"
			     class="tip"onclick="ToggleTree()" alt="[+]" title="#tiptoggle" />
			<div id="tiptoggle" style="display:none">{{CollapseAll}}</div>
		</th>
		<th style="width:70%" class="l">
			<span class="indenter" style="padding-left: 0px;"></span>
			{{Channel}}
		</th>
		<th>
			<img src="/images/ico/16x16.png" style="width:16px;height:16px" width="16" height="16" alt="" />
		</th>
		<th class="r">{{Amount}}</th>
		<th class="l">{{Unit}}</th>
		<th class="r">{{Earning}}&nbsp;/ {{Cost}}</th>
		<th>
			<img src="/images/ico/node_design.png" style="width:16px;height:16px" width="16" height="16" alt="" />
		</th>
	</tr>
	</thead>

	<tbody>
		<!-- BEGIN DATA -->
		<tr data-tt-id="{ID}" <!-- IF {PARENT} -->data-tt-parent-id="{PARENT}" <!-- ENDIF -->
		    <!-- IF !{GRAPH} -->class="no-graph"<!-- ENDIF -->>
			<td>
				<!-- IF {GRAPH} -->
				<input id="c{ID}" class="channel iCheck" type="checkbox" name="v[{ID}]"
				       data-id="{ID}" data-guid="{GUID}" data-unit="{UNIT}"
				       value='{PRESENTATION}'
							 <!-- IF {CHECKED} -->checked="checked"<!-- ENDIF --> />
				<!-- ENDIF -->
			</td>
			<td style="padding:0.4em 0">
				<img style="vertical-align:middle;width:16px;height:16px;margin-right:8px"
				     class="tip" src="/images/ico/{ICON}" alt=""
				     title="{TYPE}" width="16" height="16"/>
				<strong class="tip" title="{GUID}">{NAME}</strong>
				<!-- IF {DESCRIPTION} --> ({DESCRIPTION})<!-- ENDIF -->
			</td>
			<td>
				<img id="s{ID}" src="/images/spinner.gif" width="16" height="16"
				     style="float:right;display:none;width:16px;height:16px" />
			</td>
			<td id="cons{ID}" class="consumption r"></td>
			<td id="u{ID}">{UNIT}</td>
			<td id="costs{ID}" class="costs r"></td>
			<td>
				<!-- IF {GRAPH} -->
				<img style="cursor:pointer;width:16px;height:16px"
				     src="/images/ico/chart.png" onclick="ChartDialog({ID}, '{NAME}')"
				     class="tip" title="{{ChartSettingsTip}}" width="16" height="16" />
				<!-- ENDIF -->
			</td>
		</tr>
		<!-- END -->
	</tbody>

	<tfoot>
		<tr>
			<th colspan="3">&nbsp;</th>
			<th colspan="2" class="l">{{Total}}</th>
			<th id="costs" style="padding-right:10px" class="r"></th>
			<th></th>
		</tr>
	<tfoot>
</table>
