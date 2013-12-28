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
		$q = \DBQuery::forge('pvlng_channel_view');
		$this->view->Entities = $this->rows2view($this->db->queryRows($q));

		$this->fields = array();

		foreach (include __DIR__ . DS . 'channel.conf.php' as $key=>$field) {
			$this->fields[$key] = array_merge(array(
				'VISIBLE'  => TRUE,
				'FIELD'    => $key,
				'TYPE'     => 'text',
				'DEFAULT'  => '',
				'REQUIRED' => FALSE,
				'READONLY' => FALSE,
				'NAME'     => __('channel::'.$key),
				'HINT'     => __('channel::'.$key.'Hint'),
			), array_change_key_case($field, CASE_UPPER));
		}
	}

	/**
	 *
	 */
	public function after() {
		$this->view->APIkey = $this->model->getAPIkey();
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
		$type = $this->request->post('type');
		if ($type != '') {
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
			if ($entity->alias) {
				\Messages::Error(__('AliasStillExists'));
			} else {
				$alias = new \ORM\Channel;
				$alias->name = $entity->name;
				$alias->description = $entity->description;
				$alias->channel = $entity->guid;
				$alias->unit = $entity->unit;
				$alias->private = $entity->private;
				$alias->type = 0;
				$alias->comment = 'Alias of ['.$entity->id.'] '.$entity->name;
				if ($entity->description) {
					$alias->comment .= ' ('.$entity->description.')';
				}
				$alias->insert();

				if (!$alias->isError()) {
					\Messages::Success(__('ChannelSaved'));
				} else {
					\Messages::Error($entity->Error());
					\Messages::Info(implode(";\n", $entity->queries()).';');
				}
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
				$entity->throwException();
				try {
				    // CAN'T simply replace because of the foreign key in the tree!
				    if (!$entity->id) {
						$entity->insert();
						\Messages::Success(__('ChannelSaved'));
					} else {
						$entity->update();
						\Messages::Success(__('ChannelSaved'));

						// Update possible alias channel!
						$tree = new \ORM\Tree;

						// Find channel itself in tree to get alias Id
						if ($tree->find('entity', $entity->id)->alias) {
							// Alias channel
							$alias = new \ORM\Channel($tree->alias);
							$alias->name = $entity->name;
							$alias->description = $entity->description;
							$alias->public = $entity->public;
							$alias->unit = $entity->unit;
							$alias->update();
							\Messages::Success(__('AliasesUpdated'));

							if (\slimMVC\Config::getInstance()->get('Log.SQL')) {
								\ORM\Log::save('Update Alias', implode(";\n", $tree->queries()).';');
								\ORM\Log::save('Update Alias', implode(";\n", $alias->queries()).';');
							}
						}
					}
				} catch (Exception $e) {
					\Messages::Error('['.$e->getCode().'] '.$e->getMessage());
					\Messages::Info(implode(";\n", $entity->queries()).';');
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
			$name = $entity->name;
			$entity->delete();
			if (!$entity->isError()) {
				\Messages::Success(__('ChannelDeleted', $name));
			} else {
				\Messages::Error(__($entity->Error(), $entity->name), TRUE);
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

		if ($type->id == '') {
			\Messages::Error('Unknown entity');
			$this->app->redirect('/channel');
		}

		// Apply model specific attribute settings
		$conf = CORE_DIR . DS . 'Channel' . DS
		      . str_replace('\\', DS, $type->model) . '.conf.php';

		$this->applyFieldSettings($conf);

		$this->view->Type = $type->id;
		$this->view->TypeName = $type->name;
		if ($type->unit) $this->view->TypeName .= ' (' . $type->unit . ')';
		$this->view->Icon = $type->icon;

		if (is_object($entity)) {

			// Apply type specific attribute settings
			$conf = __DIR__ . DS
			      . ($entity->numeric ? 'channel.numeric' : 'channel.non-numeric')
				  . '.conf.php';

			$this->applyFieldSettings($conf);

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

	/**
	 *
	 */
	protected function applyFieldSettings( $conf ) {
		if (!file_exists($conf)) return;

		$attr = include $conf;

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
	}
}
