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

<!-- DEFINE MACRO PIX -->
src="data:image/gif;base64,R0lGODlhAQABAIAAAP///wAAACH5BAEAAAAALAAAAAABAAEAAAICRAEAOw"
style="width:16px;height:16px" width="16" height="16" alt=""
<!-- END DEFINE -->

<div class="grid_10">

<p>
    <a class="button tip" href="/channel/add" title="Alt+N">{{CreateChannel}}</a>
</p>

<table id="entities" class="dataTable">

    <thead>
    <tr>
        <th></th>
        <th class="l">{{Channel}}</th>
        <th class="l">{{Description}}</th>
        <th class="l">{{Unit}}</th>
        <th class="l">{{Type}}</th>
        <th></th>
        <th class="l">{{Serial}}</th>
        <th></th>
    </tr>
    </thead>

    <tbody>

    <!-- BEGIN ENTITIES -->

    <tr>
        <td class="c" style="padding:0.4em 0">
            <a href="/channel/edit/{ID}" class="tip" title="{{EditEntity}}">
                <img src="{ICON}" alt="{TYPE}" title="" width="16" height="16" />
            </a>
        </td>
        <td class="b">
            {NAME}
            <!-- IF !{PUBLIC} -->
                <img src="/images/ico/lock.png" class="tip"
                     style="margin-left:8px;width:16px;height:16px"
                     width="16" height="16" title="{{PrivateChannel}}"
                     alt="[private]"/>
            <!-- ENDIF -->
        </td>
        <td>{DESCRIPTION}</td>
        <td>{UNIT}</td>
        <td>{TYPE}</td>
        <td class="imgbar">
            <!-- IF {WRITE} -->
            <img src="/images/ico/write.png" class="imgbar tip" alt="w"
                 style="width:16px;height:16px" width="16" height="16"
                 title="{{WritableEntity}}" />
            <!-- ELSE -->
            <img <!-- MACRO PIX --> class="imgbar" />
            <!-- ENDIF -->
            <!-- IF {READ} -->
            <img src="/images/ico/read.png" class="imgbar tip" alt="r"
                 style="width:16px;height:16px" width="16" height="16"
                 title="{{ReadableEntity}}" />
            <!-- ENDIF -->
        </td>
        <td>{SERIAL}</td>
        <td style="white-space:nowrap">
            <a href="/channel/edit/{ID}" class="tip" title="{{EditEntity}}">
                <img src="/images/ico/node_design.png"
                     class="imgbar wide" alt="e" width="16" height="16" />
            </a>
            <!-- IF {TYPE_ID} != 0 -->
            <a href="/channel/add/{ID}" class="tip" title="{{CloneEntity}}">
                <img src="/images/ico/node_select_child.png"
                     class="imgbar wide" alt="c" width="16" height="16" />
            </a>
            <!-- ELSE -->
            <img <!-- MACRO PIX --> class="imgbar wide" />
            <!-- ENDIF -->
            <form id="df{ID}" action="/channel/delete" method="post" class="delete-form">
                <input type="hidden" name="id" value="{ID}" />
                <input type="image" src="/images/ico/node_delete.png" alt="-"
                       style="background-color:transparent"
                       class="imgbar wide tip nb" title="{{DeleteEntity}}" />
            </form>
        </td>

    </tr>

    <!-- END -->

    </tbody>
</table>

<p>
    <a class="button tip" href="/channel/add" title="Alt+N">{{CreateChannel}}</a>
</p>

<div id="dialog-confirm" style="display:none" title="{{DeleteEntity}}">
    <p>
        <span class="ui-icon ui-icon-alert" style="float: left; margin: 0 7px 20px 0;"></span>
        {{ConfirmDeleteEntity}}
    </p>
</div>

</div>

<div class="clear"></div>
