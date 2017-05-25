<?php
/**
 *
 *
 * @author      Knut Kohl <github@knutkohl.de>
 * @copyright   2012-2014 Knut Kohl
 * @license     MIT License (MIT) http://opensource.org/licenses/MIT
 * @version     1.0.0
 */
namespace Frontend\Controller;

/**
 *
 */
use Frontend\Controller;
use ORM\Tree as ORMTree;
use Yryie\Yryie;
use I18N;

/**
 *
 */
class Index extends Controller
{
    /**
     *
     */
    public function indexAction()
    {
        $this->view->SubTitle = I18N::translate('Charts');

        /// Yryie::StartTimer('Load tree with parents and aliases', NULL, 'db');
        $this->view->Data = (new ORMTree)->getWithParents();
        /// Yryie::StopTimer();

        // Timezone offset in seconds
        $this->view->tzOffset = date('Z');

        $this->preparePresetAndPeriod();
    }
}
