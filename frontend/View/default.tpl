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

    <title>{SUBTITLE} | {strip_tags:TITLE}</title>
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
    <link rel="stylesheet" href="/css/jquery-ui.min.css" />
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
        {STYLES}
        <!-- INCLUDE hook.style.css -->
    </style>

    <!-- INCLUDE head.tpl -->
    <!-- INCLUDE hook.head.tpl -->

</head>

<body>

    <img id="pageload" src="/images/loading_dots.gif"
         style="position:fixed;top:50%;left:50%;width:64px;height:21px;margin-left:-32px;margin-top:-15px">

    <div id="container" class="container_10" style="display:none">

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

    <script src="//code.jquery.com/jquery-2.0.0.js"></script>
    <!-- load Highcharts scripts direct from highcharts.com -->
    <script src="http://code.highcharts.com/highcharts.js"></script>
    <script src="http://code.highcharts.com/highcharts-more.js"></script>
    <script src="http://code.highcharts.com/modules/exporting.js"></script>

    <script>
        <!-- INCLUDE config.default.js -->
        <!-- INCLUDE config.js -->
        <!-- INCLUDE script.var.js -->
    </script>

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
    <!-- ELSE -->
    <script src="/js/min.js"></script>
    <!-- ENDIF -->

    <!-- IF {LANGUAGE} != "en" -->
    <script src="/js/select2_locale_{LANGUAGE}.min.js"></script>
    <!-- ENDIF -->

    <script>
        if (verbose) pvlng.verbose = true;
        <!-- INCLUDE hook.script.js -->
    </script>

    {SCRIPTS}

    <a href="#" class="back-to-top ui-state-default ui-corner-tl ui-corner-bl tipbtn"
       style="border-right:0" title="{{BackToTop}}">
        <img src="/images/ico/arrow-stop-090.png" class="ico">
    </a>

    <script>

        var overlay;

        $(function($) {

            $.extend($.fn.select2.defaults, {
                minimumResultsForSearch: 10,
                allowClear: true,
                dropdownAutoWidth: true
            });

            overlay = new pvlng.Overlay();

            /* Library versions */
            $('#highcharts-version').text(Highcharts.version);
            $('#jquery-version').text(jQuery.fn.jquery);
            $('#datatables-version').text(jQuery.fn.dataTable.version);

            $('#pageload').remove();
            $('#container').show();

            $('select').select2();
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

            pvlng.onFinished.run();
        });
    </script>

    <!-- IF !{USER} -->
        <!-- INCLUDE login.dialog.tpl -->
    <!-- ENDIF -->
</body>
</html>
