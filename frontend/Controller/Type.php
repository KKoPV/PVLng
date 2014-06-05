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
class Type extends \Controller {

    /**
     *
     */
    public function Index_Action() {
        $this->view->SubTitle = __('ChannelTypes');

        $q = new \DBQuery('pvlng_type');

        $this->view->Types = array_map(
            function ($a) { $a['description'] = __($a['description']); return $a; },
            $this->db->queryRowsArray($q->filter('id', array('gt' => 0))->order('id'))
        );
    }

    /**
     *
     */
    public function Detail_Action() {
    }
}