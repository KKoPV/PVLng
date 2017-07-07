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

<!-- DEFINE ChannelSelectOptions -->
    <option></option>
    <!-- BEGIN CHANNELS -->
    <optgroup label="{TYPE}">
        <!-- BEGIN MEMBERS -->
        <option value="{GUID}">
            {NAME}
            <!-- IF {UNIT} --> [{UNIT}]<!-- ENDIF -->
            <!-- IF {DESCRIPTION} --> ({DESCRIPTION})<!-- ENDIF -->
        </option>
        <!-- END -->
    </optgroup>
    <!-- END -->
<!-- END DEFINE -->

<input id="fromdate" type="hidden">
<input id="todate" type="hidden">
<input id="periodcnt" type="hidden" value="1">
<input id="period" type="hidden" value="i">

<div id="nav" class="ui-widget-header ui-corner-all" style="padding:.3em;height:2.2em">

    <!-- INCLUDE dateselect.inc.tpl -->

    <div style="float:right">
        <button id="btn-refresh" class="tip" title="{{ChartRefreshHint}}">
            <i class="fa fa-refresh"></i>
        </button>
    </div>
</div>

<div class="clear"></div>

<div id="chart-wrapper">
    <div id="chart">
        <div id="top-select" style="display:none">{{NoChannelsSelectedYet}}</div>
    </div>
</div>

<div class="clear"></div>

<div class="spaced">
    <div class="alpha grid_2" style="padding:.4em">
        <label for="x-axis-channel">{{xAxisChannel}}</label>
    </div>
    <div>
        <select id="x-axis-channel" data-placeholder="--- {{SelectChannel}} ---">
            <!-- MACRO ChannelSelectOptions -->
        </select>

        <button id="btn-exchange" class="tipbtn" style="margin-left:2em" title="{{SwitchChannels}}">
            <i class="fa fa-exchange"></i>
        </button>
    </div>
</div>

<div class="clear"></div>

<div class="spaced">
    <div class="alpha grid_2" style="padding:.4em">
        <label for="y-axis-channel">{{yAxisChannel}}</label>
    </div>
    <div>
        <select id="y-axis-channel" data-placeholder="--- {{SelectChannel}} ---">
            <!-- MACRO ChannelSelectOptions -->
        </select>
    </div>
</div>

<div class="clear"></div>

<hr>

<div class="spaced">
    <div class="alpha grid_4">
        <select id="load-delete-view" style="width:100%" data-placeholder="--- {{SelectChart}} ---"></select>
    </div>
    <div class="fl">
        <button id="btn-load" class="tip" style="margin-left:1em" title="{{Load}}">
            <i class="fa fa-folder-open-o"></i>
        </button>
        <button id="btn-delete" class="tip" style="margin-left:1em" title="{{Delete}}">
            <i class="fa fa-trash-o"></i>
        </button>
    </div>
</div>

<div class="clear"></div>

<div class="spaced">
    <div class="alpha grid_4">
        <input id="saveview" type="text" class="fl" value="{VIEW}" style="width:97%">
    </div>
    <button id="btn-save" class="tip"  style="margin-left:1em"title="{{Save}}">
        <i class="fa fa-floppy-o"></i>
    </button>
</div>

<div class="clear"></div>
