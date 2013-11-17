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

<!-- Use this image as spacer for not available moving actions of channels -->
<!-- DEFINE MACRO SpacerImg -->
<img src="/images/ico/16x16.png" class="imgbar" alt=""
     style="width:16px;height:16px" width="16p" height="16" />
<!-- END DEFINE -->

<div class="grid_10">

<div>{MESSAGE}</div>

<br />

<table id="tree" class="dataTable treeTable">
	<thead>
	<tr>
		<th style="text-align:left !important">
			<img src="/images/ico/toggle.png" id="treetoggle" class="fl tip"
			     style="width:16px;height:16px" width="16p" height="16"
			     onclick="return ToggleTree()" tip="#tiptoggle" alt="[+]" />
			<div class="c">{{ChannelHierarchy}}</div>
			<div id="tiptoggle" style="display:none">{{CollapseAll}}</div>
		</th>
		<th></th>
		<th></th>
		<th>GUID</th>
	</tr>
	</thead>

	<tbody>

	<!-- BEGIN DATA -->

	<tr data-tt-id="{ID}"
	    <!-- IF {PARENT} -->data-tt-parent-id="{PARENT}" <!-- ENDIF -->>
		<td style="width:90%">
			<img style="vertical-align:top;width:16px;height:16px;margin-right:8px"
			     width="16" height="16" class="tip" title="{TYPE}"
			     src="/images/ico/{ICON}" alt="" />
			<strong>{NAME}</strong>
			<!-- IF {UNIT} --> [{UNIT}]<!-- ENDIF -->
			<!-- IF {DESCRIPTION} --> ({DESCRIPTION})<!-- ENDIF -->
		</td>

		<td style="white-space:nowrap">
			<!-- IF {ACCEPTCHILDS} -->
			<a href="#" onclick="addChild({ID}); return false" class="tip"
			   title="{{AssignEntity}}">
				<img src="/images/ico/node_insert_next.png" class="imgbar" alt="add"
				     style="width:16px;height:16px" width="16p" height="16" />
			</a>
			<!-- ELSE --><!-- MACRO SpacerImg --><!-- ENDIF -->

			<!-- IF {CHILDS} == "0" -->
			<form action="/overview/delete" method="post" class="delete-form">
			<input type="hidden" name="id" value="{ID}" />
			<input type="image" src="/images/ico/node_delete_next.png" alt="-"
			       class="imgbar tip nb" title="{{DeleteEntity}}" style="background-color:transparent" />
			</form>
			<!-- ELSE -->
			<form action="/overview/deletebranch" method="post" class="delete-form">
			<input type="hidden" name="id" value="{ID}" />
			<input type="image" src="/images/ico/node_delete.png" alt="-!"
			       class="imgbar tip nb" title="{{DeleteBranch}}" style="background-color:transparent" />
			</form>
			<!-- ENDIF -->

			<a href="/channel/edit/{ENTITY}?returnto=overview" class="tip" title="{{EditEntity}}">
				<img src="/images/ico/node_design.png" class="imgbar" alt="edit"
				     style="width:16px;height:16px" width="16p" height="16" />
			</a>
		</td>

		<td style="white-space:nowrap">
			<!-- IF {LEVEL} != "1" AND {UPPER} != "0" -->
			<a href="/overview/moveleft" title="{{MoveEntityUp}}" class="tip"
			   onclick="return moveChild({ID}, 'moveleft')">
				<img src="/images/ico/navigation_090_frame.png" class="imgbar" alt="u"
				     style="width:16px;height:16px" width="16p" height="16" />
			</a>
			<!-- ELSE --><!-- MACRO SpacerImg --><!-- ENDIF -->

			<!-- IF {LEVEL} != "1" AND {LOWER} != "0" -->
			<a href="/overview/moveright" title="{{MoveEntityDown}}" class="tip"
			   onclick="return moveChild({ID}, 'moveright')">
				<img src="/images/ico/navigation_270_frame.png" class="imgbar" alt="d"
				     style="width:16px;height:16px" width="16p" height="16" />
			</a>
			<!-- ELSE --><!-- MACRO SpacerImg --><!-- ENDIF -->

			<!-- IF {LEVEL} > "2" -->
			<form action="/overview/moveup" method="post">
			<input type="hidden" name="id" value="{ID}" />
			<input type="image" src="/images/ico/navigation_180_frame.png" alt="h"
			       class="imgbar tip" title="{{MoveEntityLeft}}" style="background-color:transparent" />
			</form>
			<!-- ELSE --><!-- MACRO SpacerImg --><!-- ENDIF -->

			<!-- IF {UPPER} != "0" -->
			<form action="/overview/movedown" method="post">
			<input type="hidden" name="id" value="{ID}" />
			<input type="image" src="/images/ico/navigation_000_frame.png" alt="l"
			       class="imgbar tip" title="{{MoveEntityRight}}" style="background-color:transparent" />
			</form>
			<!-- ELSE --><!-- MACRO SpacerImg --><!-- ENDIF -->
		</td>

		<td>
			<input style="background-color:transparent;border:0;width:27em;font-family:monospace"
			       class="guid" value="{GUID}" readonly="readonly" />
		</td>
	</tr>

	<!-- END -->

	</tbody>

	<tfoot>
	<tr>
		<th style="padding-top:8px;padding-bottom:8px;text-align:left">
			<span class="indenter" style="padding-left: 0px;"></span>
			<a href="#" title="{{AddChannel}}" class="tip" onclick="addChild(1); return false">
				<img src="/images/ico/plus_circle_frame.png" alt="add"
				     style="width:16px;height:16px" width="16p" height="16" />
			</a>
		</th>
		<th colspan="3"></th>
	</tr>
	<tfoot>
