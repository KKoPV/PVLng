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

<div>
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

    <div id="channel-icons" class="fl icons">
        <img id="icon" src="/images/pix.gif" class="fl tip" data-none="/images/pix.gif" alt="">
        <i id="icon-private" class="fa fa-lock fa-fw tip" style="display:none" title="{{PrivateChannel}}"></i>
        <i id="edit-entity" class="fa fa-pencil fa-fw tipbtn" style="display:none" title="{{EditEntity}}"></i>
        <i id="guid" class="fa fa-key fa-rotate-90 fa-fw tipbtn" style="display:none" title="{{ShowGUID}}"></i>
    </span>

</div>

<div class="clear"></div>

<div id="nav" class="ui-widget-header ui-corner-all" style="padding:.3em;height:2.2em">
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
        <th></th>
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

<!-- Legend -->

<div class="icons legendtip">
    <i class="fa fa-trash"></i>{{DeleteReading}}
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
