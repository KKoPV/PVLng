<!--
/**
 * Default main template
 *
 * @author      Knut Kohl <github@knutkohl.de>
 * @copyright   2012-2014 Knut Kohl
 * @license     MIT License (MIT) http://opensource.org/licenses/MIT
 * @version     1.0.0
 */
-->
<!doctype html>
<html class="no-js" lang="en">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    <meta name="viewport" content="width=device-width,initial-scale=1">

    <title>{strip_tags:TITLE} | {SUBTITLE}</title>

    <meta name="description" content="{PVLNG}" />
    <meta name="author" content="Knut Kohl" />

    <!-- HOOK head_before.html -->

    <!-- INCLUDE favicon.inc.tpl -->

    <meta http-equiv="Content-Script-Type" content="text/javascript">

    <script>
        <!-- INCLUDE messages.js -->
    </script>

    <meta http-equiv="Content-Style-Type" content="text/css" />

    <!-- IF {DEVELOPMENT} -->
    <link rel="stylesheet" href="/css/normalize.css" />
    <link rel="stylesheet" href="/css/jquery-ui.css" />
    <link rel="stylesheet" href="/css/default.css" />
    <link rel="stylesheet" href="/css/grid.css" />
    <link rel="stylesheet" href="/css/jquery.dataTables.css" />
    <link rel="stylesheet" href="/css/jquery.dataTables_themeroller.css" />
    <link rel="stylesheet" href="/css/jquery.treetable.css" />
    <link rel="stylesheet" href="/css/jquery.pnotify.default.css" />
    <link rel="stylesheet" href="/css/default.jquery.css" />
    <link rel="stylesheet" href="/css/superfish.css" />
    <link rel="stylesheet" href="/css/tipTip.css" />
    <link rel="stylesheet" href="/css/select2.css" />
    <link rel="stylesheet" href="/css/spectrum.css" />
    <link rel="stylesheet" href="/css/sm-core-css.css" />
    <link rel="stylesheet" href="/css/sm-clean.css" />
    <link rel="stylesheet" href="/css/flags.css" />
    <link rel="stylesheet" href="/css/sprites.css" />
    <!-- ELSE -->
    <link rel="stylesheet" href="/css/min.css" />
    <!-- ENDIF -->

    <!-- With background images -->
    <link rel="stylesheet" href="/css/iCheck/flat.min.css" />
    <link rel="stylesheet" href="/css/iCheck/line.min.css" />

    {HEAD}

    <style>
        <!-- HOOK head.css -->
        {STYLES}
    </style>

    <script type="text/javascript">!function(){"use strict";function e(e,t,n){e.addEventListener?e.addEventListener(t,n,!1):e.attachEvent&&e.attachEvent("on"+t,n)}function t(e){return window.localStorage&&localStorage.font_css_cache&&localStorage.font_css_cache_file===e}function n(){if(window.localStorage&&window.XMLHttpRequest)if(t(o))c(localStorage.font_css_cache);else{var n=new XMLHttpRequest;n.open("GET",o,!0),e(n,"load",function(){4===n.readyState&&(c(n.responseText),localStorage.font_css_cache=n.responseText,localStorage.font_css_cache_file=o)}),n.send()}else{var a=document.createElement("link");a.href=o,a.rel="stylesheet",a.type="text/css",document.getElementsByTagName("head")[0].appendChild(a),document.cookie="font_css_cache"}}function c(e){var t=document.createElement("style");t.innerHTML=e,document.getElementsByTagName("head")[0].appendChild(t)}var o="/css/font.css";window.localStorage&&localStorage.font_css_cache||document.cookie.indexOf("font_css_cache")>-1?n():e(window,"load",n)}();</script><noscript><link rel="stylesheet" href="/css/font.css"></noscript>

    <!-- HOOK head_after.html -->
</head>

