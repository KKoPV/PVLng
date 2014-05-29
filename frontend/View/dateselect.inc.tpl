<!--
/**
 *
 *
 * @author      Knut Kohl <github@knutkohl.de>
 * @copyright   2012-2013 Knut Kohl
 * @license     MIT License (MIT) http://opensource.org/licenses/MIT
 * @version     1.0.0
 */
-->

<table>
    <tr>
        <td>
            <span class="ui-icon ui-icon-triangle-1-w tipbtn"
                  title="{{PrevDay}} (Alt+P)" onclick="pvlng.changeDates(-1)"></span>
        </td>
        <td>
            <input class="c" type="text" id="from" size="10" />
        </td>
        <td style="padding:0 .5em;font-weight:bold">
            &mdash;
        </td>
        <td>
            <input class="c" type="text" id="to" size="10" />
        </td>
        <td>
            <span class="ui-icon ui-icon-triangle-1-e tipbtn"
                  title="{{NextDay}} (Alt+N)" onclick="pvlng.changeDates(1)"></span>
        </td>
        <td style="padding-left:.5em">
            <button id="btn-reset" class="tip" title="{{ChartTodayHint}}">{{Today}}</button>
        </td>
    </tr>
</table>
