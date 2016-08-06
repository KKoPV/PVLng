<div id="login-dialog" style="display:none" title="{{Login}}">

<form id="login-form" action="/login" method="POST">

<table id="login-table" style="margin:0 auto">
<tbody>
<tr>
    <td>
        <label for="pass">{{Password}}</label>:
    </td>
    <td>
        <input id="pass" class="ui-corner-all" type="password" name="pass">
    </td>
</tr>
<tr>
    <td></td>
    <td>
        <div class="fl" style="margin-right:0.5em">
            <input id="save" type="checkbox" name="save" class="iCheck">
        </div>
        <label for="save">{{StayLoggedIn}}</label>
    </td>
</tr>
</tbody>
</table>

</form>

</div>

<style>
#login-table td {
    padding: .5em 1em .5em 0;
    vertical-align: middle;
}
</style>

<script>
$(function() {
    $('#login-dialog').dialog({
        autoOpen: false,
        resizable: false,
        width: '25em',
        modal: true,
        open: function() {
            $('#save').iCheck('update');
        },
        buttons: {
            '{{Login}}':  function() {
                $(this).find('form').submit();
            }
        }
    });
});
</script>
