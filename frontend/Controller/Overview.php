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
                    Messages::Error($value);
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

        while ($this->app->cache->save('Tree', $tree)) {
            $tree = \NestedSet::getInstance()->getFullTree();
            // Skip root node
            array_shift($tree);
        }

        $parent = array( 1 => 0 );

        $entity = new \ORM\Tree;

        $data = array();
        foreach ($tree as $i=>$node) {

            $parent[$node['level']] = $node['id'];
            $node['parent'] = $parent[$node['level']-1];

            if ($entity->find('id', $node['id'])) {
                $node['type']         = $entity->type;
                $node['name']         = $entity->name;
                $node['unit']         = $entity->unit;
                $node['description']  = $entity->description;
                $node['guid']         = $entity->guid;
                $node['acceptchilds'] = $entity->childs;
                $node['read']         = $entity->read;
                $node['write']        = $entity->write;
                $node['public']       = $entity->public;
                $node['icon']         = $entity->icon;
                $node['alias']        = $entity->alias;
            }
            $data[] = array_change_key_case($node, CASE_UPPER);
        }
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
        if ($id = $this->request->post('id')) {
            // Remove from old position
            $this->Tree->DeleteNode($id);
            $this->app->cache->delete('Tree');
        }

        if ($target = $this->request->post('target') AND
            $entity = $this->request->post('entity')) {

            if (\ORM::forge('Tree')->find('id', $target)->childs != 0) {
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
            $this->app->cache->delete('Tree');
        }

        $this->redirect();
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
