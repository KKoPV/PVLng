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

<!-- Add invisible spans for sorting -->
<!-- IF {READ} AND {WRITE} -->
    <span style="display:none">3</span>
    <i class="fa fa-fw fa-arrows-alt"></i>
<!-- ELSEIF {WRITE} -->
    <span style="display:none">1</span>
    <i class="fa fa-fw fa-download"></i>
<!-- ELSEIF {READ} -->
    <span style="display:none">2</span>
    <i class="fa fa-fw fa-upload"></i>
<!-- ELSE -->
    <span style="display:none">0</span>
<!-- ENDIF -->
