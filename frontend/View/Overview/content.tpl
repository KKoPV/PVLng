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
            <i id="treetoggle" class="toggle off fl tipbtn" tip="#treetoggletip"></i>
            <div id="treetoggletip" style="display:none">{{CollapseAll}}</div>
            <div class="c">{{ChannelHierarchy}}</div>
        </th>
        <th></th>
        <th><i class="ico information-frame tip" tip="#IconLegend"></i></th>
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
                    <i class="ico lock" style="margin-left:.5em"></i>
                <!-- ENDIF -->
            </span>
        </td>

        <td class="icons">
            <!-- IF {CHILDS} -->
            <i class="ico node-insert-next btn" onclick="addChild({ID}); return false"></i>
            <!-- ELSE --><i class="ico pix"></i><!-- ENDIF -->
            <i class="ico node-delete<!-- IF !{HASCHILDS} -->-next<!-- ENDIF --> btn"></i>
            <!-- IF {LEVEL} > 2 -->
            <form action="/overview/moveup" method="post">
                <input type="hidden" name="id" value="{ID}">
                <input type="image" src="/images/ico/navigation_180_frame.png"
                       style="background-color:transparent" alt="h">
            </form>
            <!-- ELSE --><i class="ico pix"></i><!-- ENDIF -->

            <!-- IF {LEVEL} != 1 AND {UPPER} != 0 -->
            <i class="ico navigation-090-frame btn" onclick="return moveChild({ID}, 'moveleft')"></i>
            <!-- ELSE --><i class="ico pix"></i><!-- ENDIF -->

            <!-- IF {LEVEL} != 1 AND {LOWER} != 0 -->
            <i class="ico navigation-270-frame btn" onclick="return moveChild({ID}, 'moveright')"></a>
            <!-- ELSE --><i class="ico pix"></i><!-- ENDIF -->
        </td>

        <td class="icons">
            <!-- IF {READ} -->
            <a href="/list/{GUID}" class="ico document-invoice"></a>
            <!-- ELSE --><i class="ico pix"></i><!-- ENDIF -->

            <!-- IF {TYPE_ID} != "0" -->
            <a href="/channel/edit/{ENTITY}?returnto=/overview" class="ico node-design"></a>
            <!-- ELSE --><i class="ico pix"></i><!-- ENDIF -->

            <!-- IF {TYPE_ID} != "0" -->
            <a href="/channel/add/{ENTITY}?returnto=/overview" class="ico node-select-child"></a>
            <!-- ELSE --><i class="ico pix"></i><!-- ENDIF -->

            <!-- IF {CHILDS} AND {GUID} AND {TYPE_ID} != "30" AND !{ALIAS} -->
            <i class="ico arrow-split btn create-alias"></i>
            <!-- ELSE --><i class="ico pix"></i><!-- ENDIF -->

            <!-- IF {GUID} -->
            <i class="ico license-key guid btn" data-guid="{GUID}"></i>
            <!-- ENDIF -->
        </td>
    </tr>

    <!-- END -->

    <tr data-tt-id="1" class="droppable group">
        <td class="icons">
            <i class="ico plus-circle-frame btn" onclick="addChild(1)"></i>
            <i class="ico information-frame tip" tip="#DragDropHelp"></i>
            <div id="DragDropHelp" style="display:none">{{DragDropHelp}}</div>
        </td>
        <td></td>
        <td></td>
    </tr>

    </tbody>

    <tfoot>
    <tr>
        <th colspan="2" style="padding-top:8px;padding-bottom:8px;text-align:left">
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
        <th><i class="ico information-frame tip" tip="#IconLegend"></i></th>
    </tr>
    <tfoot>
</table>

<!-- Legend -->

<div id="IconLegend">
    <div class="icons legendtip">
        <i class="ico lock"></i>{{PrivateChannel}}<br />
        <i class="ico plus-circle-frame"></i>{{AddOneToManyChannels}}<br />
        <i class="ico node-insert-next"></i>{{AssignEntity}}<br />
        <i class="ico node-delete-next"></i>{{DeleteEntity}}<br />
        <i class="ico node-delete"></i>{{DeleteBranch}}<br />
        <i class="ico navigation-180-frame"></i>{{MoveEntityLeft}}<br />
        <i class="ico navigation-090-frame"></i>{{MoveEntityUp}}<br />
        <i class="ico navigation-270-frame"></i>{{MoveEntityDown}}<br />
        <i class="ico document-invoice"></i>{{ListHint}}<br />
        <i class="ico node-design"></i>{{EditEntity}}<br />
        <i class="ico node-select-child"></i>{{CloneEntity}}<br />
        <i class="ico arrow-split"></i>{{AliasEntity}}<br />
        <i class="ico license-key"></i>{{ShowGUID}}
    </div>
</div>

<!-- Dialogs -->

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
