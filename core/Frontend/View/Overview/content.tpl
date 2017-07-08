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
        <option value="{raw:ID}">
            {NAME}
            <!-- IF {UNIT} --> [{UNIT}]<!-- ENDIF -->
            <!-- IF {DESCRIPTION} --> ({DESCRIPTION})<!-- ENDIF -->
        </option>
        <!-- END -->
    </optgroup>
    <!-- END -->
<!-- END DEFINE -->

<p><a href="/channels/add" class="button tip" title="Alt-N">{{CreateChannel}}</a></p>

<table id="tree" class="dataTable treeTable">
    <thead>
    <tr>
        <th style="text-align:left !important">
            <i id="treetoggle" class="toggle off fl tipbtn" tip="#treetoggletip"></i>
            <div id="treetoggletip" style="display:none">{{CollapseAll}}</div>
            <div class="c">{{ChannelHierarchy}}</div>
        </th>
        <th></th>
    </tr>
    </thead>

    <tbody>

    <!-- BEGIN DATA -->

    <tr data-tt-id="{raw:ID}" class="droppable <!-- IF {CHILDS} -->group<!-- ELSE -->channel<!-- ENDIF -->"
        <!-- IF {PARENT} -->data-tt-parent-id="{raw:PARENT}" <!-- ENDIF -->>
        <td>
            <a name="_{raw:ID}"></a>
            <span class="icons draggable" data-id="{raw:ID}">
                <img src="/images/pix.gif" data-src="{ICON}" class="def channel-icon tip" title="{TYPE}" alt="({TYPE})">
                <strong<!-- IF {ALIAS_OF} --> class="alias"<!-- ENDIF -->>{NAME}</strong>
                <!-- IF {UNIT} --> [{UNIT}]<!-- ENDIF -->
                <!-- IF {DESCRIPTION} --><small> ({DESCRIPTION})</small><!-- ENDIF -->
                <!-- IF !{PUBLIC} -->
                    <i class="fa fa-lock" style="margin-left:.5em"></i>
                <!-- ENDIF -->
            </span>
        </td>

        <td class="icons">
            <!-- IF {CHILDS} -->
            <i class="fa fa-fw fa-plus btn" onclick="addChild({raw:ID}); return false"></i>
            <!-- ELSE --><i class="fa fa-fw btn"></i><!-- ENDIF -->

            <i class="fa fa-fw fa-minus btn"></i>

            <!-- IF {LEVEL} > 1 -->
            <form id="moveup_{raw:ID}" action="/overview/moveup" method="post">
                <input type="hidden" name="id" value="{raw:ID}">
                <input type="hidden" name="returnto" value="/overview#_{raw:ID}">
            </form>
            <i class="fa fa-fw fa-arrow-left btn" onclick="$('#moveup_{raw:ID}').submit()"></i>
            <!-- ELSE --><i class="fa fa-fw btn"></i><!-- ENDIF -->

            <!-- IF {UPPER} != 0 -->
            <i class="fa fa-fw fa-arrow-up btn" onclick="return moveChild({raw:ID}, 'moveleft')"></i>
            <!-- ELSE --><i class="fa fa-fw btn"></i><!-- ENDIF -->

            <!-- IF {LOWER} != 0 -->
            <i class="fa fa-fw fa-arrow-down btn" onclick="return moveChild({raw:ID}, 'moveright')"></i>
            <!-- ELSE --><i class="fa fa-fw btn"></i><!-- ENDIF -->

            <!-- IF {TYPE_ID} != 0 -->
            <a href="/channels/edit/{ENTITY}?returnto=/overview%23_{raw:ID}" class="fa fa-fw fa-pencil btn"></a>
            <!-- ELSE -->
            <a href="/channels/edit/{ENTITY_OF}?returnto=/overview%23_{raw:ID}" class="fa fa-fw fa-pencil btn"></a>
            <!-- ENDIF -->

            <!-- IF {TYPE_ID} != 0 -->
            <a href="/channels/add/{ENTITY}?returnto=/overview%23_{raw:ID}" class="fa fa-fw fa-clone btn"></a>
            <!-- ELSE --><i class="fa fa-fw btn"></i><!-- ENDIF -->

            <!-- IF {CHILDS} AND {GUID} AND {TYPE_ID} != "30" AND !{ALIAS} -->
            <i class="fa fa-fw fa-code-fork btn"></i>
            <!-- ELSE --><i class="fa fa-fw btn"></i><!-- ENDIF -->

            <!-- IF {READ} -->
                <a href="/list/{GUID}" class="fa fa-fw fa-file-text btn"></a>
            <!-- ELSE --><i class="fa fa-fw btn"></i><!-- ENDIF -->

            <!-- IF {READ} OR {WRITE} -->
                <i class="fa fa-fw fa-key fa-rotate-90 btn guid" data-guid="{GUID}"></i>
            <!-- ENDIF -->
        </td>
    </tr>

    <!-- END -->

    <tr data-tt-id="1" class="droppable group tip" tip="#DragDropHelp">
        <td class="icons">
            <i class="fa fa-fw fa-plus btn tip" onclick="addChild(1)" title="{{AddOneToManyChannels}}"></i>
            <div id="DragDropHelp" style="display:none">{{DragDropHelp}}</div>
        </td>
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
    </tr>
    <tfoot>
</table>

<!-- Legend -->

<div class="icon-legend">
    <i class="fa fa-lock"></i>{{PrivateChannel}} &nbsp;
    <i class="fa fa-plus"></i>{{AddOneToManyChannels}} &nbsp;
    <i class="fa fa-minus"></i>{{DeleteEntity}} / {{DeleteBranch}} &nbsp;

    <i class="fa fa-arrow-left"></i>{{MoveEntityLeft}} &nbsp;
    <i class="fa fa-arrow-up"></i>{{MoveEntityUp}} &nbsp;
    <i class="fa fa-arrow-down"></i>{{MoveEntityDown}} &nbsp;

    <i class="fa fa-pencil"></i>{{EditEntity}} &nbsp;
    <i class="fa fa-clone"></i>{{CloneEntity}} &nbsp;
    <i class="fa fa-code-fork"></i>{{AliasEntity}} &nbsp;

    <i class="fa fa-file-text"></i>{{ListHint}} &nbsp;
    <i class="fa fa-key fa-rotate-90"></i>{{ShowGUID}}
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
        <input type="number" step="1" style="width:3em" class="numbersOnly" name="count" value="1">
        <label for="countmax" style="margin-left:.5em">{{Positions}}</label>
    </p>
    <p>
        <div style="float:left;padding-top:4px;width:35px">
            <input type="radio" class="iCheck" id="movecountmax" name="countmax" value="1">
        </div>
        <label for="movecountmax">{{MoveChannelStartEnd}}</label>
    </p>
    </form>
</div>
