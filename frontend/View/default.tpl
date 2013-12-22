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

	<title>{SUBTITLE} | {TITLE}</title>
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

	<link rel="stylesheet" href="/css/default.css" />
	<link rel="stylesheet" href="/css/jquery-ui.min.css" />

	<link rel="stylesheet" href="/css/jquery.dataTables.css" />
	<link rel="stylesheet" href="/css/jquery.dataTables_themeroller.css" />
	<link rel="stylesheet" href="/css/superfish.css" />
	<link rel="stylesheet" href="/css/tipTip.css" />

	<link rel="stylesheet" href="/css/jquery.pnotify.default.css" />

	<link rel="stylesheet" href="/css/iCheck/flat.css" />
	<link rel="stylesheet" href="/css/iCheck/line.css" />

	{HEAD}

	<style>{STYLES}</style>

</head>

<body>

	<div id="container" class="container_10">

		<!-- IF !{EMBEDDED} -->
			<!-- INCLUDE default.header.tpl -->
		<!-- ENDIF -->

		<div id="content" role="main">
			{CONTENT}
		</div>

		<div class="clear"></div>

		<!-- IF !{EMBEDDED} -->
			<!-- INCLUDE default.footer.tpl -->
		<!-- ENDIF -->

	</div>

	<script src="/js/trmix.js"></script>
	<script src="//code.jquery.com/jquery-2.0.0.js"></script>
	<script>
		window.jQuery || document.write('<script src="/js/jquery.min.js"><\/script>');
	</script>
	<script src="/js/jquery-ui.min.js" defer></script>
	<!-- IF {LANGUAGE} != "en" -->
	<script src="/js/jquery-ui-i18n.min.js"></script>
	<!-- ENDIF -->

	<script src="/js/jquery.tipTip.js+jquery.dataTables.js" defer></script>
	<script src="/js/jquery.pnotify.js+jquery.icheck.js" defer></script>
	<script src="/js/hoverIntent.js+superfish.js+supersubs.js+sprintf.js+lscache.js" defer></script>
	<script src="/js/dataTables.js+script.js+shortcut.js"></script>

	<script>
		var PVLngAPI = 'http://{SERVERNAME}/api/r2/';

		/* Inititilize Pines Notify labels here */
		var pnotify_defaults_labels_redisplay = '{{Redisplay}}';
		var pnotify_defaults_labels_all = '{{All}}';
		var pnotify_defaults_labels_last = '{{Last}}';
		var pnotify_defaults_labels_stick = '{{Stick}}';
		var pnotify_defaults_labels_close = '{{Close}}';
	</script>

	{SCRIPTS}

	<a href="#" class="back-to-top ui-state-default ui-corner-tl ui-corner-bl tipbtn"
	   style="border-right:0" title="{{BackToTop}}">
		<img src="/images/ico/arrow-stop-090.png" style="width:16px;height:16px" width="16" height="16" />
	</a>

</body>
</html>
