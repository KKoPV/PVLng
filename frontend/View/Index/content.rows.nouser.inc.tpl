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
		<img id="s{ID}" src="/images/spinner.gif"
		     style="float:right;display:none;width:16px;height:16px"
		     width="16" height="16" />
	</td>
	<td id="cons{ID}" class="consumption r"></td>
	<td id="u{ID}">{UNIT}</td>
</tr>
