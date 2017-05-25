<?php
/**
 *
 *
 * @author      Knut Kohl <github@knutkohl.de>
 * @copyright   2012-2013 Knut Kohl
 * @license     GNU General Public License http://www.gnu.org/licenses/gpl.txt
 * @version     1.0.0
 */
namespace Channel\PVLog;

/**
 * PV-Log generator classes
 */
use PVLog\Classes\Json\Instance;
use PVLog\Classes\Json\Plant;

/**
 *
 */
class Plant11 extends BaseAbstract11
{

    /**
     * Used by API
     *
     * @since r2
     * @return string Prettyfied JSON string
     */
    public function GET(&$request)
    {
        // Measure processing time
        $time = microtime(true);

        // transform request date into start - end
        $date = !empty($request['p1']) ? $request['p1'] : date('Y-m-d');

        $request['start']    = $date;
        $request['end']      = $date . '+1day';
        $request['period']   = '5min'; // PV-Log specific period
        $request['filename'] = $date.'.json';

        $instance = new Instance;
        $plant    = new Plant;

        foreach ($this->getChilds() as $child) {
            if ($child instanceof Inverter11) {
                $plant->addInverter($child->read($request));
            } else {
                $this->getTaggedChildData($instance, $child, $request);
            }
        }

        return $instance
            ->setCreator(sprintf('%s for %s (%.1fs)', PVLNG_VERSION_FULL, $this->name, microtime(true)-$time))
            ->setDeleteDayBeforeImport(1) // Send always all day data, so set delete flag...
            ->setPlant($plant)
            ->asJson(isset($request['pretty']) && $request['pretty']);
    }
}
