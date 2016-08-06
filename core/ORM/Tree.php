<?php
/**
 * Real access class for 'pvlng_tree_view'
 *
 * To extend the functionallity, edit here
 *
 * @author     Knut Kohl <github@knutkohl.de>
 * @copyright  2014 Knut Kohl
 * @license    MIT License (MIT) http://opensource.org/licenses/MIT
 * @version    1.0.0
 *
 * 1.0.0
 * - Initial creation
 */
namespace ORM;

/**
 *
 */
class Tree extends TreeBase {

    /**
     *
     */
    public function __construct ( $id=NULL ) {
        /* Build WITHOUT $id lookup, views have no primary key */
        parent::__construct();
        if ($id) $this->filterById($id)->findOne();
    }

    /**
     * Getter for 'extra'
     */
    public function getExtra() {
        return json_decode(parent::getExtra());
    }

    /**
     *
     */
    public function getModelClass() {
        return 'Channel\\'.$this->getModel();
    }

    /**
     *
     */
    public function getWithParents( $publicOnly=FALSE ) {
        // Remember parents Id, init level 0
        $parent = array( NULL );
        $nodes = array();

        // Without root node
        $this->reset()->filter('id', array('min'=>2));
        if ($publicOnly) $this->filterByPublic(1);
        $rows = $this->find()->asAssoc();

        foreach ($rows as $row) {
            $row['level']--;
            $parent[$row['level']] = $row['id'];
            $row['parent'] = $parent[$row['level']-1];
            $nodes[] = $row;
        }

        return $nodes;
    }

    /**
     * Get full name with description (if defined): Name (Description)
     */
    public function getFullName() {
        $name = $this->getName();
        if ($description = $this->getDescription) $name .= ' ('.$description.')';
        return $name;
    }
}
