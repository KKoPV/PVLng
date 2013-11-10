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

<p>
	<span style="margin-right:1em">{{VariantsPublic}}:</span>

	<select id="loaddeleteview" name="loaddeleteview">
		<option value="">--- {{Select}} ---</option>
		<!-- BEGIN VIEWS --><!-- IF {PUBLIC} -->
			<!-- show only public charts -->
			<option value="{NAME}" <!-- IF {SELECTED} -->selected="selected"<!-- ENDIF -->>
				{NAME}
			</option>
		<!-- ENDIF --><!-- END -->
	</select>
	<input type="submit" name="load" value="{{Load}}" style="margin-left:.5em" />
	</a>
</p>
