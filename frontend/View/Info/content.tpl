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
                        http://{SERVERNAME}/api/r4/
                    </code>
                    <br /><br />
                    {{SeeAPIReference}}
                </td>
            </tr>
            <tr>
                <td style="vertical-align:top">{{YourAPIcode}}</td>
                <td>
                    <form method="post" style="float:right">
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

        <div id="stats-chart"></div>

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
                    <img src="{ICON}" class="tip channel-icon" alt="" title="{TYPE}" />
                    {NAME}
                </td>
                <td>{DESCRIPTION}</td>
                <td class="r">
                    <span style="display:none">{raw:READINGS}</span>
                    {READINGS}
                </td>
                <td class="r last-reading" data-guid="{GUID}">?</td>
                <td>{UNIT}</td>
            </tr>
            <!-- END -->
            </tbody>

            <tfoot>
            <tr>
                <th class="l" colspan="2"></th>
                <th class="r"></th>
                <th class="l" colspan="2"></th>
            </tr>
            </tfoot>

        </table>

    </div>

    <div id="tabs-3">

        <div id="db-chart" style="height:250px"></div>

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
                <td>{CACHEINFO}</td>
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
