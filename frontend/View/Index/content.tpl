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

<!-- EVENT index_content_before_html -->

<input type="hidden" id="fromdate" />
<input type="hidden" id="todate" />

<!-- EVENT index_navigation_before_html -->

<div id="nav" class="ui-widget-header ui-corner-all no-print"
     style="<!-- IF {EMBEDDED} < "2" -->padding:.3em;height:2.2em<!-- ELSE -->display:none<!-- ENDIF -->">

    <div class="alpha grid_6">
        <!-- EVENT index_navigation_left_before_html -->
        <!-- INCLUDE dateselect.inc.tpl -->
        <!-- EVENT index_navigation_left_after_html -->
    </div>

    <div class="grid_1 c" style="margin:0 2%">
        <!-- EVENT index_navigation_middle_before_html -->
        <img id="modified" src="/images/pix.gif" data-src="/images/modified.png" class="def tip"
             style="display:none;margin-top:6px;width:24px;height:24px" alt="[ unsaved changes ]"
             title="{{UnsavedChanges}}" />&nbsp;
        <!-- EVENT index_navigation_middle_after_html -->
    </div>

    <div id="preset-wrapper" class="grid_3 omega r">
        <!-- EVENT index_navigation_right_before_html -->
        <!-- INCLUDE preset.inc.tpl -->
        <!-- EVENT index_navigation_right_after_html -->
    </div>

</div>

<div class="clear"></div>

<!-- EVENT index_chart_before_html -->

<div id="chart-wrapper" style="margin-bottom:1em">
    <div class="arrow-wrapper left no-print" onclick="pvlng.changeDate(-1)">
        <i class="fa fa-chevron-left"></i>
    </div>
    <div class="arrow-wrapper right no-print" onclick="pvlng.changeDate(1)">
        <i class="fa fa-chevron-right"></i>
    </div>
    <div id="chart">
        <div id="top-select" style="display:none">{{NoChannelsSelectedYet}}</div>
    </div>
</div>

<div class="clear"></div>

<!-- EVENT index_chart_after_html -->

<!-- IF !{EMBEDDED} -->
<div id="zoom-hint" class="no-print">{{ClickDragShiftPan}}</div>
<!-- ENDIF -->

<div <!-- IF {EMBEDDED} == "2" -->style="display:none<!-- ENDIF -->">

<!-- IF {USER} -->
    <!-- INCLUDE content.private.inc.tpl -->
<!-- ELSE -->
    <!-- INCLUDE content.public.inc.tpl -->
<!-- ENDIF -->

<!-- EVENT index_content_after_html -->

<!-- INCLUDE dialog.chart.tpl -->

</div>
