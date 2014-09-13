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
            <img style="width:100px;height:60px" src="/images/logo.png">
        </a>
        <div class="b" style="margin-left:120px">
            <!-- IF {USER} AND {DEVELOPMENT} -->
            <!-- Show actual branch -->
            {BRANCH}<br /><br />
            <!-- ENDIF -->
            <!-- IF {VERSIONNEW} -->
            v{VERSION}<br /><br />
            <a href="https://github.com/KKoPV/PVLng/releases/tag/v{VERSIONNEW}" class="tip" style="color:red" title="Changelog">
                v{VERSIONNEW}
            </a>
            <!-- ENDIF -->
        </div>
    </div>
    <div class="grid_4 c">
        <h3 style="margin-bottom:0">{SUBTITLE}</h3>
    </div>
    <div class="grid_4 r">
        <span id="title1">{TITLE}</span>
        <br />
        <!-- IF {USER} -->
        <br />
        <!-- IF {TOKEN} -->
        <a href="/login?token={TOKEN}" class="tip" title="{{LoginToken}}">&bull;</a>
        &nbsp;
        <!-- ENDIF -->
        <a href="/logout" class="tip" title="{{Logout}}">{USER}</a>
        <!-- ELSE -->
        &nbsp;
        <!-- ENDIF -->
    </div>
</div>
