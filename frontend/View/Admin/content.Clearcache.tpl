<!--
/**
 *
 *
 * @author Knut Kohl <github@knutkohl.de>
 * @copyright 2012-2013 Knut Kohl
 * @license GNU General Public License http://www.gnu.org/licenses/gpl.txt
 * @version 1.0.0
 */
-->

<form method="post">

<p>
    <div class="fl"><input id="cb-tpl" type="checkbox" name="tpl" class="iCheck"></div>
    <label for="cb-tpl" style="margin-left:.5em">
        Clear cached templates from <tt style="font-size:120%;font-weight:bold">{TEMPDIR}</tt>
    </label>
</p>

<p>
    <div class="fl"><input id="cb-cache" type="checkbox" name="cache" class="iCheck"></div>
    <label for="cb-cache" style="margin-left:.5em">
        Clear <tt style="font-size:140%;font-weight:bold">OpCode</tt> and
        <tt style="font-size:140%;font-weight:bold">user</tt> cache
    </label>
</p>

<p>
    <input type="submit" value="Clear cache(s)">
</p>

</form>