</table>

<p>
	<a class="button" href="/channel/add">{{CreateChannel}}</a>
</p>

<div id="dialog-addchild" style="display:none" title="{{AddChild}}">
	<form id="form-addchild" action="/overview/addchild" method="post">
		<div id="add1child">
		<p>
			<label for="child">{{SelectEntity}}:</label>
		</p>
		<select id="child" name="child[]" style="width:100%;margin-bottom:0.5em">
			<option value="">--- {{Select}} ---</option>
		<!-- BEGIN ENTITIES -->
			<option value="{ID}">
				{TYPE}: {NAME}
				<!-- IF {DESCRIPTION} --> ({DESCRIPTION})<!-- ENDIF -->
				<!-- IF {CHANNEL} -->  - {CHANNEL}<!-- ENDIF -->
				<!-- IF {UNIT} --> [{UNIT}]<!-- ENDIF -->
			</option>
		<!-- END -->
		</select>
		</div>
		<input type="hidden" id="parent" name="parent" />
	</form>
	<img src="/images/ico/plus_circle_frame.png" alt="[new select]"
	     style="width:16px;height:16px" width="16p" height="16"
		 onclick="$('#form-addchild').append($('#child').clone().removeAttr('id')); return false"
		 class="tip" title="{{AddAnotherChild}}" />
</div>

<div id="dialog-confirm" style="display:none" title="{{DeleteEntity}}">
	<span class="ui-icon ui-icon-alert" style="float: left; margin: 0 7px 20px 0;"></span>
	{{ConfirmDeleteTreeItems}}
</div>

</div>

<div class="clear"></div>

<!-- Move channel dialog -->

<div id="dialog-move" style="display:none" title="{{MoveChannel}}">
	<form id="form-movechild" action="" method="post">
	<input type="hidden" name="id" />
	<p>
	    {{MoveChannelHowMuchRows}}
	</p>
	<p>
		<div style="float:left;width:25px">
			<input type="radio" class="iCheck" id="countmax" name="countmax" value="0" checked="checked" />
		</div>
		<label for="countmax">
			<input type="number" step="1" style="width:3em"class="numbersOnly" name="count" value="1" />
			{{Positions}}
		</label>
	</p>
	<p>
		<div style="float:left;width:25px">
			<input type="radio" class="iCheck" id="movecountmax" name="countmax" value="1" />
		</div>
		<label for="movecountmax">{{MoveChannelStartEnd}}</label>
	</p>

	</form>
</div>
