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
class Info extends \Controller {

    // -------------------------------------------------------------------------
    // PUBLIC
    // -------------------------------------------------------------------------

    /**
     *
     */
    public function IndexPost_Action() {
        if ($this->request->post('regenerate')) {
            $this->model->resetAPIkey();
            \Messages::Success(\I18N::_('APIkeyRegenerated'));
        }
        $this->app->redirect('info');
    }

    /**
     *
     */
    public function Index_Action() {
        $this->view->SubTitle   = __('Information');
        $this->view->ServerName = $_SERVER['SERVER_NAME'];
        $this->view->Stats      = (new \ORM\ReadingStatistics)->find()->asAssoc();
    }

}
