<?php
/**
 *
 *
 * @author      Knut Kohl <github@knutkohl.de>
 * @copyright   2012-2013 Knut Kohl
 * @license     GNU General Public License http://www.gnu.org/licenses/gpl.txt
 * @version     $Id: v1.0.0.2-22-g7bc4608 2013-05-05 22:07:15 +0200 Knut Kohl $
 */
class Overview_Controller extends ControllerAuth {

	/**
	 *
	 */
	public function before() {
		parent::before();
		$this->Tree = NestedSet::getInstance();
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

		$this->view->SubTitle = I18N::_('Overview');

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
				$node['icon']         = $entity->icon;
			}
			$data[] = array_change_key_case($node, CASE_UPPER);
		}
		$this->view->Data = $data;
	}

	/**
	 * Add an entity into the tree
	 */
	public function AddChild_Post_Action() {
		if ($parent = $this->request('parent') AND
		    $childs = $this->request('child') AND is_array($childs)) {
			foreach ($childs as $child) {
				if ($child) $this->Tree->insertChildNode($child, $parent);
            }
		}
		$this->redirect('overview');
	}


	/**
	 * Delete an entity from the tree
	 */
	public function Delete_Post_Action() {
		if ($id = $this->request('id')) {
			$this->Tree->DeleteNode($id);
		}
		$this->redirect('overview');
	}

	/**
	 * Delete an entity and his childs from the tree
	 */
	public function DeleteBranch_Post_Action() {
		if ($id = $this->request('id')) {
			$this->Tree->DeleteBranch($id);
		}
		$this->redirect('overview');
	}

	/**
	 * Move an entity down in tree
	 */
	public function MoveLeft_Post_Action() {
		if ($id = $this->request('id')) {
			// Set an absurd high value, loop breaks anyway if can't move anymore...
			$cnt = $this->request('countmax') ? 99999 : $this->request('count', 1);
		    for ($i=$cnt; $i>0; $i--) {
				if (!$this->Tree->moveLft($id)) break;
			}
		}
		$this->redirect('overview');
	}

	/**
	 * Move an entity down in tree
	 */
	public function MoveRight_Post_Action() {
		if ($id = $this->request('id')) {
			// Set an absurd high value, loop breaks anyway if can't move anymore...
			$cnt = $this->request('countmax') ? 99999 : $this->request('count', 1);
		    for ($i=$cnt; $i>0; $i--) {
				if (!$this->Tree->moveRgt($id)) break;
			}
		}
		$this->redirect('overview');
	}

	/**
	 * Move an entity up in tree
	 */
	public function MoveUp_Post_Action() {
		if ($id = $this->request('id')) {
			$this->Tree->moveUp($id);
		}
		$this->redirect('overview');
	}

	/**
	 * Move an entity down in tree
	 */
	public function MoveDown_Post_Action() {
		if ($id = $this->request('id')) {
			$this->Tree->moveDown($id);
		}
		$this->redirect('overview');
	}

	// -------------------------------------------------------------------------
	// PROTECTED
	// -------------------------------------------------------------------------

	/**
	 *
	 */
	protected $Tree;

}
