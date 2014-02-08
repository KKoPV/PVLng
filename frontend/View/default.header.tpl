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

<div id="header">
    <div class="grid_2 s">
        <a class="fl" href="/">
            <img style="width:100px;height:60px" src="/images/logo.png" width="100" height="60" />
        </a>
        <div style="margin-left:120px">
            v{VERSION}
            <!-- IF {VERSIONNEW} -->
            <p class="b">
                <a href="https://github.com/K-Ko/PVLng/releases/tag/v{VERSIONNEW}" class="tip" title="Changelog">
                    v{VERSIONNEW}
                </a>
                <a href="https://github.com/K-Ko/PVLng/tree/master" class="tip" title="Check me out on Github">
                    <img src="/images/Octocat.png" style="width:16px;height:16px;margin-left:.25em" width="16" height="16" />
                </a>
            </p>
            <!-- ENDIF -->
            <!-- IF {DEVELOPMENT} -->
            <!-- Show actual branch -->
            <br /><small class="b" style="color:red">{BRANCH}</small>
            <!-- ENDIF -->
        </div>
    </div>
    <div class="grid_4 c">
        <h3 style="margin-bottom:0">{SUBTITLE}</h3>
    </div>
    <div class="grid_4 r">
        <span id="title1">{TITLE}</span>
        <br /><br />
        <!-- IF {USER} --><em>{USER}</em><!-- ELSE -->&nbsp;<!-- ENDIF -->
    </div>
</div>

<div class="clear"></div>

<div class="grid_10 hr"></div>

<div class="clear"></div>

<div class="grid_10">
    <div class="fl">
        <span class="toolbar menu">
            <a class="tipbtn" title="{{ChartHint}} (Shift+F1)" href="/">{{Charts}}</a>
            <a class="login tipbtn" title="{{DashboardHint}} (Shift+F2)" href="/dashboard">{{Dashboard}}</a>
            <a class="login tipbtn" title="{{ListHint}} (Shift+F3)" href="/list">{{List}}</a>
            <a class="login tipbtn" title="{{OverviewHint}} (Shift+F4)" href="/overview">{{Overview}}</a>
            <a class="login tipbtn" title="{{ChannelsHint}} (Shift+F5)" href="/channel">{{Channels}}</a>
            <a class="login tipbtn" title="{{InfoHint}} (Shift+F6)" href="/info">{{Information}}</a>
            <a class="tipbtn" title="{{PlantDescriptionHint}} (Shift+F7)" href="/description">{{Description}}</a>
        </span>
    </div>
    <div class="r">
        <span class="toolbar menu">
            <a class="tipbtn" title="Deutsch" href="?lang=de">
                <img style="width:20px;height:12px" src="/images/de.png" alt="D" width="20" height="12" />
            </a>
            <a class="tipbtn" title="English" href="?lang=en">
                <img style="width:20px;height:12px" src="/images/en.png" alt="E" width="20" height="12" />
            </a>
            <!-- IF {USER} -->
            <a class="tipbtn" title="{{Logout}} (Alt+L)" href="/logout">
                <img style="width:12px;height:12px" src="/images/logout.png" alt="L" width="12" height="12" />
            </a>
            <!-- ELSE -->
            <a class="tipbtn" title="{{Login}}" href="/login">
                <img style="width:12px;height:12px" src="/images/logout.png" alt="L" width="12" height="12" />
            </a>
            <!-- ENDIF -->
        </span>
    </div>
</div>

<div class="clear"></div>

<br />

<!-- IF {MESSAGES} -->
<div class="grid_10 b" style="margin-bottom:1em;padding-left:4px">
    {MESSAGES}
</div>
<div class="clear"></div>
<!-- ENDIF -->
