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

<!-- Show only channels in public charts and NOT all channel GUIDs -->

<table id="tree" class="dataTable">
	<thead>
	<tr>
		<th style="width:99%">{{Channel}}</th>
		<th>
			<img src="/images/ico/16x16.png" style="width:16px;height:16px" width="16" height="16" alt="" />
		</th>
		<th>{{Amount}}</th>
		<th>{{Unit}}</th>
	</tr>
	</thead>

	<tbody>
	<!-- BEGIN DATA -->
	<!-- IF {CHECKED} -->  <!-- MUST have also {GRAPH} before :-) -->
		<tr>
			<td>
				<input id="c{ID}" style="display:none" class="channel"
				       type="checkbox" checked="checked"
				       data-id="{ID}" data-guid="{GUID}" data-unit="{UNIT}"
				       value='{PRESENTATION}' />
				<img style="vertical-align:middle;width:16px;height:16px"
				     class="imgbar tip" src="/images/ico/{ICON}" alt=""
				     title="{TYPE}" width="16" height="16" />
				<strong class="tip" title="{GUID}">{NAME}</strong>
				<!-- IF {DESCRIPTION} --> ({DESCRIPTION})<!-- ENDIF -->
			</td>
			<td>
				<img id="s{ID}" src="/images/spinner.gif"
				     style="float:right;display:none;width:16px;height:16px"
				     width="16" height="16" />
			</td>
			<td id="cons{ID}" class="consumption r"></td>
			<td id="u{ID}">{UNIT}</td>
		</tr>
	<!-- ENDIF -->
	<!-- END -->
	</tbody>
</table>
