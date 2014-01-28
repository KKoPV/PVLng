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

<div class="grid_10">

<h3>{{SystemInformation}}</h3>

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
                http://{SERVERNAME}/api/r3/\{action\}/\{GUID\}
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

<h3>{{Statistics}}</h3>

<table id="table-stats">
    <thead>
    <tr>
        <th class="l">{{ChannelName}}</th>
        <th class="l">{{Description}}</th>
        <th class="l">{{Serial}}</th>
        <th class="l">{{Channel}}</th>
        <th class="r">{{Readings}}</th>
        <th class="r">{{LastReading}}</th>
        <th class="l">{{Unit}}</th>
    </tr>
    </thead>

    <tbody>
    <!-- BEGIN STATS -->
    <tr>
        <td>
            <img src="{ICON}" class="tip" style="width:16px;height:16px;margin-right:8px"
                 width="16" height="16" alt="" title="{TYPE}" />
            {NAME}
        </td>
        <td>{DESCRIPTION}</td>
        <td>{SERIAL}</td>
        <td>{CHANNEL}</td>
        <td class="r">{numf:READINGS}</td>
        <td class="r last-reading" data-guid="{GUID}">
            <img src="/images/spinner.gif" style="width:16px;height:16px" width="16" height="16" alt="..." />
        </td>
        <td>{UNIT}</td>
    </tr>
    <!-- END -->
    </tbody>

    <tfoot>
    <tr>
        <th class="l" colspan="3">{CHANNELCOUNT}&nbsp;{{Channels}}</th>
        <th class="l">{{Total}}</th>
        <th class="r">{numf:READINGS}</th>
        <th colspan="2"></th>
    </tr>
    </tfoot>

</table>

</div>

<div class="clear"></div>
