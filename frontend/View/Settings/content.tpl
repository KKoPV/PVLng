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

<h2>Core</h2>
<!-- BEGIN CORE -->
    <!-- INCLUDE content.scope.tpl -->
<!-- END -->

<h2>{{Controller}}</h2>
<!-- BEGIN CONTROLLER -->
    <!-- INCLUDE content.scope.tpl -->
<!-- END -->

<h2>{{Model}}</h2>
<!-- BEGIN MODEL -->
    <!-- INCLUDE content.scope.tpl -->
<!-- END -->

<p><input type="submit" value="{{Save}}"></p>

</form>
