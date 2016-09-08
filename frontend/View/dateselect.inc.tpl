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

<button class="icon" onclick="$('.d-select').toggle()">
    <i class="fa fa-random"></i>
</button>

<span id="dp1" class="d-select" style="display:inline">

    <span class="ui-buttonset">
      <input id="timerange-day" type="radio" name="timerange" value="d" checked="checked">
      <label for="timerange-day">{{Day}}</label>
      <input id="timerange-week" type="radio" name="timerange" value="w">
      <label for="timerange-week">{{Week}}</label>
      <input id="timerange-month" type="radio" name="timerange" value="m">
      <label for="timerange-month">{{Month}}</label>
      <input id="timerange-year" type="radio" name="timerange" value="y">
      <label for="timerange-year">{{Year}}</label>
    </span>

    <span style="padding:0 1em">
        <button class="icon" onclick="pvlng.changeDate(-1)">
            <i class="fa fa-chevron-left fa-fw"></i>
        </button>
        <!-- Remove all text input styling -->
        <input id="timerange" class="c" type="text" size="17"
               style="border:0; background:transparent" readonly="readonly">
        <input id="timerangedate" style="display:none">
        <button id="dpCalendar" class="icon" data-input="#timerange">
            <i class="fa fa-calendar fa-fw"></i>
        </button>
        <button class="icon" onclick="pvlng.changeDate(1)">
            <i class="fa fa-chevron-right fa-fw"></i>
        </button>
    </span>
</span>

<span id="dp2" class="d-select" style="display:none">
    <button class="icon tipbtn" onclick="pvlng.changeDates(-1)" title="{{PrevDay}} (Alt+P)">
        <i class="fa fa-chevron-left fa-fw"></i>
    </button>
    <input class="c" type="text" id="from">
    &mdash;
    <input class="c" type="text" id="to">
    <button class="icon tipbtn" onclick="pvlng.changeDates(1)" title="{{NextDay}} (Alt+N)">
        <i class="fa fa-chevron-right fa-fw"></i>
    </button>
</span>

<button id="btn-reset" class="tipbtn" title="{{ChartTodayHint}}">
    <i class="fa fa-clock-o"></i>
</button>
