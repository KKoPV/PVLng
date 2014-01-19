/**
 *
 *
 * @author      Knut Kohl <github@knutkohl.de>
 * @copyright   2012-2013 Knut Kohl
 * @license     GNU General Public License http://www.gnu.org/licenses/gpl.txt
 * @version     1.0.0
 */

document.write('<div id="pvlng-{GUID}" style="width:{WIDTH}px;height:{HEIGHT}px"></div>');

runOnLoad(function() {
    _pvlng_chart('pvlng-{GUID}', {WIDTH}, {HEIGHT}, {DATA}, '{MAX} {UNIT}', '{AREA}', '{COLOR}', '{LABELS}', '{TIME1}', '{TIME2}');
});
