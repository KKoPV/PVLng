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

<div class="grid_4">
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
	<input type="submit" name="delete" value="{{Delete}}" style="margin-left:.5em" />
</div>

<div class="grid_4">
	<input id="saveview" type="text" name="saveview" value="{VIEW}"/>
	<input style="margin:0 .5em" id="public" type="checkbox" name="public" value="1"
		<!-- IF {VIEWPUBLIC} -->checked="checked"<!-- ENDIF -->
	/>
	<label for="public">{{public}}</label>
	<img style="margin-left:.5em;width:16px;height:16px" class="tip"
	     src="/images/ico/information_frame.png" width="16" height="16"
	     title="{{publicHint}}" />
	<input type="submit" name="save" value="{{Save}}" style="margin:0 3em 0 .5em" />
</div>

<div class="grid_2">
	<a id="btn-bookmark" class="fr tip" title="{{DragBookmark}}" data-url="/chart/">
		PVLng | {VIEW}
	</a>
</div>

<div class="clear"></div>
