<?php
/**
 * PVLng - PhotoVoltaic Logger new generation
 *
 * @link       https://github.com/KKoPV/PVLng
 * @link       https://pvlng.com/
 * @author     Knut Kohl <github@knutkohl.de>
 * @copyright  2012 Knut Kohl
 * @license    MIT License (MIT) http://opensource.org/licenses/MIT
 */
namespace Frontend\Controller;

/**
 *
 */
use Channel\Channel;
use Core\Messages;
use Core\PVLng;
use Frontend\Controller;
use ORM\Channel as ORMChannel;
use ORM\ChannelTypeIcons as ORMChannelTypeIcons;
use ORM\ChannelType as ORMChannelType;
use ORM\ChannelView as ORMChannelView;
use ORM\Tree as ORMTree;
use Yryie\Yryie;
use DBQuery;
use I18n;

/**
 *
 */
class Channels extends Controller
{
    /**
     *
     */
    public function before()
    {
        parent::before();

        $tblChannel = new ORMChannelView;
        $this->view->Channels = $tblChannel->filter('type_id', array('min'=>1))->find()->asAssoc();

        $this->fields = array();
        $this->applyFieldSettings('default');

        if ($guid = $this->app->params->get('guid')) {
            // Call with GUID to edit, find channel
            $tree = new ORMTree;
            $this->view->Id = $tree->filterByGUID($guid)->findOne()->getEntity();
        } else {
            $this->view->Id = $this->app->params->get('id');
        }
    }

