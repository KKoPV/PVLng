<?php
/**
 * Scatter plots
 *
 * @author      Knut Kohl <github@knutkohl.de>
 * @copyright   2012-2016 Knut Kohl
 * @license     MIT License (MIT) http://opensource.org/licenses/MIT
 * @version     1.0.0
 */
namespace Frontend\Controller;

/**
 *
 */
use Frontend\Controller;
use ORM\ChannelView as ORMChannelView;
use I18N;

/**
 *
 */
class Scatter extends Controller
{
    /**
     *
     */
    public function indexAction()
    {
        $this->view->SubTitle = I18N::translate('ScatterCharts');

        $ORMChannels = new ORMChannelView;
        $ORMChannels->filterById(array('min' => 2)) // without root node
                    ->filterByTypeId(array('min' => 1)) // no aliases
                    ->filterByChilds(0) // real channels
                    ->filterByWrite(1)  // real channels
                    ->order('type,name,description')
                    ->find();

        $channels = array();

        foreach ($ORMChannels->asAssoc() as $channel) {
            $type = $channel['type_id'];
            if (!isset($channels[$type])) {
                $channels[$type] = array(
                    'type'    => $channel['type'],
                    'members' => array()
                );
            }
            $channels[$type]['members'][] = $channel;
        }

        $this->view->Channels = $channels;

        // Timezone offset in seconds
        $this->view->tzOffset = date('Z');
    }
}
