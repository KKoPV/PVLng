<!--
/**
 *
 *
 * @author      Knut Kohl <github@knutkohl.de>
 * @copyright   2012-2013 Knut Kohl
 * @license     GNU General Public License http://www.gnu.org/licenses/gpl.txt
 * @version     $Id$
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

	<link rel="shortcut icon" href="/images/favicon.ico" />
	<link rel="icon" type="image/x-icon" href="/images/favicon.ico" />

	<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimum-scale=1.0, maximum-scale=1.0">

	<meta name="apple-mobile-web-app-title" content="PVLng">
	<meta name="apple-mobile-web-app-capable" content="yes">
	<meta name="apple-mobile-web-app-status-bar-style" content="black">

	<link rel="apple-touch-icon" href="/images/touch-icon-iphone.png">

	<meta http-equiv="Content-Style-Type" content="text/css" />
	<meta http-equiv="Content-Script-Type" content="text/javascript">

	<link rel="stylesheet" href="http://code.jquery.com/mobile/1.3.1/jquery.mobile-1.3.1.min.css" />
	<link rel="stylesheet" href="/css/mobile.css" />

	{HEAD}

	<style>{STYLES}</style>

</head>

<body>

	{CONTENT}

	<script src="//code.jquery.com/jquery-1.9.1.js"></script>
	<script src="//code.jquery.com/mobile/1.3.1/jquery.mobile-1.3.1.min.js"></script>
	<script src="/js/jquery-ui.min.js"></script>
	<script src="/js/mobile.js"></script>

	<script>
	var PVLngAPI = 'http://{SERVERNAME}/api/r2/';
	{SCRIPTS}
	</script>

</body>
</html>
