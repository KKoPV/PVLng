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

	<title>PVLng - {TITLE} | {SUBTITLE}</title>
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

	<!-- IF !{EMBEDDED} -->
		<!-- INCLUDE default.header.tpl -->
	<!-- ENDIF -->

	<div id="content" role="main" class="grid_10">
		{CONTENT}
	</div>

	<div class="clear"></div>

	<!-- IF !{EMBEDDED} -->
		<!-- INCLUDE default.footer.tpl -->
	<!-- ENDIF -->

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

	/* Inititilize Pines Notify labels here */
	$.pnotify.defaults.labels.redisplay = '{{Redisplay}}';
	$.pnotify.defaults.labels.all = '{{All}}';
	$.pnotify.defaults.labels.last = '{{Last}}';
	$.pnotify.defaults.labels.stick = '{{Stick}}';
	$.pnotify.defaults.labels.close = '{{Close}}';

</script>

{SCRIPTS}

</body>
</html>
