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
                    <th></th>
                </tr>
            </thead>

            <tbody>
                <!-- BEGIN DATA -->
                <tr id="rc{raw:ID}" data-tt-id="{raw:ID}"
                    class="channel<!-- IF {GRAPH} --> graph<!-- ENDIF -->"
                    <!-- IF {PARENT} -->data-tt-parent-id="{raw:PARENT}" <!-- ENDIF -->
                    >
                    <td>
                        <!-- IF {GRAPH} -->
                        <input id="c{raw:ID}" type="checkbox" class="channel iCheck" data-id="{raw:ID}" />
                        <!-- ENDIF -->
                    </td>
                    <td <!-- IF {TYPE_ID} == "0" -->class="alias"<!-- ENDIF -->>
                        <!-- INCLUDE channel-details.inc.tpl -->
                    </td>
                    <td id="cons{raw:ID}" class="consumption"></td>
                    <td>{UNIT}</td>
                    <td id="costs{raw:ID}" class="costs"></td>
                    <td class="icons">
                        <i class="fa fa-fw btn<!-- IF {GRAPH} --> fa-bar-chart chartdialog<!-- ENDIF -->"></i>
                        <i class="fa fa-fw btn<!-- IF {READ} --> fa-file-text showlist<!-- ENDIF -->"></i>
                        <i class="fa fa-fw btn fa-pencil editentity"></i>
                        <!-- IF {READ} OR {WRITE} -->
                            <i class="fa fa-fw btn fa-key fa-rotate-90 guid" data-guid="{GUID}"></i>
                        <!-- ENDIF -->
                    </td>
                </tr>
                <!-- END -->
            </tbody>

            <tfoot>
                <tr>
                    <th colspan="3">&nbsp;</th>
                    <th class="l">{{Total}}:</th>
                    <th id="costs" style="padding-right:10px" class="r"></th>
                    <th></th>
                </tr>
            </tfoot>
        </table>

        <div class="icon-legend">
            <i class="fa fa-lock"></i>{{PrivateChannel}} &nbsp;
            <i class="fa fa-bar-chart"></i>{{ChartSettingsTip}} &nbsp;
            <i class="fa fa-file-text"></i>{{ListHint}} &nbsp;
            <i class="fa fa-pencil"></i>{{EditEntity}} &nbsp;
            <i class="fa fa-key fa-rotate-90"></i>{{ShowGUID}}
        </div>

    </div>

    <div id="tabs-2">
        <div>
            <div class="alpha grid_4">
                <select id="load-delete-view" style="width:100%" data-placeholder="--- {{SelectChart}} ---"></select>
            </div>
            <div class="fl">
                <button id="btn-load" class="tip" title="{{Load}}">{{Load}}</button>
                <button id="btn-delete" class="tip" style="margin-left:1em" title="{{Delete}}">{{Delete}}</button>
            </div>
            <div class="r">
                <a id="btn-bookmark" href="#" class="tip" title="{{DragBookmark}}">Bookmark</a>
            </div>
        </div>

        <div class="clear"></div><br />

        <div>
            <div class="alpha grid_4">
                <input id="saveview" type="text" class="fl" value="{VIEW}" style="width:97%">
            </div>
            <div class="fl">
                <select id="visibility">
                    <option value="0">{{PrivateChart}}</option>
                    <option value="1">{{PublicChart}}</option>
                    <option value="2">{{MobileChart}}</option>
                </select>
                <i class="fa fa-question tip" title="{{publicHint}}"></i>
                <button id="btn-save" class="tip"  style="margin-left:1em"title="{{Save}}">{{Save}}</button>
            </div>
            <div class="r">
                <a id="btn-permanent" href="#" class="tip" title="{{DragPermanent}}">Permanent bookmark</a>
            </div>
        </div>

        <div class="clear"></div>
    </div>

</div>
