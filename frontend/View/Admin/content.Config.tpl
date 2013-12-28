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

<div class="grid_10">

<form method="post">

<table id="dataTable" class="dataTable">
    <thead>
    <tr>
        <th class="l">{{Description}}</th>
        <th class="l">{{Value}}</th>
    </tr>
    </thead>

    <tbody>
    <!-- BEGIN DATA -->
    <tr>
        <td>
            {COMMENT}
        </td>
        <td style="white-space:nowrap">
            <!-- IF {TYPE} == "bool" -->
            <div class="fl">
                <input type="radio" id="y{KEY}" name="c[{KEY}]" value="1"
                       class="iCheckLine" style="margin-right:.3em"
                       <!-- IF {VALUE} == 1 -->checked="checked"<!-- ENDIF --> />
                <label for="y{KEY}">{{Yes}}</label>
            </div>
            <div class="fl" style="margin-left:1em">
                <input type="radio" id="n{KEY}" name="c[{KEY}]" value="0"
                       class="iCheckLine" style="margin-right:.3em"
                       <!-- IF {VALUE} == 0 -->checked="checked"<!-- ENDIF --> />
                <label for="n{KEY}">{{No}}</label>
            </div>
            <!-- ELSE -->
                <input type="text" name="c[{KEY}]" value="{VALUE}" size="50" />
            <!-- ENDIF -->
        </td>
    </tr>
    <!-- END -->
    </tbody>

    <tfoot><tr><th colspan="2"></th></tr></tfoot>

</table>

<p><input type="submit" value="{{Save}}" /></p>

</form>

</div>

<div class="clear"></div>
