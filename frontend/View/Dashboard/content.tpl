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

<!-- IF {EMBEDDED} -->
    <!-- INCLUDE header.embedded.tpl -->
<!-- ELSE -->

<div class="grid_10">

<!-- INCLUDE header.tpl -->

<!-- ENDIF -->

<div style="max-width:940px;margin:1em auto">

    <!-- IF !{CHANNELCOUNT} -->
    <div id="chart-placeholder">
        Please select your channels to display.
    </div>
    <!-- ELSEIF {CHANNELCOUNT} == 1 -->
        <!-- INCLUDE grid.01.tpl -->
    <!-- ELSEIF {CHANNELCOUNT} == 2 -->
        <!-- INCLUDE grid.02.tpl -->
    <!-- ELSEIF {CHANNELCOUNT} == 3 -->
        <!-- INCLUDE grid.03.tpl -->
    <!-- ELSEIF {CHANNELCOUNT} == 4 -->
        <!-- INCLUDE grid.04.tpl -->
    <!-- ELSEIF {CHANNELCOUNT} == 5 -->
        <!-- INCLUDE grid.05.tpl -->
    <!-- ELSEIF {CHANNELCOUNT} == 6 -->
        <!-- INCLUDE grid.06.tpl -->
    <!-- ELSEIF {CHANNELCOUNT} == 7 -->
        <!-- INCLUDE grid.07.tpl -->
    <!-- ELSEIF {CHANNELCOUNT} == 8 -->
        <!-- INCLUDE grid.08.tpl -->
    <!-- ELSEIF {CHANNELCOUNT} == 9 -->
        <!-- INCLUDE grid.09.tpl -->
    <!-- ELSEIF {CHANNELCOUNT} == 10 -->
        <!-- INCLUDE grid.10.tpl -->
    <!-- ELSEIF {CHANNELCOUNT} == 11 -->
        <!-- INCLUDE grid.11.tpl -->
    <!-- ELSEIF {CHANNELCOUNT} == 12 -->
        <!-- INCLUDE grid.12.tpl -->
    <!-- ELSE -->
        <!-- INCLUDE grid.12.tpl -->
        <p>
            Sorry, only up to 12 charts supported yet...
        </p>
        <p>
            You are invited to improve the layout,
            see <code>frontend/View/Dashboard/content.tpl</code> for details.
        </p>
    <!-- ENDIF -->

</div>

<div class="clear"></div>

<!-- IF {EMBEDDED} -->

    <!-- BEGIN DATA --><!-- IF {CHECKED} -->
    <input class="channel" type="checkbox" value="{ID}" checked="checked"
          data-guid="{GUID}" style="display:none"/>
    <!-- ENDIF --><!-- END -->

    <!-- INCLUDE footer.embedded.tpl -->

<!-- ELSE -->

</div>

<div class="clear"></div>

<div class="push_2 grid_6">

<p>
    <button id="togglewrapper" class="tipbtn" title="{{ToggleChannels}}">{{ToggleChannels}}</button>
</p>

<form method="post" action="/dashboard">

<div id="wrapper">

    <table id="tree" class="dataTable treeTable">
        <thead>
        <tr>
            <th>
                <img id="treetoggle" src="/images/ico/toggle.png"
                     style="width:16px;height:16px"
                     class="tip" onclick="ToggleTree()" alt="[+]"
                     title="#tiptoggle" width="16" height="16" />
                <div id="tiptoggle" style="display:none">{{CollapseAll}}</div>
            </th>
            <th style="width:99%" class="l">
                <span class="indenter" style="padding-left: 0px;"></span>
                {{Channel}}
            </th>
        </tr>
        </thead>

        <tbody>

        <!-- BEGIN DATA -->

        <tr data-tt-id="{ID}" <!-- IF {PARENT} -->data-tt-parent-id="{PARENT}" <!-- ENDIF -->
            <!-- IF !{GRAPH} -->class="no-graph"<!-- ENDIF -->>
            <td>
                <!-- IF {GRAPH} -->
                <input class="channel iCheck" type="checkbox" name="v[]"
                       value="{ID}" data-guid="{GUID}"
                       <!-- IF {CHECKED} -->checked="checked"<!-- ENDIF --> />
                <!-- ENDIF -->
            </td>
            <td style="padding:0.4em 0">
                <img style="vertical-align:middle;width:16px;height:16px"
                     class="imgbar tip" src="{ICON}" alt=""
                     title="{TYPE}" width="16" height="16"/>
                <strong class="tip" title="{GUID}">{NAME}</strong>
                <!-- IF {DESCRIPTION} --> ({DESCRIPTION})<!-- ENDIF -->
            </td>
        </tr>

        <!-- END -->

        </tbody>

    </table>

    <p>
        <input type="submit" value="{{Save}}" />
    </p>

</div> <!-- wrapper -->

</form>

<div class="clear"></div>

<!-- ENDIF -->
