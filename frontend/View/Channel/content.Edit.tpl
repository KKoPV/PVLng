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

<p>
    {{ChannelType}}: <img src="{ICON}" alt="" />&nbsp;<strong>{TYPENAME}</strong>
</p>

<!-- IF {TYPEHELP} -->
<p>
    <strong><img src="/images/ico/exclamation-circle.png"
        style="width:16px;height:16px;margin-right:8px" width="16" height="16" alt="!"/></strong>
    <em>{TYPEHELP}</em>
</p>
<!-- ENDIF -->

<form action="/channel/edit" method="post">

<input type="hidden" name="c[id]" value="{ID}" />
<input type="hidden" name="c[type]" value="{TYPE}" />
<!-- BEGIN FIELDS --><!-- IF !{VISIBLE} AND {VALUE} != "" -->
<!-- Store also invisible fields, because they can have
     non-default values from model in add mode. -->
<input type="hidden" name="c[{FIELD}]" value="{VALUE}" />
<!-- ENDIF --><!-- END -->

<table id="dataTable" class="dataTable">
    <thead>
    <tr>
        <th style="width:20%">{{channel::Param}}</th>
        <th style="width:40%">{{channel::Value}}</th>
        <th style="width:40%">{{channel::Help}}</th>
    </tr>
    </thead>

    <tbody>
    <!-- BEGIN FIELDS -->

    <!-- IF {VISIBLE} -->
    <tr>
        <td>
            <label for="{FIELD}">{NAME}</label>
            <!-- IF {REQUIRED} -->
                <img style="width:16px;height:16px" width="16" height="16"
                    src="/images/required.gif" alt="*" />
            <!-- ENDIF -->
        </td>
        <td style="white-space:nowrap">
            <!-- IF {TYPE} == "numeric" -->
                <input type="number" id="{FIELD}" name="c[{FIELD}]" value="{VALUE}" size="10" step="0.000000000000001"
                       <!-- IF {REQUIRED} --> required="required"<!-- ENDIF -->
                       <!-- IF {READONLY} --> class="ro" readonly="readonly"<!-- ENDIF -->
                />
            <!-- ELSEIF {TYPE} == "integer" -->
                <input type="number" id="{FIELD}" name="c[{FIELD}]" value="{VALUE}" size="10" step="1"
                       <!-- IF {REQUIRED} --> required="required"<!-- ENDIF -->
                       <!-- IF {READONLY} --> class="ro" readonly="readonly"<!-- ENDIF -->
                />
            <!-- ELSEIF {TYPE} == "radio" -->
                <div class="fl">
                    <input type="radio" id="y{FIELD}" name="c[{FIELD}]" value="1"
                        class="iCheckLine" style="margin-right:.3em"
                        <!-- IF {READONLY} --> readonly="readonly"<!-- ENDIF -->
                        <!-- IF {VALUE} == 1 --> checked="checked"<!-- ENDIF --> />
                    <label for="y{FIELD}">{{Yes}}</label>
                </div>
                <div class="fl" style="margin-left:1em">
                    <input type="radio" id="n{FIELD}" name="c[{FIELD}]" value="0"
                        class="iCheckLine" style="margin-right:.3em"
                        <!-- IF {READONLY} --> readonly="readonly"<!-- ENDIF -->
                        <!-- IF {VALUE} == 0 --> checked="checked"<!-- ENDIF --> />
                    <label for="n{FIELD}">{{No}}</label>
                </div>
            <!-- ELSEIF {TYPE} == "textarea" -->
                <textarea id="{FIELD}" name="c[{FIELD}]" style="width:98%" rows="5"
                          <!-- IF {REQUIRED} --> required="required"<!-- ENDIF -->
                          <!-- IF {READONLY} --> class="ro" readonly="readonly"<!-- ENDIF -->
                >{VALUE}</textarea>
            <!-- ELSEIF {TYPE} == "textextra" -->
                <textarea id="{FIELD}" name="c[{FIELD}]" style="width:98%" rows="15"
                          <!-- IF {REQUIRED} --> required="required"<!-- ENDIF -->
                          <!-- IF {READONLY} --> class="ro" readonly="readonly"<!-- ENDIF -->
                >{VALUE}</textarea>
            <!-- ELSEIF {TYPE} == "textsmall" -->
                <input type="text" id="{FIELD}" name="c[{FIELD}]" value="{VALUE}" size="10"
                       <!-- IF {REQUIRED} --> required="required"<!-- ENDIF -->
                       <!-- IF {READONLY} --> class="ro" readonly="readonly"<!-- ENDIF --> />
            <!-- ELSE -->
                <input type="text" id="{FIELD}" name="c[{FIELD}]" value="{VALUE}" size="50"
                       <!-- IF {REQUIRED} --> required="required"<!-- ENDIF -->
                       <!-- IF {READONLY} --> class="ro" readonly="readonly"<!-- ENDIF --> />
            <!-- ENDIF -->
        </td>
        <td>
            <small>{HINT}</small>
        </td>
    </tr>
    <!-- ENDIF -->

    <!-- END -->

    <!-- IF !{ID} -->
    <!-- New channel, ask for auto add to hierarchy -->
    <tr>
        <td>
            {{Overview}}
        </td>
        <td>
            <select name="add2tree">
                <option value="0">--- {{Select}} ---</option>
                <option value="1">> {{TopLevel}}</option>
                <option disabled="disabled">> {{AsChild}}:</option>
                <!-- BEGIN ADDTREE -->
                <option value="{ID}" <!-- IF !{AVAILABLE} -->disabled="disabled"<!-- ENDIF -->>{INDENT}{NAME}</option>
                <!-- END -->
            </select>

        </td>
        <td>
            {{Channel2Overview}}
        </td>
    </tr>
    <!-- ENDIF -->

    </tbody>

    <tfoot>
    <tr>
        <th class="l" colspan="3">
            <img style="width:16px;height:16px" width="16" height="16"
                 src="/images/required.gif" alt="*" />
            <small>{{Required}}</small>
        </th>
    </tr>
    </tfoot>

</table>

<p><input type="submit" value="{{Save}}" /></p>

</form>

</div>

<div class="clear"></div>
