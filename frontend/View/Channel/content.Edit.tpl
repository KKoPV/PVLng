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

<h3>
    <img src="{ICON}" class="channel-icon-large" alt="">
    <strong>{TYPENAME}</strong>
</h3>

<p>
    <!-- IF !{TYPEHELP} -->{TYPEDESC}<!-- ELSE -->{TYPEHELP}<!-- ENDIF -->
</p>

<form action="/channel/edit" method="post">

<input type="hidden" name="c[id]" value="{ID}" />
<input type="hidden" name="c[type]" value="{TYPE}" />
<!-- Invisible fields can have non-default values from model in add mode -->
<!-- BEGIN FIELDS -->
<!-- IF !{VISIBLE} AND {VALUE} != "" -->
<input type="hidden" name="c[{FIELD}]" value="{VALUE}" />
<!-- ENDIF -->
<!-- END -->

<table id="dataTable" class="dataTable">
    <thead>
    <tr>
        <th style="width:15%">{{channel::Param}}</th>
        <th>{{channel::Value}}</th>
        <th style="width:50%">{{channel::Help}}</th>
    </tr>
    </thead>

    <tbody>
    <!-- BEGIN FIELDS -->

    <!-- IF {VISIBLE} -->
    <tr <!-- IF {ERROR} -->style="background-color:#FFE0E0;border-top:solid 1px white;border-bottom:solid 1px white"<!-- ENDIF -->>
        <td style="vertical-align:top;padding-top:.75em;white-space:nowrap">
            <label for="{FIELD}">{NAME}</label>
            <!-- IF {REQUIRED} -->
                <img class="ico tip" src="/images/required.gif" title="{{required}}" alt="*">
            <!-- ENDIF -->
        </td>
        <td style="vertical-align:top;padding-top:.5em;padding-bottom:.5em">
            <div style="white-space:nowrap">
            <!-- IF {TYPE} == "numeric" -->
                <input type="text" id="{FIELD}" name="c[{FIELD}]" value="{VALUE}" size="10"
                       placeholder="{PLACEHOLDER}"
                       <!-- IF {REQUIRED} --> required="required"<!-- ENDIF -->
                       <!-- IF {READONLY} --> class="ro" readonly="readonly"<!-- ENDIF -->
                />
            <!-- ELSEIF {TYPE} == "integer" -->
                <input type="text" id="{FIELD}" name="c[{FIELD}]" value="{VALUE}" size="10"
                       placeholder="{PLACEHOLDER}"
                       <!-- IF {REQUIRED} --> required="required"<!-- ENDIF -->
                       <!-- IF {READONLY} --> class="ro" readonly="readonly"<!-- ENDIF -->
                />
            <!-- ELSEIF {TYPE} == "bool-radio" -->
                <span class="toolbar">
                <!-- BEGIN OPTIONS -->
                <input id="radio{_LOOP_ID}{_PARENT.FIELD}" type="radio" name="c[{_PARENT.FIELD}]" value="{VALUE}"
                       <!-- IF {CHECKED} -->checked="checked"<!-- ENDIF -->>
                <label for="radio{_LOOP_ID}{_PARENT.FIELD}">{TEXT}</label>
                <!-- END -->
                </span>
            <!-- ELSEIF {TYPE} == "bool" -->
                <select id="{FIELD}" name="c[{FIELD}]" <!-- IF {READONLY} --> class="ro" readonly="readonly"<!-- ENDIF -->
                        data-placeholder="<!-- IF {PLACEHOLDER} -->{PLACEHOLDER}<!-- ELSE -->--- {{Select}} ---<!-- ENDIF -->"
                    >
                    <!-- BEGIN OPTIONS -->
                    <option value="{VALUE}" <!-- IF {CHECKED} -->selected="selected"<!-- ENDIF -->>{TEXT}</option>
                    <!-- END -->
                </select>
            <!-- ELSEIF {TYPE} == "select" -->
                <select id="{FIELD}" name="c[{FIELD}]" <!-- IF {READONLY} --> class="ro" readonly="readonly"<!-- ENDIF -->
                        data-placeholder="<!-- IF {PLACEHOLDER} -->{PLACEHOLDER}<!-- ELSE -->--- {{Select}} ---<!-- ENDIF -->"
                    >
                    <!-- BEGIN OPTIONS -->
                    <option value="{VALUE}" <!-- IF {SELECTED} -->selected="selected"<!-- ENDIF -->>{TEXT}</option>
                    <!-- END -->
                </select>
            <!-- ELSEIF {TYPE} == "textarea" -->
                <textarea id="{FIELD}" name="c[{FIELD}]" class="code" placeholder="{PLACEHOLDER}"
                          <!-- IF {REQUIRED} --> required="required"<!-- ENDIF -->
                          <!-- IF {READONLY} --> class="ro" readonly="readonly"<!-- ENDIF -->
                >{VALUE}</textarea>
            <!-- ELSEIF {TYPE} == "textsmall" -->
                <input type="text" id="{FIELD}" name="c[{FIELD}]" value="{VALUE}" size="10"
                       placeholder="{PLACEHOLDER}"
                       <!-- IF {CODE} --> class="code"<!-- ENDIF -->
                       <!-- IF {REQUIRED} --> required="required"<!-- ENDIF -->
                       <!-- IF {READONLY} --> class="ro" readonly="readonly"<!-- ENDIF --> />
            <!-- ELSEIF {TYPE} == "icon" -->
                <select id="icon-select" name="c[{FIELD}]">
                    <!-- BEGIN ICONS -->
                    <option id="{ICON}" value="{ICON}" <!-- IF {ACTUAL} -->selected="selected"<!-- ENDIF -->>{NAME}</option>
                    <!-- END -->
                </select>
            <!-- ELSE --><!-- Normal text field -->
                <input type="text" id="{FIELD}" name="c[{FIELD}]" value="{VALUE}" size="50"
                       placeholder="{PLACEHOLDER}"
                       <!-- IF {CODE} --> class="code"<!-- ENDIF -->
                       <!-- IF {REQUIRED} --> required="required"<!-- ENDIF -->
                       <!-- IF {READONLY} --> class="ro" readonly="readonly"<!-- ENDIF --> />
            <!-- ENDIF -->
            </div>
            <span style="color:red" class="xs">
                <!-- BEGIN ERROR -->{ERROR}<br class="clear" /><!-- END -->
            </span>
        </td>
        <td class="hint">
            {HINT}
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
            <div class="fl" style="margin:.5em 1em 0 0">
                <input type="checkbox" id="add2tree" name="add2tree" class="iCheck" checked onchange="$('#tree').prop('disabled',!this.checked)" />
            </div>
            <select id="tree" name="tree">
                <option value="1">{{TopLevel}} &nbsp; {{or}}</option>
                <option value="0" disabled="disabled">{{AsChildOf}}</option>
                    <!-- BEGIN ADDTREE -->
                    <option value="{ID}" <!-- IF !{AVAILABLE} -->disabled="disabled"<!-- ENDIF -->>{INDENT}{NAME}</option>
                    <!-- END -->
                </optgroup>
            </select>

        </td>
        <td class="hint">
            {{Channel2Overview}}
        </td>
    </tr>
    <!-- ENDIF -->

    <!-- IF {REPLACE} -->
    <!-- Edit channel, switch type -->
    <tr>
        <td>
            <label for="type-new">{{ChangeType}}</label>
        </td>
        <td>
            <select id="type-new" name="c[type-new]">
                <!-- BEGIN REPLACE -->
                <option value="{_LOOP}" <!-- IF {_LOOP} == {__TYPE} -->selected="selected"<!-- ENDIF -->>{REPLACE}</option>
                <!-- END -->
            </select>

        </td>
        <td class="hint">
            {{ChangeTypeHint}}
        </td>
    </tr>
    <!-- ENDIF -->

    </tbody>

    <tfoot>
    <tr>
        <th class="l" colspan="3">
            <img class="ico" src="/images/required.gif" alt="*" />
            <small>{{required}}</small>
        </th>
    </tr>
    </tfoot>

</table>

<br />

<input type="submit" value="{{Save}}" />

</form>
