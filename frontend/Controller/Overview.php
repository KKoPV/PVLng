<?php
/**
 *
 *
 * @author      Knut Kohl <github@knutkohl.de>
 * @copyright   2012-2013 Knut Kohl
 * @license     GNU General Public License http://www.gnu.org/licenses/gpl.txt
 * @version     $Id: v1.0.0.2-22-g7bc4608 2013-05-05 22:07:15 +0200 Knut Kohl $
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
	}

	/**
	 *
	 */
	public function Index_Action() {

		$this->view->SubTitle = \I18N::_('Overview');

		$tree = $this->Tree->getFullTree();
		array_shift($tree);

		$parent = array( 1 => 0 );

		$data = array();
		foreach ($tree as $i=>$node) {

			$parent[$node['level']] = $node['id'];
			$node['parent'] = $parent[$node['level']-1];

			if ($entity = $this->model->getEntity($node['entity'])) {

				$node['type']         = $entity->type;
				$node['name']         = $entity->name;
				$node['unit']         = $entity->unit;
				$node['description']  = $entity->description;
				$node['guid']         = $node['guid'] ?: $entity->guid;
				$node['acceptchilds'] = $entity->childs;
				$node['read']         = $entity->read;
				$node['write']        = $entity->write;
				$node['public']       = $entity->public;
				$node['icon']         = $entity->icon;

				if ($entity->model) {
					$e = \Channel::byId($node['id']);
					$node['name']        = $e->name;
					$node['description'] = $e->description;
					$node['unit']        = $e->unit;
					$node['icon']        = $e->icon;
				}

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
		}
		$this->redirect();
	}


	/**
	 * Delete an entity from the tree
	 */
	public function DeletePOST_Action() {
		if ($id = $this->request->post('id')) {
			$this->Tree->DeleteNode($id);
		}
		$this->redirect();
	}

	/**
	 * Delete an entity and his childs from the tree
	 */
	public function DeleteBranchPOST_Action() {
		if ($id = $this->request->post('id')) {
			$this->Tree->DeleteBranch($id);
		}
		$this->redirect();
	}

	/**
	 * Move an entity down in tree
	 */
	public function MoveLeftPOST_Action() {
		if ($id = $this->request->post('id')) {
			// Set an absurd high value, loop breaks anyway if can't move anymore...
			$count = $this->request->post('countmax') ? 99999 : $this->request->post('count', 1);
		    while ($count--) {
				if (!$this->Tree->moveLft($id)) break;
			}
		}
		$this->redirect();
	}

	/**
	 * Move an entity down in tree
	 */
	public function MoveRightPOST_Action() {
		if ($id = $this->request->post('id')) {
			// Set an absurd high value, loop breaks anyway if can't move anymore...
			$count = $this->request->post('countmax') ? 99999 : $this->request->post('count', 1);
		    while ($count--) {
				if (!$this->Tree->moveRgt($id)) break;
			}
		}
		$this->redirect();
	}

	/**
	 * Move an entity up in tree
	 */
	public function MoveUpPOST_Action() {
		if ($id = $this->request->post('id')) {
			$this->Tree->moveUp($id);
		}
		$this->redirect();
	}

	/**
	 * Move an entity down in tree
	 */
	public function MoveDownPOST_Action() {
		if ($id = $this->request->post('id')) {
			$this->Tree->moveDown($id);
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
}
