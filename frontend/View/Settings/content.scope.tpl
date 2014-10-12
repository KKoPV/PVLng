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

    <div class="alpha grid_4">
        <label for="{VAR}">{DESCRIPTION}</label>
    </div>

    <div class="grid_6 omega">
        <!-- IF {TYPE} == "num" -->
            <input id="{VAR}" type="number" name="d[{VAR}]" value="{VALUE}" size="6" placeholder="0">
        <!-- ELSEIF {TYPE} == "option" -->
            <select name="d[{VAR}]">
                <!-- BEGIN DATA -->
                <option value="{VALUE}"<!-- IF {SELECTED} -->selected="selected"<!-- ENDIF -->>{TEXT}</option>
                <!-- END -->
            </select>
        <!-- ELSE -->
            <input id="{VAR}" type="text" name="d[{VAR}]" value="{VALUE}" size="60">
        <!-- ENDIF -->
    </div>

    <div class="clear"></div>
</div>

<!-- END -->
