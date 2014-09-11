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

<!-- -------------------------------------------------------------------------
PAGE 1
-------------------------------------------------------------------------- -->
<div data-role="page" id="page-home" data-theme="a" data-view="{VIEW1ST}">

    <!-- Header -->
    <div data-role="header" data-id="header">
        <a id="btn-home" class="ui-btn-left ui-btn ui-btn-icon-notext ui-btn-corner-all"
           data-iconpos="notext" data-role="button" data-icon="home" title=" Home ">
            <span class="ui-btn-inner ui-btn-corner-all">
                <span class="ui-btn-text"> Home </span>
                <span data-form="ui-icon" class="ui-icon ui-icon-home ui-icon-shadow"></span>
            </span>
        </a>
        <h1 id="view"></h1>
        <a id="btn-chart-refresh" class="ui-btn-right ui-btn ui-btn-icon-notext ui-btn-corner-all"
           data-iconpos="notext" data-role="button" data-icon="refresh" title=" {{Refresh}} ">
            <span class="ui-btn-inner ui-btn-corner-all">
                <span class="ui-btn-text"> Navigation </span>
                <span data-form="ui-icon" class="ui-icon ui-icon-refresh ui-icon-shadow"></span>
            </span>
        </a>
    </div>

    <!-- Content -->
    <div data-role="content">
        <div id="chart"></div>

        <table id="table-cons" data-role="table" class="ui-responsive">
            <thead>
            <tr>
                <th>{{Channel}}</th>
                <th>{{Production}} / {{Consumption}}</th>
                <th>{{Cost}}</th>
            </tr>
            </thead>
            <tbody>
            </tbody>
        </table>

        <a href="#page-select" data-role="button">{{SelectView}}</a>
        <a id="btn-weather" href="#page-weather" data-role="button">{{WeatherForecast}}</a>
    </div>

    <!-- INCLUDE footer.inc.tpl -->

</div>

<!-- -------------------------------------------------------------------------
PAGE 2
-------------------------------------------------------------------------- -->
<div data-role="page" id="page-select" data-theme="a">

    <!-- Header -->
    <div data-role="header" data-id="header" data-position="fixed">
        <a href="#page-home" class="ui-btn-left ui-btn ui-btn-icon-notext ui-btn-corner-all"
           data-iconpos="notext" data-role="button" data-icon="home" title=" Home ">
            <span class="ui-btn-inner ui-btn-corner-all">
                <span class="ui-btn-text"> Home </span>
                <span data-form="ui-icon" class="ui-icon ui-icon-home ui-icon-shadow"></span>
            </span>
        </a>
        <h1>{{Selection}}</h1>
    </div>

    <!-- Content -->
    <div data-role="content" class="ui-title">
        <div data-role="controlgroup">
            <!-- BEGIN VIEWS -->
            <a href="#page-home" data-role="button" onclick="$('#page-home').data('view', '{NAME}')">
                {NAME}
            </a>
            <!-- END -->
        </div>
    </div>

    <!-- INCLUDE footer.inc.tpl -->

</div>

<!-- -------------------------------------------------------------------------
PAGE 3
-------------------------------------------------------------------------- -->
<div data-role="page" id="page-weather" data-theme="a">

    <!-- Header -->
    <div data-role="header" data-id="header" data-position="fixed">
        <a href="#page-home" class="ui-btn-left ui-btn ui-btn-icon-notext ui-btn-corner-all"
           data-iconpos="notext" data-role="button" data-icon="home" title=" Home ">
            <span class="ui-btn-inner ui-btn-corner-all">
                <span class="ui-btn-text"> Home </span>
                <span data-form="ui-icon" class="ui-icon ui-icon-home ui-icon-shadow"></span>
            </span>
        </a>
        <h1>{{WeatherForecast}}</h1>
        <a id="btn-weather-refresh" class="ui-btn-right ui-btn ui-btn-icon-notext ui-btn-corner-all"
           data-iconpos="notext" data-role="button" data-icon="refresh" title=" {{Refresh}} ">
            <span class="ui-btn-inner ui-btn-corner-all">
                <span class="ui-btn-text"> Navigation </span>
                <span data-form="ui-icon" class="ui-icon ui-icon-refresh ui-icon-shadow"></span>
            </span>
        </a>
    </div>

    <!-- Content -->
    <div data-role="content" style="text-align:center">
        <div id="weather-chart"></div>
        <div id="weather"></div>
        <!-- Hidden placeholder appended to #weather-chart during loading -->
        <div id="weather-wait">{{JustAMoment}}</div>
    </div>

    <!-- INCLUDE footer.inc.tpl -->

</div>
