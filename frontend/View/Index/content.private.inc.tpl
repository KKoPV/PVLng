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

<div class="grid_10">
    <a id="togglewrapper" href="#" class="tip" title="{{ToggleChannels}} (F3)">{{ToggleChannels}} (F3)</a>
</div>

<div class="clear"></div>

<div id="wrapper" class="grid_10" style="padding-top:1em">

    <table id="tree" class="dataTable treeTable">
        <thead>
        <tr>
            <th style="width:1%">
                <img id="treetoggle" src="/images/ico/toggle.png"
                     style="width:16px;height:16px" width="16" height="16"
                     class="tip" onclick="ToggleTree()" alt="[+]" title="{{CollapseAll}} (F4)" />
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
            <tr data-tt-id="{ID}" <!-- IF {PARENT} -->data-tt-parent-id="{PARENT}" <!-- ENDIF -->
                <!-- IF !{GRAPH} -->class="no-graph"<!-- ENDIF -->>
                <td>
                    <!-- IF {GRAPH} -->
                    <input id="c{ID}" class="channel iCheck" type="checkbox" name="v[{ID}]"
                           data-id="{ID}" data-name="{NAME}" data-guid="{GUID}" data-unit="{UNIT}"
                           value='{PRESENTATION}'
                           <!-- IF {CHECKED} -->checked="checked"<!-- ENDIF --> />
                    <!-- ENDIF -->
                </td>
                <td style="padding:0.4em 0">
                    <img style="vertical-align:middle;width:16px;height:16px;margin-right:8px"
                         src="{ICON}" width="16" alt="" height="16" class="tip" title="{TYPE}" />
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
                         style="float:right;display:none;width:16px;height:16px" />
                </td>
                <td id="cons{ID}" class="consumption r"></td>
                <td id="u{ID}">{UNIT}</td>
                <td id="costs{ID}" class="costs r"></td>
                <td>
                    <!-- IF {GRAPH} -->
                    <img style="cursor:pointer;width:16px;height:16px"
                         src="/images/ico/chart.png" onclick="ChartDialog({ID}, '{NAME}')"
                         class="tip" title="{{ChartSettingsTip}}" width="16" height="16" />
                    <!-- ENDIF -->
                </td>
            </tr>
            <!-- END -->
        </tbody>

        <tfoot>
            <tr>
                <th colspan="3">&nbsp;</th>
                <th colspan="2" class="l">{{Total}}</th>
                <th id="costs" style="padding-right:10px" class="r"></th>
                <th></th>
            </tr>
        <tfoot>
    </table>

</div>

<h3 class="grid_10">
    {{Variants}}
    <img style="margin-left:.5em;width:16px;height:16px" class="tip"
         src="/images/ico/information_frame.png" width="16" height="16"
         title="{{MobileVariantHint}}" />
</h3>

<div class="clear"></div>

<div class="grid_10">
    <table style="width:100%"><tr>
    <td style="width:60%">
        <select id="loaddeleteview" name="loaddeleteview">
            <option value="">--- {{Select}} ---</option>
            <!-- BEGIN VIEWS -->
                <!-- show all charts and mark public charts -->
                <option value="{NAME}" <!-- IF {SELECTED} -->selected="selected"<!-- ENDIF -->>
                    {NAME} <!-- IF {PUBLIC} --> ({{public}})<!-- ENDIF -->
                </option>
            <!-- END -->
        </select>
        <input type="submit" name="load" value="{{Load}}" style="margin-left:.5em" />
        <input type="submit" id="delete-view" name="delete" value="{{Delete}}" style="margin-left:.5em" />
    </td>
    <td>
        <input id="saveview" type="text" style="margin-left:3em"
               name="saveview" value="{VIEW}"/>
    </td>
    <td>
        <input type="checkbox" class="iCheckLine" id="public" name="public" value="1"
               <!-- IF {VIEWPUBLIC} -->checked="checked"<!-- ENDIF --> />
        <label for="public">{{public}}</label>
    </td>
    <td style="width:30%">
        <img src="/images/ico/information_frame.png" class="tip" title="{{publicHint}}"
             style="margin-left:.5em;width:16px;height:16px" width="16" height="16" />
        <input type="submit" name="save" value="{{Save}}" style="margin:0 3em 0 .5em" />
    </td>
    <td class="r" style="white-space:nowrap">
        <a id="btn-permanent" class="tip" title="{{DragPermanent}}" data-text="{VIEW} & | {TITLE}" data-url="/chart/{SLUG}">
            {VIEW} | {TITLE}
        </a>
        <a href="/chart/{SLUG}" id="btn-bookmark" class="tip" style="margin-left:.5em" title="{{DragBookmark}}">
            {VIEW} | {TITLE}
        </a>
    </td>
    </tr></table>
</div>

<div class="clear"></div>
