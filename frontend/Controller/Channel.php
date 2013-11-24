<?php
/**
 *
 *
 * @author      Knut Kohl <github@knutkohl.de>
 * @copyright   2012-2013 Knut Kohl
 * @license     GNU General Public License http://www.gnu.org/licenses/gpl.txt
 * @version     $Id: v1.0.0.2-23-g2e4cde1 2013-05-05 22:15:44 +0200 Knut Kohl $
 */
namespace Controller;

/**
 *
 */
class Channel extends \Controller {

	/**
	 *
	 */
	public function before() {
		$this->view->Entities = $this->rows2view($this->model->getEntities());

		$fields = include __DIR__ . DS . 'channel.conf.php';

		$this->fields = array();

		foreach ($fields as $key=>$field) {
			$this->fields[$key] = array_merge(array(
				'VISIBLE'  => TRUE,
				'FIELD'    => $key,
				'TYPE'     => 'text',
				'DEFAULT'  => '',
				'REQUIRED' => FALSE,
				'NAME'     => __('channel::'.$key),
				'HINT'     => __('channel::'.$key.'Hint'),
			), array_change_key_case($field, CASE_UPPER));
		}
	}

	/**
	 *
	 */
	public function afterPost() {
		if (!$this->ignore_returnto) {
			// Handle returnto (Edit from Overview) ...
			parent::afterPOST();
			// ... or redirect to channels list
			$this->app->redirect('/channel');
		}
	}

	/**
	 *
	 */
	public function Index_Action() {
		$this->view->SubTitle = __('Channels');
	}

	/**
	 *
	 */
	public function AddPOST_Action() {
		if ($type = $this->request->post('type')) {
			$this->prepareFields($type);
			foreach ($this->fields as &$data) {
				$data['VALUE'] = $data['DEFAULT'];
			}

			$this->view->Type = $type;

			// Preset channel unit
			$type = new \ORM\EntityType($type);
			$this->fields['unit']['VALUE'] = $type->unit;

			$this->ignore_returnto = TRUE;
			$this->app->foreward('Edit');
		}
	}

	/**
	 *
	 */
	public function Add_Action() {
		$this->view->SubTitle = __('CreateChannel');

		$q = \DBQuery::forge('pvlng_type')->whereGT('id', 0);

		$this->view->EntityTypes = $this->rows2view($this->db->queryRows($q));

		if ($clone = $this->app->params->get('clone')) {
			$entity = new \ORM\Channel($clone);
			if ($entity->id) {
				unset($entity->id, $entity->guid);
				$this->prepareFields($entity);
				$this->view->Type = $entity->type;
			}

			$this->app->foreward('Edit');
		}
	}

	/**
	 *
	 */
	public function AliasPOST_Action() {

		$entity = new \ORM\Tree;
		$entity->find('entity', $this->request->post('id'));

		if ($entity->id) {
			$alias = new \ORM\Channel();
			$alias->name = $entity->name;
			$alias->description = $entity->description;
			$alias->channel = $entity->guid;
			$alias->type = 0;

			$alias->insert();

			if (!$alias->isError()) {
				\Messages::Success(__('ChannelSaved'));
			} else {
				\Messages::Error($entity->Error());
				\Messages::Info(print_r($entity->queries(), 1));
			}
		}

		$this->app->redirect('/overview');
	}

	/**
	 *
	 */
	public function EditPOST_Action() {
		if ($channel = $this->request->post('c')) {
			$ok = TRUE;

			$entity = new \ORM\Channel($channel['id']);

			// set values
			foreach ($channel as $key=>$value) $entity->set($key, $value);

			$this->prepareFields($entity);

			$type = new \ORM\EntityType($entity->type);

			if ($type->model) {
				$model = '\Channel\\' . $type->model;
				$model::afterEdit($entity);
			}

			/* check required fields */
			foreach ($this->fields as $key=>$data) {
				if ($data['REQUIRED'] AND $entity->$key == '') {
					\Messages::Error(__('channel::ParamIsRequired', $data['NAME']), TRUE);
					$ok = FALSE;
				}
			}

			if ($ok) {
			    // CAN'T simply replace because of the foreign key in the tree!
			    if ($entity->id) $entity->update(); else $entity->insert();

				if (!$entity->isError()) {
					\Messages::Success(__('ChannelSaved'));
				} else {
					\Messages::Error($entity->Error());
					\Messages::Info(print_r($entity->queries(), 1));
					$ok = FALSE;
				}
			}

			$this->ignore_returnto = !$ok;

			$this->view->Id = $entity->id;
			$this->view->Type = $entity->type;
		}
	}

	/**
	 *
	 */
	public function EditGET_Action() {
		if ($id = $this->app->params->get('id')) {
			$this->prepareFields(new \ORM\Channel($id));
			$this->view->Id = $id;
		}
	}

	/**
	 *
	 */
	public function Edit_Action() {
		$this->view->SubTitle = __('EditChannel');
		$this->view->Fields = $this->fields;
	}

	/**
	 *
	 */
	public function DeletePOST_Action() {
		$entity = new \ORM\Channel($this->request->post('id'));

		if ($entity->id) {
			// check for entity is assigned in channel tree
			$tree = new \ORM\Tree;
			$tree->find('entity', $entity->id);

			if ($tree->id) {
				\Messages::Error(__('ChannelStillInTree', $entity->name), TRUE);
			} else {
				$name = $entity->name;
				$entity->delete();
				if (!$entity->isError()) {
					\Messages::Success(__('ChannelDeleted', $name));
				} else {
					\Messages::Error($entity->Error());
					\Messages::Info(print_r($entity, 1));
				}
			}
		}

		$this->app->redirect('/channel');
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
	protected $ignore_returnto;

	/**
	 *
	 * @param $entity integer|object Type Id or Channel object
	 */
	protected function prepareFields( $entity=NULL ) {

		if (!is_object($entity)) {
			$type = new \ORM\EntityType($entity);
		} else {
			$type = new \ORM\EntityType($entity->type);
		}

		if (!$type->id) {
			\Messages::Error('Unknown entity');
			$this->app->redirect('/channel');
		}

		$conf = CORE_DIR . DS . 'Channel' . DS
		      . str_replace('\\', DS, $type->model?:'NoModel') . '.conf.php';

		$attr = file_exists($conf) ? include $conf : array();

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

		$this->view->Type = $type->id;
		$this->view->TypeName = $type->name;
		if ($type->unit) $this->view->TypeName .= ' (' . $type->unit . ')';
		$this->view->Icon = $type->icon;

		if (is_object($entity)) {
			foreach ($this->fields as $key=>&$data) {
				$h = 'model::'.$type->model.'_'.$key;
				$name = __($h);
				if ($name != $h) $data['NAME'] = $name;

				$h = 'model::'.$type->model.'_'.$key.'Hint';
				$name = __($h);
				if ($name != $h) $data['HINT'] = $name;

				$data['VALUE'] = isset($entity->$key)
				               ? htmlspecialchars($entity->$key)
				               : htmlspecialchars($data['DEFAULT']);
			}
		}
	}

}
