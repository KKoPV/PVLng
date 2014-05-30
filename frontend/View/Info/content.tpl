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

<h3>{{SystemInformation}}</h3>

<table id="table-info" class="display">
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

<h3>{{Statistics}}</h3>

<table id="table-stats" class="display">
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
