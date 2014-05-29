<?php /* // AOP // */
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
class Overview extends \Controller {

    /**
     *
     */
    public function __construct() {
        parent::__construct();

        $this->Tree = \NestedSet::getInstance();
    }

    /**
     *
     */
    public function afterPOST() {
//         if ($this->Tree->isError()) {
//             foreach ($this->Tree->getError() as $value) {
//                 if (strstr($value, 'NestedSet::checkRootNode()') == '')
//                     \Messages::Error($value);
//             }
//         }
//
        $this->app->redirect('/overview');
    }

    /**
     *
     */
    public function Index_Action() {
        $this->view->SubTitle = \I18N::_('Overview');

        /// \Yryie::StartTimer('LoadTreeWithParents', 'Load tree with parents', 'db');
        $this->view->Data = (new \ORM\Tree)->getWithParents();
        /// \Yryie::StopTimer('LoadTreeWithParents');

        $channels = array();
        $tblChannels = new \ORM\ChannelView;
        $tblChannels->filterById(array('min'=>2))
                    ->order('type')->order('name')->order('description')
                    ->find();
        foreach ($tblChannels->asAssoc() as $channel) {
            $type = $channel['type_id'];
            if (!isset($channels[$type])) {
                $channels[$type] = array(
                    'type'    => $channel['type'],
                    'memeber' => array()
                );
            }
            $channels[$type]['member'][] = $channel;
        }
        $this->view->Channels = $channels;

    }

    /**
     * Add an entity into the tree
     */
    public function AddChildPOST_Action() {
        if ($parent = $this->request->post('parent') AND
            $childs = $this->request->post('child') AND is_array($childs)) {
            $parent = \Channel::byId($parent);
            foreach ($childs as $child) {
                if ($child) $parent->addChild($child);
            }
        }
    }

    /**
     * Delete an entity (and his childs if exists) from the tree
     */
    public function DeletePOST_Action() {
        if ($id = $this->request->post('id')) {
            if ($this->AliasInTree($id)) {
                \Messages::Error(__('AliasStillInTree'), TRUE);
            } else {
                // Alias channel if exists will be deleted by trigger,
                // because it is only valid for a channel in tree!
                $channel = \Channel::byId($id, FALSE);
                $channel->removeFromTree();
            }
        }
    }

    /**
     * Move/copy an channel/group
     */
    public function DragDropPOST_Action() {
        if (($target = $this->request->post('target')) == '') return;


        if ($id = $this->request->post('entity')) {
            // Add new single channel
            $this->adjustTarget($target, $id, $real_target, $offset);

            // Add as new child
            $parent = \Channel::byId($real_target);
            $id = $parent->addChild($id);

        } elseif ($id = $this->request->post('id')) {
            // Move channel/group
            $this->adjustTarget($target, $id, $real_target, $offset);

            if (!$this->request->post('copy')) {
                // MOVE
                // Find target right
                $q = new \DBQuery('pvlng_tree');
                $q->get('rgt')->whereEQ('id', $real_target);
                $rgt = $this->db->queryOne($q);

                // Find left and right of channel to move
                $q = new \DBQuery('pvlng_tree');
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
                $parent = \Channel::byId($real_target);
                $id = $parent->addChild((new \ORM\Tree($id))->getEntity());
            }
        }

        // Correct position below new target
        while ($offset-- > 0) {
            if (!$this->Tree->moveLft($id)) break;
        }
    }

    /**
     * Move an entity down in tree
     */
    public function MoveLeftPOST_Action() {
        if ($id = $this->request->post('id')) {
            // Set an off-wall high value, loop breaks anyway if can't move anymore...
            $count = $this->request->post('countmax') ? PHP_INT_MAX : $this->request->post('count', 1);
            while ($count--) {
                if (!$this->Tree->moveLft($id)) break;
            }
        }
    }

    /**
     * Move an entity down in tree
     */
    public function MoveRightPOST_Action() {
        if ($id = $this->request->post('id')) {
            // Set an off-wall high value, loop breaks anyway if can't move anymore...
            $count = $this->request->post('countmax') ? PHP_INT_MAX : $this->request->post('count', 1);
            while ($count--) {
                if (!$this->Tree->moveRgt($id)) break;
            }
        }
    }

    /**
     * Move an entity up in tree
     */
    public function MoveUpPOST_Action() {
        if ($id = $this->request->post('id')) {
            $this->Tree->moveUp($id);
        }
    }

    /**
     * Move an entity down in tree
     */
    public function MoveDownPOST_Action() {
        if ($id = $this->request->post('id')) {
            $this->Tree->moveDown($id);
        }
    }

    // -------------------------------------------------------------------------
    // PROTECTED
    // -------------------------------------------------------------------------

    /**
     *
     */
    protected $Tree;

    /**
     * Find out if channel to delete has an alias channel and
     * if the alias channel is still in tree
     */
    protected function AliasInTree( $id ) {
        $node = new \ORM\Tree($id);
        if ($node->getAlias()) {
            $alias = new \ORM\Tree;
            $alias->filterByEntity($node->getAlias())->findOne();
            return !!$alias->getId();
        }
        return FALSE;
    }

    /**
     *
     */
    protected function adjustTarget( $target, $id, &$real_target, &$offset ) {
        $TreeTable = new \ORM\Tree($target);

        $offset = 0;

        if ($target == 1 OR $TreeTable->getChilds() != 0) {
            // Target accept childs, no change required
            $real_target = $target;
        } else {
            // Target accept NO childs, so find parent and calculate offset for later move
            $real_target = $this->Tree->getParent($target);
            $real_target = $real_target['id'];

            // Search position of drop target in existing childs
            $holdsSelf = FALSE;
            $childs = $this->Tree->getChilds($real_target);
            foreach ($childs as $pos=>$child) {
                if ($child['id'] == $id) {
                    $holdsSelf = $pos;
                }
                if ($child['id'] == $target) {
                    // Move dropped BEFORE target!
                    $offset = count($childs) - $pos;
                }
            }

            // If move inside same parrent, correct offset
            if ($holdsSelf !== FALSE AND $holdsSelf >= count($childs)-$offset) $offset--;
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
        where :r < :p or :p < :l';

}
