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

<div id="public-select" class="grid_10" style="margin-bottom:1em;display:none">
    <label for="loaddeleteview">{{VariantsPublic}}:</label>
    <select id="loaddeleteview" style="margin:0 .5em"></select>
    <button id="btn-load" class="tip" title="{{Load}}">{{Load}}</button>
</div>

<div class="clear"></div>

<div id="wrapper" class="grid_10" style="display:none">

    <table id="data-table" class="dataTable">
        <thead>
        <tr>
            <th class="l">{{Channel}}</th>
            <th style="width:1%">
                <img src="/images/ico/16x16.png" style="width:16px;height:16px" width="16" height="16" alt="" />
            </th>
            <th style="width:1%" class="r">{{Amount}}</th>
            <th style="width:1%" class="l">{{Unit}}</th>
        </tr>
        </thead>

        <tbody>

        <!-- BEGIN DATA -->
        <!-- IF {PUBLIC} -->
        <tr id="rc{ID}" class="channel">
            <td>
                <input id="c{ID}" class="channel" type="checkbox" style="display:none"
                       data-id="{ID}" data-name="{NAME}" data-guid="{GUID}" data-unit="{UNIT}" />
                <img style="vertical-align:middle;width:16px;height:16px;margin-right:8px"
                     src="{ICON}" width="16" height="16" alt="" class="tip" title="{TYPE}" />
                <strong class="tip" title="{GUID}">{NAME}</strong>
                <!-- IF {DESCRIPTION} --> ({DESCRIPTION})<!-- ENDIF -->
            </td>
            <td>
                <img id="s{ID}" src="/images/spinner.gif"
                     style="float:right;width:16px;height:16px;display:none"
                     width="16" height="16" />
            </td>
            <td id="cons{ID}" class="consumption r"></td>
            <td id="u{ID}">{UNIT}</td>
        </tr>
        <!-- ENDIF -->
        <!-- END -->

        </tbody>
    </table>

</div>

<div class="clear"></div>
