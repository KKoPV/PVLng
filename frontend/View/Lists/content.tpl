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

<input type="hidden" id="fromdate" name="fromdate" />
<input type="hidden" id="todate" name="todate" />

<div id="nav" class="grid_10" style="margin-top:1em">

    <table style="width:100%">
    <tr>
        <td class="l" style="width:1%;white-space:nowrap">
            <select id="channel">
                <option value="">--- {{SelectChannel}} ---</option>
                <!-- BEGIN CHANNELS -->
                <option value="{GUID}"
                    <!-- IF {AVAILABLE} -->
                        <!-- IF {GUID} == {__GUID} --> selected="selected"<!-- ENDIF -->
                    <!-- ELSE -->
                        disabled="disabled"
                    <!-- ENDIF -->
                >
                    <span style="font-size:120%">{INDENT}</span>{NAME}
                    <!-- IF {DESCRIPTION} --> ({DESCRIPTION})<!-- ENDIF -->
                    <!-- IF {UNIT} --> [{UNIT}]<!-- ENDIF -->
                </option>
                <!-- END -->
            </select>
            <img id="icon" src="/images/pix.gif" class="tip" data-none="/images/pix.gif" alt=""
                 style="margin:0 16px;width:16px;height:16px" width="16" height="16" />
            <img id="icon-private" src="/images/ico/lock.png" class="tip" alt="[private]"
                 style="width:16px;height:16px;display:none"
                 width="16" height="16" title="{{PrivateChannel}}"/>
        </td>
        <td class="c">
            <table><tr>
            <td>
                <span class="ui-icon ui-icon-triangle-1-w tip"
                      title="{{PrevDay}} (Alt+P)" onclick="changeDates(-1)"></span>
            </td>
            <td>
                <input class="c" type="text" id="from" name="from" size="10" />
            </td>
            <td style="padding:0 .5em;font-weight:bold">
                &mdash;
            </td>
            <td>
                <input class="c" type="text" id="to" name="to" size="10" />
            </td>
            <td>
                <span class="ui-icon ui-icon-triangle-1-e tip"
                      title="{{NextDay}} (Alt+N)" onclick="changeDates(1)"></span>
            </td>
            <td style="padding-left:.5em">
                <button id="btn-reset">{{Today}}</button>
            </td>
            </tr></table>
        </td>
        <td class="r" style="width:1%;white-space:nowrap">
            <img src="/images/ico/arrow-switch.png" style="margin-right:.5em"
                 onclick="$('.p-select').toggle();" class="tip" tip="{{UseOwnConsolidation}}" />
            <span class="p-select">{PRESETSELECT}</span>
            <span class="p-select" style="display:none">
                <input class="numbersOnly r" style="margin-right:.5em" type="text"
                       id="periodcnt" name="periodcnt" value="1" size="2" />
                {PERIODSELECT}
            </span>
            <span style="margin-left:.5em">
                <button id="btn-refresh" class="tip" title="{{ListRefreshHint}}">{{Refresh}}</button>
            </span>
        </td>
    </tr>
    </table>
</div>

<div class="clear"></div>

<div class="grid_10" style="margin-top:1em;margin-bottom:.5em">

<table id="list" class="dataTable">
    <thead>
    <tr>
        <th class="l">{{DateTime}}</th>
        <th class="r">{{Reading}}</th>
        <th class="l">{{Minimum}}</th>
        <th class="r">{{Maximum}}</th>
        <th class="r">{{Production}} / {{Consumption}}</th>
        <th class="r tip" title="{{RowCountHint}}">{{RowCount}}</th>
        <th><img src="/images/ico/minus_circle.png" /></th>
    </tr>
    </thead>

    <tbody></tbody>

    <tfoot>
    <tr>
        <th colspan="2"></th>
        <th colspan="2"></th>
        <th id="tf-consumption" class="r">&nbsp;</th>
        <th></th>
        <th></th>
    </tr>
    </tfoot>
</table>

</div>

<div style="display:none">
    <!-- Extra content for dataTable sDom -->
    <span id="toolbar" class="toolbar menu">
        <!-- http://tools.ietf.org/html/rfc4180 -->
        <a data-mime="text/csv"   data-extension="csv" data-separator=";"     class="export tipbtn" title="{{ListExportCSVHint}}"  href="#">CSV;</a>
        <a data-mime="text/csv"   data-extension="csv" data-separator=","     class="export tipbtn" title="{{ListExportCSVHint}}"  href="#">CSV,</a>
        <a data-mime="text/csv"   data-extension="csv" data-separator="&#09;" class="export tipbtn" title="{{ListExportTSVHint}}"  href="#">TSV</a>
        <a data-mime="text/plain" data-extension="txt" data-separator=" "     class="export tipbtn" title="{{ListExportTextHint}}" href="#">TXT</a>
    </span>
</div>
