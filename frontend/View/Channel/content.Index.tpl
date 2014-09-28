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
        <th><i class="ico node-select-all tip" title="Used # times in channel hierarchy"></i></th>
        <th><i class="ico drive"></i></th>
        <th><i class="ico information-frame tip" tip="#IconLegend"></i></th>
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
            <!-- IF !{PUBLIC} --><i class="ico lock"></i><!-- ENDIF -->
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
            <a href="/channel/edit/{ID}" class="ico node-design"></a>
            <a href="/channel/add/{ID}" class="ico node-select-child"></a>
            <!-- IF {TREE} -->
                <!-- Can't delete channels assigned in channel hierarchy -->
                <i class="ico pix"></i>
            <!-- ELSE -->
                <i class="ico node-delete"></i>
            <!-- ENDIF -->
            <!-- IF {GUID} -->
                <i class="ico license-key btn" onclick="$.alert('{GUID}', 'GUID'); return false"></i>
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
            <i class="ico information-frame tip" tip="#IconLegend"></i>
        </th>
    </tr>
    </tfoot>
</table>

<!-- Legend -->

<div id="IconLegend">
    <div class="icons legendtip">
        <i class="ico drive-globe"></i>{{ReadWritableEntity}}<br />
        <i class="ico drive--pencil"></i>{{WritableEntity}}<br />
        <i class="ico drive--arrow"></i>{{ReadableEntity}}<br />
        <i class="ico node-design"></i>{{EditEntity}}<br />
        <i class="ico node-select-child"></i>{{CloneEntity}}<br />
        <i class="ico node-delete"></i>{{DeleteEntityHint}}<br />
        <i class="ico license-key"></i>{{ShowGUID}}
    </div>
</div>

<!-- Dialogs -->

<div id="dialog-confirm" style="display:none" title="{{DeleteEntity}}">
    <p>
        <span class="ui-icon ui-icon-alert" style="float: left; margin: 0 7px 20px 0;"></span>
        {{ConfirmDeleteEntity}}
    </p>
</div>
