<!--
/**
 *
 *
 * @author      Knut Kohl <github@knutkohl.de>
 * @copyright   2012-2013 Knut Kohl
 * @license     GNU General Public License http://www.gnu.org/licenses/gpl.txt
 * @version     1.0.0
 */
-->

<div id="info-tabs" class="ui-tabs">

    <ul>
        <li><a href="#tabs-1">{{SystemInformation}}</a></li>
        <li><a href="#tabs-2">{{Channels}}</a></li>
        <li><a href="#tabs-3">{{Database}}</a></li>
        <li><a href="#tabs-4">{{Cache}}</a></li>
    </ul>

    <div id="tabs-1">

        <table id="table-info">
            <thead>
            <tr>
                <th></th>
                <th></th>
            </tr>
            </thead>

            <tbody>
            <tr>
                <td style="vertical-align:top">{{APIURL}}</td>
                <td>
                    <code class="b">
                        http://{SERVERNAME}/api/latest/
                    </code>
                    <br /><br />
                    {{SeeAPIReference}}
                </td>
            </tr>
            <tr>
                <td>{{LatestAPIVersion}}</td>
                <td>
                    <code id="latest" class="b"></code>
                </td>
            </tr>
            <tr>
                <td style="vertical-align:top">{{YourAPIcode}}</td>
                <td>
                    <form method="post" style="float:right;margin-left:1em;margin-bottom:1em">
                        <input type="hidden" name="regenerate" value="1" />
                        <input id="regenerate" type="submit" value="{{Regenerate}}" />
                    </form>
                    <code class="b">X-PVLng-Key: {APIKEY}</code>
                    <br /><br />
                    {{DontForgetUpdateAPIKey}}
                </td>
            </tr>
            </tbody>
        </table>

    </div>

    <div id="tabs-2">

        <div id="stats-chart">
            <p class="c" style="padding:1.5em">
                <i class="fa fa-circle-o-notch fa-spin fa-2x"></i>
            </p>
        </div>

        <table id="table-stats">
            <thead>
            <tr>
                <th class="l">{{ChannelName}}</th>
                <th class="l">{{Description}}</th>
                <th class="r">{{Readings}}</th>
                <th class="r">{{LastReading}}</th>
                <th class="l">{{Unit}}</th>
            </tr>
            </thead>

            <tbody>
            <!-- BEGIN STATS -->
            <tr class="tip" tip="#tip-{GUID}">
                <td class="icons">
                    <img src="/images/pix.gif" data-src="{ICON}" class="def channel-icon tip" title="{TYPE}" alt="">
                    {NAME}
                </td>
                <td>{DESCRIPTION}</td>
                <td id="r-{GUID}" class="r">0</td>
                <td id="d-{GUID}" class="r"></td>
                <td>{UNIT}</td>
            </tr>
            <!-- END -->
            </tbody>

            <tfoot>
            <tr>
                <th class="l" colspan="2"></th>
                <th id="sumReadings" class="r">0</th>
                <th class="l" colspan="2"></th>
            </tr>
            </tfoot>

        </table>

    </div>

    <div id="tabs-3">

        <div id="db-chart" style="height:250px"></div>

        <table id="table-db">
            <thead>
            <tr>
                <th class="l">{{DatabaseTable}}</th>
                <th class="l">{{Comment}}</th>
                <th class="r">{{Rows}}</th>
                <th class="r">{{Size}} [MB]</th>
            </tr>
            </thead>

            <tbody>
            <!-- BEGIN TABLESIZE -->
            <tr>
                <td>{TABLE_NAME}</td>
                <td>{TABLE_COMMENT}</td>
                <td class="r">{TABLE_ROWS}</td>
                <td class="r">{SIZE_MB}</td>
            </tr>
            <!-- END -->
            </tbody>

            <tfoot>
            <tr>
                <th colspan="4"></th>
            </tr>
            </tfoot>

        </table>

    </div>

    <div id="tabs-4">

        <div id="cache-chart" style="height:250px" style="display:none"></div>

        <table id="table-cache">
            <thead>
            <tr>
                <th class="l">{{Key}}</th>
                <th class="l">{{Value}}</th>
            </tr>
            </thead>

            <tbody>
            <!-- BEGIN CACHEINFO -->
            <tr>
                <td>{strtolower:_LOOP}</td>
                <td>{raw:CACHEINFO}</td>
            </tr>
            <!-- END -->
            </tbody>
        </table>

    </div>

</div>

<!-- BEGIN STATS -->
<div id="tip-{GUID}" style="display:none">
    <table>
    <tr>
        <td>{{Serial}}</td><td style="padding-right:1em">:</td><td>{SERIAL}</td>
    </tr>
    <tr>
        <td>{{Channel}}</td><td>:</td><td>{CHANNEL}</td>
    </tr>
    </table>
</div>
<!-- END -->
