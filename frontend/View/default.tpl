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
    <link rel="stylesheet" href="/css/normalize.css+default.css+grid.css" />
    <link rel="stylesheet" href="/css/jquery-ui.min.css" />
    <link rel="stylesheet" href="/css/jquery.dataTables.css+jquery.dataTables_themeroller.css+jquery.pnotify.default.css+superfish.css+tipTip.css" />
    <!-- With background images -->
    <link rel="stylesheet" href="/css/iCheck/flat.css" />
    <link rel="stylesheet" href="/css/iCheck/line.css" />

    {HEAD}

    <style>{STYLES}</style>

    <!-- INCLUDE hook.head.tpl -->

</head>

<body>
    <!-- INCLUDE hook.body.before.tpl -->

    <div id="container" class="container_10">

        <!-- IF !{EMBEDDED} -->
        <!-- INCLUDE default.header.tpl -->

        <div class="clear"></div>

        <div class="grid_10 hr"></div>

        <div class="clear"></div>

        <div class="grid_10">
            <div class="fl">
                <span class="toolbar menu">
                    <!-- BEGIN MENU -->
                    <a class="tipbtn" title="{HINT}" href="{ROUTE}">{LABEL}</a>
                    <!-- END -->
                </span>
            </div>
            <div class="r">
                <span class="toolbar menu">
                    <!-- BEGIN LANGUAGES -->
                    <a class="tipbtn language" title="{LABEL}" data-lang="{CODE}" href="?lang={CODE}">
                        <img style="width:20px;height:12px" src="/images/{CODE}.png" alt="{CODE}" width="20" height="12" />
                    </a>
                    <!-- END -->
                    <!-- IF {USER} -->
                    <a class="tipbtn" title="{{Logout}} (Alt+L)" href="/logout">
                        <img style="width:12px;height:12px" src="/images/logout.png" alt="L" width="12" height="12" />
                    </a>
                    <!-- ELSE -->
                    <a href="#" class="tipbtn" title="{{Login}}">
                        <img style="width:12px;height:12px" src="/images/logout.png"
                             alt="L" width="12" height="12" onclick="$('#login-dialog').dialog('open'); return false" />
                    </a>
                    <!-- ENDIF -->
                </span>
            </div>
        </div>

        <div class="clear"></div>
        <!-- ENDIF -->

        <!-- INCLUDE hook.content.before.tpl -->

        <div id="content" role="main">
            {CONTENT}
        <div>

        <div class="clear"></div>

        <!-- INCLUDE hook.content.after.tpl -->

        <!-- IF !{EMBEDDED} -->
        <div class="grid_10 hr"></div>
        <div class="clear"></div>

        <div id="footer">

            <div class="grid_4 xs">
                <a href="http://pvlng.com" class="fl tip" title="PVLng Homepage">
                    <img src="/images/logo.png" style="width:50px;height:30px" width="50" height="30" alt="[PVLng]">
                </a>
                <div class="fl" style="margin-left:10px">
                    Version {VERSION} / {VERSIONDATE}
                    <a href="https://github.com/KKoPV/PVLng/releases/tag/v{VERSION}" class="tip" title="Changelog">
                        <img src="/images/Octocat.png" style="width:16px;height:16px;margin-left:.5em" width="16" height="16" />
                    </a>

                    <br />
                    &copy; 2012-{YEAR} by
                    <a href="http://pvlng.com/PVLng:About" class="tip" title="Knut Kohl PhotoVoltaics"
                       tip="<strong>K</strong>nut <strong>Ko</strong>hl <strong>P</strong>hoto<strong>V</strong>oltaics">
                        <strong>KKoPV</strong>
                    </a>
                </div>
            </div>

            <div id="powered" class="grid_6 s r">

                <a href="http://php.net" target="_blank">
                    <img style="width:80px;height:15px" class="tip"
                         src="/images/php5-power-micro.png" width="80" height="15"
                         title="PHP {PHPVERSION}" alt="PHP {PHPVERSION}">
                </a>
                <a href="http://mysql.com" target="_blank">
                    <img style="width:80px;height:15px" class="tip"
                         src="/images/mysql.gif" width="80" height="15"
                         title="MySQL {MYSQLVERSION}" alt="PHP {MYSQLVERSION}">
                </a>
                <a href="http://www.jquery.com" target="_blank">
                    <img style="width:80px;height:15px" class="tip"
                         src="/images/jquery.gif" width="80" height="15"
                         title="jQuery: The Write Less, Do More, JavaScript Library"
                         alt="jQuery">
                </a>
                <a href="http://www.highcharts.com/products/highcharts" target="_blank">
                    <img style="width:80px;height:15px" class="tip"
                         src="/images/Highcharts.png" width="80" height="15"
                         title="A charting library written in pure JavaScript, offering an easy way of adding interactive charts to your web site or web application."
                         alt="Highcharts">
                </a>
                <a href="http://datatables.net/" target="_blank">
                    <img style="width:80px;height:15px" class="tip"
                         src="/images/DataTables.png" width="80" height="15"
                         title="DataTables is a plug-in for the jQuery Javascript library"
                         alt="DataTables">
                </a>
                <a href="https://github.com/drewwilson/TipTip" target="_blank">
                    <img style="width:80px;height:15px" class="tip"
                         src="/images/tiptip.gif" width="80" height="15"
                         title="TipTip is a very lightweight and intelligent custom tooltip jQuery plugin."
                         alt="tipTip">
                </a>

            </div>

        </div>
        <!-- ENDIF -->

        <div class="clear"></div>

        <div class="grid_10">
            <div id="YRYIE"></div>
        </div>
    </div>


    <script src="//code.jquery.com/jquery-2.0.0.js"></script>
    <script>
        window.jQuery || document.write('<script src="/js/jquery.min.js"><\/script>');
    </script>
    <script src="/js/jquery-ui.min.js"></script>
    <script src="/js/jquery-ui-i18n.min.js"></script>
    <script src="/js/jquery.dataTables.min.js"></script>
    <script src="/js/jquery.number.min.js"></script>
    <script src="/js/jquery.tipTip.js+jquery.icheck.js+jquery.pnotify.js+dataTables.js+shortcut.js+script.js"></script>
    <script src="/js/trmix.min.js"></script>
    <script src="/js/hoverIntent.js+superfish.js+supersubs.js+sprintf.js+lscache.js"></script>

    <script>
        var PVLngAPI = 'http://{SERVERNAME}/api/r3/',
            PVLngAPIkey = '{APIKEY}',

            /* Inititilize Pines Notify labels here with I18N */
            pnotify_defaults_labels_stick = '{{Stick}}',
            pnotify_defaults_labels_close = '{{Close}}',

            DecimalSeparator = '{DSEP}',
            ThousandSeparator = '{TSEP}',

            language = '{LANGUAGE}',
            user = '{USER}';
    </script>

    {SCRIPTS}

    <a href="#" class="back-to-top ui-state-default ui-corner-tl ui-corner-bl tipbtn"
       style="border-right:0" title="{{BackToTop}}">
        <img src="/images/ico/arrow-stop-090.png" style="width:16px;height:16px" width="16" height="16" />
    </a>

    <!-- INCLUDE hook.body.after.tpl -->

    <!-- IF !{USER} -->
        <!-- INCLUDE Admin/login.dialog.tpl -->
    <!-- ENDIF -->
</body>
</html>
