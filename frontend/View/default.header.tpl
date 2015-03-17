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

<div id="header" class="grid_10">

    <div class="grid_2 alpha s">
        <a class="fl" href="/">
            <img style="width:75px;height:45px" src="/images/logo.png">
        </a>
        <div class="b" style="margin-left:120px">
            <!-- IF {VERSIONNEW} -->
            v{VERSION}<br /><br />
            <a href="https://github.com/KKoPV/PVLng/releases/tag/v{VERSIONNEW}" class="tip" style="color:red" title="Changelog">
                v{VERSIONNEW}
            </a>
            <!-- ENDIF -->
        </div>
    </div>

    <div class="grid_4 c">
        <h3 style="margin-top:.5em;margin-bottom:0">{SUBTITLE}</h3>
    </div>

    <div class="r">
        <span id="title1">{TITLE}</span>
        <!-- IF {USER} AND {TOKEN} -->
        <br /><a href="/login/{TOKEN}" class="tip" title="{{LoginToken}}">&bull;</a>
        <!-- ENDIF -->
    </div>

    <div class="clear"></div>

</div>
