<?php
/* // AOP // */
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
    public function after() {
        if ($this->Tree->isError()) {
            foreach ($this->Tree->getError() as $value) {
                if (strstr($value, 'NestedSet::checkRootNode()') == '')
                    \Messages::Error($value);
            }
        }

        $this->view->Entities = $this->rows2view($this->model->getEntities());
        parent::after();
    }

    /**
     *
     */
    public function Index_Action() {
        $this->view->SubTitle = \I18N::_('Overview');

        /// \Yryie::StartTimer('LoadTree', NULL, 'CacheDB');
        while ($this->app->cache->save('Tree', $tree)) {
            $tree = \NestedSet::getInstance()->getFullTree();
            /// \Yryie::Info('Loaded tree from Database');
            // Skip root node
            array_shift($tree);
        }
        /// \Yryie::StopTimer('LoadTree');

        /// \Yryie::StartTimer('BuildTree');
        $parent = array( 1 => 0 );

        // Buffer channels for less database access
        $buffer = array();
        $q = new \DBQuery('pvlng_tree_view');
        if ($res = $this->db->query($q)) {
            while($row = $res->fetch_assoc()) {
                $buffer[$row['entity']] = $row;
            }
        }

        $channel = new \ORM\Tree;

        $data = array();
        foreach ($tree as $i=>$node) {

            $parent[$node['level']] = $node['id'];
            $node['parent'] = $parent[$node['level']-1];
            $node['childcount'] = $node['childs'];

            $id = $node['id'];
            $entity = $node['entity'];

            $attr = $buffer[$entity];
            $guid = $node['guid'] ?: $attr['guid'];

            $node = array_merge($node, $attr);
            $node['id'] = $id;
            $node['guid'] = $guid;

            $data[] = array_change_key_case($node, CASE_UPPER);
        }
        /// \Yryie::Debug('Channel Buffer: '.count($buffer));
        /// \Yryie::Debug('Channel count: '.count($tree));
        /// \Yryie::StopTimer('BuildTree');
        $this->view->Data = $data;
    }

    /**
     * Add an entity into the tree
     */
    public function AddChildPOST_Action() {
        if ($parent = $this->request->post('parent') AND
            $childs = $this->request->post('child') AND is_array($childs)) {
            foreach ($childs as $child) {
                if ($child) $this->Tree->insertChildNode($child, $parent);
            }
            $this->app->cache->delete('Tree');
        }
        $this->redirect();
    }

    /**
     * Delete an entity from the tree
     */
    public function DeletePOST_Action() {
        if ($id = $this->request->post('id')) {
            if ($this->AliasInTree($id)) {
                \Messages::Error(__('AliasStillInTree'), TRUE);
            } else {
                // Alias channel if exists will be deleted by trigger,
                // because it is only valid for a channel in tree!
                $this->Tree->DeleteNode($id);
                $this->app->cache->delete('Tree');
            }
        }
        $this->redirect();
    }

    /**
     * Delete an entity and his childs from the tree
     */
    public function DeleteBranchPOST_Action() {
        if ($id = $this->request->post('id')) {
            if ($this->AliasInTree($id)) {
                \Messages::Error(__('AliasStillInTree'), TRUE);
            } else {
                // Alias channel if exists will be deleted by trigger,
                // because it is only valid for a channel in tree!
                $this->Tree->DeleteBranch($id);
                $this->app->cache->delete('Tree');
            }
        }
        $this->redirect();
    }

    /**
     * Move an entity to new parent
     */
    public function DragDropPOST_Action() {
        if ($target = $this->request->post('target')) {
            // Root dummy accept childs...
            $targetAcceptChilds = ($target == 1 OR \ORM::forge('Tree')->find('id', $target)->childs != 0);

            if ($entity = $this->request->post('entity')) {
                // Add new channel

                if ($targetAcceptChilds) {
                    // Add as new child
                    $this->Tree->insertChildNode($entity, $target);
                } else {
                    // Get full path to drop target
                    $path = $this->Tree->getPathFromRoot($target);

                    // Get parent of drop target > second to last of path
                    $target_new = array_slice($path, count($path)-2, 1);
                    $target_new = $target_new[0]['id'];

                    // Search position of drop target in existing childs
                    $childs = $this->Tree->getChilds($target_new);
                    foreach ($childs as $pos=>$child) {
                        if ($child['id'] == $target) break;
                    }

                    // 1st Add as new child
                    $new = $this->Tree->insertChildNode($entity, $target_new);

                    // 2nd Move to correct position
                    $count = count($childs) - $pos - 1;
                    while ($count--) {
                        if (!$this->Tree->moveLft($new)) break;
                    }
                }

            } elseif ($id = $this->request->post('id')) {
                // Move channel/group

                $delta = 0;

                if (!$targetAcceptChilds) {
                    // Get full path to drop target
                    $path = $this->Tree->getPathFromRoot($target);

                    // Get parent of drop target > second to last of path
                    $target_new = array_slice($path, count($path)-2, 1);
                    $target_new = $target_new[0]['id'];

                    // Search position of drop target in existing childs
                    $childs = $this->Tree->getChilds($target_new);
                    $holdsSelf = FALSE;
                    foreach ($childs as $pos=>$child) {
                        if ($child['id'] == $id) {
                            $holdsSelf = $pos;
                        }
                        if ($child['id'] == $target) {
                            $delta = count($childs) - $pos - 1;
                        }
                    }

                    $target = $target_new;
                    // If move inside same parrent, correct delta
                    if ($holdsSelf !== FALSE AND $holdsSelf >= count($childs)-$delta) $delta--;
                }

                // Find target right
                $q = new \DBQuery('pvlng_tree');
                $q->get('rgt')->whereEQ('id', $target);
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

                $this->db->query($sql);

                while ($delta-- > 0) {
                    if (!$this->Tree->moveLft($id)) break;
                }
            }

            $this->app->cache->delete('Tree');
        }

        $this->redirect();
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
            $this->app->cache->delete('Tree');
        }
        $this->redirect();
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
            $this->app->cache->delete('Tree');
        }
        $this->redirect();
    }

    /**
     * Move an entity up in tree
     */
    public function MoveUpPOST_Action() {
        if ($id = $this->request->post('id')) {
            $this->Tree->moveUp($id);
            $this->app->cache->delete('Tree');
        }
        $this->redirect();
    }

    /**
     * Move an entity down in tree
     */
    public function MoveDownPOST_Action() {
        if ($id = $this->request->post('id')) {
            $this->Tree->moveDown($id);
            $this->app->cache->delete('Tree');
        }
        $this->redirect();
    }

    // -------------------------------------------------------------------------
    // PROTECTED
    // -------------------------------------------------------------------------

    /**
     *
     */
    protected $Tree;

    /**
     * Hard redirect
     */
    public function redirect() {
        Header('Location: /overview');
        exit;
    }

    /**
     * Find out if channel to delete has an alias channel and
     * if the alias channel is still in tree
     */
    protected function AliasInTree( $id ) {
        $node = new \ORM\Tree;
        return ($node->find('id', $id)->alias AND
                $node->find('entity', $node->alias)->id);
    }

}
