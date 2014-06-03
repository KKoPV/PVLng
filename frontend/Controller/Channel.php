<?php /* // AOP // */
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
class Channel extends \Controller {

    /**
     *
     */
    public function before() {
        parent::before();

        $tblChannel = new \ORM\ChannelView;
        $tblChannel->filterByTypeId(array('min'=>1))->find();
        $this->view->Entities = $tblChannel->asAssoc();

        $this->fields = array();

        foreach (include __DIR__ . DS . 'Channel' . DS . 'default.php' as $key=>$field) {
            $this->fields[$key] = array_merge(array(
                'FIELD'       => $key,
                'TYPE'        => 'text',
                'VISIBLE'     => TRUE,
                'REQUIRED'    => FALSE,
                'READONLY'    => FALSE,
                'PLACEHOLDER' => NULL,
                'DEFAULT'     => NULL,
                'VALUE'       => ''
            ), array_change_key_case($field, CASE_UPPER));
        }

        if ($guid = $this->app->params->get('guid')) {
            // Call with GUID to edit, find channel
            $tree = new \ORM\Tree;
            $this->view->Id = $tree->filterByGUID($guid)->findOne()->getEntity();
        } else {
            $this->view->Id = $this->app->params->get('id');
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
    public function New_Action() {
        $type = $this->app->params->get('type');

        $this->prepareFields($type, 0);
        foreach ($this->fields as &$data) {
            $data['VALUE'] = $data['DEFAULT'];
        }

        $this->view->Type = $type;

        // Preset channel unit
        $type = new \ORM\ChannelType($type);
        $this->fields['unit']['VALUE'] = $type->getUnit();

        $model = $type->ModelClass();
        $model::beforeCreate($this->fields);

        $this->ignore_returnto = TRUE;
        $this->app->foreward('Edit');
    }

    /**
     *
     */
    public function AddPOST_Action() {

        if (($type = $this->request->post('type')) == '') return;

        $this->prepareFields($type);

        $this->view->Type = $type;

        // Preset channel unit
        $type = new \ORM\ChannelType($type);
        $this->fields['unit']['VALUE'] = $type->getUnit();

        $model = $type->ModelClass();
        $model::beforeCreate($this->fields);

        $this->ignore_returnto = TRUE;
        $this->app->foreward('Edit');
    }

    /**
     *
     */
    public function TemplatePOST_Action() {

        if (($template = $this->request->post('template')) == '') return;

        $channels = include $template;
        $channels = $channels['channels'];

        // 1st save channels
        $oChannel = new \ORM\Channel;
        $oChannel->setThrowException();

        /// \Yryie::StartTimer('template', 'Create template', 'db');
        try {

            foreach ($channels as $id=>$channel) {
                $oChannel->reset();
                foreach ($channel as $key=>$value) {
                    $oChannel->set($key, $value);
                }
                $oChannel->insert();
                // Remember id for hierarchy build
                $channels[$id]['id'] = $oChannel->getId();
            }
            \Messages::Success(__('ChannelsSaved', count($channels)));

        } catch (\Exception $e) {
            \Messages::Error($e->getMessage());

            // Rollback in case of error
            foreach ($channels as $id=>$channel) {
                if (isset($channels[$id]['id'])) {
                    $oChannel->reset()->filterById($channels[$id]['id'])->findOne();
                    if ($oChannel->getId()) $oChannel->delete();
                }
            }

            $this->app->redirect('/channel');
        }
        /// \Yryie::StopTimer('template');

        // Build hierarchy
        /// \Yryie::StartTimer('hierarchy', 'Create hierarchy', 'db');
        $tree = \NestedSet::getInstance();

        // Remember Grouping Id for templates without own grouping channel (index 0)
        $groupId = $this->request->post('tree');

        foreach ($channels as $id=>$channel) {
            if ($id == 0) {
                $groupId = $tree->insertChildNode($channel['id'], $groupId);
            } else {
                $tree->insertChildNode($channel['id'], $groupId);
            }
        }
        /// \Yryie::StopTimer('hierarchy');

        \Messages::Success(__('HierarchyCreated', count($channels)));

        $this->app->redirect('/overview');
    }

    /**
     *
     */
    public function Add_Action() {
        $this->view->SubTitle = __('CreateChannel');

        if ($clone = $this->app->params->get('clone')) {

            $channel = new \ORM\Channel($clone);

            if ($channel->getId()) {
                foreach ($channel->asAssoc() as $key=>$value) {
                    if (array_key_exists($key, $this->fields)) $this->fields[$key]['VALUE'] = $value;
                }
                $this->prepareFields($channel->getType(), $channel->getId());
                $this->fields['id']['VALUE']   = '';
                $this->fields['guid']['VALUE'] = '';
                $this->fields['name']['VALUE'] = __('CopyOf') . ' ' . $this->fields['name']['VALUE'];

                $type = new \ORM\ChannelType($channel->getType());
                $model = $type->ModelClass();
                $model::beforeEdit($channel, $this->fields);
            }

            $this->app->foreward('Edit');

        } else {

            // Get all channel types
            $tblTypes = new \ORM\ChannelType;
            $tblTypes->filterById(array('min'=>1))->find();

            foreach ($tblTypes->asAssoc() as $type) {
                $type['description'] = __($type['description']);
                $types[] = $type;
            }

            $this->view->EntityTypes = $types;

            // Search for equipment templates
            $templates = array();
            foreach (glob(CORE_DIR . DS . 'Channel' . DS . 'Templates' . DS . '*.php') as $file) {
                $template = include $file;
                $type = new \ORM\ChannelType($template['channels'][0]['type']);
                $templates[] = array(
                    'FILE'         => $file,
                    'NAME'         => $template['name'] . ' (Type: ' . $type->name . ')',
                    'DESCRIPTION'  => $template['description'],
                    'ICON'         => $type->icon
                );
            }

            $this->view->Templates = $templates;
            $this->AddToTree();
        }
    }

    /**
     *
     */
    public function AliasPOST_Action() {

        $entity = new \ORM\Tree;
        $entity->filterByEntity($this->request->post('id'))->findOne();

        if ($entity->getId()) {
            if ($entity->getAlias()) {
                \Messages::Error(__('AliasStillExists'));
            } else {
                $alias = new \ORM\Channel;
                $alias
                    ->setType(0)
                    ->setChannel($entity->getGuid())
                    ->setComment('Alias of "'.$entity->getName()
                               . ($entity->getDescription() ? ' ('.$entity->getDescription().')' : '')
                               . '"')
                    ->insert();

                if (!$alias->isError()) {
                    \Messages::Success(__('ChannelSaved'));
                } else {
                    \Messages::Error($entity->Error());
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

            $entity = new \ORM\Channel($channel['id'] ?: 0);

            if (isset($channel['type-new'])) $channel['type'] = $channel['type-new'];

            $type  = new \ORM\ChannelType($channel['type']);

            $model = $type->ModelClass();
            $model::beforeEdit($entity, $this->fields);

            // Set values
            foreach ($channel as $key=>$value) {
                if (array_key_exists($key, $this->fields)) $this->fields[$key]['VALUE'] = $value;
            }
            $this->prepareFields($channel['type'], $entity->getId() ?: 0);

            $ok = $model::checkData($this->fields, $this->request->post('add2tree'));

            if ($ok) {
                $entity->setThrowException();
                try {
                    $entity->setType($channel['type']);
                    $model::beforeSave($this->fields, $entity);
                    $tree = 0;

                    // CAN'T simply replace because of the foreign key in the tree!
                    if (!$entity->getId()) {
                        $entity->insert();
                        if ($this->request->post('add2tree') AND $addTo = $this->request->post('tree')) {
                            $tree = \NestedSet::getInstance()->insertChildNode($entity->getId(), $addTo);
                            \Messages::Success(__('HierarchyCreated', 1));
                        }
                    } else {
                        $entity->update();
                    }

                    \Messages::Success(__('ChannelSaved'));
                    $model::afterSave($entity, $tree);
                } catch (\Exception $e) {
                    \Messages::Error('['.$e->getCode().'] '.$e->getMessage());
                    $ok = FALSE;
                }
            }

            $this->ignore_returnto = !$ok;

            $this->view->Id   = $entity->getId();
            $this->view->Type = $entity->getType();
        }
    }

    /**
     *
     */
    public function Edit_Action() {
        $this->view->SubTitle = __('EditChannel');

        if (!$this->view->Id) {
            // Add mode
            $this->AddToTree();
        } else {
            $channel = new \ORM\Channel($this->view->Id);

            if ($channel->getType() == 0) {
                \Messages::Error('You can\'t edit an alias!');
                $this->app->redirect('/channel');
            }

            $type = new \ORM\ChannelType($channel->getType());

            if ($this->app->request->isGet()) { // If POST, this is called only if there where errors
                $model = $type->ModelClass();
                foreach ($channel->asAssoc() as $key=>$value) {
                    if (array_key_exists($key, $this->fields)) $this->fields[$key]['VALUE'] = $value;
                }
                $model::beforeEdit($channel, $this->fields);
                $this->prepareFields($channel->getType(), $channel->getId());
            }

            $alternatives = new \ORM\ChannelType;
            $alternatives
                ->filterByType($type->getType())
                ->filterByChilds($type->getChilds())
                ->filterByRead($type->getRead())
                ->filterByWrite($type->getWrite())
                ->filterByGraph($type->getGraph())
                ->order('name')
                ->find();

            $replace = array();
            foreach ($alternatives as $alternative) {
                // Exclude Alias
                if ($id = $alternative->getId()) $replace[$id] = $alternative->getName();
            }
            if (count($replace) > 1) $this->view->replace = $replace;
        }

        uasort($this->fields, function($a, $b) {
            return ($a['POSITION'] < $b['POSITION']) ? -1 : 1;
        });

        $this->view->Fields = $this->fields;
    }

    /**
     *
     */
    protected function AddToTree() {
        $q = new \DBQuery('pvlng_tree_view');
        $q->get('id')
          ->get('CONCAT(REPEAT("&nbsp; &nbsp; ", `level`-2), IF(`haschilds`,"&bull; ","&rarr;"), "&nbsp;")', 'indent')
          ->get('name')
          ->get('`childs` = -1 OR `haschilds` < `childs`', 'available') // Unused child slots?
          ->whereNE('childs', 0);
        $this->view->AddTree = $this->db->queryRows($q);
    }

    /**
     *
     */
    public function DeletePOST_Action() {
        $entity = new \ORM\Channel($this->request->post('id'));

        if ($entity->getId()) {
            $name = $entity->getName();
            $entity->delete();
            if (!$entity->isError()) {
                \Messages::Success(__('ChannelDeleted', $name));
            } else {
                \Messages::Error(__($entity->Error(), $name), TRUE);
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
    protected function prepareFields( $type, $entity=NULL ) {

        // Got channel type number to create new channel
        $type = new \ORM\ChannelType($type);

        if ($type->getId() == '') {
            \Messages::Error('Unknown channel type');
            $this->app->redirect('/channel');
        }

        if (is_null($entity)) {
            $this->applyFieldSettings($type->getType());
        } else {
            $entity = new \ORM\Channel($entity);
            $this->applyFieldSettings($entity->getMeter() ? 'meter' : 'sensor');
        }

        if ($type->getWrite() AND $type->getRead()) {
            // No specials for writable AND readable channels
        } elseif ($type->getWrite()) {
            // Write only
           $this->applyFieldSettings('write');
        } elseif ($type->getRead()) {
            // Read only
           $this->applyFieldSettings('read');
        } else {
            // A grouping channel, not write, not read
            $this->applyFieldSettings('group');
        }

        // Last apply model specific settings
        $this->applyFieldSettings(str_replace('\\', DS, $type->getModel()));

        foreach ($this->fields as $key=>&$data) {
            $data = array_merge(
                array(
                    'FIELD'    => $key,
                    'TYPE'     => 'text',
                    'VISIBLE'  => TRUE,
                    'REQUIRED' => FALSE,
                    'READONLY' => FALSE,
                    'DEFAULT'  => NULL,
                    'VALUE'    => ''
                ),
                $data
            );

            $h = 'model::'.$type->model.'_'.$key;
            $name = __($h);
            $data['NAME'] = ($name != $h) ? $name : __('channel::'.$key);

            $h = 'model::'.$type->model.'_'.$key.'Hint';
            $name = __($h);
            $data['HINT'] = ($name != $h) ? $name : __('channel::'.$key.'Hint');

            if (is_null($entity)) {
                $data['VALUE'] = trim($data['DEFAULT']);
            }

            if (strpos($data['TYPE'], 'bool') === 0) {
                preg_match_all('~;([^:;]+):([^:;]+)~i', $data['TYPE'], $matches, PREG_SET_ORDER);
                foreach ($matches as $option) {
                    $data['OPTIONS'][] = array(
                        'VALUE'   => trim($option[1]),
                        'TEXT'    => __(trim($option[2])),
                        'CHECKED' => (trim($option[1]) == $data['VALUE'])
                    );
                }
                $data['TYPE'] = 'bool';
            } elseif (strpos($data['TYPE'], 'select') === 0) {
                preg_match_all('~;([^:;]+):([^:;]+)~i', $data['TYPE'], $matches, PREG_SET_ORDER);
                foreach ($matches as $option) {
                    $data['OPTIONS'][] = array(
                        'VALUE'    => trim($option[1]),
                        'TEXT'     => __(trim($option[2])),
                        'SELECTED' => (trim($option[1]) == $data['VALUE'])
                    );
                }
                $data['TYPE'] = 'select';
            } elseif (preg_match('~^sql:(.?):(.*?)$~i', $data['TYPE'], $matches)) {
                // Tranform into select options
                if ($matches[1] != '') {
                    // Empty option for select2 placeholder
                    $data['OPTIONS'][] = NULL;
                }
                foreach ($this->db->queryRowsArray($matches[2]) as $option) {
                    $option = array_values($option);
                    $data['OPTIONS'][] = array(
                        'VALUE'    => trim($option[0]),
                        'TEXT'     => __(trim($option[1])),
                        'SELECTED' => (trim($option[0]) == $data['VALUE'])
                    );
                }
                $data['TYPE'] = 'select';
            }
        }

        $this->view->Type = $type->id;
        $this->view->TypeName = $type->name;
        if ($type->unit) $this->view->TypeName .= ' [' . $type->unit . ']';
        $this->view->Icon = $type->icon;

        $h = $type->description.'Help';
        $help = __($h);
        // Check if a help text exists
        $this->view->TypeHelp = $help != $h ? $help : '';
    }

    /**
     *
     */
    protected function applyFieldSettings( $conf ) {
        // 1st try general config
        $config = __DIR__ . DS . 'Channel' . DS . $conf . '.php';
        if (!file_exists($config)) {
            // 2nd try model specific config
            $config = CORE_DIR . DS . 'Channel' . DS . $conf . '.conf.php';
            if (!file_exists($config)) return;
        }

        $attr = include $config;

        // check all fields
        foreach ($attr as $key=>$data) {
            // apply settings for this field
            $this->fields[$key] = isset($this->fields[$key])
                                ? array_merge(
                                      $this->fields[$key],
                                      array_change_key_case($data, CASE_UPPER)
                                  )
                                : array_merge(
                                      array(
                                        'POSITION'    => 400,
                                        'FIELD'       => $key,
                                        'TYPE'        => 'text',
                                        'VISIBLE'     => TRUE,
                                        'REQUIRED'    => FALSE,
                                        'READONLY'    => FALSE,
                                        'PLACEHOLDER' => NULL,
                                        'DEFAULT'     => NULL,
                                        'VALUE'       => ''
                                      ),
                                      array_change_key_case($data, CASE_UPPER)
                                  );
        }
    }
}
