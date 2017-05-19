<?php
/**
 *
 *
 * @author      Knut Kohl <github@knutkohl.de>
 * @copyright   2012-2014 Knut Kohl
 * @license     MIT License (MIT) http://opensource.org/licenses/MIT
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
        $this->view->Channels = $tblChannel->filter('type_id', array('min'=>1))->find()->asAssoc();

        $this->fields = array();

        foreach (include PVLng::path(ROOT_DIR, 'frontend', 'Controller', 'Channel', 'default.php') as $key=>$field) {
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

        $model = $type->getModelClass();
        $model::beforeCreate($this->fields);

        $this->ignore_returnto = TRUE;
        $this->app->foreward('Edit');
    }

    /**
     *
     */
    public function TemplatePOST_Action() {

        if (($template = $this->request->post('template')) == '') return;

        if (!$this->request->post('p')) {
            $this->view->Template = $template;
            $this->app->foreward('TemplateEdit');
            return;
        }

        $channels = include $template;
        $channels = $channels['channels'];

        foreach ($this->request->post('p') as $id=>$data) {
            $data['public'] = $_=&$data['public'] ?: 1;
            $channels[$id] = array_merge($channels[$id], $data);
        }

        // 1st save channels
        $oChannel = new \ORM\Channel;
        $oChannel->setThrowException();
        $oChannelType = new \ORM\ChannelType;

        $add = $this->request->post('a');

        /// \Yryie::StartTimer('template', 'Create template', 'db');
        try {

            $aSubChannels = array();
            $cnt = 0;

            foreach ($channels as $id=>$channel) {
                if ($id == 0 OR isset($add[$id])) {
                    $oChannel->reset()
                             ->setIcon($oChannelType->reset()->filterById($channel['type'])->findOne()->getIcon());
                    foreach ($channel as $key=>$value) {
                        if ($key == '_') {
                            // Act as grouping channel, e.g. accumulate string powers
                            $aSubChannels[] = $id;
                        } else {
                            $oChannel->set($key, $value);
                        }
                    }
                    $oChannel->insert();
                    // Remember id for hierarchy build
                    $channels[$id]['id'] = $oChannel->getId();
                    $cnt++;
                } else {
                    $channels[$id]['id'] = 0;
                }
            }
            \Messages::Success(__('ChannelsSaved', $cnt));

        } catch (\Exception $e) {
            \Messages::Error($e->getMessage());

            // Rollback in case of error
            foreach ($channels as $id=>$channel) {
                if ($channels[$id]['id']) {
                    $oChannel->reset()->filterById($channels[$id]['id'])->findOne();
                    if ($oChannel->getId()) $oChannel->delete();
                }
            }

            $this->app->redirect('/channel');
        }
        /// \Yryie::StopTimer('template');

        // Build hierarchy
        /// \Yryie::StartTimer('hierarchy', 'Create hierarchy', 'db');
        // Remember Grouping Id for templates without own grouping channel (index 0)
        $groupId = $this->request->post('tree') ?: 1;

        foreach ($channels as $id=>$channel) {
            if ($id == 0) {
                $groupId = $this->app->tree->insertChildNode($channel['id'], $groupId);
            } elseif ($channel['id']) {
                // Remember tree position
                $channels[$id]['tree'] = $this->app->tree->insertChildNode($channel['id'], $groupId);
            } else {
                $channels[$id]['tree'] = 0;
            }
        }

        // Any grouping channel with defined sub-channels
        foreach ($aSubChannels as $id1) {
            // Loop childs and ...
            foreach ($channels[$id1]['_'] as $id2) {
                if ($channels[$id1]['tree'] AND $channels[$id2]['id']) {
                    // ... append just created channel AS child to grouping channel
                    \Channel::ById($channels[$id1]['tree'])->addChild($channels[$id2]['id']);
                }
            }
        }
        /// \Yryie::StopTimer('hierarchy');

        \Messages::Success(__('HierarchyCreated', count($channels)));

        $this->app->redirect('/overview');
    }

    /**
     *
     */
    public function TemplateEdit_Action() {
        $this->view->SubTitle = __('AdjustTemplate');

        $channels = include $this->request->post('template');
        $channels = $channels['channels'];

        $type = new \ORM\ChannelType;

        if (isset($channels[0])) {
            $type->filterById($channels[0]['type'])->findOne();
            $this->view->GroupType = $type->getName();
            $this->view->GroupIcon = isset($channels[0]['icon']) ? $channels[0]['icon'] : $type->getIcon();
        }

        foreach ($channels as &$channel) {
            $type->reset()->filterById($channel['type'])->findOne();
            $channel = array_merge(array(
                '_type'      => $type->getName(),
                'icon'       => $type->getIcon(),
                'numeric'    => 1,
                'resolution' => 1,
                'public'     => 1,
            ), $channel);
        }

        $this->view->Channels = $channels;
        $this->AddToTree();

        $this->ignore_returnto = TRUE;
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
        $this->fields['name']['VALUE'] = $type->getName();
        $this->fields['unit']['VALUE'] = $type->getUnit();

        $model = $type->getModelClass();
        $model::beforeCreate($this->fields);

        $this->ignore_returnto = TRUE;
        $this->app->foreward('Edit');
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
                $this->fields['name']['VALUE'] = __('CopyOf') . ' ' . $this->fields['name']['VALUE'];

                $type = new \ORM\ChannelType($channel->getType());
                $model = $type->getModelClass();
                $model::beforeEdit($channel, $this->fields);
            }

            $this->app->foreward('Edit');

        } else {

            // Get all channel types
            $tblTypes = new \ORM\ChannelType;
            $tblTypes->filter('id', array('min'=>1))->find();

            foreach ($tblTypes->asAssoc() as $type) {
                $type['description'] = __($type['description']);
                $types[] = $type;
            }

            $this->view->EntityTypes = $types;

            // Search for equipment templates
            $templates = array();
            $filemask = \PVLng::path(ROOT_DIR, 'core', 'Channel', 'Templates', '*.php');
            foreach (glob($filemask) as $file) {
                $template = include $file;
                if (isset($template['channels'][0]['type'])) {
                    $type = new \ORM\ChannelType($template['channels'][0]['type']);
                    $template['name'] .= ' (' . $type->name . ')';
                    $icon = $type->icon;
                } else {
                    $icon = '/images/pix.gif';
                }

                $templates[] = array(
                    'FILE'         => $file,
                    'ICON'         => $icon,
                    'NAME'         => $template['name'],
                    'DESCRIPTION'  => $template['description']
                );
            }

            $this->view->Templates = $templates;
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
            $model = $type->getModelClass();
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
                        \Messages::Success(__('ChannelSaved'));
                        if ($this->request->post('add2tree') AND
                            $addTo = $this->request->post('tree') AND
                            $tree = \Channel::byId($addTo)->addChild($entity->getId())) {
                            \Messages::Success(__('HierarchyCreated', 1));
                        }
                    } else {
                        $entity->update();
                        \Messages::Success(__('ChannelSaved'));
                    }

                    $model::afterSave($entity, $tree);
                } catch (\Exception $e) {
                    \Messages::Error('['.$e->getCode().'] '.$e->getMessage());
                    $ok = FALSE;
                }
            }

            $this->ignore_returnto = !$ok;

            $this->view->Id   = $channel['id'];
            $this->view->Type = $channel['type'];
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

            // Try to edit an alias channel?
            if ($channel->getType() == 0) {
                \Messages::Info(__('EditSwitchAliasWithOriginal'));
                // Search orignal channel by GUID from alias in hierarchy
                $t = (new \ORM\Tree)->filterByGUID($channel->getChannel())->findOne();
                // Find now original channel by entity from hierarchy
                # $channel->reset()->filterById($t->getEntity())->findOne();
                $channel = new \ORM\Channel($t->getEntity());
                unset($t);
                // Set channel Id for edit form to new Id!
                $this->view->Id = $channel->getId();
            }

            $type = new \ORM\ChannelType($channel->getType());

            if ($this->app->request->isGet()) { // If POST, this is called only if there where errors
                $model = $type->getModelClass();
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
          ->filter('childs', array('ne' => 0));
        $this->view->AddTree = $this->db->queryRowsArray($q);
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
    protected function prepareFields($type, $entity=null)
    {
        // Got channel type number to create new channel
        $type = new \ORM\ChannelType($type);

        if ($type->getId() == '') {
            \Messages::Error('Unknown channel type');
            $this->app->redirect('/channel');
        }

        // Get general field settings from channel type itself
        $fieldSettings = array($type->getType());

        if ($entity) { // NULL or 0
            $entity = new \ORM\Channel($entity);
            if ($entity->getNumeric()) {
                $fieldSettings[] = 'numeric';
                $fieldSettings[] = $entity->getMeter() ? 'meter' : 'sensor';
            } else {
                $fieldSettings[] = 'non-numeric';
                $fieldSettings[] = 'sensor'; // There can't be non-numeric meters ...
            }
        }

        if ($type->getWrite() AND $type->getRead()) {
            // No specials for writable AND readable channels
        } elseif ($type->getWrite()) {
            // Write only
            $fieldSettings[] = 'write';
        } elseif ($type->getRead()) {
            // Read only
            $fieldSettings[] = 'read';
        } else {
            // A grouping channel, not write, not read
            $fieldSettings[] = 'group';
        }

        // Apply model specific settings
        $model = str_replace('\\', DIRECTORY_SEPARATOR, $type->getModel());
        $fieldSettings[] = $model;

        // Apply channel type specific settings
        $fieldSettings[] = $model . '.' . $type->getId();

        // Apply settings only once
        foreach (array_unique($fieldSettings) as $conf) {
            $this->applyFieldSettings($conf);
        }

        foreach ($this->fields as $key=>&$data) {
            $data = array_merge(
                array(
                    'FIELD'    => $key,
                    'TYPE'     => 'text',
                    'VISIBLE'  => true,
                    'REQUIRED' => false,
                    'READONLY' => false,
                    'DEFAULT'  => null,
                    'VALUE'    => ''
                ),
                $data
            );

            // Test if translations exists
            $h = 'model::'.$type->model.'_'.$key;
            $name = __($h);
            $data['NAME'] = ($name != $h) ? $name : __('channel::'.$key);

            $h = 'model::'.$type->model.'_'.$key.'Hint';
            $name = __($h);
            $data['HINT'] = ($name != $h) ? $name : __('channel::'.$key.'Hint');

            // Set value in ADD mode to default
            if (is_null($entity)) $data['VALUE'] = trim($data['DEFAULT']);

            switch (true) {

                // Boolean
                case strpos($data['TYPE'], 'bool') === 0:
                    // Shortcuts without options
                    if ($data['TYPE'] === 'bool' || $data['TYPE'] === 'bool-0' || $data['TYPE'] === 'boolean-0') {
                        // Defaults to 0
                        $data['TYPE'] = 'bool;0:no;1:yes';
                    } elseif ($data['TYPE'] === 'bool-1' || $data['TYPE'] === 'boolean-1') {
                        // Defaults to 1
                        $data['TYPE'] = 'bool;1:yes;0:no';
                    }

                    if (preg_match_all('~;([^:;]+):([^:;]+)~i', $data['TYPE'], $matches, PREG_SET_ORDER)) {
                        foreach ($matches as $option) {
                            $val = trim($option[1]);
                            $data['OPTIONS'][] = array(
                                'VALUE'   => $val,
                                'TEXT'    => __(trim($option[2])),
                                'CHECKED' => ($val == $data['VALUE'])
                            );
                        }
                    }

                    // Set type for template compiler
                    $data['TYPE'] = 'bool';
                    break;

                // Select
                case strpos($data['TYPE'], 'select') === 0:
                    if (preg_match_all('~;([^:;]+):([^:;]+)~i', $data['TYPE'], $matches, PREG_SET_ORDER)) {
                        foreach ($matches as $option) {
                            $val = trim($option[1]);
                            $data['OPTIONS'][] = array(
                                'VALUE'    => $val,
                                'TEXT'     => __(trim($option[2])),
                                'SELECTED' => ($val == $data['VALUE'])
                            );
                        }
                    }

                    // Set type for template compiler
                    $data['TYPE'] = 'select';
                    break;

                // Range
                case strpos($data['TYPE'], 'range') === 0:
                    list(, $start, $end, $step) = explode(';', $data['TYPE'].';1', 4); // step of 1 as default
                    for ($i=$start; $i<=$end; $i+=$step) {
                        $data['OPTIONS'][] = array(
                            'VALUE'    => $i,
                            'TEXT'     => $i,
                            'SELECTED' => ($i == $data['VALUE'])
                        );
                    }
                    // Set type for template compiler
                    $data['TYPE'] = 'select';
                    break;

                // SQL
                case preg_match('~^sql:(.?):(.*?)$~i', $data['TYPE'], $matches):
                    // Tranform into select options
                    if ($matches[1] != '') {
                        // Empty option for select2 placeholder
                        $data['OPTIONS'][] = null;
                    }

                    foreach ($this->db->queryRowsArray($matches[2]) as $option) {
                        $option = array_values($option);
                        $val    = trim($option[0]);
                        $data['OPTIONS'][] = array(
                            'VALUE'    => $val,
                            'TEXT'     => __(trim($option[1])),
                            'SELECTED' => ($val == $data['VALUE'])
                        );
                    }

                    // Set type for template compiler
                    $data['TYPE'] = 'select';
                    break;

                // Detect icon by field name, not by type
                case $key == 'icon':
                    // In ADD mode the value is empty, preset from type
                    if (empty($data['VALUE'])) $data['VALUE'] = $type->getIcon();

                    // Collect possible icons
                    if ($res = $this->db->query(new \DBQuery('pvlng_type_icons'))) {
                        while ($row = $res->fetch_object()) {
                            $name = explode(',', $row->name);
                            // Use max. the first 4 type names
                            if (count($name) < 4) {
                                $name = implode(', ', $name);
                            } else {
                                $name = implode(', ', array_slice($name, 0, 4)) . ' ...';
                            }
                            $data['ICONS'][] = array(
                                'ICON'   => $row->icon,
                                'NAME'   => $name,
                                'ACTUAL' => ($data['VALUE'] == $row->icon)
                            );
                        }
                    }
                    // Set type for template compiler
                    $data['TYPE'] = 'icon';

            } // switch
        }

        $this->view->Type = $type->getId();
        $this->view->Icon = $type->getIcon();
        $this->view->TypeName = $type->getName();
        $this->view->TypeDesc = __($type->getDescription());

        $h = $type->getDescription().'Help';
        $help = __($h);
        // Check if a help text exists
        $this->view->TypeHelp = $help != $h ? $help : '';
    }

    /**
     *
     */
    protected function applyFieldSettings( $conf ) {
        // 1st try general config
        $config = \PVLng::path(ROOT_DIR, 'frontend', 'Controller', 'Channel', $conf.'.php');
        if (!file_exists($config)) {
            // 2nd try model specific config
            $config = \PVLng::path(ROOT_DIR, 'core', 'Channel', $conf.'.conf.php');
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
