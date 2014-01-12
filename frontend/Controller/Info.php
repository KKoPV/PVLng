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
        $this->view->set('SubTitle', \I18N::_('Information'));
        $this->view->set('ServerName', $_SERVER['SERVER_NAME']);
        $this->view->set('APIkey', $this->model->getAPIkey());

        $rows = $this->model->getReadingCounts();
        $this->view->set('Stats', $this->rows2view($rows));

        $readings = 0;
        foreach ($rows as $id=>$row) {
            $readings += $row->readings;
        }
        $this->view->set('readings', $readings);
        $this->view->ChannelCount = $id+1;
    }

}
