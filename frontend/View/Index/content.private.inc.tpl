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

<!-- Use this image as spacer for not available moving actions of channels -->
<!-- DEFINE MACRO PIX -->
src="data:image/gif;base64,R0lGODlhAQABAIAAAP///wAAACH5BAEAAAAALAAAAAABAAEAAAICRAEAOw"
style="width:16px;height:16px" width="16" height="16" alt=""
<!-- END DEFINE -->

<div class="grid_10">
    <button id="togglewrapper" class="tip" title="{{ToggleChannels}} (F3)">{{ToggleChannels}} (F3)</button>
</div>

<div class="clear"></div>

<div id="wrapper" class="grid_10" style="padding-top:1em;display:none">

    <table id="data-table" class="dataTable treeTable">
        <thead>
        <tr>
            <th style="width:1%">
                <img id="treetoggle" src="/images/ico/toggle<!-- IF {VIEW} -->_expand<!-- ENDIF -->.png"
                     style="width:16px;height:16px" width="16" height="16"
                     class="tip" alt="[+]" tip="#tiptoggle" />
                <div id="tiptoggle">{{CollapseAll}} (F4)</div>
            </th>
            <th class="l">
                <span class="indenter" style="padding-left: 0px;"></span>
                {{Channel}}
            </th>
            <th style="width:1%">
                <img src="/images/ico/16x16.png" style="width:16px;height:16px" width="16" height="16" alt="" />
            </th>
            <th style="width:1%" class="r">{{Amount}}</th>
            <th style="width:1%" class="l">{{Unit}}</th>
            <th class="r">{{Earning}}&nbsp;/ {{Cost}}</th>
            <th style="width:1%">
                <img src="/images/ico/node_design.png" style="width:16px;height:16px" width="16" height="16" alt="" />
            </th>
        </tr>
        </thead>

        <tbody>
            <!-- BEGIN DATA -->
            <tr id="rc{ID}" class="channel<!-- IF !{GRAPH} --> no-graph<!-- ENDIF -->"
                data-tt-id="{ID}" <!-- IF {PARENT} -->data-tt-parent-id="{PARENT}" <!-- ENDIF --> >
                <td>
                    <!-- IF {GRAPH} -->
                    <input id="c{ID}" type="checkbox" class="channel iCheck"
                           data-id="{ID}" data-name="{NAME}" data-guid="{GUID}" data-unit="{UNIT}" />
                    <!-- ENDIF -->
                </td>
                <td style="padding:0.4em 0" <!-- IF {TYPE_ID} == "0" -->class="alias"<!-- ENDIF -->>
                    <img style="vertical-align:middle;width:16px;height:16px;margin-right:8px"
                         src="{ICON}" width="16" alt="" height="16" class="tip" title="{TYPE}" alt="">
                    <strong class="tip" title="{GUID}">{NAME}</strong>
                    <!-- IF {DESCRIPTION} --> ({DESCRIPTION})<!-- ENDIF -->
                    <!-- IF !{PUBLIC} -->
                        <img src="/images/ico/lock.png" class="tip"
                             style="margin-left:8px;width:16px;height:16px"
                             width="16" height="16" title="{{PrivateChannel}}"
                             alt="[private]"/>
                    <!-- ENDIF -->
                </td>
                <td>
                    <img id="s{ID}" src="/images/spinner.gif" width="16" height="16"
                         style="float:right;display:none;width:16px;height:16px" alt="">
                </td>
                <td id="cons{ID}" class="consumption"></td>
                <td id="u{ID}">{UNIT}</td>
                <td id="costs{ID}" class="costs"></td>
                <td style="white-space:nowrap">
                    <!-- IF {GRAPH} -->
                    <img style="cursor:pointer;width:16px;height:16px"
                         src="/images/ico/chart.png" onclick="ChartDialog({ID}, '{NAME}')"
                         class="tip imgbar" title="{{ChartSettingsTip}}" width="16" height="16" alt="">
                    <!-- ELSE -->
                    <img <!-- MACRO PIX --> class="imgbar" />
                    <!-- ENDIF -->
                    <!-- IF {READ} -->
                    <a href="/list/{GUID}" class="tip" title="{{ListHint}}">
                        <img src="/images/ico/document-invoice.png" class="imgbar"
                             width="16p" height="16" alt="l">
                    </a>
                    <!-- ELSE -->
                    <img <!-- MACRO PIX --> class="imgbar" />
                    <!-- ENDIF -->
                </td>
            </tr>
            <!-- END -->
        </tbody>

        <tfoot>
            <tr>
                <th colspan="4">&nbsp;</th>
                <th class="l">{{Total}}:</th>
                <th id="costs" style="padding-right:10px" class="r"></th>
                <th></th>
            </tr>
        <tfoot>
    </table>

</div>

<div class="clear"></div>

<h3 class="grid_10">{{Variants}}</h3>

<div class="clear"></div>

<div class="grid_8">
    <select id="loaddeleteview"></select>
    <button id="btn-load" style="margin:0 .5em" class="tip" title="{{Load}}">{{Load}}</button>
    <button id="btn-delete" data-confirmed="0" class="tip" title="{{Delete}}">{{Delete}}</button>
    <br /><br />
    <input class="fl" id="saveview" type="text" value="{VIEW}" size="35" />

    <select id="visibility" style="margin-left:.5em">
        <option value="0">{{PrivateChart}}</option>
        <option value="1">{{PublicChart}}</option>
        <option value="2">{{MobileChart}}</option>
    </select>

    <img src="/images/ico/information_frame.png" class="tip" title="{{publicHint}}"
         style="margin-left:8px;width:16px;height:16px" width="16" height="16" />
    <button id="btn-save" class="tip" style="margin-left:.5em" title="{{Save}}">{{Save}}</button>
</div>
<div class="grid_2 r">
    <a id="btn-bookmark" href="#" class="tip" title="{{DragBookmark}}">Bookmark</a>
    <br /><br />
    <a id="btn-permanent" href="#" class="tip" title="{{DragPermanent}}">Permanent bookmark</a>
</div>

<div class="clear"></div>
