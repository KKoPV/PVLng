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

<!-- Use this image as spacer for not available actions of channels -->
<!-- DEFINE MACRO SpacerImg -->
<img src="/images/pix.gif" class="imgbar wide" style="width:16px;height:16px" width="16" height="16" alt="" />
<!-- END DEFINE -->

<div class="grid_10">

<p>
    <a class="button" data-primary="ui-icon-triangle-1-s" href="#type">{{SelectEntityType}}</a>
    <a class="button" data-primary="ui-icon-triangle-1-s" href="#template">{{SelectEntityTemplate}}</a>
</p>

<h2><a name="type"></a>{{SelectEntityType}}</h2>

<form action="/channel/add" method="post">

<table id="typeTable" class="dataTable">
    <thead>
    <tr>
        <th style="width:1%"></th>
        <th>{{EntityType}}</th>
        <th style="white-space:nowrap">{{ExampleUnit}}</th>
        <th style="white-space:nowrap">{{Childs}}</th>
        <th style="width:1%"></th>
        <th>{{Description}}</th>
    </tr>
    </thead>
    <tbody>

    <!-- BEGIN ENTITYTYPES -->
    <tr>
        <td>
            <input type="radio" id="type-{ID}" name="type" value="{ID}" class="iCheck" />
        </td>
        <td style="white-space:nowrap;font-weight:bold">
            <label for="type-{ID}">
                <img style="vertical-align:middle;width:16px;height:16px;margin-right:8px"
                     src="{ICON}" width="16" height="16" alt="" />
                {NAME}
            </label>
        </td>
        <td>{UNIT}</td>
        <td class="c">
            <!-- IF {CHILDS} == 0 -->
                {{no}}
            <!-- ELSEIF {CHILDS} == -1 -->
                {{unlimited}}
            <!-- ELSE -->
                {CHILDS}
            <!-- ENDIF -->
        </td>
        <td style="white-space:nowrap">
            <!-- IF {WRITE} -->
            <img src="/images/ico/write.png" class="imgbar wide tip"
                 style="width:16px;height:16px" width="16p" height="16"
                 alt="w" title="{{WritableEntity}}" />
            <!-- ELSE --><!-- MACRO SpacerImg --><!-- ENDIF -->
            <!-- IF {READ} -->
            <img src="/images/ico/read.png" class="tip"
                 style="width:16px;height:16px" width="16p" height="16"
                 alt="r" title="{{ReadableEntity}}" />
            <!-- ENDIF -->
        </td>
        <td style="font-size:smaller">{DESCRIPTION}</td>
    </tr>
    <!-- END -->

    </tbody>

</table>

<p><input type="submit" value="{{proceed}} &raquo;" /></p>

</form>

<h2><a name="template"></a>{{SelectEntityTemplate}}</h2>

<p>{{CreateTreeWithoutReqest}}</p>

<form action="/channel/template" method="post">

<table id="tplTable" class="dataTable">
    <thead>
    <tr>
        <th style="width:1%"></th>
        <th>{{Name}}</th>
        <th>{{Description}}</th>
    </tr>
    </thead>
    <tbody>

    <!-- BEGIN TEMPLATES -->
    <tr>
        <td><input type="radio" id="{FILE}" name="template" value="{FILE}" class="iCheck" /></td>
        <td style="white-space:nowrap;font-weight:bold">
            <label for="{FILE}">
                <img style="vertical-align:middle;width:16px;height:16px;margin-right:8px"
                     src="{ICON}" width="16" height="16" alt="" />
                {NAME}
            </label>
        </td>
        <td style="font-size:smaller">{DESCRIPTION}</td>
    </tr>
    <!-- END -->

    </tbody>

    <tfoot>
    <tr>
        <th></th>
        <th class="l i" colspan="2">{{AdjustTemplateAfterwards}}</th>
    </tr>
    </tfoot>

</table>

<p>
    <select id="tree" name="tree">
        <option value="1">{{TopLevel}} &nbsp; {{or}}</option>
        <option value="0" disabled="disabled">{{AsChild}}</option>
            <!-- BEGIN ADDTREE -->
            <option value="{ID}" <!-- IF !{AVAILABLE} -->disabled="disabled"<!-- ENDIF -->>{INDENT}{NAME}</option>
            <!-- END -->
        </optgroup>
    </select>
    &nbsp;
    <input type="submit" value="{{Create}}" />
</p>

</form>

</div>

<div class="clear"></div>
