<?php
/**
 * Abstract base class for table "pvlng_type"
 *
 * *** NEVER EVER EDIT THIS FILE! ***
 *
 * To extend the functionallity, edit "ChannelType.php"!
 *
 * If you make changes here, they will be lost on next upgrade PVLng!
 *
 * @author     Knut Kohl <github@knutkohl.de>
 * @copyright  2017 Knut Kohl
 * @license    MIT License (MIT) http://opensource.org/licenses/MIT
 *
 * @author     PVLng ORM class builder
 * @version    1.4.0 / 2016-07-18
 */
namespace ORM;

/**
 *
 */
abstract class ChannelTypeBase extends \slimMVC\ORM
{

    // -----------------------------------------------------------------------
    // PUBLIC
    // -----------------------------------------------------------------------

    // -----------------------------------------------------------------------
    // Setter methods
    // -----------------------------------------------------------------------

    /**
     * Basic setter for field "id"
     *
     * @param  mixed    $id Id value
     * @return Instance For fluid interface
     */
    public function setId($id)
    {
        $this->fields['id'] = $id;
        return $this;
    }

    /**
     * Raw setter for field "id", for INSERT, REPLACE and UPDATE
     *
     * @param  mixed    $id Id value
     * @return Instance For fluid interface
     */
    public function setIdRaw($id)
    {
        $this->raw['id'] = $id;
        return $this;
    }

    /**
     * Basic setter for field "name"
     *
     * @param  mixed    $name Name value
     * @return Instance For fluid interface
     */
    public function setName($name)
    {
        $this->fields['name'] = $name;
        return $this;
    }

    /**
     * Raw setter for field "name", for INSERT, REPLACE and UPDATE
     *
     * @param  mixed    $name Name value
     * @return Instance For fluid interface
     */
    public function setNameRaw($name)
    {
        $this->raw['name'] = $name;
        return $this;
    }

    /**
     * Basic setter for field "description"
     *
     * @param  mixed    $description Description value
     * @return Instance For fluid interface
     */
    public function setDescription($description)
    {
        $this->fields['description'] = $description;
        return $this;
    }

    /**
     * Raw setter for field "description", for INSERT, REPLACE and UPDATE
     *
     * @param  mixed    $description Description value
     * @return Instance For fluid interface
     */
    public function setDescriptionRaw($description)
    {
        $this->raw['description'] = $description;
        return $this;
    }

    /**
     * Basic setter for field "model"
     *
     * @param  mixed    $model Model value
     * @return Instance For fluid interface
     */
    public function setModel($model)
    {
        $this->fields['model'] = $model;
        return $this;
    }

    /**
     * Raw setter for field "model", for INSERT, REPLACE and UPDATE
     *
     * @param  mixed    $model Model value
     * @return Instance For fluid interface
     */
    public function setModelRaw($model)
    {
        $this->raw['model'] = $model;
        return $this;
    }

    /**
     * Basic setter for field "unit"
     *
     * @param  mixed    $unit Unit value
     * @return Instance For fluid interface
     */
    public function setUnit($unit)
    {
        $this->fields['unit'] = $unit;
        return $this;
    }

    /**
     * Raw setter for field "unit", for INSERT, REPLACE and UPDATE
     *
     * @param  mixed    $unit Unit value
     * @return Instance For fluid interface
     */
    public function setUnitRaw($unit)
    {
        $this->raw['unit'] = $unit;
        return $this;
    }

    /**
     * Basic setter for field "type"
     *
     * @param  mixed    $type Type value
     * @return Instance For fluid interface
     */
    public function setType($type)
    {
        $this->fields['type'] = $type;
        return $this;
    }

    /**
     * Raw setter for field "type", for INSERT, REPLACE and UPDATE
     *
     * @param  mixed    $type Type value
     * @return Instance For fluid interface
     */
    public function setTypeRaw($type)
    {
        $this->raw['type'] = $type;
        return $this;
    }

    /**
     * Basic setter for field "childs"
     *
     * @param  mixed    $childs Childs value
     * @return Instance For fluid interface
     */
    public function setChilds($childs)
    {
        $this->fields['childs'] = $childs;
        return $this;
    }

    /**
     * Raw setter for field "childs", for INSERT, REPLACE and UPDATE
     *
     * @param  mixed    $childs Childs value
     * @return Instance For fluid interface
     */
    public function setChildsRaw($childs)
    {
        $this->raw['childs'] = $childs;
        return $this;
    }

    /**
     * Basic setter for field "read"
     *
     * @param  mixed    $read Read value
     * @return Instance For fluid interface
     */
    public function setRead($read)
    {
        $this->fields['read'] = $read;
        return $this;
    }

    /**
     * Raw setter for field "read", for INSERT, REPLACE and UPDATE
     *
     * @param  mixed    $read Read value
     * @return Instance For fluid interface
     */
    public function setReadRaw($read)
    {
        $this->raw['read'] = $read;
        return $this;
    }

