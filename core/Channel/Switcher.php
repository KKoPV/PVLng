<?php
/**
 *
 *
 * @author      Knut Kohl <github@knutkohl.de>
 * @copyright   2012-2017 Knut Kohl
 * @license     GNU General Public License http://www.gnu.org/licenses/gpl.txt
 */
namespace Channel;

/**
 *
 */
class Switcher extends Channel
{
    /**
     *
     */
    protected function beforeWrite(&$request)
    {
        parent::beforeWrite($request);

        $this->ORMReading->deleteFutureReadings($this->entity, $this->timestamp);

        // Recalc last reading
        $this->lastReading = $this->ORMReading->getLastReading($this->entity);

        if (($this->extra && $this->value == '') || // Check for empty values here
            ($this->numeric  && ((float)  $this->lastReading == (float)  $this->value)) ||
            (!$this->numeric && ((string) $this->lastReading == (string) $this->value))) {
            // Skip not changed value since last write
            throw new Exception(null, 200);
        }
    }
}
