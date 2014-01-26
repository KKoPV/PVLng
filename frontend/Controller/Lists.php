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
             ->get('guid')
             ->get('name')
             ->get('type')
             ->get('description')
             ->get('unit')
             ->get('graph', 'available')
             ->get('CONCAT(REPEAT("&nbsp; &nbsp; ", `level`-2), IF(`haschilds`,"&bull; ","&rarr;"), "&nbsp;")', 'indent')
             ->whereNE('id', 1)
             ->whereNULL('alias_of');

        $this->view->Channels = $this->rows2view($this->db->queryRows($q));

        if ($id = $this->app->params['id']) {
            try {
                $channel = \Channel::byChannel($id);
                $this->view->GUID = $channel->guid;
            } catch(Exception $e) {
                Messages::Info('Unknown channel');
            }
        }

        $bk = \BabelKitMySQLi::getInstance();

        $this->view->PresetSelect = $bk->select(
            'preset',
            LANGUAGE,
            array(
                'value'    => '-',
                'options'  => 'id="preset"'
            )
        );

        $this->view->PeriodSelect = $bk->select(
            'period',
            LANGUAGE,
            array(
                'options' => 'id="period"'
            )
        );

        parent::after();
    }
}
