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

<!-- IF {GROUPTYPE} -->
<h3>
    <img src="{GROUPICON}" class="channel-icon-large" alt="">
    {GROUPTYPE}
</h3>
<!-- ENDIF -->

<form method="post">

<input type="hidden" name="template" value="{TEMPLATE}">

<table id="channels" class="dataTable">

    <thead>
    <tr>
        <th style="width:40px !important"></th>
        <th style="width:70px !important"></th>
        <th class="l">{{Name}}</th>
        <th class="l">{{Channel::description}}</th>
        <th class="l">{{Channel::resolution}}</th>
        <th class="l">{{Channel::unit}}</th>
        <th><img src="/images/ico/lock.png" class="tip" title="{{Private}}"></th>
        <th></th>
    </tr>
    </thead>

    <tbody>

    <!-- BEGIN CHANNELS -->

    <tr>
        <td class="tip" title="{{Create}}">
            <input type="checkbox" name="a[{_LOOP}]" class="iCheck tip" checked="checked"
                   <!-- IF {_LOOP} == "0" --> disabled="disabled"<!-- ENDIF --> >
        </td>
        <td style="white-space:nowrap;font-weight:bold">
            <input type="hidden" name="p[{_LOOP}][icon]" value="{ICON}">
            <!-- IF {__GROUPTYPE} -->
            <!-- IF {_LOOP} == "0" -->
            <br />
            <img src="{ICON}" class="ico tip" title="{_TYPE}"><br />
            &nbsp;<tt>|</tt>
            <!-- ELSE -->
                &nbsp;<tt>|</tt><br />
                <!-- IF !{_LOOP_LAST} -->
                    &nbsp;<tt>|&mdash;</tt>&nbsp; <img src="{ICON}" class="ico tip" title="{_TYPE}"><br />
                    &nbsp;<tt>|</tt>
                <!-- ELSE -->
                    &nbsp;<tt>'&mdash;</tt>&nbsp; <img src="{ICON}" class="ico tip" title="{_TYPE}"><br />
                    &nbsp;
                <!-- ENDIF -->
            <!-- ENDIF -->
            <!-- ELSE -->
            <img src="{ICON}" class="ico tip" title="{_TYPE}">
            <!-- ENDIF -->
        </td>
        <td>
            <input type="text" name="p[{_LOOP}][name]" value="{NAME}" size="25">
        </td>
        <td>
            <input type="text" name="p[{_LOOP}][description]" value="{DESCRIPTION}" size="35">
        </td>
        <td>
            <!-- IF {NUMERIC} -->
            <input type="text" name="p[{_LOOP}][resolution]" value="{RESOLUTION}" size="6" placeholder="0{__TSEP}000{__DSEP}00">
            <!-- ENDIF -->
        </td>
        <td>
            <!-- IF {NUMERIC} -->
            <input type="text" name="p[{_LOOP}][unit]" value="{UNIT}" size="6">
            <!-- ENDIF -->
        </td>
        <td>
            <input type="checkbox" name="p[{_LOOP}][public]" class="iCheck" value="0"
                   <!-- IF !{PUBLIC} -->checked="checked"<!-- ENDIF -->>
        </td>
        <td class="tip" title="More attributes">
            <span style="margin-left:110px;display:none">

                <div class="attr">
                    <label for="p{_LOOP}serial">{{Channel::serial}}</label>
                    <input id="p{_LOOP}serial" type="text" name="p[{_LOOP}][serial]" value="{SERIAL}" size="40">
                </div>
                <!-- IF {_LOOP} != "0" AND {NUMERIC} -->
                <div class="attr">
                    <label for="p{_LOOP}valid_from">{{Channel::valid_from}}</label>
                    <input id="p{_LOOP}valid_from" type="text" name="p[{_LOOP}][valid_from]" value="{VALID_FROM}" size="6" placeholder="0{__TSEP}000{__DSEP}00">
                </div>
                <div class="attr">
                    <label for="p{_LOOP}decimals">{{Channel::decimals}}</label>
                    <input id="p{_LOOP}decimals" type="text" name="p[{_LOOP}][decimals]" value="{DECIMALS}" size="1" placeholder="0">
                </div>
                <!-- ENDIF -->

                <div class="clear"></div>

                <div class="attr">
                    <label for="p{_LOOP}channel">{{Channel::channel}}</label>
                    <input id="p{_LOOP}channel" type="text" name="p[{_LOOP}][channel]" value="{CHANNEL}" size="40">
                </div>
                <!-- IF {_LOOP} != "0" AND {NUMERIC} -->
                <div class="attr">
                    <label for="p{_LOOP}valid_to">{{Channel::valid_to}}</label>
                    <input id="p{_LOOP}valid_to" type="text" name="p[{_LOOP}][valid_to]" value="{VALID_TO}" size="6" placeholder="0{__TSEP}000{__DSEP}00">
                </div>
                <!-- ENDIF -->

                <div class="clear"></div>

                <div class="attr">
                    <label for="p{_LOOP}comment">{{Channel::comment}}</label>
                    <textarea id="p{_LOOP}comment" name="p[{_LOOP}][comment]" cols="41">{COMMENT}</textarea>
                </div>
                <!-- IF {_LOOP} != "0" AND {METER} -->
                <div class="attr">
                    <label for="p{_LOOP}adjust">{{Channel::adjust}}</label>
                    <input id="p{_LOOP}adjust" type="checkbox" class="iCheck" name="p[{_LOOP}][adjust]" value="1" <!-- IF {ADJUST} == 1 -->checked<!-- ENDIF -->>
                </div>
                <!-- ENDIF -->

            </span>
        </td>
    </tr>

    <!-- END -->

    </tbody>
</table>

<p>
    <!-- IF {GROUPTYPE} -->
    {{Overview}}: &nbsp;
    <select name="tree">
        <option value="1">{{TopLevel}} &nbsp; {{or}}</option>
        <option value="0" disabled="disabled">{{AsChild}}</option>
        <!-- BEGIN ADDTREE -->
            <option value="{ID}" <!-- IF !{AVAILABLE} -->disabled="disabled"<!-- ENDIF -->>
                {INDENT}{NAME}
            </option>
        <!-- END -->
    </select>

    &nbsp;
    <!-- ENDIF -->

    <input type="submit" value="{{Create}}" />
</p>

</form>
