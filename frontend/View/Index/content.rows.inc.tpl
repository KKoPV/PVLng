<!--
/**
 *
 *
 * @author      Knut Kohl <github@knutkohl.de>
 * @copyright   2012-2013 Knut Kohl
 * @license     GNU General Public License http://www.gnu.org/licenses/gpl.txt
 * @version     $Id: v1.0.0.2-19-gf67765b 2013-05-05 22:03:31 +0200 Knut Kohl $
 */
-->

<tr data-tt-id="{ID}" <!-- IF {PARENT} -->data-tt-parent-id="{PARENT}" <!-- ENDIF -->>
	<td>
		<!-- IF {GRAPH} -->
		<input id="c{ID}" class="channel iCheck" type="checkbox" name="v[{ID}]"
		       data-id="{ID}" data-guid="{GUID}" data-unit="{UNIT}"
		       value='{PRESENTATION}'
					 <!-- IF {CHECKED} -->checked="checked"<!-- ENDIF --> />
		<!-- ENDIF -->
	</td>
	<td style="padding:0.4em 0">
		<img style="vertical-align:middle;width:16px;height:16px"
		     class="imgbar tip" src="/images/ico/{ICON}" alt=""
		     title="{TYPE}" width="16" height="16"/>
		<strong class="tip" title="{GUID}">{NAME}</strong>
		<!-- IF {DESCRIPTION} --> ({DESCRIPTION})<!-- ENDIF -->
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
