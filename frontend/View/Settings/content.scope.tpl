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

<h3>{NAME}</h3>

<!-- BEGIN KEY -->

<div class="{CLASS}">
    <a name="{VAR}"></a>

    <div class="alpha grid_5">
        <!-- IF {VAR} == "core--Password" -->
            <label for="p1">{DESCRIPTION}</label>
            <br />
            <label for="p2">repeat password</label>
        <!-- ELSE -->
            <label for="{VAR}">{DESCRIPTION}</label>
        <!-- ENDIF -->
    </div>

    <div class="grid_5 omega">
        <!-- IF {VAR} == "core--Password" -->
            <input id="p1" type="password" name="d[p1]">
            <small style="margin-left:1em">Fill only to change it!</small>
            <br />
            <input id="p2" type="password" name="d[p2]" style="margin-top:.5em">
        <!-- ELSEIF {TYPE} == "num" -->
            <input id="{VAR}" type="number" name="d[{VAR}]" value="{VALUE}" size="6" placeholder="0">
        <!-- ELSEIF {TYPE} == "option" -->
            <select name="d[{VAR}]">
                <!-- BEGIN DATA -->
                <option value="{VALUE}"<!-- IF {SELECTED} -->selected="selected"<!-- ENDIF -->>{TEXT}</option>
                <!-- END -->
            </select>
        <!-- ELSE -->
            <input id="{VAR}" type="text" name="d[{VAR}]" value="{VALUE}" style="width:100%">
        <!-- ENDIF -->
    </div>

    <div class="clear"></div>
</div>

<!-- END -->
