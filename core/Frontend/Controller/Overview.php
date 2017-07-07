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
use Channel\Channel;
use Core\Messages;
use Core\Session;
use Frontend\Controller;
use ORM\ChannelView as ORMChannelView;
use ORM\Tree as ORMTree;
use Yryie\Yryie;
use DBQuery;
use I18N;

/**
 *
 */
class Overview extends Controller
{
    /**
     *
     */
    public function afterPost()
    {
        if ($returnto = Session::get('returnto')) {
            Session::set('returnto');
            $this->app->redirect($returnto);
        } else {
            $this->app->redirect('/overview');
        }
    }

    /**
     *
     */
    public function indexAction()
    {
        $this->view->SubTitle = I18N::_('Overview');

        /// Yryie::StartTimer('Load tree with parents', NULL, 'db');
        $this->view->Data = (new ORMTree)->getWithParents();
        /// Yryie::StopTimer();

        $channels = array();
        $tblChannels = new ORMChannelView;
        $tblChannels->filter('id', array('min'=>2))
                    ->order('type,name,description')
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
    }

    /**
     * Add an entity into the tree
     */
    public function addChildPostAction()
    {
        if ($parent = $this->request->post('parent') &&
            $childs = $this->request->post('child') && is_array($childs)) {
            $parent = Channel::byId($parent);
            foreach ($childs as $child) {
                if ($child) {
                    $parent->addChild($child);
                }
            }
        }
    }

    /**
     * Delete an entity (and his childs if exists) from the tree
     */
    public function deletePostAction()
    {
        if ($id = $this->request->post('id')) {
            if ($this->AliasInTree($id)) {
                Messages::error(I18N::translate('AliasStillInTree'), true);
            } else {
                // Alias channel if exists will be deleted by trigger,
                // because it is only valid for a channel in tree!
                $channel = Channel::byId($id, false);
                $channel->removeFromTree();
            }
        }
    }

    /**
     * Move/copy an channel/group
     */
    public function dragDropPostAction()
    {
        if (($target = $this->request->post('target')) == '') {
            return;
        }

        if ($id = $this->request->post('entity')) {
            // Add new single channel
            $this->adjustTarget($target, $id, $real_target, $offset);

            // Add as new child
            $parent = Channel::byId($real_target);
            $id = $parent->addChild($id);
        } elseif ($id = $this->request->post('id')) {
            // Move channel/group
            $this->adjustTarget($target, $id, $real_target, $offset);

            if (!$this->request->post('copy')) {
                // MOVE
                // Find target right
                $q = new DBQuery('pvlng_tree');
                $q->get('rgt')->whereEQ('id', $real_target);
                $rgt = $this->db->queryOne($q);

                // Find left and right of channel to move
                $q = new DBQuery('pvlng_tree');
                $q->whereEQ('id', $id);
                $data = $this->db->queryRow($q);

                $sql = str_replace(
                    array(':p',    ':l',       ':r'),
                    array($rgt, $data->lft, $data->rgt),
                    $this->MoveSubTreeSQL
                );
                // Move group/channel
                $this->db->query($sql);
            } else {
                // COPY
                // Add as new child
                $parent = Channel::byId($real_target);
                $id = $parent->addChild((new ORMTree($id))->getEntity());
            }
        }

        // Correct position below new target
        while ($offset-- > 0) {
            if (!$this->tree->moveLft($id)) {
                break;
            }
        }
    }

    /**
     * Move an entity down in tree
     */
    public function moveLeftPostAction()
    {
        if ($id = $this->request->post('id')) {
            // Set an off-wall high value, loop breaks anyway if can't move anymore...
            $count = $this->request->post('countmax') ? PHP_INT_MAX : $this->request->post('count', 1);
            while ($count--) {
                if (!$this->tree->moveLft($id)) {
                    break;
                }
            }
        }
    }

    /**
     * Move an entity down in tree
     */
    public function moveRightPostAction()
    {
        if ($id = $this->request->post('id')) {
            // Set an off-wall high value, loop breaks anyway if can't move anymore...
            $count = $this->request->post('countmax') ? PHP_INT_MAX : $this->request->post('count', 1);
            while ($count--) {
                if (!$this->tree->moveRgt($id)) {
                    break;
                }
            }
        }
    }

    /**
     * Move an entity up in tree
     */
    public function moveUpPostAction()
    {
        if ($id = $this->request->post('id')) {
            $this->tree->moveUp($id);
        }
    }

    /**
     * Move an entity down in tree
     */
    public function moveDownPostAction()
    {
        if ($id = $this->request->post('id')) {
            $this->tree->moveDown($id);
        }
    }

    // -------------------------------------------------------------------------
    // PROTECTED
    // -------------------------------------------------------------------------

    /**
     * Find out if channel to delete has an alias channel and
     * if the alias channel is still in tree
     */
    protected function aliasInTree($id)
    {
        $node = new ORMTree($id);
        if ($node->getAlias()) {
            $alias = new ORMTree;
            $alias->filterByEntity($node->getAlias())->findOne();
            return !!$alias->getId();
        }
        return false;
    }

    /**
     *
     */
    protected function adjustTarget($target, $id, &$real_target, &$offset)
    {
        $TreeTable = new ORMTree($target);

        $offset = 0;

        if ($target == 1 || $TreeTable->getChilds() != 0) {
            // Target accept childs, no change required
            $real_target = $target;
        } else {
            // Target accept NO childs, so find parent and calculate offset for later move
            $real_target = $this->tree->getParent($target);
            $real_target = $real_target['id'];

            // Search position of drop target in existing childs
            $holdsSelf = false;
            $childs = $this->tree->getChilds($real_target);
            foreach ($childs as $pos => $child) {
                if ($child['id'] == $id) {
                    $holdsSelf = $pos;
                }
                if ($child['id'] == $target) {
                    // Move dropped BEFORE target!
                    $offset = count($childs) - $pos;
                }
            }

            // If move inside same parrent, correct offset
            if ($holdsSelf !== false && $holdsSelf >= count($childs)-$offset) {
                $offset--;
            }
        }
    }

    /**
     * http://www.php-resource.de/forum/blogs/amicanoctis/25-nested-set-move-subtree.html
     *
     * moves a subtree before the specified position
     *   if the position is the lft of a node, the subtree will be inserted before
     *   if the position is the rgt of a node, the subtree will be its last child
     * @param p the position to move the subtree before
     * @param l the lft of the subtree to move
     * @param r the rgt of the subtree to move
     */
    private $MoveSubTreeSQL = '
        update pvlng_tree
        set
            lft = lft + if (:p > :r,
                if (:r < lft and lft < :p,
                    :l - :r - 1,
                    if (:l <= lft and lft < :r, :p - :r - 1, 0)
                ),
                if (:p <= lft and lft < :l,
                    :r - :l + 1,
                    if (:l <= lft and lft < :r, :p - :l, 0)
                )
            ),
            rgt = rgt + if (:p > :r,
                if (:r < rgt and rgt < :p,
                    :l - :r - 1,
                    if (:l < rgt and rgt <= :r, :p - :r - 1, 0)
                ),
                if (:p <= rgt and rgt < :l,
                    :r - :l + 1,
                    if (:l < rgt and rgt <= :r, :p - :l, 0)
                )
            )
        where :r < :p or :p < :l
    ';
}