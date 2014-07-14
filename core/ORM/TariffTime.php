<?php
/**
 * Real access class for 'pvlng_tariff_time'
 *
 * To extend the functionallity, edit here
 *
 * @author     Knut Kohl <github@knutkohl.de>
 * @copyright  2014 Knut Kohl
 * @license    MIT License (MIT) http://opensource.org/licenses/MIT
 * @version    1.0.0
 *
 * 1.0.0
 * - Initial creation
 */
namespace ORM;

/**
 *
 */
class TariffTime extends TariffTimeBase {

    /**
     * Find all times for Id and a date
     *
     * @param mixed Field value(s)
     */
    public function findByIdDate( $id, $date, $order=array(), $type=self::INSTANCE ) {
        return $this->findMany(array('id', 'date'), array($id, $date), $order, $type=self::INSTANCE);
    }

}