    /**
     *
     */
    public function afterPost()
    {
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
    public function indexAction()
    {
        $this->view->SubTitle = I18N::translate('Channels');
    }

    /**
     *
     */
    public function newAction()
    {
        $type = $this->app->params->get('type');

        $this->prepareFields($type, 0);
        foreach ($this->fields as &$data) {
            $data['VALUE'] = $data['DEFAULT'];
        }

        $this->view->Type = $type;

        // Preset channel unit
        $type = new ORMChannelType($type);
        $this->fields['unit']['VALUE'] = $type->getUnit();

        $model = $type->getModelClass();
        $model::beforeCreate($this->fields);

        $this->ignore_returnto = true;
        $this->app->foreward('Edit');
    }

    /**
     *
     */
    public function templatePostAction()
    {
        if (($template = $this->request->post('template')) == '') {
            return;
        }

        if (!$this->request->post('p')) {
            $this->view->Template = $template;
            $this->app->foreward('TemplateEdit');
            return;
        }

        $channels = include $template;
        $channels = $channels['channels'];

        foreach ($this->request->post('p') as $id => $data) {
            $data['public'] = $_=&$data['public'] ?: 1;
            $channels[$id] = array_merge($channels[$id], $data);
        }

        // 1st save channels
        $oChannel = new ORMChannel;
        $oChannel->setThrowException();
        $oChannelType = new ORMChannelType;

        $add = $this->request->post('a');

        /// Yryie::startTimer('template', 'Create template', 'db');
        try {
            $aSubChannels = array();
            $cnt = 0;

            foreach ($channels as $id => $channel) {
                if ($id == 0 || isset($add[$id])) {
                    $oChannel->reset()->setIcon(
                        $oChannelType->reset()->filterById($channel['type'])->findOne()->getIcon()
                    );
                    foreach ($channel as $key => $value) {
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
            Messages::success(I18N::translate('ChannelsSaved', $cnt));
        } catch (Exception $e) {
            Messages::error($e->getMessage());

            // Rollback in case of error
            foreach ($channels as $id => $channel) {
                if ($channels[$id]['id']) {
                    $oChannel->reset()->filterById($channels[$id]['id'])->findOne();
                    if ($oChannel->getId()) {
                        $oChannel->delete();
                    }
                }
            }

            $this->app->redirect('/channels');
        }
        /// Yryie::stopTimer('template');

        // Build hierarchy
        /// Yryie::startTimer('hierarchy', 'Create hierarchy', 'db');
        // Remember Grouping Id for templates without own grouping channel (index 0)
        $groupId = $this->request->post('tree') ?: 1;

        foreach ($channels as $id => $channel) {
            if ($id == 0) {
                $groupId = $this->tree->insertChildNode($channel['id'], $groupId);
            } elseif ($channel['id']) {
                // Remember tree position
                $channels[$id]['tree'] = $this->tree->insertChildNode($channel['id'], $groupId);
            } else {
                $channels[$id]['tree'] = 0;
            }
        }

        // Any grouping channel with defined sub-channels
        foreach ($aSubChannels as $id1) {
            // Loop childs and ...
            foreach ($channels[$id1]['_'] as $id2) {
                if ($channels[$id1]['tree'] && $channels[$id2]['id']) {
                    // ... append just created channel AS child to grouping channel
                    Channel::byId($channels[$id1]['tree'])->addChild($channels[$id2]['id']);
                }
            }
        }
        /// Yryie::stopTimer('hierarchy');

        Messages::success(I18N::translate('HierarchyCreated', count($channels)));

        $this->app->redirect('/overview');
    }

    /**
     *
     */
    public function templateEditAction()
    {
        $this->view->SubTitle = I18N::translate('AdjustTemplate');

        $channels = include $this->request->post('template');
        $channels = $channels['channels'];

        $type = new ORMChannelType;

        if (isset($channels[0])) {
            $type->filterById($channels[0]['type'])->findOne();
            $this->view->GroupType = $type->getName();
            $this->view->GroupIcon = isset($channels[0]['icon']) ? $channels[0]['icon'] : $type->getIcon();
        }

        foreach ($channels as &$channel) {
            $type->reset()->filterById($channel['type'])->findOne();
            $channel = array_merge(
                array(
                '_type'      => $type->getName(),
                'icon'       => $type->getIcon(),
                'numeric'    => 1,
                'resolution' => 1,
                'public'     => 1,
                ),
                $channel
            );
        }

        $this->view->Channels = $channels;
        $this->AddToTree();

        $this->ignore_returnto = true;
    }

    /**
     *
     */
    public function addPostAction()
    {
        if (($type = $this->request->post('type')) == '') {
            return;
        }

        $this->prepareFields($type);

        $this->view->Type = $type;

        // Preset channel unit
        $type = new ORMChannelType($type);
        $this->fields['name']['VALUE'] = $type->getName();
        $this->fields['unit']['VALUE'] = $type->getUnit();

        $model = $type->getModelClass();
        $model::beforeCreate($this->fields);

        $this->ignore_returnto = true;
        $this->app->foreward('Edit');
    }

    /**
     *
     */
    public function addAction()
    {
        $this->view->SubTitle = I18N::translate('CreateChannel');

        if ($clone = $this->app->params->get('clone')) {
            $channel = new ORMChannel($clone);

            if ($channel->getId()) {
                foreach ($channel->asAssoc() as $key => $value) {
                    if (array_key_exists($key, $this->fields)) {
                        $this->fields[$key]['VALUE'] = $value;
                    }
                }
                $this->prepareFields($channel->getType(), $channel->getId());
                $this->fields['name']['VALUE'] = I18N::translate('CopyOf') . ' ' . $this->fields['name']['VALUE'];

                $type = new ORMChannelType($channel->getType());
                $model = $type->getModelClass();
                $model::beforeEdit($channel, $this->fields);
            }

            $this->app->foreward('Edit');
        } else {
            // Get all channel types
            $tblTypes = new ORMChannelType;
            // Get only not obsolete types
            $tblTypes->filter('id', array('min'=>1))->filterByObsolete(0)->find();

            foreach ($tblTypes->asAssoc() as $type) {
                $type['description'] = I18N::translate($type['description']);
                $types[] = $type;
            }

            $this->view->EntityTypes = $types;

            // Search for equipment templates
            $templates = array();
            $filemask = PVLng::path(PVLng::$RootDir, 'core', 'Channel', 'Templates', '*.php');
            foreach (glob($filemask) as $file) {
                $template = include $file;
                if (isset($template['channels'][0]['type'])) {
                    $type = new ORMChannelType($template['channels'][0]['type']);
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
    public function aliasPostAction()
    {
        $entity = new ORMTree;
        $entity->filterByEntity($this->request->post('id'))->findOne();

        if ($entity->getId()) {
            if ($entity->getAlias()) {
                Messages::error(I18N::translate('AliasStillExists'));
            } else {
                $alias = new ORMChannel;
                $alias
                    ->setType(0)
                    ->setChannel($entity->getGuid())
                    ->setComment(
                        'Alias of "'.$entity->getName()
                               . ($entity->getDescription() ? ' ('.$entity->getDescription().')' : '')
                        . '"'
                    )
                    ->insert();

                if (!$alias->isError()) {
                    Messages::success(I18N::translate('ChannelSaved'));
                } else {
                    Messages::error($entity->Error());
                }
            }
        }

        $this->app->redirect('/overview');
    }

    /**
     *
     */
    public function editPostAction()
    {
        if ($channel = $this->request->post('c')) {
            $entity = new ORMChannel($channel['id'] ?: 0);

            if (isset($channel['type-new'])) {
                $channel['type'] = $channel['type-new'];
            }

            $type  = new ORMChannelType($channel['type']);
            $model = $type->getModelClass();
            $model::beforeEdit($entity, $this->fields);

            // Set values
            foreach ($channel as $key => $value) {
                if (array_key_exists($key, $this->fields)) {
                    $this->fields[$key]['VALUE'] = $value;
                }
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
                        Messages::success(I18N::translate('ChannelSaved'));
                        if ($this->request->post('add2tree') &&
                            ($addTo = $this->request->post('tree')) &&
                            ($tree = Channel::byId($addTo)->addChild($entity->getId()))
                        ) {
                            Messages::success(I18N::translate('HierarchyCreated', 1));
                        }
                    } else {
                        $entity->update();
                        Messages::success(I18N::translate('ChannelSaved'));
                    }

                    $model::afterSave($entity, $tree);
                } catch (Exception $e) {
                    Messages::error('['.$e->getCode().'] '.$e->getMessage());
                    $ok = false;
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
    public function editAction()
    {
        $this->view->SubTitle = I18N::translate('EditChannel');

        if (!$this->view->Id) {
            // Add mode
            $this->AddToTree();
        } else {
            $channel = new ORMChannel($this->view->Id);

            // Try to edit an alias channel?
            if ($channel->getType() == 0) {
                Messages::info(I18N::translate('EditSwitchAliasWithOriginal'));
                // Search orignal channel by GUID from alias in hierarchy
                $t = (new ORMTree)->filterByGUID($channel->getChannel())->findOne();
                // Find now original channel by entity from hierarchy
                // $channel->reset()->filterById($t->getEntity())->findOne();
                $channel = new ORMChannel($t->getEntity());
                unset($t);
                // Set channel Id for edit form to new Id!
                $this->view->Id = $channel->getId();
            }

            $type = new ORMChannelType($channel->getType());

            if ($this->app->request->isGet()) { // If POST, this is called only if there where errors
                $model = $type->getModelClass();
                foreach ($channel->asAssoc() as $key => $value) {
                    if (array_key_exists($key, $this->fields)) {
                        $this->fields[$key]['VALUE'] = $value;
                    }
                }
                $model::beforeEdit($channel, $this->fields);
                $this->prepareFields($channel->getType(), $channel->getId());
            }

            $alternatives = new ORMChannelType;
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
                if ($id = $alternative->getId()) {
                    $replace[$id] = $alternative->getName();
                }
            }

            if (count($replace) > 1) {
                $this->view->replace = $replace;
            }
        }

        uasort(
            $this->fields,
            function ($a, $b) {
                return ($a['POSITION'] < $b['POSITION']) ? -1 : 1;
            }
        );

        $this->view->Fields = $this->fields;
    }

    /**
     *
     */
    public function deletePostAction()
    {
        $entity = new ORMChannel($this->request->post('id'));

        if ($entity->getId()) {
            $name = $entity->getName();
            $entity->delete();
            if (!$entity->isError()) {
                Messages::success(I18N::translate('ChannelDeleted', $name));
            } else {
                Messages::error(I18N::translate($entity->Error(), $name), true);
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
    protected function prepareFields($type, $entity = null)
    {
        // Got channel type number to create new channel
        $type = new ORMChannelType($type);

        if ($type->getId() == '') {
            Messages::error('Unknown channel type');
            $this->app->redirect('/channels');
        }

        // Get general field settings from channel type itself
        $fieldSettings = array($type->getType());

        if ($entity) { // null or 0
            $entity = new ORMChannel($entity);
            if ($entity->getNumeric()) {
                $fieldSettings[] = 'numeric';
                $fieldSettings[] = $entity->getMeter() ? 'meter' : 'sensor';
            } else {
                $fieldSettings[] = 'non-numeric';
                $fieldSettings[] = 'sensor'; // There can't be non-numeric meters ...
            }
        }

        if ($type->getWrite() && $type->getRead()) {
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

        foreach ($this->fields as $key => &$data) {
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
            $name = I18N::translate($h);
            $data['NAME'] = ($name != $h) ? $name : I18N::translate('channel::'.$key);

            $h = 'model::'.$type->model.'_'.$key.'Hint';
            $name = I18N::translate($h);
            $data['HINT'] = ($name != $h) ? $name : I18N::translate('channel::'.$key.'Hint');

            // Set value in ADD mode to default
            if (is_null($entity)) {
                $data['VALUE'] = trim($data['DEFAULT']);
            }

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
                            'TEXT'    => I18N::translate(trim($option[2])),
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
                            'TEXT'     => I18N::translate(trim($option[2])),
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
                        'TEXT'     => I18N::translate(trim($option[1])),
                        'SELECTED' => ($val == $data['VALUE'])
                        );
                    }
                    // Set type for template compiler
                    $data['TYPE'] = 'select';
                    break;

                // Detect icon by field name, not by type
                case $key == 'icon':
                    // In ADD mode the value is empty, preset from type
                    if (empty($data['VALUE'])) {
                        $data['VALUE'] = $type->getIcon();
                    }

                    // Collect possible icons
                    $TypeIcons = new ORMChannelTypeIcons;
                    foreach ($TypeIcons->find()->asObject() as $row) {
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
                    // Set type for template compiler
                    $data['TYPE'] = 'icon';
                    break;
            } // switch
        }

        $this->view->Type = $type->getId();
        $this->view->Icon = $type->getIcon();
        $this->view->TypeName = $type->getName();
        $this->view->TypeDesc = I18N::translate($type->getDescription());

        $h = $type->getDescription().'Help';
        $help = I18N::translate($h);
        // Check if a help text exists
        $this->view->TypeHelp = $help != $h ? $help : '';
    }

    /**
     *
     */
    protected function applyFieldSettings($conf)
    {
        // 1st try general config
        $config = PVLng::path(PVLng::$RootDir, 'core', 'Frontend', 'Channel', $conf.'.php');
        if (!file_exists($config)) {
            // 2nd try model specific config
            $config = PVLng::path(PVLng::$RootDir, 'core', 'Channel', $conf.'.conf.php');
            if (!file_exists($config)) {
                return;
            }
        }

        /// Yryie::debug('Load channel config: '.basename($config));
        $attr = include $config;

        // check all fields
        foreach ($attr as $key => $data) {
            // apply settings for this field
            $this->fields[$key] =
                isset($this->fields[$key])
              ? array_merge(
                  $this->fields[$key],
                  array_change_key_case($data, CASE_UPPER)
              )
              : array_merge(
                  array(
                      'POSITION'    => 400,
                      'FIELD'       => $key,
                      'TYPE'        => 'text',
                      'VISIBLE'     => true,
                      'REQUIRED'    => false,
                      'READONLY'    => false,
                      'PLACEHOLDER' => null,
                      'DEFAULT'     => null,
                      'VALUE'       => ''
                    ),
                  array_change_key_case($data, CASE_UPPER)
              );
        }
    }

    /**
     *
     */
    protected function addToTree()
    {
        $q = DBQuery::factory('pvlng_tree_view')
            ->get('id')
            ->get('CONCAT(REPEAT("&nbsp; &nbsp; ", `level`-2), IF(`haschilds`,"&bull; ","&rarr;"), "&nbsp;")', 'indent')
            ->get('name')
            ->get('`childs` = -1 OR `haschilds` < `childs`', 'available') // Unused child slots?
            ->filter('childs', array('ne' => 0));

        $this->view->AddTree = $this->db->queryRowsArray($q);
    }
}
