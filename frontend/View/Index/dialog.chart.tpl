<!--
/**
 *
 *
 * @author      Knut Kohl <github@knutkohl.de>
 * @copyright   2012-2014 Knut Kohl
 * @license     MIT License (MIT) http://opensource.org/licenses/MIT
 * @version     1.0.0
 */
-->

<div id="dialog-chart" style="display:none" title="{{ChartSettings}}">
    <table id="d-table">
        <tbody>
        <tr>
            <td style="max-width:35%">{{Axis}}</td>
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
        <tr>
            <td>
                <label for="d-type">{{SeriesType}}</label>
                <i class="fa fa-question tip" title="{{ChartTypeHint}}"></i>
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
                <i id="scatter-candidate" class="fa fa-question tip" title="{{ScatterCandidate}}"></i>
            </td>
        </tr>
        <tr>
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
        <tr>
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
        <tr>
            <td>{{MarkExtremes}}</td>
            <td>
                <div class="fl not-bar not-scatter not-meter" style="margin-right:1em">
                    <input id="d-min" type="checkbox" class="iCheck" />
                    <label for="d-min">{{MarkMin}}</label>
                </div>
                <div class="fl not-scatter not-meter" style="margin-right:1em">
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
        <tr>
            <td>
                <label for="d-cons">{{ShowConsumption}}</label>
                <i class="fa fa-question tip" title="{{ShowConsumptionHint}}"></i>
            </td>
            <td class="not-scatter"><input type="checkbox" id="d-cons" class="iCheck" /></td>
        </tr>
        <tr>
            <td>{{Color}}</td>
            <td><input id="d-color" type="color" class="spectrum" /></td>
        </tr>
        <tr>
            <td>
                <label for="d-outline">{{DrawOutline}}</label>
                <i class="fa fa-question tip" title="{{DrawOutlineHint}}"></i>
            </td>
            <td class="not-bar not-scatter"><input type="checkbox" id="d-outline" class="iCheck" /></td>
        </tr>
        <tr>
            <td><label for="d-color-use-neg">{{UseDifferentColor}}</label></td>
            <td class="not-bar not-scatter">
                <!-- Align controls correct using a simple table ... -->
                <table><tr>
                <td style="padding-right:1em">
                    <input id="d-color-use-diff" class="iCheck" type="radio" name="color-pos-neg" value="0" checked="checked">
                    <label for="d-color-use-diff">{{no}}</label>
                </td>
                <td style="padding-right:1em">
                    <input id="d-color-pos" class="iCheck" type="radio" name="color-pos-neg" value="1">
                    <label for="d-color-pos">{{above}}</label>
                </td>
                <td style="padding-right:1em">
                    <input id="d-color-neg" class="iCheck" type="radio" name="color-pos-neg" value="-1">
                    <label for="d-color-neg">{{below}}</label>
                </td>
                <td>
                    <input id="d-color-threshold" type="number" style="width:4em" class="not-scatter tip" title="{{Threshold}}">
                    &nbsp;
                    <input id="d-color-diff" type="color" class="not-scatter spectrum" />
                </td>
                </tr></table>
            </td>
        </tr>
        <tr>
            <td>
                <label for="d-time1">{{TimeRange}}</label>
                <i class="fa fa-question tip" title="{{TimeRangeHint}}"></i>
            </td>
            <td>
                <div class="fl">
                    <input id="d-time1" type="text" class="c" style="width:5em" />
                    <span style="padding:0 .5em;font-weight:bold">&mdash;</span>
                    <input id="d-time2" type="text" class="c" style="width:5em" />
                </div>
                <div id="d-time-slider"></div>
            </td>
        </tr>
        <tr>
            <td><label for="d-daylight">{{DuringDaylight}}</label></td>
            <td>
                <table><tr>
                <td style="padding-right:1em">
                    <input id="d-daylight" type="checkbox" class="iCheck">
                </td>
                <td>
                    &plusmn;
                    <input id="d-daylight-grace" type="number" style="text-align:right" size="3">
                    &nbsp;{{Minutes}}
                </td>
                </tr></table>
            </td>
        </tr>
        <tr>
            <td><label for="d-legend">{{Legend}}</label></td>
            <td><input id="d-legend" type="checkbox" class="iCheck"></td>
        </tr>
        <tr>
            <td><label for="d-hidden">{{StartHidden}}</label></td>
            <td><input id="d-hidden" type="checkbox" class="iCheck"></td>
        </tr>
        <tr>
            <td>
                <label for="d-position">{{ChartPosition}}</label>
                <i class="fa fa-question tip" title="{{ChartPositionHint}}"></i>
            </td>
            <td>
                <!-- Align layout with a table -->
                <table id="table-pos"><tr>
                    <td class="xs" style="width:16px">
                        <img src="/images/pix.gif" data-src="/images/layers-stack-arrange-back.png"
                             class="def ico tip" title="{{MoreIntoBackground}}" alt="<<">
                    </td>
                    <td style="padding-left:1.4em;padding-right:1.4em">
                        <div id="d-position-slider" style="margin-top:5px"></div>
                    </td>
                    <td class="xs r" style="width:16px">
                        <img src="/images/pix.gif" data-src="/images/layers-stack-arrange.png"
                             class="def ico tip" title="{{MoreIntoForeground}}" alt=">>">
                    </td>
                </tr></table>
            </td>
        </tr>
        </tbody>
    </table>
</div>
