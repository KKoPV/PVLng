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

<h3>{NAME}</h3>

<!-- IF {COMMENT} --><p><small>{COMMENT}</small></p><!-- ENDIF -->

<table class="datatable">
<thead>
<tr>
    <th class="l">{{StartDate}}</th>
    <th class="l">{{StartTime}}</th>
    <th class="l">{{WeekDays}}</th>
    <th class="l">{{FixCostPerDay}} [<small>{CURRENCYISO}</small>]</th>
    <th class="l">{{Tariff}} [<small>{CURRENCYISO}</small>]</th>
    <th class="l">{{Comment}}</th>
    <th></th>
</tr>
</thead>

<tbody>

<!-- BEGIN TARIFF -->

<tr>
    <td>{DATE}</td>
    <td>{TIME}</td>
    <td>{DAYS}</td>
    <td>{COST}</td>
    <td>{TARIFF}</td>
    <td>{COMMENT}</td>
    <td>
        <a href="/tariff/date/edit/{ID}/{DATE}?returnto=/tariff/{ID}" class="tipbtn imgbar wide" title="{{EditTariffDate}}">
            <img src="/images/ico/node_design.png"
                 class="imgbar wide" alt="e" width="16" height="16" />
        </a>
    </td>
</tr>

<!-- END -->

</tbody>
</table>

<h3>{{TariffThisWeek}}</h3>

<table class="datatable">
<thead>
<tr>
    <th class="l">{{Date}}</th>
    <th class="l">{{StartTime}}</th>
    <th class="l">{{EndTime}}</th>
    <th class="l">{{Tariff}} [<small>{CURRENCYISO}</small>]</th>
</tr>
</thead>

<tbody>

<!-- BEGIN TARIFFWEEK -->

<tr>
    <td>{_LOOP}</td>
    <td></td>
    <td></td>
    <td></td>
</tr>

<!-- BEGIN DATA -->

<tr>
    <td></td>
    <td>{START}</td>
    <td>{END}</td>
    <td>{TARIFF}</td>
</tr>

<!-- END -->
<!-- END -->

</tbody>
</table>
