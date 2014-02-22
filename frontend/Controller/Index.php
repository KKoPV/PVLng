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
class Index extends \Controller {

    /**
     *
     */
    public function Index_Action() {
        $this->view->SubTitle = \I18N::_('Charts');

        while ($this->app->cache->save('Tree', $tree)) {
            $tree = \NestedSet::getInstance()->getFullTree();
            // Skip root node
            array_shift($tree);
        }

        $channel = new \ORM\ChannelView;

        $parent = array( 1 => 0 );
        $data = array();
        foreach ($tree as $node) {

            $parent[$node['level']] = $node['id'];
            $node['parent'] = $parent[$node['level']-1];
            $id = $node['id'];

            while ($this->app->cache->save('ChannelView'.$node['entity'], $attr)) {
                $attr = $channel->find('id', $node['entity'])->getAll();
            }
            $guid = $node['guid'] ?: $attr['guid'];

            $node = array_merge($node, $attr);
            $node['id'] = $id;
            $node['guid'] = $guid;

            $data[] = array_change_key_case($node, CASE_UPPER);
        }
        $this->view->Data = $data;

        $this->view->NotifyLoad = $this->config->Controller_Chart_NotifyLoad;

        $bk = \BabelKitMySQLi::getInstance();

        while ($this->app->cache->save('preset/'.LANGUAGE, $preset)) {
            $preset = $bk->select('preset', LANGUAGE);
        }
        $this->view->PresetSelect = $preset;

        while ($this->app->cache->save('period/'.LANGUAGE, $period)) {
            $period = $bk->select('period', LANGUAGE);
        }
        $this->view->PeriodSelect = $period;
    }
}
