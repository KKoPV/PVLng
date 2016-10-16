<!--
/**
 *
 *
 * @author      Knut Kohl <github@knutkohl.de>
 * @copyright   2012-2013 Knut Kohl
 * @license     MIT License (MIT) http://opensource.org/licenses/MIT
 * @version     1.0.0
 */
-->

<button class="icon" onclick="$('.p-select').toggle();" tip="{{UseOwnConsolidation}}">
    <i class="fa fa-random tipbtn"></i>
</button>

<span class="p-select">
    {PRESETSELECT}
</span>

<span class="p-select" style="display:none">
    <input id="periodcnt" class="numbersOnly r" style="margin-right:.5em" type="text" value="1" size="2" />
    {PERIODSELECT}
</span>

<span style="margin-left:.5em">
    <button id="btn-refresh" class="tip" title="{{ChartRefreshHint}}">
        <i class="fa fa-refresh"></i>
    </button>
</span>
