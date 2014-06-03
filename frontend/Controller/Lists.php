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
class Lists extends \Controller {

    /**
     *
     */
    public function Index_Action() {
        $this->view->SubTitle = \I18N::_('List');

        $q = \DBQuery::forge('pvlng_tree_view')
             ->get('guid')->get('name')->get('description')
             ->get('type')->get('unit')->get('graph', 'available')->get('icon')
             ->get('CONCAT(REPEAT("&nbsp; &nbsp; ", `level`-2), IF(`haschilds`,"&nbsp;&bull;","&rarr;"), "&nbsp;")', 'indent')
             ->filter('`id` <> 1 AND `alias_of` IS NULL');

        $this->view->Channels = $this->db->queryRowsArray($q);

        try {
            if ($id = $this->app->params['id']) {
                $channel = \Channel::byChannel($id);
                $this->view->GUID = $channel->guid;
            } elseif ($guid = $this->app->params['guid']) {
                $channel = \Channel::byGUID($guid);
                $this->view->GUID = $channel->guid;
            }
        } catch(Exception $e) {
            \Messages::Info('Unknown channel');
        }

        $this->PresetAndPeriod();
    }
}
