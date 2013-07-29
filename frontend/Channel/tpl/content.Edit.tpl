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

<p>
	{{ChannelType}}: <strong>{TYPENAME}</strong>
</p>

<form action="/channel/edit" method="post">

<input type="hidden" name="action" value="edit" />
<input type="hidden" name="c[id]" value="{ID}" />
<input type="hidden" name="c[type]" value="{TYPE}" />
<!-- BEGIN FIELDS -->
<!-- IF ! {VISIBLE} -->
<input type="hidden" name="c[{FIELD}]" value="{VALUE}" />
<!-- ENDIF -->
<!-- END -->

<table id="dataTable" class="dataTable">
	<thead>
	<tr>
		<th class="l" style="width:20%">{{channel::Param}}</th>
		<th class="l">{{channel::Value}}</th>
		<th class="l">{{channel::Help}}</th>
	</tr>
	</thead>

	<tbody>
	<!-- BEGIN FIELDS -->

	<!-- IF {VISIBLE} -->
	<tr>
		<td>
			<label for="{FIELD}">{NAME}</label>
		</td>
		<td style="white-space:nowrap">
			<!-- IF {TYPE} == "radio" -->
				<input type="radio" id="y{FIELD}" name="c[{FIELD}]" value="1"
				       <!-- IF {VALUE} == 1 -->checked="checked"<!-- ENDIF --> />
				<label for="y{FIELD}">{{Yes}}</label>
				<input type="radio" id="n{FIELD}" name="c[{FIELD}]" value="0" style="margin-left:1em"
				       <!-- IF {VALUE} == 0 -->checked="checked"<!-- ENDIF --> />
				<label for="n{FIELD}">{{No}}</label>
			<!-- ELSEIF {TYPE} == "textarea" -->
				<textarea id="{FIELD}" name="c[{FIELD}]" style="width:95%" rows="4">{VALUE}</textarea>
			<!-- ELSE -->
				<input type="text" id="{FIELD}" name="c[{FIELD}]" value="{VALUE}" size="50" />
			<!-- ENDIF -->
			<!-- IF {REQUIRED} -->
				<img src="/images/required.gif" alt="(required)" />
			<!-- ENDIF -->
		</td>
		<td>
			<small>{HINT}</small>
		</td>
	</tr>
	<!-- ENDIF -->

	<!-- END -->
	</tbody>

	<tfoot>
	<tr>
		<th class="l" colspan="3">
			<img src="/images/required.gif" alt="(required)" /> <small>{{Required}}</small>
		</th>
	</tr>
	</tfoot>

</table>

<p><input type="submit" value="{{Save}}" /></p>

</form>