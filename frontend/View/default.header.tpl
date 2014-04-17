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
            <!-- IF {USER} AND {DEVELOPMENT} -->
            <!-- Show actual branch -->
            <p class="xs b" style="color:red">{BRANCH}</p>
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
