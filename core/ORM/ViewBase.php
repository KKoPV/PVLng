<?php
/**
 * Base access class for "pvlng_view"
 *
 * NEVER EVER EDIT THIS FILE
 *
 * To extend the functionallity, edit "View.php"
 *
 * If you make changes here, they will be lost on next upgrade PVLng!
 *
 * @author     PVLng ORM class builder
 * @version    1.0.0
 */
namespace ORM;

/**
 *
 */
abstract class ViewBase extends \slimMVC\ORM {

    // -----------------------------------------------------------------------
    // PUBLIC
    // -----------------------------------------------------------------------

    /**
     * Forge method for key field(s)
     *
     * @param mixed Field value
     */
    public static function forge( $name, $public ) {
        return new static(array($name, $public));
    } // forge()

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
    } // setName()

    /**
     * Basic setter for field 'public'
     *
     * @param  mixed    $public Public value
     * @return Instance For fluid interface
     */
    public function setPublic( $public ) {
        $this->fields['public'] = $public;
        return $this;
    } // setPublic()

    /**
     * Basic setter for field 'data'
     *
     * @param  mixed    $data Data value
     * @return Instance For fluid interface
     */
    public function setData( $data ) {
        $this->fields['data'] = $data;
        return $this;
    } // setData()

    /**
     * Basic setter for field 'slug'
     *
     * @param  mixed    $slug Slug value
     * @return Instance For fluid interface
     */
    public function setSlug( $slug ) {
        $this->fields['slug'] = $slug;
        return $this;
    } // setSlug()

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
    } // getName()

    /**
     * Basic getter for field 'public'
     *
     * @return mixed Public value
     */
    public function getPublic() {
        return $this->fields['public'];
    } // getPublic()

    /**
     * Basic getter for field 'data'
     *
     * @return mixed Data value
     */
    public function getData() {
        return $this->fields['data'];
    } // getData()

    /**
     * Basic getter for field 'slug'
     *
     * @return mixed Slug value
     */
    public function getSlug() {
        return $this->fields['slug'];
    } // getSlug()

    // -----------------------------------------------------------------------
    // Filter methods
    // -----------------------------------------------------------------------

    /**
     * Filter for field slug
     *
     * @param mixed Field value
     */
    public function filterBySlug( $slug ) {
        return $this->filter('slug', $slug);
    } // filterBySlug()

    /**
     * Filter for field name
     *
     * @param mixed Field value
     */
    public function filterByName( $name ) {
        return $this->filter('name', $name);
    } // filterByName()

    /**
     * Filter for field public
     *
     * @param mixed Field value
     */
    public function filterByPublic( $public ) {
        return $this->filter('public', $public);
    } // filterByPublic()

    /**
     * Filter for field data
     *
     * @param mixed Field value
     */
    public function filterByData( $data ) {
        return $this->filter('data', $data);
    } // filterByData()

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
