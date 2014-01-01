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

<form method="post" action="/">

<input type="hidden" id="fromdate" name="fromdate" />
<input type="hidden" id="todate" name="todate" />

<div id="nav" class="grid_10" style="margin-top:1em<!-- IF {EMBEDDED} == "2" -->;display:none<!-- ENDIF -->">

    <table style="width:100%">
    <tr>
        <td>
            <span class="ui-icon ui-icon-triangle-1-w tip"
                  title="{{PrevDay}} (Alt+P)" onclick="changeDates(-1)"></span>
        </td>
        <td>
            <input class="c" type="text" id="from" name="from" size="10" />
        </td>
        <td style="padding:0 .75em;font-weight:bold">
            &mdash;
        </td>
        <td>
            <input class="c" type="text" id="to" name="to" size="10" />
        </td>
        <td>
            <span class="ui-icon ui-icon-triangle-1-e tip"
                  title="{{NextDay}} (Alt+N)" onclick="changeDates(1)"></span>
        </td>
        <td>
            <button id="btn-reset" style="margin-left:1em">{{Today}}</button>
        </td>
        <td style="width:99%;text-align:right">
            <label for="periodcnt" style="margin-right:1em" >{{Aggregation}}:</label>
            <input class="numbersOnly r" style="margin-right:.5em" type="text"
                   id="periodcnt" name="periodcnt" value="1" size="2" />
            {PERIODSELECT} &nbsp;
            <button id="btn-refresh" class="tip" title="{{ChartRefreshHint}}">{{Refresh}}</button>
        </td>
    </tr>
    </table>
</div>

<div class="clear"></div>

<div id="chart" class="grid_10">
    <!-- IF {VIEW} -->
    <p style="height:528px;text-align:center">
        <img src="/images/loading.gif" alt="{{JustAMoment}}"
             style="margin-top:250px;width:48px;height47px" width="48" height="47" />
    </p>
    <!-- ELSE -->
    <p class="b">
        <!-- IF {USER} -->
            {{NoChannelsSelectedYet}}
        <!-- ELSE -->
            {{NoViewSelectedYet}}
        <!-- ENDIF -->
    </p>

    <label for="top-loadview" class="b" style="margin-right:1em">{{Variants}}:</label>

    <select id="top-loadview" name="top-loadview" onChange="this.form.submit()">
        <option value="">--- {{Select}} ---</option>
        <!-- BEGIN VIEWS -->
            <!-- IF {__USER} -->
                <!-- show all charts and mark public charts -->
                <option value="{NAME}" <!-- IF {SELECTED} -->selected="selected"<!-- ENDIF -->>
                    {NAME} <!-- IF {PUBLIC} --> ({{public}})<!-- ENDIF -->
                </option>
            <!-- ELSEIF {PUBLIC} -->
                <!-- show only public charts -->
                <option value="{NAME}" <!-- IF {SELECTED} -->selected="selected"<!-- ENDIF -->>
                    {NAME}
                </option>
            <!-- ENDIF -->
        <!-- END -->
    </select>
    <noscript>
        <input type="submit" name="load" value="{{Load}}" style="margin-left:.5em" />
    </noscript>
    <!-- ENDIF -->
</div>

<div class="clear"></div>

<!-- IF {EMBEDDED} != "2" -->

<div class="grid_10">
    <a id="togglewrapper" href="#">{{ToggleChannels}} (F3)</a>
</div>

<div class="clear"></div>

<div id="wrapper" class="grid_10" style="padding-top:1em">
    <!-- IF {USER} -->
        <!-- INCLUDE datatable.inc.tpl -->
    <!-- ELSE -->
        <!-- INCLUDE datatable.nouser.inc.tpl -->
    <!-- ENDIF -->
</div>

<div class="clear"></div>

<!-- IF {USER} -->
    <!-- INCLUDE variants.inc.tpl -->
<!-- ELSE -->
    <!-- INCLUDE variants.nouser.inc.tpl -->
<!-- ENDIF -->

<!-- ELSE -->
    <!-- BEGIN DATA -->
    <!-- IF {PUBLIC} AND {CHECKED} -->  <!-- MUST have also {GRAPH} before :-) -->
        <input id="c{ID}" style="display:none" class="channel"
               type="checkbox" checked="checked"
               data-id="{ID}" data-name="{NAME}" data-guid="{GUID}" data-unit="{UNIT}"
               value='{PRESENTATION}' />
    <!-- ENDIF -->
    <!-- END -->
    <input id="loaddeleteview" type="hidden" value="{VIEW}"/>
<!-- ENDIF -->

</form>

<!-- INCLUDE dialog.chart.tpl -->