    /**
     * Basic setter for field "write"
     *
     * @param  mixed    $write Write value
     * @return Instance For fluid interface
     */
    public function setWrite($write)
    {
        $this->fields['write'] = $write;
        return $this;
    }

    /**
     * Raw setter for field "write", for INSERT, REPLACE and UPDATE
     *
     * @param  mixed    $write Write value
     * @return Instance For fluid interface
     */
    public function setWriteRaw($write)
    {
        $this->raw['write'] = $write;
        return $this;
    }

    /**
     * Basic setter for field "graph"
     *
     * @param  mixed    $graph Graph value
     * @return Instance For fluid interface
     */
    public function setGraph($graph)
    {
        $this->fields['graph'] = $graph;
        return $this;
    }

    /**
     * Raw setter for field "graph", for INSERT, REPLACE and UPDATE
     *
     * @param  mixed    $graph Graph value
     * @return Instance For fluid interface
     */
    public function setGraphRaw($graph)
    {
        $this->raw['graph'] = $graph;
        return $this;
    }

    /**
     * Basic setter for field "icon"
     *
     * @param  mixed    $icon Icon value
     * @return Instance For fluid interface
     */
    public function setIcon($icon)
    {
        $this->fields['icon'] = $icon;
        return $this;
    }

    /**
     * Raw setter for field "icon", for INSERT, REPLACE and UPDATE
     *
     * @param  mixed    $icon Icon value
     * @return Instance For fluid interface
     */
    public function setIconRaw($icon)
    {
        $this->raw['icon'] = $icon;
        return $this;
    }

    /**
     * Basic setter for field "obsolete"
     *
     * @param  mixed    $obsolete Obsolete value
     * @return Instance For fluid interface
     */
    public function setObsolete($obsolete)
    {
        $this->fields['obsolete'] = $obsolete;
        return $this;
    }

    /**
     * Raw setter for field "obsolete", for INSERT, REPLACE and UPDATE
     *
     * @param  mixed    $obsolete Obsolete value
     * @return Instance For fluid interface
     */
    public function setObsoleteRaw($obsolete)
    {
        $this->raw['obsolete'] = $obsolete;
        return $this;
    }

    // -----------------------------------------------------------------------
    // Getter methods
    // -----------------------------------------------------------------------

    /**
     * Basic getter for field "id"
     *
     * @return mixed Id value
     */
    public function getId()
    {
        return $this->fields['id'];
    }

    /**
     * Basic getter for field "name"
     *
     * @return mixed Name value
     */
    public function getName()
    {
        return $this->fields['name'];
    }

    /**
     * Basic getter for field "description"
     *
     * @return mixed Description value
     */
    public function getDescription()
    {
        return $this->fields['description'];
    }

    /**
     * Basic getter for field "model"
     *
     * @return mixed Model value
     */
    public function getModel()
    {
        return $this->fields['model'];
    }

    /**
     * Basic getter for field "unit"
     *
     * @return mixed Unit value
     */
    public function getUnit()
    {
        return $this->fields['unit'];
    }

    /**
     * Basic getter for field "type"
     *
     * @return mixed Type value
     */
    public function getType()
    {
        return $this->fields['type'];
    }

    /**
     * Basic getter for field "childs"
     *
     * @return mixed Childs value
     */
    public function getChilds()
    {
        return $this->fields['childs'];
    }

    /**
     * Basic getter for field "read"
     *
     * @return mixed Read value
     */
    public function getRead()
    {
        return $this->fields['read'];
    }

    /**
     * Basic getter for field "write"
     *
     * @return mixed Write value
     */
    public function getWrite()
    {
        return $this->fields['write'];
    }

    /**
     * Basic getter for field "graph"
     *
     * @return mixed Graph value
     */
    public function getGraph()
    {
        return $this->fields['graph'];
    }

    /**
     * Basic getter for field "icon"
     *
     * @return mixed Icon value
     */
    public function getIcon()
    {
        return $this->fields['icon'];
    }

    /**
     * Basic getter for field "obsolete"
     *
     * @return mixed Obsolete value
     */
    public function getObsolete()
    {
        return $this->fields['obsolete'];
    }

    // -----------------------------------------------------------------------
    // Filter methods
    // -----------------------------------------------------------------------

    /**
     * Filter for field "id"
     *
     * @param  mixed    $id Filter value
     * @return Instance For fluid interface
     */
    public function filterById($id)
    {
        return $this->filter('id', $id);
    }

    /**
     * Filter for field "name"
     *
     * @param  mixed    $name Filter value
     * @return Instance For fluid interface
     */
    public function filterByName($name)
    {
        return $this->filter('name', $name);
    }

    /**
     * Filter for field "childs"
     *
     * @param  mixed    $childs Filter value
     * @return Instance For fluid interface
     */
    public function filterByChilds($childs)
    {
        return $this->filter('childs', $childs);
    }

    /**
     * Filter for field "read"
     *
     * @param  mixed    $read Filter value
     * @return Instance For fluid interface
     */
    public function filterByRead($read)
    {
        return $this->filter('read', $read);
    }

