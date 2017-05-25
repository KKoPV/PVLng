<?php
/**
 *
 *
 * @author      Knut Kohl <github@knutkohl.de>
 * @copyright   2012-2013 Knut Kohl
 * @license     GNU General Public License http://www.gnu.org/licenses/gpl.txt
 * @version     1.1.0
 *
 * v1.1.0
 * - Switch to InternalCalc to work correct with periods last/readlast
 *
 */
namespace Channel;

/**
 *
 */
class SensorToMeter extends InternalCalc
{
    /**
     * Accept only childs without meter attribute set
     */
    public function addChild($channel)
    {
        $childs = $this->getChilds();
        if (empty($childs)) {
            $new  = new \ORM\Channel($channel);
            if ($new->getType() == 0) {
                // Is an alias, get real channel
                $guid = $new->getChannel();
                $new = new \ORM\Tree;
                $new->filterByGuid($guid)->findOne();
            }

            if ($new->getMeter() == 1) {
                throw new \Exception('"SensorToMeter" accept only a non-meter channel as child!');
            }
        }
        // Add child or throw exception about only 1 child...
        return parent::addChild($channel);
    }

    /**
     *
     */
    protected function beforeRead(&$request)
    {
        parent::beforeRead($request);

        $child = $this->getChild(1);

        if ($child->type_id == 51 && $child->extra == 1) {
            $this->table[1] = 'pvlng_reading_num_calc';
            $this->entity = $child->entity;
            return;
        }

        if ($this->dataExists()) {
            return;
        }

        if (!$child->childs) {
            // Calc direct inside database, if child is a real channel
            $this->db->query('CALL pvlng_model_sensortometer({1}, {2})', $this->entity, $child->entity);
        } else {
            // Calc in PHP
            // Read out all data
            unset($request['period']);

            $last = $sum = 0;

            foreach ($child->read($request) as $row) {
                $sum += $last ? ($row['timestamp'] - $last) / 3600 * $row['data'] : 0;
                $last = $row['timestamp'];
                $this->saveValue($last, $sum);
            }
        }

        $this->dataCreated();
    }
}
