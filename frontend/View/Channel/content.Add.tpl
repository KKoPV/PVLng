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

<table id="dataTable" class="dataTable">
    <thead>
    <tr>
        <th style="width:1%">
            <input type="radio" name="type" value="" class="iCheck" checked="checked" />
        </th>
        <th>{{EntityType}}</th>
        <th style="white-space:nowrap">{{ExampleUnit}}</th>
        <th style="white-space:nowrap">{{Childs}}</th>
        <th style="width:1%"></th>
        <th>{{DESCRIPTION}}</th>
    </tr>
    </thead>
    <tbody>

    <!-- BEGIN ENTITYTYPES -->
    <tr>
        <td>
            <input type="radio" name="type" value="{ID}" class="iCheck" />
        </td>
        <td nowrap>
            <img style="vertical-align:middle;width:16px;height:16px;margin-right:8px"
                 src="{ICON}" width="16" height="16" alt="" />
            <strong>{NAME}</strong>
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
            <!-- IF {CHILDS} -->
            <img src="/images/ico/node_select_child.png" class="imgbar tip"
                 style="width:16px;height:16px" width="16p" height="16"
                 alt="c" title="{{MustHaveChilds}}" />
            <!-- ELSE --><!-- MACRO SpacerImg --><!-- ENDIF -->
            <!-- IF {WRITE} -->
            <img src="/images/ico/write.png" class="imgbar tip"
                 style="width:16px;height:16px" width="16p" height="16"
                 alt="w" title="{{WritableEntity}}" />
            <!-- ELSE --><!-- MACRO SpacerImg --><!-- ENDIF -->
            <!-- IF {READ} -->
            <img src="/images/ico/read.png" class="tip"
                 style="width:16px;height:16px" width="16p" height="16"
                 alt="r" title="{{ReadableEntity}}" />
            <!-- ENDIF -->
        </td>
        <td><small>{DESCRIPTION}</small></td>
    </tr>
    <!-- END -->

    </tbody>
</table>

<p><input type="submit" value="{{proceed}} &raquo;" /></p>

</form>

</div>

<div class="clear"></div>
