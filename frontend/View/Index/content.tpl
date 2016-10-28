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

<input type="hidden" id="fromdate" />
<input type="hidden" id="todate" />

<div id="nav" class="ui-widget-header ui-corner-all"
     style="<!-- IF {EMBEDDED} < "2" -->padding:.3em;height:2.2em<!-- ELSE -->display:none<!-- ENDIF -->">

    <div class="alpha grid_6">
        <!-- INCLUDE dateselect.inc.tpl -->
    </div>
    <div class="grid_1 c" style="margin:0 2%">
        <img id="modified" src="/images/pix.gif" data-src="/images/modified.png" class="def tip"
             style="display:none;margin-top:6px;width:24px;height:24px" alt="[ unsaved changes ]"
             title="{{UnsavedChanges}}" />
        &nbsp;
    </div>
    <div id="preset-wrapper" class="grid_3 omega r">
        <!-- INCLUDE preset.inc.tpl -->
    </div>
</div>

<div class="clear"></div>

<div id="chart-wrapper" style="margin-bottom:1em">
    <div class="arrow left" onclick="pvlng.changeDate(-1)">
        <i class="fa fa-chevron-left"></i>
    </div>
    <div class="arrow right" onclick="pvlng.changeDate(1)">
        <i class="fa fa-chevron-right"></i>
    </div>
    <div id="chart">
        <div id="top-select" style="display:none">{{NoChannelsSelectedYet}}</div>
    </div>
</div>

<div class="clear"></div>

<!-- IF !{EMBEDDED} -->
<div id="zoom-hint">{{ClickDragShiftPan}}</div>
<!-- ENDIF -->

<div <!-- IF {EMBEDDED} == "2" -->style="display:none<!-- ENDIF -->">

<!-- IF {USER} -->
    <!-- INCLUDE content.private.inc.tpl -->
<!-- ELSE -->
    <!-- INCLUDE content.public.inc.tpl -->
<!-- ENDIF -->

<!-- INCLUDE dialog.chart.tpl -->

</div>
