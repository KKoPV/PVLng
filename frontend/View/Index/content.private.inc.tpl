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

<!-- Use this image as spacer for not available moving actions of channels -->
<!-- DEFINE SpacerImg --><i class="ico pix"></i><!-- END DEFINE -->

<div id="tabs" class="ui-tabs">

    <ul>
        <li><a href="#tabs-1">{{Channels}}</a></li>
        <li><a href="#tabs-2">{{Variants}}</a></li>
    </ul>

    <div id="tabs-1">
        <table id="data-table" class="dataTable treeTable">
            <thead>
                <tr>
                    <th>
                        <img id="treetoggle" src="/images/ico/toggle<!-- IF {VIEW} -->_expand<!-- ENDIF -->.png"
                             class="ico tipbtn" tip="#tiptoggle" alt="[+]" />
                        <div id="tiptoggle">{{CollapseAll}} (F4)</div>
                    </th>
                    <th class="l">
                        <span class="indenter" style="padding-left: 0px;"></span>
                        {{Channel}}
                    </th>
                    <th class="r">{{Amount}}</th>
                    <th class="l">{{Unit}}</th>
                    <th class="r" style="white-space:nowrap">{{Earning}} / {{Cost}}</th>
                    <th><i class="ico information-frame tip" tip="#IconLegend"></i></th>
                </tr>
            </thead>

            <tbody>
                <!-- BEGIN DATA -->
                <tr id="rc{ID}" data-tt-id="{ID}"
                    class="channel<!-- IF {GRAPH} --> graph<!-- ENDIF -->"
                    <!-- IF {PARENT} -->data-tt-parent-id="{PARENT}" <!-- ENDIF -->
                    >
                    <td>
                        <!-- IF {GRAPH} -->
                        <input id="c{ID}" type="checkbox" class="channel iCheck" data-id="{ID}" />
                        <!-- ENDIF -->
                    </td>
                    <td style="padding:0.4em 0" <!-- IF {TYPE_ID} == "0" -->class="alias"<!-- ENDIF -->>
                        <img id="s{ID}" src="/images/spinner.gif" class="spinner" alt="o" />
                        <!-- INCLUDE channel-details.inc.tpl -->
                    </td>
                    <td class="icons r">
                        <span id="cons{ID}" class="consumption"></span>
                    </td>
                    <td>{UNIT}</td>
                    <td id="costs{ID}" class="costs"></td>
                    <td class="icons">
                        <!-- IF {GRAPH} -->
                        <i class="ico chart btn chartdialog"></i>
                        <!-- ELSE --><!-- MACRO SpacerImg --><!-- ENDIF -->
                        <!-- IF {READ} -->
                        <i class="ico document-invoice btn showlist"></i>
                        <!-- ELSE --><!-- MACRO SpacerImg --><!-- ENDIF -->
                        <i class="ico node-design btn editentity"></i>
                        </a>
                    </td>
                </tr>
                <!-- END -->
            </tbody>

            <tfoot>
                <tr>
                    <th colspan="3">&nbsp;</th>
                    <th class="l">{{Total}}:</th>
                    <th id="costs" style="padding-right:10px" class="r"></th>
                    <th><i class="ico information-frame tip" tip="#IconLegend"></i></th>
                </tr>
            </tfoot>
        </table>
    </div>

    <div id="tabs-2">
        <div class="alpha grid_8">
            <select id="load-delete-view" data-placeholder="--- {{SelectChart}} ---"></select>
            <button id="btn-load" style="margin:0 1em" class="tip" title="{{Load}}">{{Load}}</button>
            <button id="btn-delete" data-confirmed="0" class="tip" title="{{Delete}}">{{Delete}}</button>
            <br /><br />
            <input id="saveview" type="text" class="fl" value="{VIEW}" size="35" />

            <select id="visibility" style="margin-left:.5em">
                <option value="0">{{PrivateChart}}</option>
                <option value="1">{{PublicChart}}</option>
                <option value="2">{{MobileChart}}</option>
            </select>

            <img src="/images/ico/information_frame.png" class="tip" title="{{publicHint}}"
                 style="margin-left:8px;width:16px;height:16px" width="16" height="16" />
            <button id="btn-save" class="tip" style="margin-left:.5em" title="{{Save}}">{{Save}}</button>
        </div>
        <div class="r">
            <a id="btn-bookmark" href="#" class="tip" title="{{DragBookmark}}">Bookmark</a>
            <br /><br />
            <a id="btn-permanent" href="#" class="tip" title="{{DragPermanent}}">Permanent bookmark</a>
        </div>

        <div class="clear"></div>
    </div>

</div>

<div id="IconLegend">
    <div class="icons legendtip">
        <i class="ico lock"></i>{{PrivateChannel}}<br />
        <i class="ico chart"></i>{{ChartSettingsTip}}<br />
        <i class="ico document-invoice"></i>{{ListHint}}<br />
        <i class="ico node-design"></i>{{EditEntity}}
    </div>
</div>
