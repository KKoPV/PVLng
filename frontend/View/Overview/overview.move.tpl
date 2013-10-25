<!--
/**
 *
 *
 * @author      Knut Kohl <github@knutkohl.de>
 * @copyright   2012-2013 Knut Kohl
 * @license     GNU General Public License http://www.gnu.org/licenses/gpl.txt
 * @version     $Id: v1.0.0.2-22-g7bc4608 2013-05-05 22:07:15 +0200 Knut Kohl $
 */
-->

<div id="dialog-move" style="display:none" title="{{MoveChannel}}">
	<form id="form-movechild" action="" method="post">
	<input type="hidden" name="id" />
	<p>
	    {{MoveChannelHowMuchRows}}
	</p>
	<p>
		<div style="float:left;width:25px">
			<input type="radio" class="iCheck" id="countmax" name="countmax" value="0" checked="checked" />
		</div>
		<label for="countmax">
			<input type="number" step="1" style="width:3em"class="numbersOnly" name="count" value="1" />
			{{Positions}}
		</label>
	</p>
	<p>
		<div style="float:left;width:25px">
			<input type="radio" class="iCheck" id="movecountmax" name="countmax" value="1" />
		</div>
		<label for="movecountmax">{{MoveChannelStartEnd}}</label>
	</p>

	</form>
</div>
