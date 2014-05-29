<!--
/**
 *
 *
 * @author      Knut Kohl <github@knutkohl.de>
 * @copyright   2012-2013 Knut Kohl
 * @license     MIT License (MIT) http://opensource.org/licenses/MIT
 * @version     1.0.0
 */
-->

<!-- Wrap menu widget for correct with calulation -->
<div id="menu" class="grid_10">
    <div class="ui-widget-header ui-corner-all">
        <div class="fl">
            <span class="toolbar">
                <!-- BEGIN MENU -->
                <a class="module tipbtn" title="{HINT}" href="{ROUTE}" data-module="{MODULE}">{LABEL}</a>
                <!-- END -->
            </span>
        </div>
        <div class="r">
            <span class="toolbar">
                <!-- BEGIN LANGUAGES -->
                <a class="tipbtn language" title="{LABEL}" data-lang="{CODE}" href="?lang={CODE}">
                    <img style="width:20px;height:12px" src="/images/{CODE}.png" alt="{CODE}" width="20" height="12" />
                </a>
                <!-- END -->
                <!-- IF !{USER} -->
                <a href="#" class="tipbtn" title="{{Login}}">
                    <img style="width:12px;height:12px" src="/images/logout.png"
                         alt="L" width="12" height="12" onclick="$('#login-dialog').dialog('open'); return false" />
                </a>
                <!-- ENDIF -->
            </span>
        </div>

        <div class="fl" style="position:absolute;z-index:10;display:none">
            <span id="submenu"></span>
        </div>
    </div>
</div>
