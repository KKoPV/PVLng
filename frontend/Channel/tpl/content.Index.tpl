<!--
/**
 *
 *
 * @author      Knut Kohl <github@knutkohl.de>
 * @copyright   2012-2013 Knut Kohl
 * @license     GNU General Public License http://www.gnu.org/licenses/gpl.txt
 * @version     $Id$
 */
-->

<div>{MESSAGE}</div>

<p>
	<a class="button" href="/channel/add">{{CreateChannel}}</a>
</p>

<table id="entities" class="dataTable">

	<thead>
	<tr>
		<th style="width:1%"></th>
		<th class="l">{{Channel}}</th>
		<th class="l">{{Type}}</th>
		<th class="l">{{Serial}}</th>
		<th class="l">{{Description}}</th>
		<th style="width:1%"></th>
	</tr>
	</thead>

	<tbody>

	<!-- BEGIN ENTITIES -->

	<tr>
		<td class="c" style="padding:0.4em 0">
			<a href="/channel/edit/{ID}" class="tip" title="{{EditEntity}}">
				<img src="/images/ico/{ICON}" alt="{TYPE}" title="" width="16" height="16" />
			</a>
		</td>
		<td class="b">{NAME}</td>
		<td>{TYPE}</td>
		<td>{SERIAL}</td>
		<td>{DESCRIPTION}</td>
		<td style="white-space:nowrap">
			<a href="/channel/edit/{ID}" class="imgbar tip" title="{{EditEntity}}">
				<img src="/images/ico/node_design.png" alt="e" width="16" height="16" />
			</a>
			<a href="/channel/add/{ID}" class="imgbar tip" title="{{CloneEntity}}">
				<img src="/images/ico/node_select_child.png" alt="c" width="16" height="16" />
			</a>
			<form id="df{ID}" action="/channel/delete" method="post" class="delete-form">
			<input type="hidden" name="id" value="{ID}" />
			<input type="image" src="/images/ico/node_delete.png" alt="-"
						 style="background-color:transparent" class="tip nb" title="{{DeleteEntity}}" />
			</form>
		</td>

	</tr>

	<!-- END -->

	</tbody>
</table>

<p>
	<a class="button" href="/channel/add">{{CreateChannel}}</a>
</p>

<div id="dialog-confirm" style="display:none" title="{{DeleteEntity}}">
	<p>
		<span class="ui-icon ui-icon-alert" style="float: left; margin: 0 7px 20px 0;"></span>
		{{ConfirmDeleteEntity}}
	</p>
</div>
