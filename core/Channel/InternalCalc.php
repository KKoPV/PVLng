<?php
/**
 *
 *
 * @author      Knut Kohl <github@knutkohl.de>
 * @copyright   2012-2013 Knut Kohl
 * @license     GNU General Public License http://www.gnu.org/licenses/gpl.txt
 * @version     1.0.0
 */
namespace Channel;

/**
 *
 */
abstract class InternalCalc extends \Channel {

    /**
     * Channel type
     * UNDEFINED_CHANNEL - concrete channel decides
     * NUMERIC_CHANNEL   - concrete channel decides if sensor or meter
     * SENSOR_CHANNEL    - numeric
     * METER_CHANNEL     - numeric
     */
    const TYPE = NUMERIC_CHANNEL;

    /**
     *
     */
    protected function __construct( \ORM\Tree $channel ) {
        parent::__construct($channel);
        $this->data = $this->numeric ? new \ORM\ReadingNumMemory :  new \ORM\ReadingStrMemory;
        $this->data->id = $this->entity;
        /* Clean up */
        $this->data->deleteById($this->entity);
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
