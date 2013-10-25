<?php
/**
 *
 *
 * @author      Knut Kohl <github@knutkohl.de>
 * @copyright   2012-2013 Knut Kohl
 * @license     GNU General Public License http://www.gnu.org/licenses/gpl.txt
 * @version     $Id: v1.0.0.2-19-gf67765b 2013-05-05 22:03:31 +0200 Knut Kohl $
 */
namespace Controller;

/**
 *
 */
class Index extends \Controller {

	/**
	 *
	 */
	public function before() {
		$this->Tree = \NestedSet::getInstance();
		$this->channels = array();
		$this->views = $this->model->getViews();
		$this->date = time();
	}

	/**
	 *
	 */
	public function after() {
		$this->view->PeriodCount = isset($this->Channels->c) ? $this->Channels->c : 1;

		$this->view->PeriodSelect =
			\BabelKitMySQLi::getInstance()->select(
				'period',
				LANGUAGE,
				array(
					'var_name'     => 'v[p]',
					'blank_prompt' => \I18N::_('None'),
					'value'        => isset($this->Channels->p) ? $this->Channels->p : '',
					'options'      => 'id="period"'
				)
			);

		$this->view->Date = date('m/d/Y', $this->date);
	}

	/**
	 *
	 */
	public function IndexGET_Action() {
#		if ($view = $this->app->params->get('view')) {
		if ($view = $this->app->params['view']) {
			if ($data = $this->model->getView($view)) {
				$this->actView = $view;
				$this->Channels = $data->data;
			} else {
				\Messages::Error(\I18N::_('UnknownView', $view));
			}
		}
		if ($date = $this->app->params->get('date')) {
			$this->date = strtotime($date);
		}
	}

	/**
	 *
	 */
	public function IndexPOST_Action() {

		if ($view = $this->request->post('loadview')) {
			// Load view from top select
			$this->actView = $view;
			$this->Channels = $this->model->getView($view)->data;

		} elseif ($this->request->post('save') AND
		          $view = $this->request->post('saveview')) {
			// Allowed only for logged in user
			if (!\Session::get('user')) return;
		    // Save view
			if ($channels = $this->request->post('v')) {
				$this->actView = $view;
				// Save ...
				$this->model->saveView($view, $channels, $this->request->post('public'));
				// ... and read back
				$this->Channels = $this->model->getView($view)->data;
			}

		} elseif ($this->request->post('load') AND
		          $view = $this->request->post('loaddeleteview')) {
			// Load view
			$this->actView = $view;
			$this->Channels = $this->model->getView($view)->data;

		} elseif ($this->request->post('delete') AND
		          $view = $this->request->post('loaddeleteview')) {
			// Allowed only for logged in user
			if (!\Session::get('user')) return;
			// Delete view
			if ($this->model->deleteView($view)) {
				\Messages::Success(\I18N::_('ViewDeleted', $view));
			} else {
				\Messages::Error(\I18N::_('DeleteViewFailed', $view));
			}

		}
	}

	/**
	 *
	 */
	public function Index_Action() {
		$this->view->SubTitle = \I18N::_('Charts');

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

	/**
	 *
	 */
	protected $date;

}
