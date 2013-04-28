<?php
/**
 *
 *
 * @author      Knut Kohl <github@knutkohl.de>
 * @copyright   2012-2013 Knut Kohl
 * @license     GNU General Public License http://www.gnu.org/licenses/gpl.txt
 * @version     $Id$
 */
class Channel_Controller extends ControllerAuth {

	/**
	 *
	 */
	public function before() {
		parent::before();

		$this->view->Entities = $this->rows2view($this->model->getEntities());

		$fields = include __DIR__ . DS . 'channel.conf.php';

		$this->fields = array();

		foreach ($fields as $key=>$field) {
			$field = array_merge(array(
				'visible'  => TRUE,
				'field'    => $key,
				'radio'    => FALSE,
				'default'  => '',
				'required' => FALSE,
				'Name'     => I18N::_('channel::'.$key),
				'Hint'     => I18N::_('channel::'.$key.'Hint'),
			), $field);

			$this->fields[$key] = array_change_key_case($field, CASE_UPPER);
		}
	}

	/**
	 *
	 */
	public function Index_Action() {
		$this->view->SubTitle = I18N::_('Channels');
	}

	/**
	 *
	 */
	public function Add_Post_Action() {
		if ($this->type = $this->request('type')) {
			foreach ($this->fields as &$data) {
				$data['VALUE'] = $data['DEFAULT'];
			}

			/* preset channel unit */
			$q = new DBQuery('entity_type', 'unit');
			$q->whereEQ('id', $this->type);
			$this->fields['unit']['VALUE'] = $this->db->queryOne($q);

			$this->view->Fields = $this->fields;
			$this->foreward('Edit');
		}
	}

	/**
	 *
	 */
	public function Add_Action() {
		$this->view->SubTitle = I18N::_('CreateChannel');

		$q = new DBQuery;
		$q->select('pvlng_type')->whereNE('id',0)->order('id');

		$this->view->EntityTypes = $this->rows2view($this->db->queryRows($q));

		if ($id = $this->request('clone')) {
			$entity = new PVLng\Entity($id);
			if ($entity->id) {
				unset($entity->id, $entity->guid);
				$this->type = $entity->type;
				$this->prepareFields($entity);
			}

			$this->view->Fields = $this->fields;
			$this->foreward('Edit');
		}
	}

	/**
	 *
	 */
	public function Edit_Get_Action() {
		if (!$this->request('clone') AND !$this->request('id')) {
			$this->redirect('channel', 'add');
		}
	}

	/**
	 *
	 */
	public function Edit_Post_Action() {
		if ($channel = $this->request('c')) {
			$ok = TRUE;

			$attr = include CORE_DIR . DS . 'type.conf.php';

			/* check required fields */
			foreach ($this->fields as $key=>$data) {
				if ($data['REQUIRED'] AND $channel[$key] == '') {
					Messages::Error(I18N::_('channel::ParamIsRequired', $data['NAME']), TRUE);
					$ok = FALSE;
				}
			}

			$entity = new PVLng\Entity($channel['id']);
			foreach ($channel as $key=>$value) $entity->set($key, $value);

			if ($ok) {
				if ($entity->id) $entity->update(); else $entity->insert();

				if (!$entity->isError()) {
					Messages::Success(I18N::_('ChannelSaved'));
					$this->redirect('channel');
				} else {
					Messages::Error($entity->Error());
					#Messages::Info(print_r($entity, 1));
				}
			}

			$this->prepareFields($entity);
			$this->type = $entity->type;
			$this->view->Id = $entity->id;
			$this->view->Fields = $this->fields;
		}
	}

	/**
	 *
	 */
	public function Edit_Action() {
		$this->view->SubTitle = I18N::_('EditChannel');

		if ($id = $this->request('id')) {
			$entity = new PVLng\Entity($id);
			$this->view->Id = $id;
			$this->type = $entity->type;
			$this->prepareFields($entity);
		}

		$type = new PVLng\EntityType($this->type);
		if ($type->id) {
			$this->view->TypeName = $type->name;
			if ($type->unit) $this->view->TypeName .= ' (' . $type->unit . ')';
		}

		$this->view->Type = $this->type;
		$this->view->Fields = $this->fields;
	}

	/**
	 *
	 */
	public function Delete_Post_Action() {
		$entity = new PVLng\Entity($this->request('id'));

		if ($entity->id) {
			// check for entity is assigned in channel tree
			$tree = new PVLng\Tree;
			$tree->find('entity', $entity->id);

			if ($tree->id) {
				Messages::Error(I18N::_('ChannelStillInTree', $entity->name), TRUE);
			} else {
				$entity->delete();
				if ($entity->isError()) {
					Messages::Error($entity->Error());
					Messages::Info(print_r($entity, 1));
				}
			}
		}
		$this->redirect('channel');
	}

	// -------------------------------------------------------------------------
	// PROTECTED
	// -------------------------------------------------------------------------

	/**
	 *
	 */
	protected $fields = array();

	/**
	 *
	 */
	protected $type = 0;

	/**
	 *
	 */
	protected function prepareFields( $entity ) {
		$type = new PVLng\EntityType($entity->type);

		$attr = include CORE_DIR . DS . 'Channel' . DS
		              . str_replace('\\', DS, $type->model?:'_') . '.conf.php';

		// check all fields
		foreach ($this->fields as $key=>$data) {
			if (isset($attr[$key])) {
				// apply settings for this field
				$this->fields[$key] = array_merge(
					$this->fields[$key],
					array_change_key_case($attr[$key], CASE_UPPER)
				);
			}
		}

		foreach ($this->fields as $key=>&$data) {
			$data['VALUE'] = isset($entity->$key)
			               ? htmlspecialchars($entity->$key)
			               : htmlspecialchars($data['DEFAULT']);
		}
	}

}