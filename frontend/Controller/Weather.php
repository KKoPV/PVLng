<?php
/**
 *
 *
 * @author      Knut Kohl <github@knutkohl.de>
 * @copyright   2012-2013 Knut Kohl
 * @license     GNU General Public License http://www.gnu.org/licenses/gpl.txt
 * @version     1.0.0
 */
namespace Controller;

/**
 *
 */
class Weather extends \Controller {

    /**
     *
     */
    public function Index_Action() {
        $this->view->SubTitle = __('WeatherForecast');
    }
}
