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
                <a href="https://github.com/KKoPV/PVLng/releases/tag/v{VERSIONNEW}" class="tip" title="Changelog">
                    v{VERSIONNEW}
                </a>
                <a href="https://github.com/KKoPV/PVLng/tree/master" class="tip" title="Check me out on Github">
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
        <!-- IF {USER} --><br /><br /><em>{USER}</em><!-- ELSE -->&nbsp;<!-- ENDIF -->
    </div>
</div>

<div class="clear"></div>

<div class="grid_10 hr"></div>

<div class="clear"></div>

<div class="grid_10">
    <div class="fl">
        <span class="toolbar menu">
            <!-- BEGIN MENU -->
            <a class="tipbtn<!-- IF LOGIN --> login<!-- ENDIF -->" title="{HINT}" href="{ROUTE}">{LABEL}</a>
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
