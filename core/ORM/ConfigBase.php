<?php
/**
 * Abstract base class for table "pvlng_config"
 *
 *****************************************************************************
 *                       NEVER EVER EDIT THIS FILE!
 *****************************************************************************
 * To extend functionallity edit "Config.php"
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
abstract class ConfigBase extends ORM
{

    // -----------------------------------------------------------------------
    // PUBLIC
    // -----------------------------------------------------------------------

    // -----------------------------------------------------------------------
    // Setter methods
    // -----------------------------------------------------------------------

    /**
     * Basic setter for field "key"
     *
     * @param  mixed    $key Key value
     * @return Instance For fluid interface
     */
    public function setKey($key)
    {
        $this->fields['key'] = $key;
        return $this;
    }

    /**
     * Raw setter for field "key", for INSERT, REPLACE and UPDATE
     *
     * @param  mixed    $key Key value
     * @return Instance For fluid interface
     */
    public function setKeyRaw($key)
    {
        $this->raw['key'] = $key;
        return $this;
    }

    /**
     * Basic setter for field "value"
     *
     * @param  mixed    $value Value value
     * @return Instance For fluid interface
     */
    public function setValue($value)
    {
        $this->fields['value'] = $value;
        return $this;
    }

    /**
     * Raw setter for field "value", for INSERT, REPLACE and UPDATE
     *
     * @param  mixed    $value Value value
     * @return Instance For fluid interface
     */
    public function setValueRaw($value)
    {
        $this->raw['value'] = $value;
        return $this;
    }

    /**
     * Basic setter for field "comment"
     *
     * @param  mixed    $comment Comment value
     * @return Instance For fluid interface
     */
    public function setComment($comment)
    {
        $this->fields['comment'] = $comment;
        return $this;
    }

    /**
     * Raw setter for field "comment", for INSERT, REPLACE and UPDATE
     *
     * @param  mixed    $comment Comment value
     * @return Instance For fluid interface
     */
    public function setCommentRaw($comment)
    {
        $this->raw['comment'] = $comment;
        return $this;
    }

    /**
     * Basic setter for field "changed"
     *
     * @param  mixed    $changed Changed value
     * @return Instance For fluid interface
     */
    public function setChanged($changed)
    {
        $this->fields['changed'] = $changed;
        return $this;
    }

    /**
     * Raw setter for field "changed", for INSERT, REPLACE and UPDATE
     *
     * @param  mixed    $changed Changed value
     * @return Instance For fluid interface
     */
    public function setChangedRaw($changed)
    {
        $this->raw['changed'] = $changed;
        return $this;
    }

    // -----------------------------------------------------------------------
    // Getter methods
    // -----------------------------------------------------------------------

    /**
     * Basic getter for field "key"
     *
     * @return mixed Key value
     */
    public function getKey()
    {
        return $this->fields['key'];
    }

    /**
     * Basic getter for field "value"
     *
     * @return mixed Value value
     */
    public function getValue()
    {
        return $this->fields['value'];
    }

    /**
     * Basic getter for field "comment"
     *
     * @return mixed Comment value
     */
    public function getComment()
    {
        return $this->fields['comment'];
    }

    /**
     * Basic getter for field "changed"
     *
     * @return mixed Changed value
     */
    public function getChanged()
    {
        return $this->fields['changed'];
    }

    // -----------------------------------------------------------------------
    // Filter methods
    // -----------------------------------------------------------------------

    /**
     * Filter for field "key"
     *
     * @param  mixed    $key Filter value
     * @return Instance For fluid interface
     */
    public function filterByKey($key)
    {
        return $this->filter('key', $key);
    }

    /**
     * Filter for field "value"
     *
     * @param  mixed    $value Filter value
     * @return Instance For fluid interface
     */
    public function filterByValue($value)
    {
        return $this->filter('value', $value);
    }

    /**
     * Filter for field "comment"
     *
     * @param  mixed    $comment Filter value
     * @return Instance For fluid interface
     */
    public function filterByComment($comment)
    {
        return $this->filter('comment', $comment);
    }

    /**
     * Filter for field "changed"
     *
     * @param  mixed    $changed Filter value
     * @return Instance For fluid interface
     */
    public function filterByChanged($changed)
    {
        return $this->filter('changed', $changed);
    }

    // -----------------------------------------------------------------------
    // PROTECTED
    // -----------------------------------------------------------------------

    /**
     * Update fields on insert on duplicate key
     */
    protected function onDuplicateKey()
    {
        return '`value` = VALUES(`value`)
              , `comment` = VALUES(`comment`)
              , `changed` = VALUES(`changed`)';
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
        CREATE TABLE IF NOT EXISTS `pvlng_config` (
          `key` varchar(64) NOT NULL DEFAULT \'\',
          `value` varchar(1024) NOT NULL DEFAULT \'\',
          `comment` varchar(255) NOT NULL DEFAULT \'\',
          `changed` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
          PRIMARY KEY (`key`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 PACK_KEYS=1 COMMENT=\'Application settings\'
    ';
    // @codingStandardsIgnoreEnd

    /**
     * Table name
     *
     * @var string $table Table name
     */
    protected $table = 'pvlng_config';

    /**
     *
     */
    protected $fields = [
        'key'     => '',
        'value'   => '',
        'comment' => '',
        'changed' => ''
    ];

    /**
     *
     */
    protected $nullable = [
        'key'     => false,
        'value'   => false,
        'comment' => false,
        'changed' => false
    ];

    /**
     *
     */
    protected $primary = ['key'];

    /**
     *
     */
    protected $autoinc = '';
}
