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

<div id="public-select" style="margin-bottom:1em;display:none">
    <label for="load-delete-view" style="margin-right:20px">{{VariantsPublic}}:</label>
    <select id="load-delete-view" style="margin:0 .5em" data-placeholder="--- {{SelectChart}} ---"></select>
    <button id="btn-load" style="margin:0 1em" class="tip" title="{{Load}}">{{Load}}</button>
</div>

<div id="wrapper" style="display:none">

    <table id="data-table" class="dataTable">
        <thead>
        <tr>
            <th class="l">{{Channel}}</th>
            <th class="r">{{Amount}}</th>
            <th class="l">{{Unit}}</th>
        </tr>
        </thead>

        <tbody>

        <!-- BEGIN DATA -->
        <!-- IF {PUBLIC} -->
        <tr id="rc{ID}" class="channel">
            <td>
                <input id="c{ID}" class="channel" type="checkbox" style="display:none"
                       data-id="{ID}" data-name="{NAME}" data-guid="{GUID}" data-unit="{UNIT}" />
                <img id="s{ID}" src="/images/spinner.gif" class="spinner" alt="o">
                <!-- INCLUDE channel-details.inc.tpl -->
            </td>
            <td class="icons r">
                <span id="cons{ID}" class="consumption"></span>
            </td>
            <td id="u{ID}">{UNIT}</td>
        </tr>
        <!-- ENDIF -->
        <!-- END -->

        </tbody>
    </table>

</div>
