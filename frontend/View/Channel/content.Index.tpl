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

<p><a href="/channel/add" class="button tip" title="Alt-N">{{CreateChannel}}</a></p>

<table id="entities" class="dataTable">

    <thead>
    <tr>
        <th class="l">{{Channel}}</th>
        <th class="l">{{Description}}</th>
        <th class="l">{{Unit}}</th>
        <th class="l">{{Type}}</th>
        <th class="l">{{Serial}}</th>
        <th></th>
        <th></th>
    </tr>
    </thead>

    <tbody>

    <!-- BEGIN ENTITIES -->

    <tr>
        <td class="icons b">
            <a href="/channel/edit/{ID}" class="tip" title="{{EditEntity}}">
                <img src="{ICON}" class="channel-icon" title="" alt="({TYPE})">
            </a>
            {NAME}
            <!-- IF !{PUBLIC} -->
            <img src="/images/ico/lock.png" alt="[private]">
            <!-- ENDIF -->
        </td>
        <td>{DESCRIPTION}</td>
        <td>{UNIT}</td>
        <td>{TYPE}</td>
        <td>{SERIAL}</td>
        <td class="icons">
            <!-- INCLUDE channeltype.inc.tpl -->
        </td>
        <td class="icons">
            <a href="/channel/edit/{ID}">
                <img src="/images/ico/node_design.png" alt="e">
            </a>
            <a href="/channel/add/{ID}">
                <img src="/images/ico/node_select_child.png" alt="c">
            </a>
            <form id="df{ID}" action="/channel/delete" method="post" class="delete-form">
                <input type="hidden" name="id" value="{ID}">
                <input type="image" src="/images/ico/node_delete.png"
                       style="background-color:transparent" alt="-">
            </form>
            <!-- IF {GUID} -->
            <img src="/images/ico/license-key.png" class="btn" onclick="alert('{GUID}', 'GUID'); return false" alt="G">
            <!-- ENDIF -->
        </td>

    </tr>

    <!-- END -->

    </tbody>
</table>

<div id="legend" class="icons">
    <strong>{{Legend}}</strong>:
    <span><img src="/images/ico/read-write.png">{{ReadWritableEntity}}</span>,
    <span><img src="/images/ico/write.png">{{WritableEntity}}</span>,
    <span><img src="/images/ico/read.png">{{ReadableEntity}}</span>,
    <span><img src="/images/ico/node_design.png">{{EditEntity}}</span>,
    <span><img src="/images/ico/node_select_child.png">{{CloneEntity}}</span>,
    <span><img src="/images/ico/node_delete.png">{{DeleteEntity}}</span>,
    <span><img src="/images/ico/license-key.png">{{ShowGUID}}</span>
</div>

<!-- Dialogs -->

<div id="dialog-confirm" style="display:none" title="{{DeleteEntity}}">
    <p>
        <span class="ui-icon ui-icon-alert" style="float: left; margin: 0 7px 20px 0;"></span>
        {{ConfirmDeleteEntity}}
    </p>
</div>