    /**
     * Filter for field "write"
     *
     * @param  mixed    $write Filter value
     * @return Instance For fluid interface
     */
    public function filterByWrite($write)
    {
        return $this->filter('write', $write);
    }

    /**
     * Filter for field "description"
     *
     * @param  mixed    $description Filter value
     * @return Instance For fluid interface
     */
    public function filterByDescription($description)
    {
        return $this->filter('description', $description);
    }

    /**
     * Filter for field "model"
     *
     * @param  mixed    $model Filter value
     * @return Instance For fluid interface
     */
    public function filterByModel($model)
    {
        return $this->filter('model', $model);
    }

    /**
     * Filter for field "unit"
     *
     * @param  mixed    $unit Filter value
     * @return Instance For fluid interface
     */
    public function filterByUnit($unit)
    {
        return $this->filter('unit', $unit);
    }

    /**
     * Filter for field "type"
     *
     * @param  mixed    $type Filter value
     * @return Instance For fluid interface
     */
    public function filterByType($type)
    {
        return $this->filter('type', $type);
    }

    /**
     * Filter for field "graph"
     *
     * @param  mixed    $graph Filter value
     * @return Instance For fluid interface
     */
    public function filterByGraph($graph)
    {
        return $this->filter('graph', $graph);
    }

    /**
     * Filter for field "icon"
     *
     * @param  mixed    $icon Filter value
     * @return Instance For fluid interface
     */
    public function filterByIcon($icon)
    {
        return $this->filter('icon', $icon);
    }

    /**
     * Filter for field "obsolete"
     *
     * @param  mixed    $obsolete Filter value
     * @return Instance For fluid interface
     */
    public function filterByObsolete($obsolete)
    {
        return $this->filter('obsolete', $obsolete);
    }

    // -----------------------------------------------------------------------
    // PROTECTED
    // -----------------------------------------------------------------------

    /**
     * Update fields on insert on duplicate key
     */
    protected function onDuplicateKey()
    {
        return '`name` = VALUES(`name`)
              , `description` = VALUES(`description`)
              , `model` = VALUES(`model`)
              , `unit` = VALUES(`unit`)
              , `type` = VALUES(`type`)
              , `childs` = VALUES(`childs`)
              , `read` = VALUES(`read`)
              , `write` = VALUES(`write`)
              , `graph` = VALUES(`graph`)
              , `icon` = VALUES(`icon`)
              , `obsolete` = VALUES(`obsolete`)';
    }

    /**
     * Call create table sql on first run and set to false
     */
    protected static $memory = false;

    /**
     * SQL for creation
     *
     * @var string $createSQL
     */
    // @codingStandardsIgnoreStart
    protected static $createSQL = '
        CREATE TABLE IF NOT EXISTS `pvlng_type` (
          `id` smallint(5) unsigned NOT NULL DEFAULT \'0\',
          `name` varchar(60) NOT NULL DEFAULT \'\',
          `description` varchar(255) NOT NULL DEFAULT \'\',
          `model` varchar(30) NOT NULL DEFAULT \'Group\',
          `unit` varchar(10) NOT NULL DEFAULT \'\',
          `type` enum(\'group\',\'general\',\'numeric\',\'sensor\',\'meter\') NOT NULL DEFAULT \'group\',
          `childs` tinyint(1) NOT NULL DEFAULT \'0\',
          `read` tinyint(1) unsigned NOT NULL DEFAULT \'0\',
          `write` tinyint(1) unsigned NOT NULL DEFAULT \'0\',
          `graph` tinyint(1) unsigned NOT NULL DEFAULT \'0\',
          `icon` varchar(255) NOT NULL DEFAULT \'\',
          `obsolete` tinyint(1) unsigned NOT NULL DEFAULT \'0\',
          PRIMARY KEY (`id`),
          UNIQUE KEY `name` (`name`),
          KEY `childs` (`childs`),
          KEY `read` (`read`),
          KEY `write` (`write`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 PACK_KEYS=1 COMMENT=\'Channel types\'
    ';
    // @codingStandardsIgnoreEnd

    /**
     * Table name
     *
     * @var string $table Table name
     */
    protected $table = 'pvlng_type';

    /**
     *
     */
    protected $fields = array(
        'id'          => '',
        'name'        => '',
        'description' => '',
        'model'       => '',
        'unit'        => '',
        'type'        => '',
        'childs'      => '',
        'read'        => '',
        'write'       => '',
        'graph'       => '',
        'icon'        => '',
        'obsolete'    => ''
    );

    /**
     *
     */
    protected $nullable = array(
        'id'          => false,
        'name'        => false,
        'description' => false,
        'model'       => false,
        'unit'        => false,
        'type'        => false,
        'childs'      => false,
        'read'        => false,
        'write'       => false,
        'graph'       => false,
        'icon'        => false,
        'obsolete'    => false
    );

    /**
     *
     */
    protected $primary = array(
        'id'
    );

    /**
     *
     */
    protected $autoinc = '';
}
