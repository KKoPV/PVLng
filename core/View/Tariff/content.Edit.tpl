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

<form method="post">

<input type="hidden" name="id" value="{ID}" />
<input type="hidden" name="clone" value="{CLONE}" />

<p>
    <label for="name" class="autowidth">
        {{Name}}
        <img style="width:16px;height:16px" width="16" height="16"
             src="/images/required.gif" alt="*" />
    </label>
    <input id="name" type="text" name="name" value="{NAME}" size="40" required="required" />
</p>

<p>
    <label for="comment" class="autowidth">{{Comment}}</label>
    <input id="comment" type="text" name="comment" value="{COMMENT}" size="100" />
</p>

<p>
    <img style="width:16px;height:16px" width="16" height="16"
         src="/images/required.gif" alt="*" />
    <small>{{required}}</small>
</p>

<p>
    <!-- IF {CLONE} OR {EDIT} -->
    <input type="submit" value="{{Save}}" />
    <!-- ELSE -->
    <input type="submit" value="{{proceed}} &raquo;" />
    <!-- ENDIF -->
</p>


</form>
