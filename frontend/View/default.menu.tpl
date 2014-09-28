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
    <div class="ui-widget-header ui-corner-all" style="height:34px">
        <div class="fl">
            <ul class="sm sm-clean">
                <!-- BEGIN MENU -->
                <li>
                    <a href="{ROUTE}" class="tip-top" title="{HINT}">{LABEL}</a>
                    <!-- IF {SUBMENU1} -->
                    <ul>
                    <!-- BEGIN SUBMENU1 -->
                        <li>
                            <a href="{ROUTE}" class="tip-top" title="{HINT}">{LABEL}</a>
                            <!-- IF {SUBMENU2} -->
                            <ul>
                            <!-- BEGIN SUBMENU2 -->
                                <li>
                                    <a href="{ROUTE}" class="tip-top" title="{HINT}">{LABEL}</a>
                                    <!-- IF {SUBMENU3} -->
                                    <ul>
                                    <!-- BEGIN SUBMENU3 -->
                                        <li><a href="{ROUTE}" class="tip-top" title="{HINT}">{LABEL}</a></li>
                                    <!-- END -->
                                    </ul>
                                    <!-- ENDIF -->
                                </li>
                            <!-- END -->
                            </ul>
                            <!-- ENDIF -->
                        </li>
                    <!-- END -->
                    </ul>
                    <!-- ENDIF -->
                </li>
                <!-- END -->
            </ul>
        </div>

        <div class="fr">
            <ul class="sm sm-clean">
                <li>
                    <a href="#"><img src="/images/lang-select.gif" style="width:33px;height:21px" alt="L"></a>
                    <ul>
                        <!-- BEGIN LANGUAGES -->
                        <li>
                        <a class="language" data-lang="{CODE}" href="?lang={CODE}">
                            <img class="flag flag-{ICON}" style="margin-right:10px" src="/images/pix.gif" alt="{CODE}">
                            {LABEL}
                        </a>
                        </li>
                        <!-- END -->
                    </ul>
                </li>
                <!-- IF !{USER} -->
                <li>
                    <a href="#" class="tip" title="{{Login}}">
                        <img style="width:12px;height:12px" src="/images/logout.png"
                             alt="L" width="12" height="12" onclick="$('#login-dialog').dialog('open'); return false" />
                    </a>
                </li>
                <!-- ENDIF -->
            </ul>
        </div>

    </div>
</div>
