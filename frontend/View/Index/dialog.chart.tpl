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

<div id="dialog-chart" style="display:none" title="{{ChartSettings}}">
    <table id="d-table">
        <tbody>
        <tr>
            <td style="width:50%">
                {{Axis}}
            </td>
            <td id="td-axis">
                <div class="fl"><input class="iCheck" type="radio" name="d-axis" value="9" /></div>
                <div class="fl"><input class="iCheck" type="radio" name="d-axis" value="7" /></div>
                <div class="fl"><input class="iCheck" type="radio" name="d-axis" value="5" /></div>
                <div class="fl"><input class="iCheck" type="radio" name="d-axis" value="3" /></div>
                <div class="fl nm"><input class="iCheck" type="radio" name="d-axis" value="1" /></div>
                <img class="fl nm" src="/images/chart.png" width="35" height="20"
                     style="margin-left:.5em;margin-right:.5em;vertical-align:top;width:35px;height:20px"/>
                <div class="fl"><input class="iCheck" type="radio" name="d-axis" value="2" /></div>
                <div class="fl"><input class="iCheck" type="radio" name="d-axis" value="4" /></div>
                <div class="fl"><input class="iCheck" type="radio" name="d-axis" value="6" /></div>
                <div class="fl"><input class="iCheck" type="radio" name="d-axis" value="8" /></div>
                <div class="fl nm"><input class="iCheck" type="radio" name="d-axis" value="10" /></div>
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
        <tr class="line-style">
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
        <tr class="line-style">
            <td>{{LineWidth}}</td>
            <td>
                <div class="fl" style="margin-right:.5em">
                    <input type="radio" class="iCheckLine" id="d-width-1" name="d-width" value="1" />
                    <label for="d-width-1">{{ThinLine}}</label>
                </div>
                <div class="fl" style="margin-right:.5em">
                    <input type="radio" class="iCheckLine" id="d-width-2" name="d-width" value="2" />
                    <label for="d-width-2">{{LineNormal}}</label>
                </div>
                <div class="fl">
                    <input type="radio" class="iCheckLine" id="d-width-3" name="d-width" value="4" />
                    <label for="d-width-3">{{LineBold}}</label>
                </div>
            </td>
        </tr>
        <tr class="line-style">
            <td>
                {{MarkExtremes}}
            </td>
            <td>
                <div class="fl" style="margin-right:.5em">
                    <input type="checkbox" id="d-min" class="iCheckLine" />
                    <label for="d-min">{{min}}</label>
                </div>
                <div class="fl">
                    <input type="checkbox" id="d-max" class="iCheckLine" />
                    <label for="d-max">{{max}}</label>
                </div>
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
                {{Color}}
            </td>
            <td>
                <input id="d-color" type="color" class="spectrum" />
            </td>
        </tr>
        <tr>
            <td>
                <label for="d-color-use-neg">{{UseNegativeColor}}</label>
            </td>
            <td>
                <table>
                <tr>
                    <!-- Align controls correct using a table ... -->
                    <td style="padding-left:0;vertical-align:middle">
                        <input type="checkbox" id="d-color-use-neg" class="iCheck" />
                    </td>
                    <td style="vertical-align:middle">
                        <input id="d-threshold" type="number" style="width:6em" class="tip" title="{{Threshold}}" />
                    </td>
                    <td style="vertical-align:middle">
                        <input id="d-color-neg" type="color" class="spectrum" />
                    </td>
                </tr>
                </table>
            </td>
        </tr>
        </tbody>
    </table>
</div>
