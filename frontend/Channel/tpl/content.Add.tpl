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

<h2>{{SelectEntityType}}</h2>

<form action="/channel/add" method="post">

<input type="hidden" name="action" value="add" />

<table id="dataTable" class="dataTable">
	<thead>
	<tr>
		<th></th>
		<th>{{EntityType}}</th>
		<th>{{Unit}}</th>
		<th>{{Model}}</th>
		<th></th>
		<th>{{DESCRIPTION}}</th>
	</tr>
	</thead>
	<tbody>
	<!-- BEGIN ENTITYTYPES -->
	<tr>
		<td>
			<input type="radio" name="type" value="{ID}" />
		</td>
		<td nowrap>
			<img style="vertical-align:middle;margin-right:5px"
			     src="/images/ico/{ICON}" width="16" height="16" alt="" />
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

<p><input type="submit" value="{{Select}}" /></p>

</form>