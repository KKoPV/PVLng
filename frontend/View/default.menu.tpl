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

<!-- DEFINE MENUITEM -->
    <!-- IF {LABEL} == "---" -->
        <hr />
    <!-- ELSEIF {ACTIVE} -->
        <a href="{ROUTE}" class="tip-top" title="{HINT}"
           <!-- IF {ROUTE} == "#" -->onclick="return false"<!-- ENDIF -->>
            {LABEL}
        </a>
    <!-- ELSE -->
        <a href="#" class="disabled tip-top" title="{HINT}" onclick="return false">
            {LABEL}
        </a>
    <!-- ENDIF -->
<!-- END DEFINE -->

<!-- Wrap menu widget for correct with calulation -->
<div id="menu" class="grid_10">
    <div class="ui-widget-header ui-corner-all" style="height:35px">
        <div class="fl">
            <ul class="sm sm-clean">
                <!-- BEGIN MENU -->
                <li>
                    <!-- MACRO MENUITEM -->
                    <!-- IF {SUBMENU1} -->
                    <ul>
                    <!-- BEGIN SUBMENU1 -->
                        <li>
                            <!-- MACRO MENUITEM -->
                            <!-- IF {SUBMENU2} -->
                            <ul>
                            <!-- BEGIN SUBMENU2 -->
                                <li>
                                    <!-- MACRO MENUITEM -->
                                    <!-- IF {SUBMENU3} -->
                                    <ul>
                                    <!-- BEGIN SUBMENU3 -->
                                        <li>
                                            <!-- MACRO MENUITEM -->
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
                    <!-- ENDIF -->
                </li>
                <!-- END -->

            </ul>
        </div>

        <div class="fr">
            <ul class="sm sm-clean">
                <!-- QR code for mobile view -->
                <li>
                    <a href="#" class="tip" tip="#qr">
                        <img src="/images/pix.gif" data-src="/images/ico/barcode-2d.png" class="ico def">
                    </a>
                    <div id="qr">
                        <div id="qr" class="c ui-corner-all"
                             style="font-size:larger;margin:.5em;padding:2em;color:black;background-color:white;text-shadow:0">
                            {{ScanForMobileView}}
                            <br /><br />
                            <img id="qr-code" style="width:100px;height:100px">
                        </div>
                    </div>
                </li>

                <li>
                    <a href="#"><img src="/images/pix.gif" data-src="/images/lang-select.gif"
                                     class="def" style="width:33px;height:21px" alt="L"></a>
                    <ul>
                        <!-- BEGIN LANGUAGES -->
                        <li>
                        <a class="language" data-lang="{CODE}" href="?lang={CODE}">
                            <img src="/images/pix.gif" class="flag flag-{ICON}" style="margin-right:10px">
                            {LABEL}
                        </a>
                        </li>
                        <!-- END -->
                    </ul>
                </li>
                <li>
                <!-- IF !{USER} -->
                    <a href="#" class="tip" title="{{Login}}">
                        <img src="/images/pix.gif" data-src="/images/sign-in.png" class="ico def"
                             onclick="$('#login-dialog').dialog('open'); return false" alt="L">
                    </a>
                <!-- ELSE -->
                    <a href="/logout" class="tip" title="{{Logout}}">
                        <img src="/images/pix.gif" data-src="/images/sign-out.png" class="ico def" alt="L">
                    </a>
                <!-- ENDIF -->
                </li>
            </ul>
        </div>

    </div>
</div>
