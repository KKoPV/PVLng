<?php
/**
 *
 *
 * @author     Knut Kohl <github@knutkohl.de>
 * @copyright  2012-2014 Knut Kohl
 * @license    MIT License (MIT) http://opensource.org/licenses/MIT
 * @version    1.0.0
 */
namespace Channel;

/**
 *
 */
abstract class InternalCalc extends Channel {

    /**
     * Readings table object
     */
    protected $orgId;

    /**
     * Readings table object
     */
    protected $data;

    /**
     *
     */
    protected $LifeTime;

    /**
     *
     */
    protected function __construct( \ORM\Tree $channel ) {
        parent::__construct($channel);

        $this->orgId = $this->entity;

        $this->data = \ORM\ReadingMemory::factory($this->numeric);
    }

    /**
     * Overwrite default channel tables
     */
    protected $table = array(
        'pvlng_reading_str_tmp', // numeric == 0
        'pvlng_reading_num_tmp'  // numeric == 1
    );

    /**
     *
     */
    protected function saveValue( $timestamp, $value ) {
        $this->data->setTimestamp($timestamp);
        $this->data->setData($value);
        return $this->data->insert();
    }

    /**
     *
     */
    protected function saveValues( $values ) {
        $cnt = 0;
        if ($values instanceof \Buffer) {
            foreach ($values as $row) {
                $this->data->timestamp = $row['timestamp'];
                $this->data->data      = $row['data'];
                $cnt += $this->data->insert();
            }
        } else {
            foreach ($values as $this->data->timestamp=>$this->data->data) {
                $cnt += $this->data->insert();
            }
        }
        return $cnt;
    }

    /**
     *
     */
    protected function dataExists( $lifetime=NULL ) {

        if (!is_null($lifetime)) {
            $this->LifeTime = $lifetime;
        } else {
            $this->LifeTime = $this->end-1 < strtotime('midnight')
                           // Buffer data in the past (before today) for 1 day
                            ? 86400
                           // Use configration setting
                            : (new \ORM\Settings)->getModelValue('InternalCalc', 'LifeTime');
        }

        $sql = $this->db->sql(
            'SELECT `pvlng_reading_tmp_start`({1}, {2}, {3}, {4})',
            $this->orgId, $this->start, $this->end, $this->LifeTime
        );

        while (($uid = $this->db->queryOne($sql)) == 0) {
            // Another instance is generating the data, wait some time before next check
            usleep(200 * 1000);
        }

        // < 0 - This instance have to generate the data
        // > 0 - temp. Id of data, reuse
        if ($uid < 0) {
            $this->entity = -$uid;
            $this->data->setId($this->entity);
            return FALSE;
        } else {
            $this->entity = $uid;
            return TRUE;
        }
    }

    /**
     *
     */
    protected function dataCreated() {
        // Data was just created, mark
        $this->db->query('CALL `pvlng_reading_tmp_done`({1})', $this->entity);
    }
}
