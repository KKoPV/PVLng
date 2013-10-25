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

<div id="dialog-chart" style="display:none" title="{{ChartSettings}}">
	<table id="d-table">
		<tbody>
		<tr>
			<td>
				{{Axis}}
			</td>
			<td id="td-axis">
				<input type="radio" name="d-axis" value="9" />
				<input type="radio" name="d-axis" value="7" />
				<input type="radio" name="d-axis" value="5" />
				<input type="radio" name="d-axis" value="3" />
				<input type="radio" name="d-axis" value="1" />
				<img style="margin:0 0.5em 0.7em;vertical-align:top;width:35px;height:18px"
				     src="/images/chart.png" width="35" height="18" />
				<input type="radio" name="d-axis" value="2" />
				<input type="radio" name="d-axis" value="4" />
				<input type="radio" name="d-axis" value="6" />
				<input type="radio" name="d-axis" value="8" />
				<input type="radio" name="d-axis" value="10" />
			</td>
		</tr>
		<tr>
			<td>
				<label for="d-type">{{SeriesType}}</label>
			</td>
			<td>
				<select id="d-type">
					<option value="line">Line</option>
					<option value="spline">Spline</option>
					<option value="areasplinerange">Spline min/max</option>
					<option value="areaspline">Spline with area</option>
					<option value="bar">Bar</option>
					<option value="scatter">Scatter</option>
				</select>
			</td>
		</tr>
		<tr>
			<td>
				<label for="d-cons">{{Presentation}}</label>
			</td>
			<td>
				<div class="fl">
					<input type="checkbox" id="d-cons" class="iCheckLine tip" />
					<label for="d-cons">{{ShowConsumption}}</label>
				</div>
				<img style="margin-left:.5em;width:16px;height:16px" class="tip"
				     src="/images/ico/information_frame.png" width="16" height="16"
				     title="{{ShowConsumptionHint}}" />
			</td>
		</tr>
		<tr>
			<td>
				<label for="d-style">{{dashStyle}}</label>
			</td>
			<td>
				<select id="d-style">
					<option value="">None</option>
					<option value="Solid">Solid</option>
					<option value="LongDash">Long Dash</option>
					<option value="Dash">Dash</option>
					<option value="Dot">Dot</option>
					<option value="DashDot">Dash-Dot</option>
					<optgroup label="Long">
						<option value="LongDashDot">Dash-Dot</option>
						<option value="LongDashDotDot">Dash-Dot-Dot</option>
					</optgroup>
					<optgroup label="Short">
						<option value="ShortDash">Dash</option>
						<option value="ShortDot">Dot</option>
						<option value="ShortDashDot">Dash-Dot</option>
						<option value="ShortDashDotDot">Dash-Dot-Dot</option>
					</optgroup>
				</select>
			</td>
		</tr>
		<tr>
			<td>{{LineWidth}}</td>
			<td>
				<input type="checkbox" id="d-bold" class="iCheckLine" />
				<label for="d-bold">{{LineBold}}</label>
			</td>
		</tr>
		<tr>
			<td>
				{{MarkExtremes}}
			</td>
			<td>
				<div style="float:left;margin-right:2em">
					<input type="checkbox" id="d-min" class="iCheckLine" />
					<label for="d-min">{{min}}</label>
				</div>
				<div style="float:left">
					<input type="checkbox" id="d-max" class="iCheckLine" />
					<label for="d-max">{{max}}</label>
				</div>
			</td>
		</tr>
		<tr>
			<td>
				<label for="d-color">{{Color}}</label>
			</td>
			<td>
				<input id="spectrum" type="color" id="d-color" />
			</td>
		</tr>
		</tbody>
	</table>
</div>
