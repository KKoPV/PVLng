<?php
/**
 * Real access class for 'pvlng_tariff_view'
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
class TariffView extends TariffViewBase {

    /**
     *
     * @return int
     */
    public function getDateTS() {
        return strtotime($this->fields['date']);
    } // getDateTS()

}
