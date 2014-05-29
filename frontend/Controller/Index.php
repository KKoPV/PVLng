<?php /* // AOP // */
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
class Index extends \Controller {

    /**
     *
     */
    public function Index_Action() {
        $this->view->SubTitle = \I18N::_('Charts');

        /// \Yryie::StartTimer('LoadTreeWithParents', 'Load tree with parents and aliases', 'db');
        $this->view->Data = (new \ORM\Tree)->getWithParents();
        /// \Yryie::StopTimer('LoadTreeWithParents');

        $this->view->NotifyLoad = $this->config->get('Controller.Chart.NotifyLoad');
        $this->PresetAndPeriod();
    }
}
