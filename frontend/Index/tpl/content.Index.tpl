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
		<td>
			<a href="/channel/edit/{ENTITY}" class="tip" title="{{EditEntity}}">
			<img src="/images/ico/node_design.png" alt="edit" width="16" height="16" /></a>
		</td>
		<td>
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
		<td>
			<!-- IF {LEVEL} != "1" --><!-- IF {UPPER} != "0" -->
			<form action="/index/moveleft" method="post">
			<input type="hidden" name="id" value="{ID}" />
			<input type="image" src="/images/ico/navigation_090_frame.png" alt="up"
						 class="tip nb" title="{{MoveEntityUp}}" style="background-color:transparent" />
			</form>
			<!-- ENDIF --><!-- ENDIF -->
		</td>

		<td>
			<!-- IF {LEVEL} != "1" --><!-- IF {LOWER} != "0" -->
			<form action="/index/moveright" method="post">
			<input type="hidden" name="id" value="{ID}" />
			<input type="image" src="/images/ico/navigation_270_frame.png" alt="down"
						 class="tip" title="{{MoveEntityDown}}" style="background-color:transparent" />
			</form>
			<!-- ENDIF --><!-- ENDIF -->
		</td>


	</tr>

	<!-- END -->

	<tr>
		<td style="padding:16px 0">
			<span class="indenter" style="padding-left: 0px;"></span>
			<a href="#" title="{{AddEntity}}" class="tip" onclick="addChild(1); return false">
				<img src="/images/ico/plus_circle_frame.png" alt="add"
						 width="16" height="16" />
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
