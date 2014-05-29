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
class Dashboard extends \Controller {

    /**
     *
     */
    const CHANNEL_TYPE = 30; // Dashboard channel

    /**
     *
     */
    public function before() {
        $this->view->SubTitle = __('Dashboards');
    }

    /**
     *
     */
    public function IndexEmbedded_Action() {
        $this->view->Embedded = TRUE;
        $this->app->foreward('Index');
    }

    /**
     *
     */
    public function IndexPOST_Action() {
        $tblDashboard = new \ORM\Dashboard;

        if ($this->request->post('delete') AND $id = $this->request->post('id')) {
            $tblDashboard->filterById($id)->findOne();
            if ($tblDashboard->getId()) $tblDashboard->delete();
            /// foreach ($tblDashboard->queries() as $sql) \Yryie::SQL($sql);
        } else {
            $id = $this->request->post('save') ? $this->request->post('id') : 0;
            $name = $this->request->post('name');
            $channels = $this->request->post('c');
            try {
                if ($name == '') throw new Exception('Name is required.');
                if (empty($channels)) throw new Exception('No channels selected.');

                $tblDashboard
                    ->filterById($id)->findOne()
                    ->setName($name)
                    ->setPublic($this->request->post('public'))
                    ->setData(json_encode($channels))
                    ->replace();
                // Reload
                $tblDashboard = new \ORM\Dashboard($tblDashboard->getId()); // AutoInc
                $this->app->redirect('/dashboard/'.$tblDashboard->findOne()->getSlug());
            } catch (Exception $e) {
                \Messages::Error($e->getMessage());
            }
        }
        $this->app->redirect('/dashboard');
    }

    /**
     *
     */
    public function Index_Action() {

        $tblDashboard = new \ORM\Dashboard;
        $tblDashboard->filterBySlug($this->app->params->get('slug'))->findOne();
        if ($name = $tblDashboard->getName()) {
            $this->view->SubTitle = $name;
            $data = array_flip(json_decode($tblDashboard->getData(), TRUE));
            $this->view->Id     = $tblDashboard->getId();
            $this->view->Name   = $name;
            $this->view->Public = $tblDashboard->getPublic();
            $this->view->Slug   = $tblDashboard->getSlug();
        } else {
            $data = array();
        }

        $this->view->ChannelCount = count($data);

        $tree = new \ORM\Tree;
        foreach ($tree->filterByTypeId(self::CHANNEL_TYPE)->find() as $channel) {
            $id = $channel->getId();
            $channel = $channel->asAssoc();
            if (array_key_exists($id, $data)) {
                $channel['checked'] = 'checked';
            }
            $data[$id] = $channel;
        }

        $this->view->Data = $data;
    }

}
