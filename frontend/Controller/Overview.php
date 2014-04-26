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
        while ($this->app->cache->save('OverviewTree', $data)) {
            /// \Yryie::Info('Reload tree from Database');

            $q = new \DBQuery('pvlng_tree_view');
            $q->whereNE('id', 1);
            $res = $this->db->query($q);

            $parent = array( 1 => '' );

            while ($node = $res->fetch_assoc()) {

                $parent[$node['level']] = $node['id'];
                $node['parent'] = $parent[$node['level']-1];

                $data[] = array_change_key_case($node, CASE_UPPER);
            }
        }
        /// \Yryie::StopTimer('LoadTree');

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
            $this->app->cache->delete('OverviewTree');
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
                $this->app->cache->delete('OverviewTree');
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
                $this->app->cache->delete('OverviewTree');
            }
        }
        $this->redirect();
    }

    /**
     *
     */
    protected function adjustTarget( $target, $id, &$real_target, &$offset ) {
        $TreeTable = new \ORM\Tree($target);

        $offset = 0;

        if ($target == 1 OR $TreeTable->childs != 0) {
            // Target accept childs, no change required
            $real_target = $target;
        } else {
            // Target accept NO childs, so find parent and calculate offset for later move
            // Get full path to drop target
            $path = $this->Tree->getPathFromRoot($target);

            // Get parent of drop target > second to last of path
            $real_target = array_slice($path, count($path)-2, 1);
            $real_target = $real_target[0]['id'];

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

            // If move inside same parrent, correct delta
            if ($holdsSelf !== FALSE AND $holdsSelf >= count($childs)-$offset) $offset--;
        }
    }

    /**
     * Move/copy an channel/group
     */
    public function DragDropPOST_Action() {
        if ($target = $this->request->post('target')) {

            if ($id = $this->request->post('entity')) {
                // Add new single channel
                $this->adjustTarget($target, $id, $real_target, $offset);

                // Add as new child
                $id = $this->Tree->insertChildNode($id, $real_target);

                // Correct position below new target
                while ($offset-- > 0) {
                    if (!$this->Tree->moveLft($id)) break;
                }

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

                    // Correct position below new target
                    while ($offset-- > 0) {
                        if (!$this->Tree->moveLft($id)) break;
                    }

                } else {
                    // COPY
                    // Find entity for dropped id
                    $q = new \DBQuery('pvlng_tree');
                    $q->get('entity')->whereEQ('id', $id);
                    $id = $this->Tree->insertChildNode($this->db->queryOne($q), $real_target);

                    // Correct position below new target
                    while ($offset-- > 0) {
                        if (!$this->Tree->moveLft($id)) break;
                    }
                }
            }

            $this->app->cache->delete('OverviewTree');
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
            $this->app->cache->delete('OverviewTree');
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
            $this->app->cache->delete('OverviewTree');
        }
        $this->redirect();
    }

    /**
     * Move an entity up in tree
     */
    public function MoveUpPOST_Action() {
        if ($id = $this->request->post('id')) {
            $this->Tree->moveUp($id);
            $this->app->cache->delete('OverviewTree');
        }
        $this->redirect();
    }

    /**
     * Move an entity down in tree
     */
    public function MoveDownPOST_Action() {
        if ($id = $this->request->post('id')) {
            $this->Tree->moveDown($id);
            $this->app->cache->delete('OverviewTree');
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
