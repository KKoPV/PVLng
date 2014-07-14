<form method="post">

<input type="hidden" name="template" value="{TEMPLATE}">

<table id="channels" class="dataTable">

    <thead>
    <tr>
        <th style="width:1%"></th>
        <th style="width:1%"></th>
        <th class="l">{{Name}}</th>
        <th class="l">{{Description}}</th>
        <th class="l">{{Resolution}}</th>
        <th class="l">{{Unit}}</th>
    </tr>
    </thead>

    <tbody>

    <!-- BEGIN CHANNELS -->

    <tr>
        <td>
            <input type="checkbox" name="a[{_LOOP}]" class="iCheck" checked="checked">
        </td>
        <td>
            <img src="{_ICON}" class="ico tip" title="{_TYPE}">
        </td>
        <td>
            <input type="text" name="p[{_LOOP}][name]" value="{NAME}" size="30">
        </td>
        <td>
            <input type="text" name="p[{_LOOP}][description]" value="{DESCRIPTION}" size="40">
        </td>
        <td>
            <input type="text" name="p[{_LOOP}][resolution]" value="{RESOLUTION}" size="10" placeholder="0{__TSEP}000{__DSEP}00">
        </td>
        <td>
            <input type="text" name="p[{_LOOP}][unit]" value="{UNIT}" size="8">
        </td>
    </tr>

    <!-- END -->

    </tbody>
</table>

<br />
<select name="tree">
    <option value="1">{{TopLevel}} &nbsp; {{or}}</option>
    <option value="0" disabled="disabled">{{AsChild}}</option>
    <!-- BEGIN ADDTREE -->
        <option value="{ID}" <!-- IF !{AVAILABLE} -->disabled="disabled"<!-- ENDIF -->>{INDENT}{NAME}</option>
    <!-- END -->
</select>

&nbsp;
<input type="submit" value="{{Create}}" />

</form>