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

<table id="tree" class="dataTable treeTable">
    <thead>
    <tr>
        <th style="text-align:left !important">
            <img id="treetoggle" data-expanded="1" src="/images/ico/toggle.png" class="fl tip"
                 width="16p" height="16" tip="#tiptoggle" alt="[+]">
            <div class="c">{{ChannelHierarchy}}</div>
            <div id="tiptoggle" style="display:none">{{CollapseAll}}</div>
        </th>
        <th></th>
        <th></th>
        <th></th>
        <th>GUID</th>
    </tr>
    </thead>

    <tbody>

    <!-- BEGIN DATA -->

    <tr data-tt-id="{ID}" class="droppable"
        <!-- IF {PARENT} -->data-tt-parent-id="{PARENT}" <!-- ENDIF -->>
        <td>
            <span class="<!-- IF {CHILDS} -->non-<!-- ENDIF -->draggable"
                  data-id="{ID}" data-entity="{ENTITY}">
                <img style="vertical-align:top;margin-right:8px"
                     width="16" height="16" class="tip" title="{TYPE}"
                     src="{ICON}" alt="">
                <strong<!-- IF {ALIAS_OF} --> class="alias"<!-- ENDIF -->>{NAME}</strong>
                <!-- IF {UNIT} --> [{UNIT}]<!-- ENDIF -->
                <!-- IF {DESCRIPTION} --> ({DESCRIPTION})<!-- ENDIF -->
                <!-- IF !{PUBLIC} -->
                    <img src="/images/ico/lock.png" class="tip"
                         style="margin-left:8px;width:16px;height:16px"
                         width="16" height="16" title="{{PrivateChannel}}"
                         alt="[private]">
                <!-- ENDIF -->
                <span></span>
            </span>
        </td>

        <td style="white-space:nowrap">
            <!-- IF {CHILDS} -->
            <a href="#" onclick="addChild({ID}); return false" class="tip"
               title="{{AssignEntity}}">
                <img src="/images/ico/node_insert_next.png" class="imgbar"
                     width="16p" height="16" alt="add">
            </a>
            <!-- ELSE -->
            <img <!-- MACRO PIX --> class="imgbar" />
            <!-- ENDIF -->

            <!-- IF {CHILDS} == "0" -->
            <form action="/overview/delete" method="post" class="delete-form">
            <input type="hidden" name="id" value="{ID}">
            <input type="image" src="/images/ico/node_delete_next.png"
                   class="imgbar tip nb" title="{{DeleteEntity}}"
                   style="background-color:transparent" alt="-">
            </form>
            <!-- ELSE -->
            <form action="/overview/deletebranch" method="post" class="delete-form">
            <input type="hidden" name="id" value="{ID}">
            <input type="image" src="/images/ico/node_delete.png"
                   class="imgbar tip nb" title="{{DeleteBranch}}"
                   style="background-color:transparent" alt="-!">
            </form>
            <!-- ENDIF -->
        </td>

        <td style="white-space:nowrap">
            <!-- IF {READ} -->
            <a href="/list/{ENTITY}" class="tip" title="{{ListHint}}">
                <img src="/images/ico/document-invoice.png" class="imgbar"
                     width="16p" height="16" alt="l">
            </a>
            <!-- ELSE -->
            <img <!-- MACRO PIX --> class="imgbar" />
            <!-- ENDIF -->

            <a href="/channel/edit/{ENTITY}?returnto=/overview" class="tip" title="{{EditEntity}}">
                <img src="/images/ico/node_design.png" class="imgbar"
                     width="16" height="16" alt="e">
            </a>

            <!-- IF {CHILDS} AND {GUID} AND !{ALIAS} -->
            <form action="/channel/alias" method="post">
            <input type="hidden" name="id" value="{ENTITY}">
            <input type="image" src="/images/ico/arrow-split.png"
                   style="background-color:transparent" class="imgbar wide tip nb"
                   title="{{AliasEntity}}" alt="a">
            </form>
            <!-- ENDIF -->
        </td>

        <td style="white-space:nowrap">
            <!-- IF {LEVEL} > "2" -->
            <form action="/overview/moveup" method="post">
            <input type="hidden" name="id" value="{ID}">
            <input type="image" src="/images/ico/navigation_180_frame.png"
                   class="imgbar tip" style="background-color:transparent"
                   title="{{MoveEntityLeft}}" alt="h">
            </form>
            <!-- ELSE -->
            <img <!-- MACRO PIX --> class="imgbar" />
            <!-- ENDIF -->

            <!-- IF {LEVEL} != 1 AND {UPPER} != 0 -->
            <a href="/overview/moveleft" title="{{MoveEntityUp}}" class="tip"
               onclick="return moveChild({ID}, 'moveleft')">
                <img src="/images/ico/navigation_090_frame.png" class="imgbar"
                     width="16p" height="16" alt="u">
            </a>
            <!-- ELSE -->
            <img <!-- MACRO PIX --> class="imgbar" />
            <!-- ENDIF -->

            <!-- IF {LEVEL} != 1 AND {LOWER} != 0 -->
            <a href="/overview/moveright" title="{{MoveEntityDown}}" class="tip"
               onclick="return moveChild({ID}, 'moveright')">
                <img src="/images/ico/navigation_270_frame.png" class="imgbar"
                     width="16p" height="16" alt="d">
            </a>
            <!-- ELSE -->
            <img <!-- MACRO PIX --> class="imgbar" />
            <!-- ENDIF -->
        </td>

        <td>
            <input style="background-color:transparent;border:0;width:24em;font-family:monospace"
                   class="guid" value="{GUID}" readonly="readonly">
        </td>
    </tr>

    <!-- END -->

    <tr data-tt-id="1" class="droppable">
        <td>
            <span class="non-draggable">
                <img src="/images/ico/plus_circle_frame.png" alt="add"
                     style="width:16px;height:16px" width="16" height="16"
                     onclick="addChild(1)" class="tip" title="{{AddOneToManyChannels}}">
            </span>
        </td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
    </tr>

    </tbody>

    <tfoot>
    <tr>
        <th colspan="5" style="padding-top:8px;padding-bottom:8px;text-align:left">
                <div id="drag-new-wrapper" style="display:none;margin-top:.5em;margin-bottom:1em">
                    <span id="drag-new" class="draggable">
                        <img src="/images/ico/hand.png"
                             style="width:16px;height:16px;margin-right:8px"
                             width="16" height="16">
                        <span id="drag-text"></span>
                    </span>
                </div>
                <select id="add-child" style="margin-right:1em">
                    <option value="">--- Drag & Drop ---</option>
                    <!-- BEGIN ENTITIES -->
                    <option value="{ID}">
                        {TYPE}: {NAME}
                        <!-- IF {UNIT} --> [{UNIT}]<!-- ENDIF -->
                        <!-- IF {DESCRIPTION} --> ({DESCRIPTION})<!-- ENDIF -->
                    </option>
                    <!-- END -->
                </select>
        </th>
    </tr>
    <tfoot>
