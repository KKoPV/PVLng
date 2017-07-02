<?php
/**
 * Abstract base class for table "pvlng_view"
 *
 * *** NEVER EVER EDIT THIS FILE! ***
 *
 * To extend the functionallity, edit "View.php"!
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
use Core\ORM;

/**
 *
 */
abstract class ViewBase extends ORM
{

    // -----------------------------------------------------------------------
    // PUBLIC
    // -----------------------------------------------------------------------

    // -----------------------------------------------------------------------
    // Setter methods
    // -----------------------------------------------------------------------

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
     * Basic setter for field "public"
     *
     * @param  mixed    $public Public value
     * @return Instance For fluid interface
     */
    public function setPublic($public)
    {
        $this->fields['public'] = $public;
        return $this;
    }

    /**
     * Raw setter for field "public", for INSERT, REPLACE and UPDATE
     *
     * @param  mixed    $public Public value
     * @return Instance For fluid interface
     */
    public function setPublicRaw($public)
    {
        $this->raw['public'] = $public;
        return $this;
    }

    /**
     * Basic setter for field "data"
     *
     * @param  mixed    $data Data value
     * @return Instance For fluid interface
     */
    public function setData($data)
    {
        $this->fields['data'] = $data;
        return $this;
    }

    /**
     * Raw setter for field "data", for INSERT, REPLACE and UPDATE
     *
     * @param  mixed    $data Data value
     * @return Instance For fluid interface
     */
    public function setDataRaw($data)
    {
        $this->raw['data'] = $data;
        return $this;
    }

    /**
     * Basic setter for field "slug"
     *
     * @param  mixed    $slug Slug value
     * @return Instance For fluid interface
     */
    public function setSlug($slug)
    {
        $this->fields['slug'] = $slug;
        return $this;
    }

    /**
     * Raw setter for field "slug", for INSERT, REPLACE and UPDATE
     *
     * @param  mixed    $slug Slug value
     * @return Instance For fluid interface
     */
    public function setSlugRaw($slug)
    {
        $this->raw['slug'] = $slug;
        return $this;
    }

    // -----------------------------------------------------------------------
    // Getter methods
    // -----------------------------------------------------------------------

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
     * Basic getter for field "public"
     *
     * @return mixed Public value
     */
    public function getPublic()
    {
        return $this->fields['public'];
    }

    /**
     * Basic getter for field "data"
     *
     * @return mixed Data value
     */
    public function getData()
    {
        return $this->fields['data'];
    }

    /**
     * Basic getter for field "slug"
     *
     * @return mixed Slug value
     */
    public function getSlug()
    {
        return $this->fields['slug'];
    }

    // -----------------------------------------------------------------------
    // Filter methods
    // -----------------------------------------------------------------------

    /**
     * Filter for unique fields "name', 'public"
     *
     * @param  mixed    $name, $public Filter values
     * @return Instance For fluid interface
     */
    public function filterByNamePublic($name, $public)
    {
        $this->filter('name', $name);
        $this->filter('public', $public);
        return $this;
    }

    /**
     * Filter for field "slug"
     *
     * @param  mixed    $slug Filter value
     * @return Instance For fluid interface
     */
    public function filterBySlug($slug)
    {
        return $this->filter('slug', $slug);
    }

    /**
     * Filter for field "public"
     *
     * @param  mixed    $public Filter value
     * @return Instance For fluid interface
     */
    public function filterByPublic($public)
    {
        return $this->filter('public', $public);
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
     * Filter for field "data"
     *
     * @param  mixed    $data Filter value
     * @return Instance For fluid interface
     */
    public function filterByData($data)
    {
        return $this->filter('data', $data);
    }

    // -----------------------------------------------------------------------
    // PROTECTED
    // -----------------------------------------------------------------------

    /**
     * Update fields on insert on duplicate key
     */
    protected function onDuplicateKey()
    {
        return '`data` = VALUES(`data`)
              , `slug` = VALUES(`slug`)';
    }

    /**
     * Call create table sql on class creation and set to false
     */
    protected static $memory = false;

    /**
     * SQL for creation
     *
     * @var string $createSQL
     */
    // @codingStandardsIgnoreStart
    protected static $createSQL = '
        CREATE TABLE IF NOT EXISTS `pvlng_view` (
          `name` varchar(50) NOT NULL DEFAULT \'\' COMMENT \'Chart name\',
          `public` tinyint(1) unsigned NOT NULL DEFAULT \'0\' COMMENT \'View type (private/public/mobile)\',
          `data` text COMMENT \'Serialized channel data\',
          `slug` varchar(50) NOT NULL DEFAULT \'\' COMMENT \'URL-save slug\',
          PRIMARY KEY (`name`,`public`),
          UNIQUE KEY `slug` (`slug`),
          KEY `public` (`public`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 PACK_KEYS=1 COMMENT=\'View variants\'
    ';
    // @codingStandardsIgnoreEnd

    /**
     * Table name
     *
     * @var string $table Table name
     */
    protected $table = 'pvlng_view';

    /**
     *
     */
    protected $fields = array(
        'name'   => '',
        'public' => '',
        'data'   => '',
        'slug'   => ''
    );

    /**
     *
     */
    protected $nullable = array(
        'name'   => false,
        'public' => false,
        'data'   => true,
        'slug'   => false
    );

    /**
     *
     */
    protected $primary = array(
        'name',
        'public'
    );

    /**
     *
     */
    protected $autoinc = '';
}
