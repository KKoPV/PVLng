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

<h2>{{SelectEntityType}}</h2>

<form action="/channel/add" method="post">

<table id="typeTable" class="dataTable">
    <thead>
    <tr>
        <th style="width:1%">
            <input type="radio" name="type" value="" class="iCheck" checked="checked" />
        </th>
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

<h2>{{SelectEntityTemplate}}</h2>

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
        <td><input type="radio" id="{FILE}" name="type" value="{FILE}" class="iCheck" /></td>
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

<p><input type="submit" value="{{proceed}} &raquo;" /></p>

</form>

</div>

<div class="clear"></div>
