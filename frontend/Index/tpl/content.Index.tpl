<!--
/**
 *
 *
 * @author      Knut Kohl <github@knutkohl.de>
 * @copyright   2012-2013 Knut Kohl
 * @license     GNU General Public License http://www.gnu.org/licenses/gpl.txt
 * @version     $Id: v1.0.0.2-22-g7bc4608 2013-05-05 22:07:15 +0200 Knut Kohl $
 */
-->

<div>{MESSAGE}</div>

<table id="tree" class="dataTable treeTable">
	<thead>
	<tr>
		<th style="width:99%;text-align:left !important;padding-left:24px !important">
			<img src="/images/ico/toggle.png" id="treetoggle" class="tip"
					 onclick="return ToggleTree()" tip="#tiptoggle" alt="[+]" />
			<div id="tiptoggle" style="display:none">{{CollapseAll}}</div>
		</th>
		<th></th>
		<th></th>
		<th></th>
	</tr>
	</thead>
	<tbody>

	<!-- BEGIN DATA -->

	<tr data-tt-id="{ID}"
	    <!-- IF {PARENT} -->data-tt-parent-id="{PARENT}" <!-- ENDIF -->>
		<td style="padding:0.4em 0">
			<img style="vertical-align:top" width="16" height="16"
					 src="/images/ico/{ICON}" alt="" class="tip" title="{TYPE}" />
			<strong class="tip nb" title="{{ClickForGUID}}" onclick="showGUID('{GUID}')">
				{NAME}<!-- IF {UNIT} --> [{UNIT}]<!-- ENDIF -->
				<!-- IF {DESCRIPTION} --> <small>({DESCRIPTION})</small><!-- ENDIF -->
			</strong>
		</td>
		<td>
			<!-- IF {ACCEPTCHILDS} -->
			<a href="#" onclick="addChild({ID}); return false" class="tip" title="{{AssignEntity}}">
			<img src="/images/ico/node_insert_next.png" alt="add" width="16" height="16" /></a>
			<!-- ENDIF -->
		</td>

		<td style="white-space:nowrap">
			<a href="/channel/edit/{ENTITY}?returnto=index" class="tip" title="{{EditEntity}}">
			<img src="/images/ico/node_design.png" alt="edit" width="16" height="16" /></a>

			<!-- IF {CHILDS} == "0" -->
			<form action="/index/delete" method="post" class="delete-form">
			<input type="hidden" name="id" value="{ID}" />
			<input type="image" src="/images/ico/node_delete_next.png" alt="-"
			       class="tip nb" title="{{DeleteEntity}}" style="background-color:transparent" />
			</form>
			<!-- ELSE -->
			<form action="/index/deletebranch" method="post" class="delete-form">
			<input type="hidden" name="id" value="{ID}" />
			<input type="image" src="/images/ico/node_delete.png" alt="-!"
			       class="tip nb" title="{{DeleteBranch}}" style="background-color:transparent" />
			</form>
			<!-- ENDIF -->
		</td>

		<td style="white-space:nowrap">
			<!-- IF {LEVEL} != "1" AND {UPPER} != "0" -->
			<a href="/index/moveleft" title="{{MoveEntityUp}}" class="tip"
			   onclick="moveChild({ID}, 'moveleft'); return false">
				<img src="/images/ico/navigation_090_frame.png" alt="u" />
			</a>
			<!-- ELSE -->
			<img src="/images/ico/16x16.png" alt="" />
			<!-- ENDIF -->

			<!-- IF {LEVEL} != "1" AND {LOWER} != "0" -->
			<a href="/index/moveright" title="{{MoveEntityDown}}" class="tip"
			   onclick="moveChild({ID}, 'moveright'); return false">
				<img src="/images/ico/navigation_270_frame.png" alt="d" />
			</a>
			<!-- ELSE -->
			<img src="/images/ico/16x16.png" alt="" />
			<!-- ENDIF -->

			<!-- IF {LEVEL} > "2" -->
			<form action="/index/moveup" method="post">
			<input type="hidden" name="id" value="{ID}" />
			<input type="image" src="/images/ico/navigation_180_frame.png" alt="h"
			       class="tip" title="{{MoveEntityLeft}}" style="background-color:transparent" />
			</form>
			<!-- ELSE -->
			<img src="/images/ico/16x16.png" alt="" />
			<!-- ENDIF -->

			<!-- IF {UPPER} != "0" -->
			<form action="/index/movedown" method="post">
			<input type="hidden" name="id" value="{ID}" />
			<input type="image" src="/images/ico/navigation_000_frame.png" alt="l"
			       class="tip" title="{{MoveEntityRight}}" style="background-color:transparent" />
			</form>
			<!-- ELSE -->
			<img src="/images/ico/16x16.png" alt="" />
			<!-- ENDIF -->
		</td>


	</tr>

	<!-- END -->

	<tr>
		<td style="padding:16px 0">
			<span class="indenter" style="padding-left: 0px;"></span>
			<a href="#" title="{{AddEntity}}" class="tip" onclick="addChild(1); return false">
				<img src="/images/ico/plus_circle_frame.png" alt="add" width="16" height="16" />
			</a>
		</td>
		<!-- IF {DATA} -->
		<td></td>
		<!-- ELSE -->
		<td style="width:99%">
			<span style="font-size:150%">&laquo; </span> {{Add1Channel}}
		</td>
		<!-- ENDIF -->
		<td></td>
		<td></td>
	</tr>
	</tbody>

</table>

<p>
	<a class="button" href="/channel/add">{{CreateChannel}}</a>
</p>

<div id="dialog-addchild" style="display:none" title="{{AddChild}}">
	<form id="form-addchild" action="/index/addchild" method="post">
		<p>
		<label for="child">{{SelectEntity}}:</label>
	</p>
		<select id="child" name="child" style="width:100%">
		<!-- BEGIN ENTITIES -->
			<option value="{ID}">
				{TYPE}: {NAME} {DESCRIPTION} - {CHANNEL} [{UNIT}]
			</option>
		<!-- END -->
		</select>
		<input type="hidden" id="parent" name="parent" />
	</form>
</div>

<div id="dialog-confirm" style="display:none" title="{{DeleteEntity}}">
	<span class="ui-icon ui-icon-alert" style="float: left; margin: 0 7px 20px 0;"></span>
	{{ConfirmDeleteTreeItems}}
</div>

<div id="dialog-guid" style="display:none" title="GUID">
	<p>
		<input id="show-guid" class="b c" style="border:0;width:100%" readonly="readonly" />
	</p>
</div>

<div id="dialog-move" style="display:none" title="{{MoveChannel}}">
	<form id="form-movechild" action="" method="post">
	<input type="hidden" name="id" />
	<p>
	    {{MoveChannelHowMuchRows}}
	</p>
	<p class="c">
		<input type="number" step="1" style="width:3em"class="numbersOnly c" name="count" value="1" />
		{{Positions}}
	</p>
	</form>
</div>
