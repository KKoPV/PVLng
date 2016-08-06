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
<html class="no-js ui-mobile" lang="en">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />

    <title>PVLng</title>
    <meta name="description" content="{PVLNG}" />
    <meta name="author" content="Knut Kohl" />

    <meta name="apple-mobile-web-app-title" content="PVLng">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <!-- INCLUDE favicon.inc.tpl -->

    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimum-scale=1.0, maximum-scale=1.0">

    <meta http-equiv="Content-Style-Type" content="text/css" />
    <meta http-equiv="Content-Script-Type" content="text/javascript">

    <link rel="stylesheet" href="http://code.jquery.com/mobile/1.3.2/jquery.mobile-1.3.2.min.css" />
    <link rel="stylesheet" href="/css/mobile.min.css" />

    {HEAD}

    <style>{STYLES}</style>

</head>

<body>

    {CONTENT}

    <!-- IF {DEVELOPMENT} -->
    <script src="//code.jquery.com/jquery-2.1.1.js"></script>
    <script src="//code.jquery.com/mobile/1.3.2/jquery.mobile-1.3.2.js"></script>
    <!-- ELSE -->
    <script src="//code.jquery.com/jquery-2.1.1.min.js"></script>
    <script src="//code.jquery.com/mobile/1.3.2/jquery.mobile-1.3.2.min.js"></script>
    <!-- ENDIF -->
    <script src="/js/jquery-ui.min.js"></script>

    <!-- INCLUDE highcharts.tpl -->

    <!-- IF {DEVELOPMENT} -->
    <script src="/js/mobile.js"></script>
    <script src="/js/chart.js"></script>
    <!-- ELSE -->
    <script src="/js/min.mobile.js"></script>
    <!-- ENDIF -->

    <script>
        <!-- INCLUDE script.var.js -->
    </script>

    {SCRIPTS}

</body>
</html>
