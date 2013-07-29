<?php
/**
 *
 *
 * @author      Knut Kohl <github@knutkohl.de>
 * @copyright   2012-2013 Knut Kohl
 * @license     GNU General Public License http://www.gnu.org/licenses/gpl.txt
 * @version     $Id: v1.0.0.2-22-g7bc4608 2013-05-05 22:07:15 +0200 Knut Kohl $
 */
class Index_Controller extends ControllerAuth {

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
				$child = $this->request('child')) {
			$this->Tree->insertChildNode($child, $parent);
		}
		$this->redirect('index');
	}


	/**
	 * Delete an entity from the tree
	 */
	public function Delete_Post_Action() {
		if ($id = $this->request('id')) {
			$this->Tree->DeleteNode($id);
		}
		$this->redirect('index');
	}

	/**
	 * Delete an entity and his childs from the tree
	 */
	public function DeleteBranch_Post_Action() {
		if ($id = $this->request('id')) {
			$this->Tree->DeleteBranch($id);
		}
		$this->redirect('index');
	}

	/**
	 * Move an entity down in tree
	 */
	public function MoveLeft_Post_Action() {
		if ($id = $this->request('id')) {
		    for ($i=$this->request('count', 1); $i>0; $i--) {
				$this->Tree->moveLft($id);
			}
		}
		$this->redirect('index');
	}

	/**
	 * Move an entity down in tree
	 */
	public function MoveRight_Post_Action() {
		if ($id = $this->request('id')) {
		    for ($i=$this->request('count', 1); $i>0; $i--) {
				$this->Tree->moveRgt($id);
			}
		}
		$this->redirect('index');
	}

	/**
	 * Move an entity up in tree
	 */
	public function MoveUp_Post_Action() {
		if ($id = $this->request('id')) {
			$this->Tree->moveUp($id);
		}
		$this->redirect('index');
	}

	/**
	 * Move an entity down in tree
	 */
	public function MoveDown_Post_Action() {
		if ($id = $this->request('id')) {
			$this->Tree->moveDown($id);
		}
		$this->redirect('index');
	}

	/**
	 *
	 */
	public function Login_Post_Action() {

		$hasher = new PasswordHash();

		if ($this->config->Admin_User == $this->request('user') AND
		    $hasher->CheckPassword($this->request('pass'), $this->config->Admin_Password)) {

			$this->User = $this->request('user');
			Session::set('user', $this->User);

			if ($this->request('save')) {
				setcookie(Session::token(), 1, time()+60*60*24*7, '/');
			}
			Messages::Success(I18N::_('Welcome', $this->User));

			if ($r = Session::get('returnto')) {
				// clear before redirect
				Session::set('returnto');
				$this->redirect($r);
			} else {
				$this->redirect('');
			}
			

		} else {
			Messages::Error(I18N::_('UnknownUser'));
		}
	}

	/**
	 *
	 */
	public function Logout_Action() {
		if ($this->User) {
			$this->view->Message = I18N::_('LogoutSuccessful', $this->User);
		}
		$this->User = '';
		Session::destroy();
		setcookie(Session::token(), '', time()-60*60*24, '/');
	}

	/**
	 *
	 */
	public function AdminPassword_POST_Action() {
		if ($this->request('u') == '' OR $this->request('p1') == '' OR $this->request('p2') == '') {
			Messages::Error(I18N::_('AdminAndPasswordRequired'), TRUE);
			return;
		}

		if ($this->request('p1') != $this->request('p2')) {
			Messages::Error(I18N::_('PasswordsNotEqual'), TRUE);
			return;
		}

		$hasher = new PasswordHash();
		$this->view->AdminPass = $hasher->HashPassword($this->request('p1'));
	}

	/**
	 *
	 */
	public function AdminPassword_Action() {
		$this->view->SubTitle = I18N::_('GenerateAdminHash');
		$this->view->AdminUser = $this->request('u');
	}

	// -------------------------------------------------------------------------
	// PROTECTED
	// -------------------------------------------------------------------------

	/**
	 *
	 */
	protected $Tree;

}
