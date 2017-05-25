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
use ORM\View as ORMView;
use DBQuery;

/**
 *
 */
class Mobile extends Controller
{
    /**
     *
     */
    public function indexAction()
    {
        // Switch layout
        $this->Layout = 'mobile';

        $ORMTree = new ORMTree;

        // Get views
        $ORMView = new ORMView;
        $ORMView->filterByPublic(2)->order('name');

        $views = array();

        foreach ($ORMView->find() as $row) {
            $data = json_decode($row->data);

            $new_data = array();
            foreach ($data as $id => $presentation) {
                if ($id == 'p') {
                    continue;
                }

                // Get entity attributes
                $ORMTree->reset()->filterById($id)->findOne();
                $new_data[] = array(
                    'id'           => +$ORMTree->id,
                    'guid'         => $ORMTree->guid,
                    'unit'         => $ORMTree->unit,
                    'public'       => +$ORMTree->public,
                    'presentation' => addslashes($presentation)
                );
            }

            $views[] = array(
                'name'   => $row->name,
                'period' => $data->p,
                'data'   => json_encode($new_data)
            );

            if ($this->view->View1st == '') {
                $this->view->View1st = $row->name;
            }
        }

        $this->view->Views = $views;
    }
}
