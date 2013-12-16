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
		<th class="l">{{Channel}}</th>
		<th style="width:1%">
			<img src="/images/ico/16x16.png" style="width:16px;height:16px" width="16" height="16" alt="" />
		</th>
		<th style="width:1%" class="r">{{Amount}}</th>
		<th style="width:1%" class="l">{{Unit}}</th>
	</tr>
	</thead>

	<tbody>
	<!-- BEGIN DATA -->
	<!-- IF {PUBLIC} AND {CHECKED} -->  <!-- MUST have also {GRAPH} before :-) -->
		<tr>
			<td>
				<input id="c{ID}" style="display:none" class="channel"
				       type="checkbox" checked="checked" value='{PRESENTATION}'
				       data-id="{ID}" data-name="{NAME}" data-guid="{GUID}" data-unit="{UNIT}" />
				<img style="vertical-align:middle;width:16px;height:16px;margin-right:8px"
				     src="{ICON}" width="16" height="16" alt="" class="tip" title="{TYPE}" />
				<strong class="tip" title="{GUID}">{NAME}</strong>
				<!-- IF {DESCRIPTION} --> ({DESCRIPTION})<!-- ENDIF -->
			</td>
			<td>
				<img id="s{ID}" src="/images/spinner.gif"
				     style="float:right;width:16px;height:16px;display:none"
				     width="16" height="16" />
			</td>
			<td id="cons{ID}" class="consumption r"></td>
			<td id="u{ID}">{UNIT}</td>
		</tr>
	<!-- ENDIF -->
	<!-- END -->
	</tbody>
</table>
