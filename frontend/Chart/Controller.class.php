<?php
/**
 *
 *
 * @author      Knut Kohl <github@knutkohl.de>
 * @copyright   2012-2013 Knut Kohl
 * @license     GNU General Public License http://www.gnu.org/licenses/gpl.txt
 * @version     $Id: v1.0.0.2-19-gf67765b 2013-05-05 22:03:31 +0200 Knut Kohl $
 */
class Chart_Controller extends Controller {

	/**
	 *
	 */
	public function before() {
		parent::before();

		$this->Tree = NestedSet::getInstance();
		$this->channels = array();
		$this->views = $this->model->getViews();
	}

	/**
	 *
	 */
	public function after() {
		$this->view->PeriodCount = isset($this->Channels->c) ? $this->Channels->c : 1;

		$this->view->PeriodSelect =
			BabelKitMySQLi::getInstance()->select(
				'period',
				LANGUAGE,
				array(
					'var_name' => 'v[p]',
					'blank_prompt' => I18N::_('None'),
					'value'    => isset($this->Channels->p) ? $this->Channels->p : '',
					'options'  => 'id="period"'
				)
			);

		parent::after();
	}

	/**
	 *
	 */
	public function Index_GET_Action() {
		if ($view = $this->request('view')) {
			if ($data = $this->model->getView($view)) {
				$this->actView = $view;
				$this->Channels = $data->data;
			} else {
				Messages::Error(I18N::_('UnknownView', $view));
			}
		}
	}

	/**
	 *
	 */
	public function Index_POST_Action() {

		if ($this->request('save') AND $this->request('saveview')) {
			// Allowed only for logged in user
			if (!Session::get('user')) return;
		    // Save view
			if ($channels = $this->request('v')) {
				$this->actView = $this->request('saveview');
				// Save ...
				$this->model->saveView($this->actView, $channels, $this->request('public'));
				// ... and read back
				$this->Channels = $this->model->getView($this->actView)->data;
			}

		} elseif ($this->request('load') AND $this->request('loadview')) {
			// Load view
			$this->actView = $this->request('loadview');
			$this->Channels = $this->model->getView($this->actView)->data;

		} elseif ($this->request('delete') AND $this->request('loadview')) {
			// Allowed only for logged in user
			if (!Session::get('user')) return;
			// Delete view
			$this->model->deleteView($this->request('loadview'));

		}
	}

	/**
	 *
	 */
	public function Index_Action() {
		$this->view->SubTitle = I18N::_('Charts');

		$tree = $this->Tree->getFullTree();
		array_shift($tree);
		$parent = array( 1 => 0 );

		$data = array();
		foreach ($tree as $node) {

			$parent[$node['level']] = $node['id'];
			$node['parent'] = $parent[$node['level']-1];

			if ($entity = $this->model->getEntity($node['entity'])) {
				// remove id, is the same as $node[entity]
				unset($entity->id);
				$guid = $node['guid'] ?: $entity->guid;
				$node = array_merge($node, (array) $entity);
				$node['guid'] = $guid;
				$id = $node['id'];
				if (isset($this->Channels->$node['id'])) {
					$node['checked'] = 'checked';
					$node['presentation'] = $this->Channels->$id;
				}
			}

			$data[] = array_change_key_case($node, CASE_UPPER);
		}
		$this->view->Data = $data;

		$views = array();
		foreach ($this->model->getViews() as $row) {
			$views[] = array(
				'NAME'     => $row->name,
				'PUBLIC'   => $row->public,
				'SELECTED' => ($row->name == $this->actView)
			);
			if ($row->name == $this->actView AND $row->public) {
				$this->view->ViewPublic = TRUE;
			}
		}
		$this->view->View = $this->actView;
		$this->view->Views = $views;
		$this->view->NotifyLoad = $this->config->Controller_Chart_NotifyLoad;
	}

	// -------------------------------------------------------------------------
	// PROTECTED
	// -------------------------------------------------------------------------

	/**
	 *
	 */
	protected $Tree;

	/**
	 *
	 */
	protected $Channels;

	/**
	 *
	 */
	protected $actView;

}
