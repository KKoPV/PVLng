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

<h3 class="grid_10">
	{{Variants}}
	<img style="margin-left:.5em;width:16px;height:16px" class="tip"
	     src="/images/ico/information_frame.png" width="16" height="16"
	     title="{{MobileVariantHint}}" />
</h3>

<div class="clear"></div>


<div class="grid_10">
	<table style="width:100%"><tr>
	<td style="width:60%">
		<select id="loaddeleteview" name="loaddeleteview">
			<option value="">--- {{Select}} ---</option>
			<!-- BEGIN VIEWS -->
				<!-- show all charts and mark public charts -->
				<option value="{NAME}" <!-- IF {SELECTED} -->selected="selected"<!-- ENDIF -->>
					{NAME} <!-- IF {PUBLIC} --> ({{public}})<!-- ENDIF -->
				</option>
			<!-- END -->
		</select>
		<input type="submit" name="load" value="{{Load}}" style="margin-left:.5em" />
		<input type="submit" id="delete-view" name="delete" value="{{Delete}}" style="margin-left:.5em" />
	</td>
	<td>
		<input id="saveview" type="text" style="margin-left:3em"
		       name="saveview" value="{VIEW}"/>
	</td>
	<td>
		<input type="checkbox" class="iCheckLine" id="public" name="public" value="1"
		       <!-- IF {VIEWPUBLIC} -->checked="checked"<!-- ENDIF --> />
		<label for="public">{{public}}</label>
	</td>
	<td style="width:30%">
		<img src="/images/ico/information_frame.png" class="tip" title="{{publicHint}}"
		     style="margin-left:.5em;width:16px;height:16px" width="16" height="16" />
		<input type="submit" name="save" value="{{Save}}" style="margin:0 3em 0 .5em" />
	</td>
	<td class="r" style="white-space:nowrap">
		<a id="btn-permanent" class="tip" title="{{DragPermanent}}" data-text="{VIEW} & | {TITLE}" data-url="/chart/{SLUG}">
			{VIEW} | {TITLE}
		</a>
		<a href="/chart/{SLUG}" id="btn-bookmark" class="tip" style="margin-left:.5em" title="{{DragBookmark}}">
			{VIEW} | {TITLE}
		</a>
	</td>
	</tr></table>
</div>

<div class="clear"></div>
