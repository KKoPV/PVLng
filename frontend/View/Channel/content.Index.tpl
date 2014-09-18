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
        <th class="icons">
            <img src="/images/ico/node_select_all.png" class="tip" title="Used # times in channel hierarchy" alt="#">
        </th>
        <th>
            <img src="/images/ico/drive.png" alt="?">
        </th>
        <th class="icons">
            <img src="/images/ico/information_frame.png" style="margin-left:10px" class="tip" tip="#IconLegend" alt="?">
        </th>
    </tr>
    </thead>

    <tbody>

    <!-- BEGIN ENTITIES -->

    <tr data-id="{ID}">
        <td>
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
        <td>{TREE}</td>
        <td class="icons">
            <!-- INCLUDE channeltype.inc.tpl -->
        </td>
        <td>
            <a href="/channel/edit/{ID}">
                <img src="/images/ico/node_design.png" alt="e">
            </a>
            <a href="/channel/add/{ID}">
                <img src="/images/ico/node_select_child.png" alt="c">
            </a>
            <!-- IF {TREE} -->
            <!-- Can't delete channels assigned in channel hierarchy -->
            <img src="data:image/gif;base64,R0lGODlhAQABAIAAAP///wAAACH5BAEAAAAALAAAAAABAAEAAAICRAEAOw" style="height:0px" alt="" />
            <!-- ELSE -->
            <img class="delete-channel" src="/images/ico/node_delete.png" alt="-">
            <!-- ENDIF -->
            <!-- IF {GUID} -->
            <img src="/images/ico/license-key.png" class="btn" onclick="$.alert('{GUID}', 'GUID'); return false" alt="G">
            <!-- ENDIF -->
        </td>
    </tr>

    <!-- END -->

    </tbody>

    <tfoot>
    <tr>
        <th></th>
        <th></th>
        <th></th>
        <th></th>
        <th></th>
        <th></th>
        <th class="icons" colspan="2">
            <img src="/images/ico/information_frame.png" class="tip" tip="#IconLegend" alt="?">
        </th>
    </tr>
    </tfoot>
</table>

<!-- Legend -->

<div id="IconLegend">
    <div class="icons legendtip">
        <img src="/images/ico/read-write.png">{{ReadWritableEntity}}<br />
        <img src="/images/ico/write.png">{{WritableEntity}}<br />
        <img src="/images/ico/read.png">{{ReadableEntity}}<br />
        <img src="/images/ico/node_design.png">{{EditEntity}}<br />
        <img src="/images/ico/node_select_child.png">{{CloneEntity}}<br />
        <img src="/images/ico/node_delete.png">{{DeleteEntityHint}}<br />
        <img src="/images/ico/license-key.png">{{ShowGUID}}
    </div>
</div>

<!-- Dialogs -->

<div id="dialog-confirm" style="display:none" title="{{DeleteEntity}}">
    <p>
        <span class="ui-icon ui-icon-alert" style="float: left; margin: 0 7px 20px 0;"></span>
        {{ConfirmDeleteEntity}}
    </p>
</div>
