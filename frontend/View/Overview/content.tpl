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

<!-- Use this image as spacer for not available moving actions of channels, 1px transparent GIF -->
<!-- DEFINE SpacerImg -->
<img src="data:image/gif;base64,R0lGODlhAQABAIAAAP///wAAACH5BAEAAAAALAAAAAABAAEAAAICRAEAOw" style="height:0px" alt="" />
<!-- END DEFINE -->

<!-- DEFINE ChannelSelectOptions -->
    <option></option>
    <!-- BEGIN CHANNELS -->
    <optgroup label="{TYPE}">
        <!-- BEGIN MEMBERS -->
        <option value="{ID}">
            {NAME}
            <!-- IF {UNIT} --> [{UNIT}]<!-- ENDIF -->
            <!-- IF {DESCRIPTION} --> ({DESCRIPTION})<!-- ENDIF -->
        </option>
        <!-- END -->
    </optgroup>
    <!-- END -->
<!-- END DEFINE -->

<p><a href="/channel/add" class="button tip" title="Alt-N">{{CreateChannel}}</a></p>

<table id="tree" class="dataTable treeTable">
    <thead>
    <tr>
        <th style="text-align:left !important">
            <img id="treetoggle" data-expanded="1" src="/images/ico/toggle.png" class="fl tipbtn"
                 width="16" height="16" tip="#treetoggletip" alt="[+]">
            <div id="treetoggletip" style="display:none">{{CollapseAll}}</div>
            <div class="c">{{ChannelHierarchy}}</div>
        </th>
        <th></th>
        <th></th>
    </tr>
    </thead>

    <tbody>

    <!-- BEGIN DATA -->

    <tr data-tt-id="{ID}" class="droppable <!-- IF {CHILDS} -->group<!-- ELSE -->channel<!-- ENDIF -->"
        <!-- IF {PARENT} -->data-tt-parent-id="{PARENT}" <!-- ENDIF -->>
        <td>
            <span class="icons draggable" data-id="{ID}">
                <img src="{ICON}" class="channel-icon tip" title="{TYPE}" alt="({TYPE})">
                <strong<!-- IF {ALIAS_OF} --> class="alias"<!-- ENDIF -->>{NAME}</strong>
                <!-- IF {UNIT} --> [{UNIT}]<!-- ENDIF -->
                <!-- IF {DESCRIPTION} --><small> ({DESCRIPTION})</small><!-- ENDIF -->
                <!-- IF !{PUBLIC} -->
                    <img src="/images/ico/lock.png" style="margin-left:.5em" alt="[private]">
                <!-- ENDIF -->
            </span>
        </td>

        <td class="icons">
            <!-- IF {CHILDS} -->
            <img src="/images/ico/node_insert_next.png" class="btn" onclick="addChild({ID}); return false" alt="+">
            <!-- ELSE --><!-- MACRO SpacerImg --><!-- ENDIF -->

            <!-- IF {HASCHILDS} -->
            <img src="/images/ico/node_delete.png" class="btn delete-node" alt="--">
            <!-- ELSE -->
            <img src="/images/ico/node_delete_next.png" class="btn delete-node" alt="--">
            <!-- ENDIF -->

            <!-- IF {LEVEL} > 2 -->
            <form action="/overview/moveup" method="post">
            <input type="hidden" name="id" value="{ID}">
            <input type="image" src="/images/ico/navigation_180_frame.png"
                   style="background-color:transparent" alt="h">
            </form>
            <!-- ELSE --><!-- MACRO SpacerImg --><!-- ENDIF -->

            <!-- IF {LEVEL} != 1 AND {UPPER} != 0 -->
            <a href="/overview/moveleft" onclick="return moveChild({ID}, 'moveleft')">
                <img src="/images/ico/navigation_090_frame.png" alt="u">
            </a>
            <!-- ELSE --><!-- MACRO SpacerImg --><!-- ENDIF -->

            <!-- IF {LEVEL} != 1 AND {LOWER} != 0 -->
            <a href="/overview/moveright" onclick="return moveChild({ID}, 'moveright')">
                <img src="/images/ico/navigation_270_frame.png" alt="d">
            </a>
            <!-- ELSE --><!-- MACRO SpacerImg --><!-- ENDIF -->
        </td>

        <td class="icons">
            <!-- IF {READ} -->
            <a href="/list/{GUID}">
                <img src="/images/ico/document-invoice.png" alt="l">
            </a>
            <!-- ELSE --><!-- MACRO SpacerImg --><!-- ENDIF -->

            <!-- IF {TYPE_ID} != "0" -->
            <a href="/channel/edit/{ENTITY}?returnto=/overview">
                <img src="/images/ico/node_design.png" alt="e">
            </a>
            <!-- ELSE --><!-- MACRO SpacerImg --><!-- ENDIF -->

            <!-- IF {TYPE_ID} != "0" -->
            <a href="/channel/add/{ENTITY}?returnto=/overview">
                <img src="/images/ico/node_select_child.png" alt="e">
            </a>
            <!-- ELSE --><!-- MACRO SpacerImg --><!-- ENDIF -->

            <!-- IF {CHILDS} AND {GUID} AND {TYPE_ID} != "30" AND !{ALIAS} -->
            <img src="/images/ico/arrow-split.png" class="btn create-alias" alt="a">
            <!-- ELSE --><!-- MACRO SpacerImg --><!-- ENDIF -->

            <!-- IF {GUID} -->
            <img src="/images/ico/license-key.png" class="btn guid" data-guid="{GUID}" alt="G">
            <!-- ENDIF -->
        </td>
    </tr>

    <!-- END -->

    <tr data-tt-id="1" class="droppable group">
        <td class="icons">
            <img src="/images/ico/plus_circle_frame.png" class="btn" alt="+"
                 style="margin-left:8px;margin-right:6px" onclick="addChild(1)">
            <img src="/images/ico/information_frame.png" class="tip" tip="#DragDropHelp" ali="?">
            <div id="DragDropHelp" style="display:none">{{DragDropHelp}}</div>
        </td>
        <td></td>
        <td></td>
    </tr>

    </tbody>

    <tfoot>
    <tr>
        <th colspan="3" style="padding-top:8px;padding-bottom:8px;text-align:left">
            <div id="drag-new-wrapper" style="display:none;margin-top:.5em;margin-bottom:1em">
                <span id="drag-new" class="draggable">
                    <img src="/images/ico/hand.png" class="channel-icon">
                    <span id="drag-text"></span>
                </span>
            </div>
            <select id="add-child" data-placeholder="--- {{SelectChannel}} ---">
                <!-- MACRO ChannelSelectOptions -->
            </select>
        </th>
    </tr>
    <tfoot>
