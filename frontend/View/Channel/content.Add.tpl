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

<h2>{{SelectEntityType}}</h2>

<form action="/channel/add" method="post">

<table id="dataTable" class="dataTable">
	<thead>
	<tr>
		<th style="width:1%">
			<input type="radio" name="type" value="" class="iCheck" checked="checked" />
		</th>
		<th>{{EntityType}}</th>
		<th style="width:1%">{{Unit}}</th>
		<th>{{Model}}</th>
		<th style="width:1%"></th>
		<th>{{DESCRIPTION}}</th>
	</tr>
	</thead>
	<tbody>

	<!-- BEGIN ENTITYTYPES -->
	<tr>
		<td>
			<input type="radio" name="type" value="{ID}" class="iCheck" />
		</td>
		<td nowrap>
			<img style="vertical-align:middle;width:16px;height:16px;margin-right:8px"
			     src="{ICON}" width="16" height="16" alt="" />
			<strong>{NAME}</strong>
		</td>
		<td>{UNIT}</td>
		<td>{MODEL}</td>
		<td nowrap>
			<!-- IF {CHILDS} -->
			<img src="/images/ico/node_select_child.png" class="imgbar tip"
				 style="width:16px;height:16px" width="16p" height="16"
			     alt="c" title="{{CanHaveChilds}}" />
			<!-- ELSE -->
			<img src="/images/ico/16x16.png" class="imgbar" alt=""
				 style="width:16px;height:16px" width="16p" height="16" />
			<!-- ENDIF -->
			<!-- IF {WRITE} -->
			<img src="/images/ico/write.png" class="imgbar tip"
				 style="width:16px;height:16px" width="16p" height="16"
			     alt="w" title="{{WritableEntity}}" />
			<!-- ELSE -->
			<img src="/images/ico/16x16.png" class="imgbar" alt=""
				 style="width:16px;height:16px" width="16p" height="16" />
			<!-- ENDIF -->
			<!-- IF {READ} -->
			<img src="/images/ico/read.png" class="tip"
				 style="width:16px;height:16px" width="16p" height="16"
			     alt="r" title="{{ReadableEntity}}" />
			<!-- ENDIF -->
		</td>
		<td><small>{DESCRIPTION}</small></td>
	</tr>
	<!-- END -->

	</tbody>
</table>

<p><input type="submit" value="{{proceed}} &raquo;" /></p>

</form>

</div>

<div class="clear"></div>