</table>

<p>
    <a class="button tip" href="/channel/add" title="Alt+N">{{CreateChannel}}</a>
</p>

</div>

<div class="clear"></div>

<!-- Dialogs ------------------------------------------------------------- -->

<div id="dialog-addchild" style="display:none" title="{{AddChild}}">
    <form id="form-addchild" action="/overview/addchild" method="post">
        <input type="hidden" id="parent" name="parent">
        <p>
            <label for="child">{{SelectEntity}}:</label>
        </p>
        <select id="child" name="child[]" style="width:100%;margin-bottom:0.5em">
            <option value="">--- {{Select}} ---</option>
            <!-- BEGIN ENTITIES -->
            <option value="{ID}">
                {TYPE}: {NAME}
                <!-- IF {UNIT} --> [{UNIT}]<!-- ENDIF -->
                <!-- IF {DESCRIPTION} --> ({DESCRIPTION})<!-- ENDIF -->
            </option>
            <!-- END -->
        </select>
    </form>
    <img id="addmorechild" src="/images/ico/plus_circle_frame.png"
         style="width:16px;height:16px" width="16" height="16"
         class="tip" title="{{AddAnotherChild}}" alt="[new select]">
</div>

<div id="dialog-move" style="display:none" title="{{MoveChannel}}">
    <form id="form-movechild" action="" method="post">
    <input type="hidden" name="id">
    <p>
        {{MoveChannelHowMuchRows}}
    </p>
    <p>
        <div style="float:left;padding-top:4px;width:35px">
            <input type="radio" class="iCheck" id="countmax" name="countmax" value="0" checked="checked">
        </div>
        <label for="countmax">
            <input type="number" step="1" style="width:3em;margin-right:.5em" class="numbersOnly" name="count" value="1">
            {{Positions}}
        </label>
    </p>
    <p>
        <div style="float:left;padding-top:4px;width:35px">
            <input type="radio" class="iCheck" id="movecountmax" name="countmax" value="1">
        </div>
        <label for="movecountmax">{{MoveChannelStartEnd}}</label>
    </p>

    </form>
</div>

<div id="dialog-confirm" style="display:none" title="{{DeleteEntity}}">
    <span class="ui-icon ui-icon-alert" style="float: left; margin: 0 7px 20px 0;"></span>
    {{ConfirmDeleteTreeItems}}
</div>
