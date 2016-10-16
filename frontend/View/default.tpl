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

<!doctype html>
<html class="no-js" lang="en">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />

    <title>{strip_tags:TITLE} | {SUBTITLE}</title>
    <meta name="description" content="{PVLNG}" />
    <meta name="author" content="Knut Kohl" />

    <!-- INCLUDE favicon.inc.tpl -->

    <meta name="viewport" content="width=device-width,initial-scale=1">

    <meta http-equiv="Content-Script-Type" content="text/javascript">

    <script>

    var messages = [
        <!-- BEGIN MESSAGESRAW -->
        { type: '{TYPE}', text: '{MESSAGE}' },
        <!-- END -->
    ];

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
    <link rel="stylesheet" href="/css/font-awesome.min.css">
    <!-- ELSE -->
    <link rel="stylesheet" href="/css/min.css" />
    <!-- ENDIF -->

    <!-- With background images -->
    <link rel="stylesheet" href="/css/iCheck/flat.min.css" />
    <link rel="stylesheet" href="/css/iCheck/line.min.css" />

    {HEAD}

    <style>
        {STYLES}
        <!-- INCLUDE hook.style.css -->
    </style>

    <!-- INCLUDE head.tpl -->
    <!-- INCLUDE hook.head.tpl -->

    <script type="text/javascript">!function(){"use strict";function e(e,t,n){e.addEventListener?e.addEventListener(t,n,!1):e.attachEvent&&e.attachEvent("on"+t,n)}function t(e){return window.localStorage&&localStorage.font_css_cache&&localStorage.font_css_cache_file===e}function n(){if(window.localStorage&&window.XMLHttpRequest)if(t(o))c(localStorage.font_css_cache);else{var n=new XMLHttpRequest;n.open("GET",o,!0),e(n,"load",function(){4===n.readyState&&(c(n.responseText),localStorage.font_css_cache=n.responseText,localStorage.font_css_cache_file=o)}),n.send()}else{var a=document.createElement("link");a.href=o,a.rel="stylesheet",a.type="text/css",document.getElementsByTagName("head")[0].appendChild(a),document.cookie="font_css_cache"}}function c(e){var t=document.createElement("style");t.innerHTML=e,document.getElementsByTagName("head")[0].appendChild(t)}var o="/css/font.css";window.localStorage&&localStorage.font_css_cache||document.cookie.indexOf("font_css_cache")>-1?n():e(window,"load",n)}();</script><noscript><link rel="stylesheet" href="/css/font.css"></noscript>
</head>

<body>
    <img id="pageload" src="/images/loading_dots.gif"
         style="position:fixed;top:50%;left:50%;width:64px;height:21px;margin-left:-32px;margin-top:-15px">

    <div id="container" class="container_10" style="opacity:0">

        <!-- INCLUDE hook.body.before.tpl -->

        <!-- IF !{EMBEDDED} -->
        <!-- INCLUDE default.header.tpl -->
        <div class="clear"></div>
        <!-- INCLUDE default.menu.tpl -->
        <div class="clear"></div>
        <!-- ENDIF -->

        <!-- INCLUDE hook.content.before.tpl -->

        <div id="content" role="main" class="grid_10">
            {CONTENT}
        </div>

        <div class="clear"></div>

        <!-- INCLUDE hook.content.after.tpl -->

        <!-- IF !{EMBEDDED} -->
        <!-- INCLUDE default.footer.tpl -->
        <div class="clear"></div>
        <div class="grid_10">
            <div id="YRYIE"></div>
        </div>
        <!-- ENDIF -->

        <!-- INCLUDE hook.body.after.tpl -->

    </div>

    <i class="fa fa-arrow-up go-top ui-state-default ui-corner-tl ui-corner-bl tipbtn"
       style="display:none" title="{{BackToTop}}"></i>

    <!-- IF {DEVELOPMENT} -->
    <script src="//code.jquery.com/jquery-2.1.1.js"></script>
    <!-- ELSE -->
    <script src="//code.jquery.com/jquery-2.1.1.min.js"></script>
    <!-- ENDIF -->

    <script>
        <!-- INCLUDE config.default.js -->
        <!-- INCLUDE config.js -->
        <!-- INCLUDE script.var.js -->
    </script>

    <!-- INCLUDE highcharts.tpl -->

    <!-- IF {DEVELOPMENT} -->
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
    <script src="/js/min.js"></script>
    <!-- ENDIF -->

    <!-- IF {LANGUAGE} != "en" -->
    <script src="/js/select2_locale_{LANGUAGE}.min.js"></script>
    <!-- ENDIF -->

    <script>
        pvlng.verbose = development;
        <!-- INCLUDE hook.script.js -->
    </script>

    {SCRIPTS}

    <script>

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

            pvlng.onFinished.run();
        });


    </script>

    <!-- IF !{USER} --><!-- INCLUDE login.dialog.tpl --><!-- ENDIF -->
    <!-- INCLUDE hook.end.tpl -->
</body>
</html>
