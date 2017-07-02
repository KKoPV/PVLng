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

<!-- HOOK channel_content_before.html -->

<p><a href="/channels/add" class="button tip" title="Alt-N">{{CreateChannel}}</a></p>

<table id="entities" class="dataTable">

    <thead>
    <tr>
        <th class="l">{{Channel}}</th>
        <th class="l">{{Description}}</th>
        <th class="l">{{Unit}}</th>
        <th class="l">{{Type}}</th>
        <th class="l">{{Serial}}</th>
        <th><i class="fa fa-list-ol" title="Used # times in channel hierarchy"></i></th>
        <th><i class="fa fa-database"></i></th>
        <th></th>
    </tr>
    </thead>

    <tbody>

    <!-- BEGIN CHANNELS -->

    <tr data-id="{ID}">
        <td>
            <a href="/channels/edit/{ID}" class="tip" title="{{EditEntity}}">
                <img src="/images/pix.gif" data-src="{ICON}" class="def channel-icon" alt="({TYPE})">
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
            <a href="/channels/edit/{ID}" class="fa fa-fw fa-pencil btn"></a>
            <a href="/channels/add/{ID}" class="fa fa-fw fa-clone btn"></a>
            <!-- IF {READ} -->
                <a href="/list/{ID}" class="fa fa-fw fa-file-text btn"></a>
            <!-- ELSE -->
                <i class="fa fa-fw"></i>
            <!-- ENDIF -->
            <!-- IF {TREE} -->
                <!-- Can't delete channels assigned in channel hierarchy -->
                <i class="fa fa-fw"></i>
            <!-- ELSE -->
                <i class="node-delete fa fa-fw fa-trash"></i>
            <!-- ENDIF -->
            <!-- IF {GUID} -->
                <i class="guid fa fa-fw fa-key fa-rotate-90" data-guid="{GUID}"></i>
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
        <th colspan="3"></th>
    </tr>
    </tfoot>
</table>

<!-- HOOK channel_content_after.html -->

<!-- Legend -->

<div class="icons legendtip">
    <i class="fa fa-arrows-alt"></i>{{ReadWritableEntity}} &nbsp;
    <i class="fa fa-download"></i>{{WritableEntity}} &nbsp;
    <i class="fa fa-upload"></i>{{ReadableEntity}} &nbsp;

    <i class="fa fa-pencil"></i>{{EditEntity}} &nbsp;
    <i class="fa fa-clone"></i>{{CloneEntity}} &nbsp;
    <i class="fa fa-trash"></i>{{DeleteEntityHint}} &nbsp;

    <i class="fa fa-file-text"></i>{{ListHint}} &nbsp;
    <i class="fa fa-key fa-rotate-90"></i>{{ShowGUID}}
</div>

<!-- Dialogs -->

<div id="dialog-confirm" style="display:none" title="{{DeleteEntity}}">
    <p>
        <span class="ui-icon ui-icon-alert" style="float: left; margin: 0 7px 20px 0;"></span>
        {{ConfirmDeleteEntity}}
    </p>
</div>
