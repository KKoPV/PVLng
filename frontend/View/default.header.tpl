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
			<span id="title1">{TITLE}</span>
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
		<a class="tipbtn" title="{{DashboardHint}}" href="/dashboard">{{Dashboard}}</a>
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
