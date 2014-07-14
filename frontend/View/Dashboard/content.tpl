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

<!-- IF {EMBEDDED} == "1" -->
    <!-- INCLUDE embedded.header.tpl -->
<!-- ENDIF -->

<div style="max-width:940px;margin:1em auto">

    <!-- IF !{CHANNELCOUNT} -->
    <div id="chart-placeholder">
        <!-- IF {USER} -->
        <p>{{DashboardIntro}}</p>
        <p><a href="/channel/new/30">{{CreateDashboardChannel}}</a></p>
        <!-- ENDIF -->
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

<!-- IF {EMBEDDED} OR !{USER} -->

    <!-- BEGIN DATA --><!-- IF {CHECKED} -->
    <input class="channel" type="checkbox" value="{ID}" checked="checked" data-guid="{GUID}" style="display:none"/>
    <!-- ENDIF --><!-- END -->

    <!-- IF {EMBEDDED} == "1" -->
        <!-- INCLUDE embedded.footer.tpl -->
    <!-- ENDIF -->

<!-- ELSE -->

<p>

    <button id="togglewrapper" class="fl tip" title="{{ToggleChannels}}">{{ToggleChannels}}</button>
    <!-- IF {SLUG} -->
    <div class="fr">
        <a href="/dashboard/embed/{SLUG}" class="button tip" style="margin-right:.5em"
           title="Display / embedded mode" data-primary="ui-icon-image" data-text="">Display / embedded mode</a>
        <a href="/dashboard/{SLUG}" class="button tip"
           title="{{DragBookmark}}" data-primary="ui-icon-bookmark" data-text="">Bookmark</a>
    </div>
    <!-- ENDIF -->
</p>

<div class="clear"></div>

<form id="form-dashboard" method="post" action="/dashboard">

<input type="hidden" name="id" value="{ID}" />

<div id="wrapper">

    <p>
        <label for="name" class="autowidth">{{Name}}:</label>
        <input id="name" type="text" name="name" value="{NAME}" size="50" required="required" />

        <select name="public" style="margin-left:10px">
            <option value="0">{{Private}}</option>
            <option value="1" <!-- IF {PUBLIC} -->selected="selected"<!-- ENDIF -->>{{Public}}</option>
        </select>

        <input type="submit" style="margin-left:20px" value="{{Create}}" />
        <input type="submit" style="margin-left:10px" class="with-id" name="save" value="{{Change}}" />
        <input type="submit" style="margin-left:10px" class="with-id" name="delete" value="{{Delete}}" />
    </p>

    <table id="tree" class="dataTable">
        <thead>
        <tr>
            <th></th>
            <th></th>
            <th class="l">{{Channel}}</th>
            <th></th>
        </tr>
        </thead>

        <tbody>

        <!-- BEGIN DATA -->

        <tr id="{_LOOP_ID}">
            <td>
                {_LOOP_ID}
            </td>
            <td>
                <input class="channel iCheck" type="checkbox" name="c[]" data-guid="{GUID}"
                       value="{ID}" <!-- IF {CHECKED} -->checked="checked"<!-- ENDIF --> />
            </td>
            <td class="drag">
                <img src="{ICON}" class="channel-icon" alt="" />
                <strong>{NAME}</strong>
                <!-- IF {DESCRIPTION} --> ({DESCRIPTION})<!-- ENDIF -->
            </td>
            <td class="icons">
                <a href="/channel/edit/{ENTITY}?returnto=/dashboard<!-- IF {__SLUG} -->/{__SLUG}<!-- ENDIF -->" class="tip" title="{{EditEntity}}">
                    <img src="/images/ico/node_design.png" class="imgbar" width="16" height="16" alt="e">
                </a>
            </td>
        </tr>

        <!-- END -->

        </tbody>

        <tfoot>
        <tr>
            <th colspan="4" class="l s">{{DragRowsToReorder}}</th>
        </tr>
        </tfoot>

    </table>

</div> <!-- wrapper -->

</form>

<!-- ENDIF -->
