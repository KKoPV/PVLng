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

        $this->data = \ORM\ReadingMemory::factory($this->numeric);
        $this->data->id = $this->entity;

        $this->LifeTime = (new \ORM\Settings)->getModelValue('InternalCalc', 'LifeTime');
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
        $this->data->timestamp = $timestamp;
        $this->data->data      = $value;
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
        $sql = $this->db->SQL(
            'SELECT `pvlng_reading_tmp`({1}, {2}, {3}, {4}, 0)',
            $this->entity, $this->start, $this->end, $lifetime ?: $this->LifeTime
        );
        while (($flag = $this->db->queryOne($sql)) == 1) {
            // Another instance is generating the data, wait 200ms before next check
            usleep(200 * 1000);
        }
        // = 0 - This instance have to generate the data
        // > 1 - Timestamp of data generation, reuse
        return (bool) $flag;
    }

    /**
     *
     */
    protected function dataCreated() {
        // Data was just created, mark
        $this->db->query(
            'SELECT `pvlng_reading_tmp`({1}, {2}, {3}, 0, 1)',
            $this->entity, $this->start, $this->end
        );
    }
}
