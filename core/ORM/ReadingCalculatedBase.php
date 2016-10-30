<?php
/**
 * Abstract base class for table "pvlng_reading_tmp"
 *
 * *** NEVER EVER EDIT THIS FILE! ***
 *
 * To extend the functionallity, edit "ReadingCalculated.php"!
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
abstract class ReadingCalculatedBase extends \slimMVC\ORM
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
     * Basic setter for field "start"
     *
     * @param  mixed    $start Start value
     * @return Instance For fluid interface
     */
    public function setStart($start)
    {
        $this->fields['start'] = $start;
        return $this;
    }

    /**
     * Raw setter for field "start", for INSERT, REPLACE and UPDATE
     *
     * @param  mixed    $start Start value
     * @return Instance For fluid interface
     */
    public function setStartRaw($start)
    {
        $this->raw['start'] = $start;
        return $this;
    }

    /**
     * Basic setter for field "end"
     *
     * @param  mixed    $end End value
     * @return Instance For fluid interface
     */
    public function setEnd($end)
    {
        $this->fields['end'] = $end;
        return $this;
    }

    /**
     * Raw setter for field "end", for INSERT, REPLACE and UPDATE
     *
     * @param  mixed    $end End value
     * @return Instance For fluid interface
     */
    public function setEndRaw($end)
    {
        $this->raw['end'] = $end;
        return $this;
    }

    /**
     * Basic setter for field "lifetime"
     *
     * @param  mixed    $lifetime Lifetime value
     * @return Instance For fluid interface
     */
    public function setLifetime($lifetime)
    {
        $this->fields['lifetime'] = $lifetime;
        return $this;
    }

    /**
     * Raw setter for field "lifetime", for INSERT, REPLACE and UPDATE
     *
     * @param  mixed    $lifetime Lifetime value
     * @return Instance For fluid interface
     */
    public function setLifetimeRaw($lifetime)
    {
        $this->raw['lifetime'] = $lifetime;
        return $this;
    }

    /**
     * Basic setter for field "uid"
     *
     * @param  mixed    $uid Uid value
     * @return Instance For fluid interface
     */
    public function setUid($uid)
    {
        $this->fields['uid'] = $uid;
        return $this;
    }

    /**
     * Raw setter for field "uid", for INSERT, REPLACE and UPDATE
     *
     * @param  mixed    $uid Uid value
     * @return Instance For fluid interface
     */
    public function setUidRaw($uid)
    {
        $this->raw['uid'] = $uid;
        return $this;
    }

    /**
     * Basic setter for field "created"
     *
     * @param  mixed    $created Created value
     * @return Instance For fluid interface
     */
    public function setCreated($created)
    {
        $this->fields['created'] = $created;
        return $this;
    }

    /**
     * Raw setter for field "created", for INSERT, REPLACE and UPDATE
     *
     * @param  mixed    $created Created value
     * @return Instance For fluid interface
     */
    public function setCreatedRaw($created)
    {
        $this->raw['created'] = $created;
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
     * Basic getter for field "start"
     *
     * @return mixed Start value
     */
    public function getStart()
    {
        return $this->fields['start'];
    }

    /**
     * Basic getter for field "end"
     *
     * @return mixed End value
     */
    public function getEnd()
    {
        return $this->fields['end'];
    }

    /**
     * Basic getter for field "lifetime"
     *
     * @return mixed Lifetime value
     */
    public function getLifetime()
    {
        return $this->fields['lifetime'];
    }

    /**
     * Basic getter for field "uid"
     *
     * @return mixed Uid value
     */
    public function getUid()
    {
        return $this->fields['uid'];
    }

    /**
     * Basic getter for field "created"
     *
     * @return mixed Created value
     */
    public function getCreated()
    {
        return $this->fields['created'];
    }

    // -----------------------------------------------------------------------
    // Filter methods
    // -----------------------------------------------------------------------

    /**
     * Filter for unique fields "id', 'start', 'end"
     *
     * @param  mixed    $id, $start, $end Filter values
     * @return Instance For fluid interface
     */
    public function filterByIdStartEnd($id, $start, $end)
    {

        $this->filter[] = $this->field('id').' = '.$this->quote($id).'';
        $this->filter[] = $this->field('start').' = '.$this->quote($start).'';
        $this->filter[] = $this->field('end').' = '.$this->quote($end).'';
        return $this;
    }

    /**
     * Filter for field "uid"
     *
     * @param  mixed    $uid Filter value
     * @return Instance For fluid interface
     */
    public function filterByUid($uid)
    {
        $this->filter[] = $this->field('uid').' = '.$this->quote($uid);
        return $this;
    }

    /**
     * Filter for field "created"
     *
     * @param  mixed    $created Filter value
     * @return Instance For fluid interface
     */
    public function filterByCreated($created)
    {
        $this->filter[] = $this->field('created').' = '.$this->quote($created);
        return $this;
    }

    /**
     * Filter for field "id"
     *
     * @param  mixed    $id Filter value
     * @return Instance For fluid interface
     */
    public function filterById($id)
    {
        $this->filter[] = $this->field('id').' = '.$this->quote($id);
        return $this;
    }

    /**
     * Filter for field "start"
     *
     * @param  mixed    $start Filter value
     * @return Instance For fluid interface
     */
    public function filterByStart($start)
    {
        $this->filter[] = $this->field('start').' = '.$this->quote($start);
        return $this;
    }

    /**
     * Filter for field "end"
     *
     * @param  mixed    $end Filter value
     * @return Instance For fluid interface
     */
    public function filterByEnd($end)
    {
        $this->filter[] = $this->field('end').' = '.$this->quote($end);
        return $this;
    }

    /**
     * Filter for field "lifetime"
     *
     * @param  mixed    $lifetime Filter value
     * @return Instance For fluid interface
     */
    public function filterByLifetime($lifetime)
    {
        $this->filter[] = $this->field('lifetime').' = '.$this->quote($lifetime);
        return $this;
    }

    // -----------------------------------------------------------------------
    // PROTECTED
    // -----------------------------------------------------------------------

    /**
     * Update fields on insert on duplicate key
     */
    protected function onDuplicateKey()
    {
        return '`lifetime` = VALUES(`lifetime`)
              , `uid` = VALUES(`uid`)
              , `created` = VALUES(`created`)';
    }

    /**
     * Table name
     *
     * @var string $table Table name
     */
    protected $table = 'pvlng_reading_tmp';

    /**
     * SQL for creation
     *
     * @var string $createSQL
     */
    protected $createSQL = '
        CREATE TABLE `pvlng_reading_tmp` (
          `id` smallint(5) unsigned NOT NULL DEFAULT \'0\' COMMENT \'pvlng_channel -> id\',
          `start` int(10) unsigned NOT NULL DEFAULT \'0\' COMMENT \'Generated for start .. end\',
          `end` int(10) unsigned NOT NULL DEFAULT \'0\' COMMENT \'Generated for start .. end\',
          `lifetime` mediumint(8) unsigned NOT NULL DEFAULT \'0\' COMMENT \'Lifetime of data\',
          `uid` smallint(5) unsigned NOT NULL DEFAULT \'0\' COMMENT \'Tempory data Id\',
          `created` int(10) NOT NULL DEFAULT \'0\' COMMENT \'Record created\',
          PRIMARY KEY (`id`,`start`,`end`),
          UNIQUE KEY `uid` (`uid`),
          KEY `created` (`created`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT=\'Buffer and remember internal calculated data\'
    ';

    /**
     *
     */
    protected $fields = array(
        'id'       => '',
        'start'    => '',
        'end'      => '',
        'lifetime' => '',
        'uid'      => '',
        'created'  => ''
    );

    /**
     *
     */
    protected $nullable = array(
        'id'       => false,
        'start'    => false,
        'end'      => false,
        'lifetime' => false,
        'uid'      => false,
        'created'  => false
    );

    /**
     *
     */
    protected $primary = array(
        'id',
        'start',
        'end'
    );

    /**
     *
     */
    protected $autoinc = '';

}
