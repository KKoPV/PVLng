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
        $this->reset()->filter('id', array('min' => 2));
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
    public function getFullName($format='%$1s (%$2s') {
        $name = $this->getName();
        if ($desc = $this->getDescription()) {
            $name = sprintf($format, $name, $desc);
        }
        return $name;
    }

    /**
     * Translate fields with mapping
     */
    protected function field($field)
    {
        return preg_match('~^[[:alpha:]_]\w*$~', $field) ? self::$fieldMapping[$field] : $field;
    }

    /**
     *
     */
    protected function _sql()
    {
        return '
            SELECT `n`.`id`                                                 AS `id`,
                   `n`.`entity`                                             AS `entity`,
                   IFNULL(`n`.`guid`,`c`.`guid`)                            AS `guid`,
                   IF(`co`.`id`,`co`.`name`,`c`.`name`)                     AS `name`,
                   IF(`co`.`id`,`co`.`serial`,`c`.`serial`)                 AS `serial`,
                   `c`.`channel`                                            AS `channel`,
                   IF(`co`.`id`,`co`.`description`,`c`.`description`)       AS `description`,
                   IF(`co`.`id`,`co`.`resolution`,`c`.`resolution`)         AS `resolution`,
                   IF(`co`.`id`,`co`.`cost`,`c`.`cost`)                     AS `cost`,
                   IF(`co`.`id`,`co`.`meter`,`c`.`meter`)                   AS `meter`,
                   IF(`co`.`id`,`co`.`numeric`,`c`.`numeric`)               AS `numeric`,
                   IF(`co`.`id`,`co`.`offset`,`c`.`offset`)                 AS `offset`,
                   IF(`co`.`id`,`co`.`adjust`,`c`.`adjust`)                 AS `adjust`,
                   IF(`co`.`id`,`co`.`unit`,`c`.`unit`)                     AS `unit`,
                   IF(`co`.`id`,`co`.`decimals`,`c`.`decimals`)             AS `decimals`,
                   IF(`co`.`id`,`co`.`threshold`,`c`.`threshold`)           AS `threshold`,
                   IF(`co`.`id`,`co`.`valid_from`,`c`.`valid_from`)         AS `valid_from`,
                   IF(`co`.`id`,`co`.`valid_to`,`c`.`valid_to`)             AS `valid_to`,
                   IF(`co`.`id`,`co`.`public`,`c`.`public`)                 AS `public`,
                   IF(`co`.`id`,`co`.`tags`,`c`.`tags`)                     AS `tags`,
                   IF(`co`.`id`,`co`.`extra`,`c`.`extra`)                   AS `extra`,
                   IF(`co`.`id`,`co`.`comment`,`c`.`comment`)               AS `comment`,
                   `t`.`id`                                                 AS `type_id`,
                   `t`.`name`                                               AS `type`,
                   `t`.`model`                                              AS `model`,
                   `t`.`childs`                                             AS `childs`,
                   `t`.`read`                                               AS `read`,
                   `t`.`write`                                              AS `write`,
                   `t`.`graph`                                              AS `graph`,
                   IF(`co`.`id`,`co`.`icon`,`c`.`icon`)                     AS `icon`,
                   `ca`.`id`                                                AS `alias`,
                   `ta`.`id`                                                AS `alias_of`,
                   `ta`.`entity`                                            AS `entity_of`,
                   COUNT(1) + (`n`.`lft` > 1)                               AS `level`,
                   ROUND((`n`.`rgt` - `n`.`lft` - 1) / 2, 0)                AS `haschilds`,
                   ((MIN(`p`.`rgt`) - `n`.`rgt` - (`n`.`lft` > 1)) / 2) > 0 AS `lower`,
                   (`n`.`lft` - MAX(`p`.`lft`)) > 1                         AS `upper`
              FROM `pvlng_tree` `n` USE INDEX (PRIMARY) -- Force index for performance
              JOIN `pvlng_tree` `p` ON `n`.`lft` >= `p`.`lft` AND `n`.`rgt` <= `p`.`rgt` AND (`p`.`id` <> `n`.`id` OR `n`.`lft` = 1)
              LEFT JOIN `pvlng_channel` `c` on `n`.`entity` = `c`.`id`
              LEFT JOIN `pvlng_type` `t` on `c`.`type` = `t`.`id`
              LEFT JOIN `pvlng_channel` `ca` on IF(`t`.`childs`,`n`.`guid`,`c`.`guid`) = `ca`.`channel` AND `ca`.`type` = 0
              LEFT JOIN `pvlng_tree` `ta` on `c`.`channel` = `ta`.`guid`
              LEFT JOIN `pvlng_channel` `co` on `ta`.`entity` = `co`.`id` AND `c`.`type` = 0
             ' . $this->_filter() . '
             GROUP BY `n`.`id`  -- Fixed grouping
             ORDER BY `n`.`lft` -- Fixed order
        ';
    }

    // -----------------------------------------------------------------------
    // PRIVATE
    // -----------------------------------------------------------------------

    /**
     *
     */
    private static $fieldMapping = array(
        'id'            => '`n`.`id`',
        'entity'        => '`n`.`entity`',
        'guid'          => 'IFNULL(`n`.`guid`,`c`.`guid`)',
        'name'          => 'IF(`co`.`id`,`co`.`name`,`c`.`name`)',
        'serial'        => 'IF(`co`.`id`,`co`.`serial`,`c`.`serial`)',
        'channel'       => '`c`.`channel`',
        'description'   => 'IF(`co`.`id`,`co`.`description`,`c`.`description`)',
        'resolution'    => 'IF(`co`.`id`,`co`.`resolution`,`c`.`resolution`)',
        'cost'          => 'IF(`co`.`id`,`co`.`cost`,`c`.`cost`)',
        'meter'         => 'IF(`co`.`id`,`co`.`meter`,`c`.`meter`)',
        'numeric'       => 'IF(`co`.`id`,`co`.`numeric`,`c`.`numeric`)',
        'offset'        => 'IF(`co`.`id`,`co`.`offset`,`c`.`offset`)',
        'adjust'        => 'IF(`co`.`id`,`co`.`adjust`,`c`.`adjust`)',
        'unit'          => 'IF(`co`.`id`,`co`.`unit`,`c`.`unit`)',
        'decimals'      => 'IF(`co`.`id`,`co`.`decimals`,`c`.`decimals`)',
        'threshold'     => 'IF(`co`.`id`,`co`.`threshold`,`c`.`threshold`)',
        'valid_from'    => 'IF(`co`.`id`,`co`.`valid_from`,`c`.`valid_from`)',
        'valid_to'      => 'IF(`co`.`id`,`co`.`valid_to`,`c`.`valid_to`)',
        'public'        => 'IF(`co`.`id`,`co`.`public`,`c`.`public`)',
        'tags'          => 'IF(`co`.`id`,`co`.`tags`,`c`.`tags`)',
        'extra'         => 'IF(`co`.`id`,`co`.`extra`,`c`.`extra`)',
        'comment'       => 'IF(`co`.`id`,`co`.`comment`,`c`.`comment`)',
        'type_id'       => '`t`.`id`',
        'type'          => '`t`.`name`',
        'model'         => '`t`.`model`',
        'childs'        => '`t`.`childs`',
        'read'          => '`t`.`read`',
        'write'         => '`t`.`write`',
        'graph'         => '`t`.`graph`',
        'icon'          => 'IF(`co`.`id`,`co`.`icon`,`c`.`icon`)',
        'alias'         => '`ca`.`id`',
        'alias_of'      => '`ta`.`id`',
        'entity_of'     => '`ta`.`entity`',
        'level'         => 'COUNT(1) + (`n`.`lft` > 1)',
        'haschilds'     => 'ROUND((`n`.`rgt` - `n`.`lft` - 1) / 2, 0)',
        'lower'         => '((MIN(`p`.`rgt`) - `n`.`rgt` - (`n`.`lft` > 1)) / 2) > 0',
        'upper'         => '(`n`.`lft` - MAX(`p`.`lft`)) > 1'
    );

}
