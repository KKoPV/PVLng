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
     style="padding:4px;height:34px;<!-- IF {EMBEDDED} == "2" -->;display:none<!-- ENDIF -->">

    <div class="alpha grid_4">
        <!-- INCLUDE dateselect.inc.tpl -->
    </div>
    <div class="grid_2 c" style="margin:0 2%">
        <img id="modified" src="/images/pix.gif" data-src="/images/modified.png" class="def tip"
             style="display:none;margin-top:6px;width:24px;height:24px" alt="[ unsaved changes ]"
             title="{{UnsavedChanges}}" />
        &nbsp;
    </div>
    <div class="r">
        <!-- INCLUDE preset.inc.tpl -->
    </div>
</div>

<div class="clear"></div>

<div id="chart">
    <div id="top-select" style="display:none">{{NoChannelsSelectedYet}}</div>
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
