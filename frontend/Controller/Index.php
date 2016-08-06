<?php
/**
 *
 *
 * @author      Knut Kohl <github@knutkohl.de>
 * @copyright   2012-2014 Knut Kohl
 * @license     MIT License (MIT) http://opensource.org/licenses/MIT
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

        /// \Yryie::StartTimer('Load tree with parents and aliases', NULL, 'db');
        $this->view->Data = (new \ORM\Tree)->getWithParents();
        /// \Yryie::StopTimer();

        // Timezone offset in seconds
        $this->view->tzOffset = date('Z');

        $this->preparePresetAndPeriod();
    }
}