<body>
    <!-- HOOK body_page_before.html -->

    <img id="pageload" src="/images/loading_dots.gif">

    <div id="container" class="container_10" style="opacity:0">

        <!-- HOOK body_container_before.html -->

        <!-- IF !{EMBEDDED} -->
        <div id="header" class="grid_10 no-print">

            <!-- HOOK body_header_before.html -->

            <div class="grid_2 alpha s">
                <!-- HOOK body_header_left_before.html -->
                <a class="fl" href="/">
                    <img style="width:75px;height:45px" src="/images/logo.png">
                </a>
                <div class="b" style="margin-left:120px">
                    <!-- HOOK body_header_left_version_before.html -->
                    <!-- IF {VERSIONNEW} -->
                    <p>v{VERSION}</p>
                    <p>
                        <a href="https://github.com/KKoPV/PVLng/releases/tag/v{VERSIONNEW}" class="tip" style="color:red" title="Changelog">
                            v{VERSIONNEW}
                        </a>
                    </p>
                    <!-- ENDIF -->
                    <!-- HOOK body_header_left_version_after.html -->
                </div>
                <!-- HOOK body_header_left_after.html -->
            </div>

            <div class="grid_4 c">
                <!-- HOOK body_header_center_before.html -->
                <h3 style="margin-top:.5em;margin-bottom:0">{SUBTITLE}</h3>
                <!-- HOOK body_header_center_after.html -->
            </div>

            <div class="r">
                <!-- HOOK body_header_right_before.html -->
                <span id="title1">{TITLE}</span>
                <!-- IF {USER} AND {TOKEN} -->
                <!-- HOOK body_header_right_user_before.html -->
                <br /><a href="/login/{TOKEN}" class="tip" title="{{LoginToken}}">&bull;</a>
                <!-- HOOK body_header_right_user_after.html -->
                <!-- ENDIF -->
                <!-- HOOK body_header_right_after.html -->
            </div>

            <div class="clear"></div>

            <!-- HOOK body_header_after.html -->

        </div>

        <div class="clear"></div>

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
        <div id="menu" class="grid_10 no-print">

            <!-- HOOK body_menu_before.html -->

            <div class="ui-widget-header ui-corner-all" style="height:35px">

                <!-- HOOK body_menu_left_before.html -->

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

                <!-- HOOK body_menu_left_middle.html -->

                <div class="fl extra"></div>

                <!-- HOOK body_menu_middle_right.html -->

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
                                <a class="language" data-lang="{CODE}" href="/?lang={CODE}">
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

                <!-- HOOK body_menu_right_after.html -->

            </div>

            <!-- HOOK body_menu_after.html -->

        </div>

        <div class="clear"></div>

        <!-- ENDIF -->

        <!-- HOOK body_content_before.html -->

        <div id="content" role="main" class="grid_10">
            <!-- HOOK body_content_content_before.html -->
            {CONTENT}
            <!-- HOOK body_content_content_after.html -->
        </div>

        <div class="clear"></div>

        <!-- HOOK body_content_after.html -->

        <!-- IF !{EMBEDDED} -->

        <!-- HOOK body_footer_before.html -->

        <div id="footer" class="grid_10 xs no-print">
            <div class="ui-widget-header ui-corner-all">
                <!-- HOOK body_footer_inner_before.html -->
                <div class="fl icons">
                    &copy; 2012-{raw:YEAR} by
                    <a href="http://pvlng.com/PhotoVoltaic_Logger_new_generation:About"
                       class="tip" title="Knut Kohl PhotoVoltaics" target="_blank"
                       tip="<strong>K</strong>nut <strong>Ko</strong>hl <strong>P</strong>hoto<strong>V</strong>oltaics">
                        <strong>KKoPV</strong>
                    </a>
                    <a href="https://github.com/KKoPV/PVLng/releases/tag/v{VERSION}" target="_blank"
                       class="tip" style="margin-left:.5em" title="Changelog version {VERSION} / {VERSIONDATE} on GitHub">
                        <img src="/images/pix.gif" data-src="/images/Octocat.png" class="def">
                    </a>
                    v{VERSION}&nbsp;<span id="commit"></span>
                </div>

                <div class="extra fl"></div>

                <div id="powered" class="r">
                    <a href="http://php.net" target="_blank">
                        <img src="/images/pix.gif" data-src="/images/php5-power-micro.png"
                             class="def tip" title="PHP {PHPVERSION}" alt="PHP {PHPVERSION}">
                    </a>
                    <a href="http://mysql.com" target="_blank">
                        <img src="/images/pix.gif" data-src="/images/mysql.gif"
                             class="def tip" title="MySQL {MYSQLVERSION}" alt="PHP {MYSQLVERSION}">
                    </a>
                    <a href="http://www.jquery.com" target="_blank">
                        <img src="/images/pix.gif" data-src="/images/jquery.gif"
                             class="def tip" alt="jQuery" tip="#jquery-powered"
                             title="jQuery: The Write Less, Do More, JavaScript Library">
                    </a>
                    <div id="jquery-powered">
                        jQuery v<span id="jquery-version"></span>: The Write Less, Do More, JavaScript Library.
                    </div>
                    <a href="http://www.highcharts.com/products/highcharts" target="_blank">
                        <img src="/images/pix.gif" data-src="/images/Highcharts.png"
                             class="def tip" alt="Highcharts" tip="#highcharts-powered"
                             title="Highcharts: A charting library written in pure JavaScript, offering an easy way of adding interactive charts to your web site or web application.">
                    </a>
                    <div id="highcharts-powered">
                        Highcharts v<span id="highcharts-version"></span>: A charting library written in pure JavaScript, offering an easy way of adding interactive charts to your web site or web application.
                    </div>
                    <a href="http://datatables.net/" target="_blank">
                        <img src="/images/pix.gif" data-src="/images/DataTables.png"
                             class="def tip" alt="DataTables" tip="#datatables-powered" title="DataTables: A plug-in for the jQuery Javascript library. It is a highly flexible tool, based upon the foundations of progressive enhancement, and will add advanced interaction controls to any HTML table.">
                    </a>
                    <div id="datatables-powered">
                        DataTables v<span id="datatables-version"></span>: A plug-in for the jQuery Javascript library. It is a highly flexible tool, based upon the foundations of progressive enhancement, and will add advanced interaction controls to any HTML table.
                    </div>
                    <a href="http://ivaynberg.github.io/select2/" target="_blank">
                        <img src="/images/pix.gif" data-src="/images/select2.gif"
                             class="def tip" alt="select2"
                             title="Select2 is a jQuery based replacement for select boxes. It supports searching, remote data sets, and infinite scrolling of results.">
                    </a>
                    <a href="https://github.com/drewwilson/TipTip" target="_blank">
                        <img src="/images/pix.gif" data-src="/images/tiptip.gif"
                             class="def tip" alt="tipTip"
                             title="TipTip is a very lightweight and intelligent custom tooltip jQuery plugin.">
                    </a>
                    <a href="http://pvlng.com/Contributions" target="_blank">
                        <img src="/images/pix.gif" data-src="/images/plus-white.png"
                             class="def tip" style="width:16px" alt="more" title="More contributions">
                    </a>
                </div>
                <!-- HOOK body_footer_inner_after.html -->
            </div>
        </div>

        <div class="clear"></div>

        <!-- HOOK body_footer_after.html -->

        <div class="grid_10">
            <div id="YRYIE"></div>
        </div>

        <!-- ENDIF -->

        <!-- HOOK body_container_after.html -->

    </div>

    <i class="fa fa-arrow-up go-top ui-state-default ui-corner-tl ui-corner-bl tipbtn no-print"
       style="display:none" title="{{BackToTop}}"></i>

    <!-- IF !{USER} -->

    <!-- Login form -->

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

    <!-- ENDIF -->

    <script>
        <!-- INCLUDE script.var.js -->
        /**
         * DataTables can use cookies in the end user's web-browser in order to
         * store it's state after each change in drawing. What this means is
         * that if the user were to reload the page, the table should remain
         * exactly as it was (length, filtering, pagination and sorting).
         * This is disabled by default.
         */
        var DatatablesStateSave = false;
        <!-- HOOK body_config.js -->
    </script>

    <!-- HOOK javascript_files_before.html -->

    <!-- IF {DEVELOPMENT} -->
    <script src="//code.jquery.com/jquery-2.1.1.js"></script>
    <script src="/js/jquery-ui.min.js"></script>
    <script src="/js/jquery-ui-i18n.min.js"></script>
    <script src="/js/jquery.dataTables.min.js"></script>
    <script src="/js/jquery.dataTables.rowReordering.js"></script>
    <script src="/js/jquery.treetable.min.js"></script>
    <script src="/js/jquery.number.min.js"></script>
    <script src="/js/jquery.tipTip.js"></script>
    <script src="/js/jquery.icheck.js"></script>
    <script src="/js/jquery.pnotify.js"></script>
    <script src="/js/jquery.smartmenus.min.js"></script>
    <script src="/js/jquery.autosize.min.js"></script>
    <script src="/js/dataTables.js"></script>
    <script src="/js/select2.min.js"></script>
    <script src="/js/shortcut.js"></script>
    <script src="/js/script.js"></script>
    <script src="/js/pvlng.js"></script>
    <script src="/js/chart.js"></script>
    <script src="/js/hoverIntent.js"></script>
    <script src="/js/spectrum.js"></script>
    <script src="/js/superfish.js"></script>
    <script src="/js/supersubs.js"></script>
    <script src="/js/sprintf.js"></script>
    <script src="/js/lscache.js"></script>
    <script src="/js/trmix.min.js"></script>
    <script src="/js/Blob.min.js"></script>
    <script src="/js/FileSaver.min.js"></script>
    <script src="/js/qr.js"></script>
    <!-- ELSE -->
    <script src="//code.jquery.com/jquery-2.1.1.min.js"></script>
    <script src="/js/min.js"></script>
    <!-- ENDIF -->

    <script src="https://use.fontawesome.com/1ef5b408e5.js"></script>

    <!-- IF {LANGUAGE} != "en" -->
    <script src="/js/select2_locale_{LANGUAGE}.min.js"></script>
    <!-- ENDIF -->

    <!-- INCLUDE highcharts.inc.tpl -->

    <!-- HOOK javascript_files_after.html -->

    <script>
        pvlng.verbose = development;

        $(function($) {

            $.extend($.fn.select2.defaults, {
                minimumResultsForSearch: 10,
                allowClear: true,
                dropdownAutoWidth: true
            });

            /* Library versions */
            $('#highcharts-version').text(Highcharts.version);
            $('#jquery-version').text(jQuery.fn.jquery);
            $('#datatables-version').text(jQuery.fn.dataTable.version);

            $('#pageload').remove();
            $('#container').show();

            $('select').select2();

            $('.ui-buttonset').buttonset();

            $('.ui-tabs').tabs({
                /* Selects on hidden tabs must recreated on tab activation */
                activate: function( event, ui ) { $('select', this).select2() }
            });
            $('label.autowidth').autoWidth();

            var ta = $('textarea');
            if (ta.length) {
                ta.autosize();
                document.body.offsetWidth; /* Force a reflow before the class gets applied */
                ta.addClass('textarea-transition');
            }

            /* Deferred image loading */
            $('img.def').prop('src', function() { return $(this).data('src') });

            /* Actual branch and commit */
            var branch = '{PVLNG_BRANCH}';
            if (branch) {
                $('#commit').text('(' + branch + '/' + '{PVLNG_COMMIT})'.substr(0,7) + ')');
            }

            /* QR-Code for mobile view, cache in local storage */
            $('#qr-code').prop('src', function() {
                var q = 'qrcode-mobile', qrData = lscache.get(q);
                if (!qrData) {
                    qrData = qr.toDataURL({
                        value: location.protocol+'//'+location.hostname+'/m',
                        level: 'M'
                    });
                    /* Save to local storage */
                    lscache.set(q, qrData);
                }
                return qrData;
            });

            $('#pageload').remove();
            $('#container').fadeTo(0, 1);

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

            if (user) {
                $.ajaxSetup({
                    beforeSend: function setHeader(XHR) { XHR.setRequestHeader('Authorization', 'Bearer '+PVLngAPIkey) }
                });
            }
        });

        /* Inline scripts from Controllers */
        {INLINEJS}

        $(function($) {
            /**
             * Run postponed script actions as last
             */
            pvlng.onFinished.run();

        });

        <!-- HOOK body.js -->

    </script>

    <!-- HOOK body_page_after.html -->

</body>
</html>
