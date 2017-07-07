/**
 * PVLng - PhotoVoltaic Logger new generation
 *
 * @link       https://github.com/KKoPV/PVLng
 * @link       https://pvlng.com/
 * @author     Knut Kohl <github@knutkohl.de>
 * @copyright  2012 Knut Kohl
 * @license    MIT License (MIT) http://opensource.org/licenses/MIT
 */

/* Wrapper div with chart div */
document.write('
    <div id="pvlng-widget-{raw:UID}">
        <div id="pvlng-chart-{raw:UID}" style="width:{WIDTH}px;height:{HEIGHT}px">
            <!-- Will replaced with chart anyway if there was no error... -->
            <i style="color:red;font-family:monospace">{ERROR}</i>
        </div>
    </div>
');

<!-- IF !{ERROR} -->
/* Draw chart */
PVLngOnLoad(function() {
    PVLngWidget(
        {raw:UID}, {WIDTH}, {HEIGHT}, {DATA}, '{MAX} {UNIT}', {AREA}, '{COLOR}', {LABELS}, '{TIME1}', '{TIME2}', '{LINK}'
    );
});
<!-- ENDIF -->
