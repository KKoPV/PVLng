/**
 *
 *
 * @author      Knut Kohl <github@knutkohl.de>
 * @copyright   2012-2013 Knut Kohl
 * @license     GNU General Public License http://www.gnu.org/licenses/gpl.txt
 * @version     1.0.0
 */

/* Wrapper div with chart div */
document.write('
    <div id="pvlng-widget-{raw:UID}" style="width:{WIDTH}px">
        <div id="pvlng-chart-{raw:UID}" style="height:{HEIGHT}px"></div>
    </div>
');

runOnLoad(function() {
    _pvlng_chart({raw:UID}, {WIDTH}, {HEIGHT}, {DATA}, '{MAX}'+' '+'{UNIT}',
                 '{AREA}', '{COLOR}', '{LABELS}', '{TIME1}', '{TIME2}');
});
