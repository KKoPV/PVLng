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
        <th><i class="ico information-frame tip" tip="#IconLegend"></i></th>
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
            <a href="/tariff/{ID}" class="ico document-invoice"></a>
            <a href="/tariff/date/add/{ID}" class="ico node-insert-next""></a>
            <a href="/tariff/edit/{ID}" class="ico node-design"></a>
            <a href="/tariff/add/{ID}" class="ico node-select-child"></a>
            <form id="df-{ID}" action="/tariff/delete" method="post" class="delete-tariff">
                <input type="hidden" name="id" value="{ID}" />
                <input type="image" src="/images/ico/node_delete.png" alt="-" style="background-color:transparent">
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
        <td class="icons">
            <i class="ico pix"></i><i class="ico pix"></i>
            <a href="/tariff/date/edit/{_PARENT.ID}/{DATE}" class="ico node-design"></a>
            <a href="/tariff/date/add/{_PARENT.ID}/{DATE}" class="ico node-select-child"></a>
            <form id="df-{ID}-{DATE}" action="/tariff/date/delete" method="post" class="delete-date">
                <input type="hidden" name="id" value="{_PARENT.ID}" />
                <input type="hidden" name="date" value="{DATE}" />
                <input type="image" src="/images/ico/node_delete_next.png" alt="-" style="background-color:transparent">
            </form>
        </td>
    </tr>
    <!-- END -->

    <!-- END -->

</tbody>

</table>

<!-- Legend -->

<div id="IconLegend">
    <div class="icons legendtip">
        <i class="ico document-invoice"></i>{{Details}}<br />
        <i class="ico node-insert-next"></i>{{AddTariffDate}}<br />
        <i class="ico node-design"></i>{{EditTariffDate}}<br />
        <i class="ico node-select-child"></i>{{CloneTariffDate}}<br />
        <i class="ico node-delete"></i>{{DeleteTariff}}<br />
        <i class="ico node-delete-next"></i>{{DeleteTariffDate}}
    </div>
</div>

<!-- Dialogs -------------------------------------------------------------- -->

<div id="dialog-delete-tariff" title="{{Confirm}}" style="display:none">
    <p>{{RemoveTariffIfUsed}}</p>
    <p>{{AreYouSure}}</p>
</div>

<div id="dialog-delete-date" title="{{Confirm}}" style="display:none">
    <p>{{AreYouSure}}</p>
</div>
