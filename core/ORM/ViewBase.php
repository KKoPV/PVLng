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
 * @copyright  2016 Knut Kohl
 * @license    MIT License (MIT) http://opensource.org/licenses/MIT
 *
 * @author     PVLng ORM class builder
 * @version    1.4.0 / 2016-07-18
 */
namespace ORM;

/**
 *
 */
abstract class ViewBase extends \slimMVC\ORM
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
    }   // setName()

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
    }   // setNameRaw()

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
    }   // setPublic()

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
    }   // setPublicRaw()

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
    }   // setData()

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
    }   // setDataRaw()

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
    }   // setSlug()

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
    }   // setSlugRaw()

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
    }   // getName()

    /**
     * Basic getter for field "public"
     *
     * @return mixed Public value
     */
    public function getPublic()
    {
        return $this->fields['public'];
    }   // getPublic()

    /**
     * Basic getter for field "data"
     *
     * @return mixed Data value
     */
    public function getData()
    {
        return $this->fields['data'];
    }   // getData()

    /**
     * Basic getter for field "slug"
     *
     * @return mixed Slug value
     */
    public function getSlug()
    {
        return $this->fields['slug'];
    }   // getSlug()

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

        $this->filter[] = $this->field('name').' = '.$this->quote($name).'';
        $this->filter[] = $this->field('public').' = '.$this->quote($public).'';
        return $this;
    }   // filterByNamePublic()

    /**
     * Filter for field "slug"
     *
     * @param  mixed    $slug Filter value
     * @return Instance For fluid interface
     */
    public function filterBySlug($slug)
    {
        $this->filter[] = $this->field('slug').' = '.$this->quote($slug);
        return $this;
    }   // filterBySlug()

    /**
     * Filter for field "public"
     *
     * @param  mixed    $public Filter value
     * @return Instance For fluid interface
     */
    public function filterByPublic($public)
    {
        $this->filter[] = $this->field('public').' = '.$this->quote($public);
        return $this;
    }   // filterByPublic()

    /**
     * Filter for field "name"
     *
     * @param  mixed    $name Filter value
     * @return Instance For fluid interface
     */
    public function filterByName($name)
    {
        $this->filter[] = $this->field('name').' = '.$this->quote($name);
        return $this;
    }   // filterByName()

    /**
     * Filter for field "data"
     *
     * @param  mixed    $data Filter value
     * @return Instance For fluid interface
     */
    public function filterByData($data)
    {
        $this->filter[] = $this->field('data').' = '.$this->quote($data);
        return $this;
    }   // filterByData()

    // -----------------------------------------------------------------------
    // PROTECTED
    // -----------------------------------------------------------------------

    /**
     * Update fields on insert on duplicate key
     */
    protected function onDuplicateKey()
    {
        return '`data` = '.$this->quote($this->fields['data']).'
              , `slug` = '.$this->quote($this->fields['slug']).'';
    }   // onDuplicateKey()

    /**
     * Table name
     *
     * @var string $table Table name
     */
    protected $table = 'pvlng_view';

    /**
     * SQL for creation
     *
     * @var string $createSQL
     */
    protected $createSQL = '
        CREATE TABLE `pvlng_view` (
          `name` varchar(50) NOT NULL DEFAULT \'\' COMMENT \'Chart name\',
          `public` tinyint(1) unsigned NOT NULL DEFAULT \'0\' COMMENT \'View type (private/public/mobile)\',
          `data` text NOT NULL COMMENT \'Serialized channel data\',
          `slug` varchar(50) NOT NULL DEFAULT \'\' COMMENT \'URL-save slug\',
          PRIMARY KEY (`name`,`public`),
          UNIQUE KEY `slug` (`slug`),
          KEY `public` (`public`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 PACK_KEYS=1 COMMENT=\'View variants\'
    ';

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
        'data'   => false,
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
