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
     *
     */
    protected function __construct( \ORM\Tree $channel ) {
        parent::__construct($channel);

        $this->data = $this->numeric ? new \ORM\ReadingNumMemory :  new \ORM\ReadingStrMemory;

        // If the same channel is used in one chart multiple times (also as Alias),
        // we have a race condition and the instances deletes the data of the others ...
        // So save for each instance its own data set
        $this->entity = rand(60000, 65535);

        // Clean up
        $this->data->deleteById($this->entity);

        $this->data->id = $this->entity;
    }

    /**
     * Readings table object
     */
    protected $data;

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
    protected function after_read( \Buffer $buffer ) {
        /* Clean up */
        $this->data->deleteById($this->entity);
        return parent::after_read($buffer);
    }

}
