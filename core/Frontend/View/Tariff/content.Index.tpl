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
    </tr>
</thead>

<tbody>
    <!-- BEGIN TARIFF -->
    <tr>
        <td class="b" style="font-size:120%">
            <a href="/tariff/{ID}" class="tip" title="{{Details}}">{NAME}</a>
        </td>
        <td>
            <!-- IF {COMMENT} --><small>{COMMENT}</small><!-- ENDIF -->
        </td>
        <td class="icons">
            <a href="/tariff/date/add/{ID}" class="fa fa-fw fa-plus btn"></a>
            <a href="/tariff/edit/{ID}" class="fa fa-fw fa-pencil btn"></a>
            <a href="/tariff/add/{ID}" class="fa fa-fw fa-clone btn"></a>
            <form id="df-{ID}" action="/tariff/delete" method="post" class="delete-tariff">
                <input type="hidden" name="id" value="{ID}" />
                <button class="icon"><i class="fa fa-fw fa-trash"></button>
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
            <i class="fa fa-fw btn"></i>
            <a href="/tariff/date/edit/{_PARENT.ID}/{DATE}" class="fa fa-fw fa-pencil btn"></a>
            <a href="/tariff/date/add/{_PARENT.ID}/{DATE}" class="fa fa-fw fa-clone btn"></a>
            <form id="df-{ID}-{DATE}" action="/tariff/date/delete" method="post" class="delete-date">
                <input type="hidden" name="id" value="{_PARENT.ID}" />
                <input type="hidden" name="date" value="{DATE}" />
                <button class="icon"><i class="fa fa-fw fa-trash"></button>
            </form>
        </td>
    </tr>
    <!-- END -->

    <!-- END -->
</tbody>

</table>

<!-- Legend -->

<div class="icon-legend">
    <i class="fa fa-plus"></i>{{AddTariffDate}} &nbsp;
    <i class="fa fa-pencil"></i>{{EditTariff}} / {{EditTariffDate}} &nbsp;
    <i class="fa fa-clone"></i>{{CloneTariffDate}} &nbsp;
    <i class="fa fa-trash"></i>{{DeleteTariff}} / {{DeleteTariffDate}}
</div>

<!-- Dialogs -------------------------------------------------------------- -->

<div id="dialog-delete-tariff" title="{{Confirm}}" style="display:none">
    <p>{{RemoveTariffIfUsed}}</p>
    <p>{{AreYouSure}}</p>
</div>

<div id="dialog-delete-date" title="{{Confirm}}" style="display:none">
    <p>{{AreYouSure}}</p>
</div>
