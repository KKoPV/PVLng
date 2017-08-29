<?php
/**
 * Abstract base class for table "pvlng_type_icons"
 *
 *****************************************************************************
 *                       NEVER EVER EDIT THIS FILE!
 *****************************************************************************
 * To extend functionallity edit "ChannelTypeIcons.php"
 * If you make changes here, they will be lost on next upgrade!
 *
 * @author     Knut Kohl <github@knutkohl.de>
 * @copyright  2017 Knut Kohl
 * @license    MIT License (MIT) http://opensource.org/licenses/MIT
 *
 * @author     ORM class builder
 * @version    2.0.0 / 2017-08-17
 */
namespace ORM;

/**
 *
 */
use Core\ORM;

/**
 *
 */
abstract class ChannelTypeIconsBase extends ORM
{

    // -----------------------------------------------------------------------
    // PUBLIC
    // -----------------------------------------------------------------------

    // -----------------------------------------------------------------------
    // Setter methods
    // -----------------------------------------------------------------------

    /**
     * "pvlng_type_icons" is a view, no setters
     */

    // -----------------------------------------------------------------------
    // Getter methods
    // -----------------------------------------------------------------------

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
     * Basic getter for field "name"
     *
     * @return mixed Name value
     */
    public function getName()
    {
        return $this->fields['name'];
    }

    // -----------------------------------------------------------------------
    // Filter methods
    // -----------------------------------------------------------------------

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
     * Filter for field "name"
     *
     * @param  mixed    $name Filter value
     * @return Instance For fluid interface
     */
    public function filterByName($name)
    {
        return $this->filter('name', $name);
    }

    // -----------------------------------------------------------------------
    // PROTECTED
    // -----------------------------------------------------------------------

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
        CREATE VIEW `pvlng_type_icons` AS select `pvlng_type`.`icon` AS `icon`,group_concat(`pvlng_type`.`name` order by `pvlng_type`.`name` ASC separator \', \') AS `name` from `pvlng_type` where (`pvlng_type`.`id` <> 0) group by `pvlng_type`.`icon` order by group_concat(`pvlng_type`.`name` order by `pvlng_type`.`name` ASC separator \',\')
    ';
    // @codingStandardsIgnoreEnd

    /**
     * Table name
     *
     * @var string $table Table name
     */
    protected $table = 'pvlng_type_icons';

    /**
     *
     */
    protected $fields = [
        'icon' => '',
        'name' => ''
    ];

    /**
     *
     */
    protected $nullable = [

    ];

    /**
     *
     */
    protected $primary = [];

    /**
     *
     */
    protected $autoinc = '';
}
