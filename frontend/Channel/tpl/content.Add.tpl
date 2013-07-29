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
		<th></th>
		<th>{{Model}}</th>
		<th></th>
		<th></th>
		<th></th>
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
		<td>
			{UNIT}
		</td>
		<td>
			<small>{DESCRIPTION}</small>
		</td>
		<td>
			<small>{MODEL}</small>
		</td>
		<td>
			<!-- IF {CHILDS} -->
			<img src="/images/ico/node_select_child.png" alt="childs" class="tip" title="{{CanHaveChilds}}" />
			<!-- ENDIF -->
		</td>
		<td>
			<!-- IF {WRITE} -->
			<img src="/images/ico/write.png" alt="write" class="tip" title="{{WritableEntity}}" />
			<!-- ENDIF -->
		</td>
		<td>
			<!-- IF {READ} -->
			<img src="/images/ico/read.png" alt="read" class="tip" title="{{ReadableEntity}}" />
			<!-- ENDIF -->
		</td>
	</tr>
	<!-- END -->
	</tbody>
</table>

<p><input type="submit" value="{{Select}}" /></p>

</form>