<?php
/**
 *
 *
 * @author      Knut Kohl <github@knutkohl.de>
 * @copyright   2012-2013 Knut Kohl
 * @license     GNU General Public License http://www.gnu.org/licenses/gpl.txt
 * @version     1.0.0
 */
namespace Model;

/**
 *
 */
class Info extends \Model {

    /**
     *
     */
    public function __construct() {
        parent::__construct();

        $this->db->load(dirname(__FILE__) . DS . 'Info.xml');
    }

    /**
     *
     */
    public function getReadingCounts() {
        return $this->db->queryRows($this->db->SQL->ReadingCounts);
    }

}
