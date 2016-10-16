<?php
/**
 * Scatter plots
 *
 * @author      Knut Kohl <github@knutkohl.de>
 * @copyright   2012-2016 Knut Kohl
 * @license     MIT License (MIT) http://opensource.org/licenses/MIT
 * @version     1.0.0
 */
namespace Controller;

/**
 *
 */
class Scatter extends \Controller
{
    /**
     *
     */
    public function Index_Action()
    {
        $this->view->SubTitle = \I18N::_('ScatterCharts');

        $channels = array();
        $tblChannels = new \ORM\ChannelView;
        $tblChannels->filter('id', array('min' => 2)) // without root node
                    ->filter('type_id', array('min' => 1)) // no aliases
                    ->filterByChilds(0) // real channels
                    ->filterByWrite(1)  // real channels
                    ->order('type')->order('name')->order('description')
                    ->find();

        foreach ($tblChannels->asAssoc() as $channel) {
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
