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
class Mobile extends \Controller {

    /**
     *
     */
    public function Index_Action() {
        // Switch layout
        $this->Layout = 'mobile';

        // Get views
        $q = \DBQuery::forge('pvlng_view')->whereEQ('public', 2)->order('name');
        $views = array();
        $tree = new \ORM\Tree;

        foreach ($this->db->queryRows($q) as $row) {

            $data = json_decode($row->data);

            $new_data = array();
            foreach ($data as $id=>$presentation) {
                if ($id == 'p') continue;

                // Get entity attributes
                $tree->find('id', $id);
                $new_data[] = array(
                    'id'           => +$tree->id,
                    'guid'         => $tree->guid,
                    'unit'         => $tree->unit,
                    'public'       => +$tree->public,
                    'presentation' => addslashes($presentation)
                );
            }

            $views[] = array(
                'NAME'    => $row->name,
                'PERIOD'  => $data->p,
                'DATA'    => json_encode($new_data)
            );

            if ($this->view->View1st == '') $this->view->View1st = $row->name;
        }

        $this->view->Views = $views;
    }

}
