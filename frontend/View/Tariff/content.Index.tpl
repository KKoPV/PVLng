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

<p><a href="/tariff/add" class="button">{{CreateTariff}}</a></p>

<table id="table-tariff" class="datatable treeTable">

<thead>
    <tr>
        <th class="l">
            {{Tariff}}<br />
            <ul style="margin-top:0;margin-bottom:0"><li>{{StartDate}}</li></ul>
        </th>
        <th class="l">{{Comment}}</th>
        <th></th>
        <th></th>
    </tr>
</thead>

<tbody>

    <!-- BEGIN TARIFF -->
    <tr>
        <td class="b" style="font-size:120%">
            {NAME}
        </td>
        <td>
            <!-- IF {COMMENT} --><small>{COMMENT}</small><!-- ENDIF -->
        </td>
        <td class="icons">
            <a href="/tariff/{ID}" class="tipbtn imgbar wide" title="{{Details}}">
                <img src="/images/ico/document-invoice.png" alt="(details)" width="16" height="16" />
            </a>
            <a href="/tariff/date/add/{ID}" class="tipbtn imgbar wide" title="{{AddTariffDate}}">
                <img src="/images/ico/node_insert_next.png"
                     class="imgbar wide" alt="c" width="16" alt="+" height="16" />
            </a>
        </td>
        <td class="icons">
            <a href="/tariff/edit/{ID}" class="tipbtn imgbar wide" title="{{EditTariff}}">
                <img src="/images/ico/node_design.png"
                     class="imgbar wide" alt="e" width="16" height="16" />
            </a>
            <a href="/tariff/add/{ID}" class="tipbtn imgbar wide" title="{{CloneTariff}}">
                <img src="/images/ico/node_select_child.png"
                     class="imgbar wide" alt="c" width="16" height="16" />
            </a>
            <form id="df-{ID}" action="/tariff/delete" method="post" class="delete-tariff">
                <input type="hidden" name="id" value="{ID}" />
                <input type="image" src="/images/ico/node_delete.png" alt="-"
                       style="background-color:transparent"
                       class="imgbar wide tipbtn nb" title="{{DeleteTariff}}" />
            </form>
        </td>
    </tr>

    <!-- Rows for each start date -->
    <!-- BEGIN DATE -->
    <tr>
        <td class="b">
            <ul style="margin-top:0;margin-bottom:0"><li>{DATE}</li></ul>
        </td>
        <td></td>
        <td></td>
        <td class="icons">
            <a href="/tariff/date/edit/{_PARENT.ID}/{DATE}" class="tipbtn imgbar wide" title="{{EditTariffDate}}">
                <img src="/images/ico/node_design.png"
                     class="imgbar wide" alt="e" width="16" height="16" />
            </a>
            <a href="/tariff/date/add/{_PARENT.ID}/{DATE}" class="tipbtn imgbar wide" title="{{CloneTariffDate}}">
                <img src="/images/ico/node_select_child.png"
                     class="imgbar wide" alt="c" width="16" height="16" />
            </a>
            <form id="df-{ID}-{DATE}" action="/tariff/date/delete" method="post" class="delete-date">
                <input type="hidden" name="id" value="{_PARENT.ID}" />
                <input type="hidden" name="date" value="{DATE}" />
                <input type="image" src="/images/ico/node_delete_next.png" alt="-"
                       style="background-color:transparent"
                       class="imgbar wide tipbtn nb" title="{{DeleteTariffDate}}" />
            </form>
        </td>
    </tr>
    <!-- END -->

    <!-- END -->

</tbody>

</table>

<!-- Dialogs -------------------------------------------------------------- -->

<div id="dialog-delete-tariff" title="{{Confirm}}" style="display:none">
    <p>{{RemoveTariffIfUsed}}</p>
    <p>{{AreYouSure}}</p>
</div>

<div id="dialog-delete-date" title="{{Confirm}}" style="display:none">
    <p>{{AreYouSure}}</p>
</div>
