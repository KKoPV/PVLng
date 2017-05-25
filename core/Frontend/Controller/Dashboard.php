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
use Core\Messages;
use Frontend\Controller;
use ORM\Dashboard as ORMDashboard;
use ORM\Tree as ORMTree;
use PVLng\PVLng;
use Yryie\Yryie;
use I18N;

/**
 *
 */
class Dashboard extends Controller
{
    /**
     *
     */
    const TYPE = 30; // Dashboard channel type

    /**
     *
     */
    public function before()
    {
        $this->view->SubTitle = I18N::translate('Dashboards');
    }

    /**
     *
     */
    public function indexEmbeddedAction()
    {
        $this->view->Embedded = true;
        $this->app->foreward('Index');
    }

    /**
     *
     */
    public function indexPostAction()
    {
        $tblDashboard = new ORMDashboard;

        if ($this->request->post('delete') && $id = $this->request->post('id')) {
            $tblDashboard->filterById($id)->findOne();
            if ($tblDashboard->getId()) {
                $tblDashboard->delete();
            }
            /// foreach ($tblDashboard->queries() as $sql) Yryie::SQL($sql);
        } else {
            $id = $this->request->post('save') ? $this->request->post('id') : 0;
            $name = $this->request->post('name');
            $channels = $this->request->post('c');
            try {
                if ($name == '') {
                    throw new Exception('Name is required.');
                }
                if (empty($channels)) {
                    throw new Exception('No channels selected.');
                }

                $tblDashboard
                    ->filterById($id)->findOne()
                    ->setName($name)
                    ->setPublic($this->request->post('public'))
                    ->setData(json_encode($channels))
                    ->replace();
                // Reload
                $tblDashboard = new ORMDashboard($tblDashboard->getId()); // AutoInc
                $this->app->redirect('/dashboard/'.$tblDashboard->findOne()->getSlug());
            } catch (Exception $e) {
                Messages::Error($e->getMessage());
            }
        }
        $this->app->redirect('/dashboard');
    }

    /**
     *
     */
    public function indexAction()
    {
        $tblDashboard = new ORMDashboard;
        $tblDashboard->filterBySlug($this->app->params->get('slug'))->findOne();
        if ($name = $tblDashboard->getName()) {
            $this->view->SubTitle = $name;
            $data = array_flip(json_decode($tblDashboard->getData(), true));
            $this->view->Id     = $tblDashboard->getId();
            $this->view->Name   = $name;
            $this->view->Public = $tblDashboard->getPublic();
            $this->view->Slug   = $tblDashboard->getSlug();
        } else {
            $data = array();
        }

        $this->view->ChannelCount = count($data);

        $tree = new ORMTree;
        $tree->filterByTypeId(self::TYPE);

        foreach ($tree->find() as $channel) {
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