</table>

<div id="legend" class="icons">
    <strong>{{Legend}}</strong>:
    <span><img src="/images/ico/lock.png">{{PrivateChannel}}</span>,
    <span><img src="/images/ico/plus_circle_frame.png">{{AddOneToManyChannels}}</span>,
    <span><img src="/images/ico/node_insert_next.png">{{AssignEntity}}</span>,
    <span><img src="/images/ico/node_delete_next.png">{{DeleteEntity}}</span>,
    <span><img src="/images/ico/node_delete.png">{{DeleteBranch}}</span>,
    <span><img src="/images/ico/navigation_180_frame.png">{{MoveEntityLeft}}</span>,
    <span><img src="/images/ico/navigation_090_frame.png">{{MoveEntityUp}}</span>,
    <span><img src="/images/ico/navigation_270_frame.png">{{MoveEntityDown}}</span>,
    <span><img src="/images/ico/document-invoice.png">{{ListHint}}</span>,
    <span><img src="/images/ico/node_design.png">{{EditEntity}}</span>,
    <span><img src="/images/ico/node_select_child.png">{{CloneEntity}}</span>,
    <span><img src="/images/ico/arrow-split.png">{{AliasEntity}}</span>,
    <span><img src="/images/ico/license-key.png">{{ShowGUID}}</span>
</div>

<!-- Dialogs ------------------------------------------------------------- -->

<div id="dialog-addchild" style="display:none" title="{{AddChild}}">
    <p>
    <form id="form-addchild" style="margin:.5em 0" action="/overview/addchild" method="post">
        <input type="hidden" id="parent" name="parent">
        <select id="child" name="child[]" style="width:100%;margin-bottom:0.5em" data-placeholder="--- {{SelectChannel}} ---">
            <!-- MACRO ChannelSelectOptions -->
        </select>
    </form>
    </p>
    <img id="addmorechild" src="/images/ico/plus_circle_frame.png" class="ico tipbtn" title="{{AddAnotherChild}}" alt="[+]">
</div>

<div id="dialog-move" style="display:none" title="{{MoveChannel}}">
    <form id="form-move" method="post">
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
