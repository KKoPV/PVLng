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
class Index extends \Controller {

    /**
     *
     */
    public function before() {
        parent::before();
        $this->Tree = \NestedSet::getInstance();
        $this->channels = array();
        $this->views = $this->model->getViews();
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

        parent::after();
    }

    /**
     *
     */
    public function IndexGET_Action() {
        if ($slug = $this->app->params['view']) {
            $view = new \ORM\View;
            $view->findBySlug($slug);
            if ($view->name) {
                $this->loadView($view->name);
            } else {
                \Messages::Error(\I18N::_('UnknownView', $view));
            }
        }
    }

    /**
     *
     */
    public function IndexPOST_Action() {

        if ($view = $this->request->post('top-loadview') OR
            ($this->request->post('load') AND
             $view = $this->request->post('loaddeleteview'))) {
            // Load view
            $this->loadView($view);
        } elseif ($this->request->post('save') AND
                  $view = $this->request->post('saveview')) {
            // Allowed only for logged in user
            if (!\Session::get('user')) return;

            // Save view
            if ($channels = $this->request->post('v')) {
                // Save ...
                $this->model->saveView($view, $channels, $this->request->post('public'), $this->slug($view));
                // ... and read back
                $this->loadView($view);
            }

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

                // remove id, is the same as $node['entity']
                unset($entity->id);
                $guid = $node['guid'] ?: $entity->guid;
                $node = array_merge($node, (array) $entity);
                $node['guid'] = $guid;

                if ($node['model']) {
                    $e = \Channel::byId($node['id']);
                    $node['name']        = $e->name;
                    $node['description'] = $e->description;
                    $node['unit']        = $e->unit;
                    $node['public']      = $e->public;
                    $node['icon']        = $e->icon;
                }

                if (isset($this->Channels->$node['id'])) {
                    $node['checked'] = 'checked';
                    $node['presentation'] = $this->Channels->$node['id'];
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
                'SELECTED' => ($row->name == $this->actView),
                'SLUG'     => $row->slug
            );
            if ($row->name == $this->actView AND $row->public) {
                $this->view->ViewPublic = TRUE;
            }
        }
        $this->view->View = $this->actView;
        $this->view->Slug = $this->actSlug;
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
    protected $actSlug;

    /**
     *
     */
    protected function slug( $string ) {

        $mobile = substr($string, 0, 1) == '@' ? '@' : '';

        $translate = array(
            'Š' => 'S',  'š' => 's',  'Đ' => 'Dj', 'đ' => 'dj', 'Ž' => 'Z',
            'ž' => 'z',  'Č' => 'C',  'č' => 'c',  'Ć' => 'C',  'ć' => 'c',
            'À' => 'A',  'Á' => 'A',  'Â' => 'A',  'Ã' => 'A',  'Ä' => 'Ae',
            'Å' => 'A',  'Æ' => 'A',  'Ç' => 'C',  'È' => 'E',  'É' => 'E',
            'Ê' => 'E',  'Ë' => 'E',  'Ì' => 'I',  'Í' => 'I',  'Î' => 'I',
            'Ï' => 'I',  'Ñ' => 'N',  'Ò' => 'O',  'Ó' => 'O',  'Ô' => 'O',
            'Õ' => 'O',  'Ö' => 'Oe', 'Ø' => 'O',  'Ù' => 'U',  'Ú' => 'U',
            'Û' => 'U',  'Ü' => 'Ue', 'Ý' => 'Y',  'Þ' => 'B',  'ß' => 'Ss',
            'à' => 'a',  'á' => 'a',  'â' => 'a',  'ã' => 'a',  'ä' => 'ae',
            'å' => 'a',  'æ' => 'a',  'ç' => 'c',  'è' => 'e',  'é' => 'e',
            'ê' => 'e',  'ë' => 'e',  'ì' => 'i',  'í' => 'i',  'î' => 'i',
            'ï' => 'i',  'ð' => 'o',  'ñ' => 'n',  'ò' => 'o',  'ó' => 'o',
            'ô' => 'o',  'õ' => 'o',  'ö' => 'oe', 'ø' => 'o',  'ù' => 'u',
            'ú' => 'u',  'û' => 'u',  'ü' => 'ue', 'ý' => 'y',  'ý' => 'y',
            'þ' => 'b',  'ÿ' => 'y',  'Ŕ' => 'R',  'ŕ' => 'r'
        );

        // Remove multiple spaces
        $string = preg_replace(array('~\s{2,}~', '~[\t\r\n]+~'), ' ', $string);
        $string = strtr($string, $translate);
        $string = preg_replace('~[^\w\d]~', '-', $string);
        $string = preg_replace('~-{2,}~', '-', $string);

        return strtolower($mobile . trim($string, '-'));
    }

    /**
     *
     */
    protected function loadView( $view ) {
        $view = new \ORM\View($view);
        $this->Channels = json_decode($view->data);
        $this->actView = $view->name;
        $this->actSlug = $view->slug;
    }
}
