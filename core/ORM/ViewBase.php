<?php
/**
 * Abstract base class for table 'pvlng_view'
 *
 * *** NEVER EVER EDIT THIS FILE! ***
 *
 * To extend the functionallity, edit "View.php"
 *
 * If you make changes here, they will be lost on next upgrade PVLng!
 *
 * @author     Knut Kohl <github@knutkohl.de>
 * @copyright  2014 Knut Kohl
 * @license    MIT License (MIT) http://opensource.org/licenses/MIT
 *
 * @author     PVLng ORM class builder
 * @version    1.1.0 / 2014-06-04
 */
namespace ORM;

/**
 *
 */
abstract class ViewBase extends \slimMVC\ORM {

    // -----------------------------------------------------------------------
    // PUBLIC
    // -----------------------------------------------------------------------

    // -----------------------------------------------------------------------
    // Setter methods
    // -----------------------------------------------------------------------

    /**
     * Basic setter for field 'name'
     *
     * @param  mixed    $name Name value
     * @return Instance For fluid interface
     */
    public function setName( $name ) {
        $this->fields['name'] = $name;
        return $this;
    }   // setName()

    /**
     * Basic setter for field 'public'
     *
     * @param  mixed    $public Public value
     * @return Instance For fluid interface
     */
    public function setPublic( $public ) {
        $this->fields['public'] = $public;
        return $this;
    }   // setPublic()

    /**
     * Basic setter for field 'data'
     *
     * @param  mixed    $data Data value
     * @return Instance For fluid interface
     */
    public function setData( $data ) {
        $this->fields['data'] = $data;
        return $this;
    }   // setData()

    /**
     * Basic setter for field 'slug'
     *
     * @param  mixed    $slug Slug value
     * @return Instance For fluid interface
     */
    public function setSlug( $slug ) {
        $this->fields['slug'] = $slug;
        return $this;
    }   // setSlug()

    // -----------------------------------------------------------------------
    // Getter methods
    // -----------------------------------------------------------------------

    /**
     * Basic getter for field 'name'
     *
     * @return mixed Name value
     */
    public function getName() {
        return $this->fields['name'];
    }   // getName()

    /**
     * Basic getter for field 'public'
     *
     * @return mixed Public value
     */
    public function getPublic() {
        return $this->fields['public'];
    }   // getPublic()

    /**
     * Basic getter for field 'data'
     *
     * @return mixed Data value
     */
    public function getData() {
        return $this->fields['data'];
    }   // getData()

    /**
     * Basic getter for field 'slug'
     *
     * @return mixed Slug value
     */
    public function getSlug() {
        return $this->fields['slug'];
    }   // getSlug()

    // -----------------------------------------------------------------------
    // Filter methods
    // -----------------------------------------------------------------------

    /**
     * Filter for unique fields 'name', 'public'
     *
     * @param  mixed    $name, $public Filter values
     * @return Instance For fluid interface
     */
    public function filterByNamePublic( $name, $public ) {
        $this->filter[] = '`name` = "'.$this->quote($name).'"';
        $this->filter[] = '`public` = "'.$this->quote($public).'"';
        return $this;
    }   // filterByNamePublic()

    /**
     * Filter for field 'slug'
     *
     * @param  mixed    $slug Filter value
     * @return Instance For fluid interface
     */
    public function filterBySlug( $slug ) {
        $this->filter[] = '`slug` = "'.$this->quote($slug).'"';
        return $this;
    }   // filterBySlug()

    /**
     * Filter for field 'name'
     *
     * @param  mixed    $name Filter value
     * @return Instance For fluid interface
     */
    public function filterByName( $name ) {
        $this->filter[] = '`name` = "'.$this->quote($name).'"';
        return $this;
    }   // filterByName()

    /**
     * Filter for field 'public'
     *
     * @param  mixed    $public Filter value
     * @return Instance For fluid interface
     */
    public function filterByPublic( $public ) {
        $this->filter[] = '`public` = "'.$this->quote($public).'"';
        return $this;
    }   // filterByPublic()

    /**
     * Filter for field 'data'
     *
     * @param  mixed    $data Filter value
     * @return Instance For fluid interface
     */
    public function filterByData( $data ) {
        $this->filter[] = '`data` = "'.$this->quote($data).'"';
        return $this;
    }   // filterByData()

    // -----------------------------------------------------------------------
    // PROTECTED
    // -----------------------------------------------------------------------

    /**
     * Table name
     *
     * @var string $table Table name
     */
    protected $table = 'pvlng_view';

}
