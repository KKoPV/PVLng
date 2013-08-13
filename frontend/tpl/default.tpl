<!--
/**
 *
 *
 * @author      Knut Kohl <github@knutkohl.de>
 * @copyright   2012-2013 Knut Kohl
 * @license     GNU General Public License http://www.gnu.org/licenses/gpl.txt
 * @version     $Id: v1.0.0.2-14-g2a8e482 2013-05-01 20:44:21 +0200 Knut Kohl $
 */
-->

<!doctype html>
<html class="no-js" lang="en">
<head>
	<meta charset="utf-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />

	<title>PVLng - {PVLNG} | {SUBTITLE}</title>
	<meta name="description" content="{PVLNG}" />
	<meta name="author" content="Knut Kohl" />

	<link rel="shortcut icon" href="/images/favicon.ico" />
	<link rel="icon" type="image/x-icon" href="/images/favicon.ico" />

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

	<link rel="stylesheet" href="/css/normalize.css" />
	<link rel="stylesheet" href="/css/960.10.css" />
	<link rel="stylesheet" href="/css/jquery-ui.css" />

	<link rel="stylesheet" href="/css/jquery.dataTables.css" />
	<link rel="stylesheet" href="/css/jquery.dataTables_themeroller.css" />
	<link rel="stylesheet" href="/css/superfish.css" />
	<link rel="stylesheet" href="/css/tipTip.css" />

	<link rel="stylesheet" href="/css/jquery.pnotify.default.css" />

	<link rel="stylesheet" href="/css/iCheck/minimal/orange.css" />
	<link rel="stylesheet" href="/css/iCheck/flat/orange.css" />
	<link rel="stylesheet" href="/css/iCheck/line/orange.css" />

	<link rel="stylesheet" href="/css/default.css" />

	{HEAD}

	<style>{STYLES}</style>

</head>

<body>

<div id="container" class="container_10">

	<div id="header" class="grid_10">
		<div class="alpha grid_2 s">
			<a class="fl" href="/">
				<img style="width:100;height:60px" src="/images/logo.png" width="100" height="60" />
			</a>
			<div style="margin-left:120px">
				{VERSION}
				<!-- IF {VERSIONNEW} -->
				<br /><br />
				<a href="https://github.com/K-Ko/PVLng/wiki"
				   style="font-weight:bold;color:red">
					{VERSIONNEW}
				</a>
				<!-- ENDIF -->
			</div>
		</div>
		<div class="grid_8 omega">
			<div class="r">
				<span id="title1">{PVLNG}</span>
			</div>
			<h3 class="alpha grid_6 c">{SUBTITLE}</h3>
			<!-- IF {USER} -->
			<div class="grid_2 omega r"><br/ ><em>{USER}</em></div>
			<!-- ENDIF -->
		</div>
	</div>

	<div class="clear"></div>

	<div class="grid_8">
		<span class="toolbar">
			<a class="tipbtn" title="{{ChartHint}}" href="/">{{Charts}}</a>
			<!-- IF {USER} -->
			<a class="tipbtn" title="{{OverviewHint}}" href="/overview">{{Overview}}</a>
			<a class="tipbtn" title="{{ChannelsHint}}" href="/channel">{{Channels}}</a>
			<a class="tipbtn" title="{{InfoHint}}" href="/info">{{Information}}</a>
			<!-- ENDIF -->
			<a class="tipbtn" title="{{PlantDescriptionHint}}" href="/description">{{Description}}</a>
		</span>
	</div>
	<div class="grid_2 r">
		<span class="toolbar">
			<a class="tipbtn" title="Deutsch" href="?lang=de">
				<img style="width:20px;height:12px" src="/images/de.png" alt="D" width="20" height="12" />
			</a>
			<a class="tipbtn" title="English" href="?lang=en">
				<img style="width:20px;height:12px" src="/images/en.png" alt="E" width="20" height="12" />
			</a>
			<!-- IF {USER} -->
			<a class="tipbtn" title="Logout {USER}" href="/logout">
				<img style="width:12px;height:12px" src="/images/logout.png" alt="L" width="12" height="12" />
			</a>
			<!-- ELSE -->
			<a class="tipbtn" title="Login" href="/login">
				<img style="width:12px;height:12px" src="/images/logout.png" alt="L" width="12" height="12" />
			</a>
			<!-- ENDIF -->
		</span>
	</div>

	<div class="clear"></div>
	<br />

	<!-- IF {MESSAGES} -->
	<div class="grid_10 b" style="margin-bottom:1em;padding-left:4px">
		{MESSAGES}
	</div>
	<div class="clear"></div>
	<!-- ENDIF -->

	<div id="content" role="main" class="grid_10">
		{CONTENT}
	</div>

	<div class="clear"></div>

	<div id="footer" class="grid_10 s" style="height:5em">
		<div class="alpha grid_3">
			Version {VERSION} / {VERSIONDATE}
			<br />
			&copy; 2012-{YEAR} by
			<a href="http://pvlng.com/Author.html" class="tip" title="Knut Kohl PhotoVoltaics"
			   tip="<strong>K</strong>nut <strong>Ko</strong>hl <strong>P</strong>hoto<strong>V</strong>oltaics">
				<strong>KKoPV</strong>
			</a>
		</div>

		<div class="grid_1">
		<!-- IF {QUERYCOUNT} -->
			{QUERYCOUNT} queries in {sprintf:"%.2f",QUERYTIME}&nbsp;ms
		<!-- ELSE -->
			&nbsp;
		<!-- ENDIF -->
		</div>

		<div id="powered" class="grid_6 r omega">
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
			<a href="http://code.drewwilson.com/entry/tiptip-jquery-plugin" target="_blank">
				<img style="width:80px;height:15px" class="tip"
				     src="/images/tiptip.gif" width="80" height="15"
				     title="TipTip is a very lightweight and intelligent custom tooltip jQuery plugin."
				     alt="tipTip">
			</a>
		</div>
	</div>

</div>

<script src="http://code.jquery.com/jquery-2.0.0.js"></script>
<script>
	window.jQuery || document.write('<script src="/js/jquery.min.js"><\/script>');
</script>
<script src="/js/jquery-ui.min.js"></script>
<script src="/js/jquery.tipTip.js"></script>
<script src="/js/jquery.dataTables.js"></script>
<script src="/js/dataTables.js"></script>
<script src="/js/hoverIntent.js"></script>
<script src="/js/superfish.js"></script>
<script src="/js/supersubs.js"></script>
<script src="/js/jquery.pnotify.js"></script>
<script src="/js/jquery.icheck.js"></script>
<script src="/js/sprintf.js"></script>
<script src="/js/lscache.js"></script>
<script src="/js/script.js"></script>

<!-- IF {LANGUAGE} != "en" -->
<script src="/js/jquery-ui-i18n.min.js"></script>
<!-- ENDIF -->

<script>

	var PVLngAPI = 'http://{SERVERNAME}/api/r2/';

	{SCRIPTS}

	/* Inititilize Pines Notify labels here */
	$.pnotify.defaults.labels.redisplay = '{{Redisplay}}';
	$.pnotify.defaults.labels.all = '{{All}}';
	$.pnotify.defaults.labels.last = '{{Last}}';
	$.pnotify.defaults.labels.stick = '{{Stick}}';
	$.pnotify.defaults.labels.close = '{{Close}}';

</script>

</body>
</html>

