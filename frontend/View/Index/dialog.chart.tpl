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

<div id="dialog-reading" style="display:none" title="{{DeleteReading}}">
    <p>
        <span class="ui-icon ui-icon-alert" style="float:left;margin:0 7px 20px 0"></span>
        {{DeleteReadingConfirm}}
    </p>
    <ul>
        <li id="reading-serie"></li>
        <li id="reading-timestamp"></li>
        <li>{{Reading}} : <span id="reading-value"></span></li>
    </ul>
</div>

<div id="dialog-chart" style="display:none" title="{{ChartSettings}}">
    <table id="d-table">
        <tbody>
        <tr class="odd">
            <td style="width:40%">{{Axis}}</td>
            <td id="td-axis">
                <input class="iCheck" type="radio" name="d-axis" value="9" />
                <input class="iCheck" type="radio" name="d-axis" value="7" />
                <input class="iCheck" type="radio" name="d-axis" value="5" />
                <input class="iCheck" type="radio" name="d-axis" value="3" />
                <div class="fl nm"><input class="iCheck" type="radio" name="d-axis" value="1" />
                <img class="fl nm" src="/images/chart.png" width="35" height="20"
                     style="margin:0 .5em;vertical-align:top;width:35px;height:20px"/>
                <input class="iCheck" type="radio" name="d-axis" value="2" />
                <input class="iCheck" type="radio" name="d-axis" value="4" />
                <input class="iCheck" type="radio" name="d-axis" value="6" />
                <input class="iCheck" type="radio" name="d-axis" value="8" />
                <div class="fl nm"><input class="iCheck" type="radio" name="d-axis" value="10" />
            </td>
        </tr>
        <tr class="even">
            <td>
                <label for="d-type">{{SeriesType}}</label>
                <img style="margin-left:.5em;width:16px;height:16px" class="tip"
                     src="/images/ico/information_frame.png" width="16" height="16"
                     title="{{ChartTypeHint}}" />
            </td>
            <td>
                <select id="d-type">
                    <option value="line">{{LineChart}}</option>
                    <option value="spline">{{SplineChart}}</option>
                    <option value="areasplinerange"> {{AreaSplineRangeChart}}</option>
                    <option value="areaspline">{{AreaSplineChart}}</option>
                    <option value="bar">{{BarChart}}</option>
                    <option value="scatter">{{ScatterChart}}</option>
                </select>
            </td>
        </tr>
        <tr class="odd">
            <td><label for="d-style">{{dashStyle}}</label></td>
            <td class="not-bar not-scatter">
                <select id="d-style">
                    <option value="Solid">{{LineSolid}}</option>
                    <optgroup label="{{LinesDashed}}">
                        <option value="Dash">{{LineDash}}</option>
                        <option value="LongDash">{{LineLongDash}}</option>
                        <option value="ShortDash">{{LineShortDash}}</option>
                    </optgroup>
                    <optgroup label="{{LinesDotted}}">
                        <option value="Dot">{{LineDot}}</option>
                        <option value="ShortDot">{{LineShortDot}}</option>
                    </optgroup>
                    <optgroup label="{{LinesDashedDotted}}">
                        <option value="DashDot">{{LineDashDot}}</option>
                        <option value="LongDashDot">{{LineLongDashDot}}</option>
                        <option value="ShortDashDot">{{LineShortDashDot}}</option>
                    </optgroup>
                    <optgroup label="{{LinesDashedDottedDotted}}">
                        <option value="LongDashDotDot">{{LineLongDashDotDot}}</option>
                        <option value="ShortDashDotDot">{{LineShortDashDotDot}}</option>
                    </optgroup>
                </select>
            </td>
        </tr>
        <tr class="even">
            <td>{{LineWidth}}</td>
            <td>
                <div class="fl not-bar not-scatter" style="margin-right:1em">
                    <input id="d-width-1" type="radio" class="iCheck" name="d-width" value="1" />
                    <label for="d-width-1">{{ThinLine}}</label>
                </div>
                <div class="fl not-bar not-scatter" style="margin-right:1em">
                    <input id="d-width-2" type="radio" class="iCheck" name="d-width" value="2" />
                    <label for="d-width-2">{{LineNormal}}</label>
                </div>
                <div class="fl not-bar not-scatter">
                    <input id="d-width-3" type="radio" class="iCheck" name="d-width" value="4" />
                    <label for="d-width-3">{{LineBold}}</label>
                </div>
            </td>
        </tr>
        <tr class="odd">
            <td>{{MarkExtremes}}</td>
            <td>
                <div class="fl not-bar not-scatter not-meter" style="margin-right:1em">
                    <input id="d-min" type="checkbox" class="iCheck" />
                    <label for="d-min">{{MarkMin}}</label>
                </div>
                <div class="fl not-scatter not-meter" style="margin-right:1em">
                    <input id="d-max" type="checkbox" class="iCheck" />
                    <label for="d-max">{{MarkMax}}</label>
                </div>
                <div class="fl not-bar not-scatter" style="margin-right:1em">
                    <input id="d-last" type="checkbox" class="iCheck" />
                    <label for="d-last">{{MarkLast}}</label>
                </div>
                <div class="fl">
                    <input id="d-all" type="checkbox" class="iCheck" />
                    <label for="d-all">{{MarkAll}}</label>
                </div>
            </td>
        </tr>
        <tr class="even">
            <td>
                <label for="d-cons">{{Presentation}}</label>
                <img style="margin-left:.5em;width:16px;height:16px" class="tip"
                     src="/images/ico/information_frame.png" width="16" height="16"
                     title="{{ShowConsumptionHint}}" />
            </td>
            <td>
                <div class="fl not-scatter">
                    <input type="checkbox" id="d-cons" class="iCheck" />
                    <label for="d-cons">{{ShowConsumption}}</label>
                </div>
            </td>
        </tr>
        <tr class="odd">
            <td>{{Color}}</td>
            <td><input id="d-color" type="color" class="spectrum" /></td>
        </tr>
        <tr class="even">
            <td><label for="d-color-use-neg">{{UseNegativeColor}}</label></td>
            <td>
                <!-- Align controls correct using a table ... -->
                <table><tr>
                <td style="padding-left:0">
                    <input id="d-color-use-neg" type="checkbox" class="not-scatter iCheck" />
                    {{below}}
                </td>
                <td>
                    <input id="d-threshold" type="number" style="width:6em" class="tip" title="{{Threshold}}" />
                    &nbsp;
                    <input id="d-color-neg" type="color" class="spectrum" />
                </td>
                </tr></table>
            </td>
        </tr>
        <tr class="odd">
            <td>
                <label for="d-time1">{{TimeRange}}</label>
                <img style="margin-left:.5em;width:16px;height:16px" class="tip"
                     src="/images/ico/information_frame.png" width="16" height="16"
                     title="{{TimeRangeHint}}" />
            </td>
            <td>
                <input id="d-time1" type="text" class="c" style="width:5em" />
                <span style="padding:0 .5em;font-weight:bold">&mdash;</span>
                <input id="d-time2" type="text" class="c" style="width:5em" />
                <br />
                <div id="d-time-slider" style="margin-top:.5em;margin-left:.4em;width:13em"></div>
            </td>
        </tr>
        </tbody>
    </table>
</div>
