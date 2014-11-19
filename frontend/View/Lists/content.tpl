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

<input type="hidden" id="fromdate" name="fromdate" />
<input type="hidden" id="todate" name="todate" />

<div class="icons">
    <select id="channel" class="fl" data-placeholder="--- {{SelectChannel}} ---">
        <option></option>
        <!-- BEGIN CHANNELS -->
        <option value="{GUID}"
            <!-- IF {AVAILABLE} -->
                <!-- IF {GUID} AND {GUID} == {__GUID} --> selected="selected"<!-- ENDIF -->
            <!-- ELSE -->
                disabled="disabled"
            <!-- ENDIF -->
        >
            <span style="font-size:120%">{INDENT}</span>
            {NAME}
            <!-- IF {DESCRIPTION} --> ({DESCRIPTION})<!-- ENDIF -->
            <!-- IF {UNIT} --> [{UNIT}]<!-- ENDIF -->
        </option>
        <!-- END -->
    </select>

    <img id="icon" src="/images/pix.gif" class="fl tip" style="margin-left:16px;margin-top:5px" data-none="/images/pix.gif" alt="">

    <div class="fl" style="padding-top:5px">
        <i id="icon-private" class="ico lock tip" style="display:none" title="{{PrivateChannel}}"></i>
        <i id="edit-entity" class="ico node-design tipbtn" style="display:none" title="{{EditEntity}}"></i>
        <i id="guid" class="ico license-key guid tipbtn" style="display:none" title="{{ShowGUID}}"></i>
    </div>

</div>

<div class="clear"></div>

<div id="nav" class="ui-widget-header ui-corner-all" style="padding:4px;height:32px;margin:.5em 0">
    <div class="fl">
        <!-- INCLUDE dateselect.inc.tpl -->
    </div>
    <div class="fr r">
        <!-- INCLUDE preset.inc.tpl -->
    </div>
</div>

<div class="clear"></div>

<table id="list" class="dataTable">
    <thead>
    <tr>
        <th class="l">{{DateTime}}</th>
        <th class="r">{{Reading}}</th>
        <th class="l">{{Minimum}}</th>
        <th class="r">{{Maximum}}</th>
        <th class="r">{{Production}} / {{Consumption}}</th>
        <th class="r tip" title="{{RowCountHint}}">{{RowCount}}</th>
        <th><img src="/images/ico/minus_circle.png" style="margin-right:-10px"></th>
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
