<?php
/**
 * Class to manage Mysql Nested Set Trees.
 *
 * @author Tobias Breunig <t.breunig@live.de>
 * @license http://www.opensource.org/licenses/bsd-license.html BSD License
 * @version 1.1
 * @copyright 2009 Tobias Breunig
 */
class NestedSet {
  /**
   * Database Table Vars
   *
   * @var   array
   */
  private $params = array();

  /**
   * Error Messages
   *
   * @var array
   */
  private $errors = array();

  /**
   * Array with Erros Messages, there are stored in an external File
   *
   * @var array
   */
  public $msg = array();

  /**
   *
   */
  protected static $Instance = array();

  /**
   *
   */
  public static function Init( $params, $id = 0 ) {
    self::$Instance[$id] = new NestedSet($params);
  }

  /**
   *
   */
  public static function setDatabase( $db, $id = 0 ) {
    if (is_null(self::$Instance[$id]))
      throw new Exception('Call NestedSet::Init() first!');
    self::$Instance[$id]->db = $db;
  }

  /**
   *
   */
  public static function getInstance( $id = 0 ) {
    if (is_null(self::$Instance[$id]))
      throw new Exception('Call NestedSet::Init() first!');
    return self::$Instance[$id];
  }

  /**
   * Set up Vars
   * 
   * @param   object     $db      Object of mysqli-Connection
   * @param   array     $params    Array with Database-Table Vars
   * @return   void
   */
  protected function __construct( $params ) {
    $params = array_merge(
        array(
            'debug'    => false,
            'lang'     => 'en',
            'db'       => null,
            'db_table' => null,
            'path'     => 'messages'
        ), $params
    );
    $this->debug  = $params['debug'];
    $this->lang   = $params['lang'];
    $this->db     = $params['db'];
    $this->table  = $params['db_table'];

    $this->msg = include $params['path'] . DIRECTORY_SEPARATOR . $this->lang . '.php';
  }

/**
 * @access public
 * 
 * Methods to manipulate Nested Set Trees
 */

  /**
   * Checks whether a Root Node exists
   *
   * @return   boolean     TRUE or FALSE
   */
  public function checkRootNode() {
    $sql = sprintf('SELECT `%1$s` FROM `%2$s` WHERE `%3$s` = %4$d',
                   $this->table['nid'], $this->table['tbl'], $this->table['l'], 1);

    if (!$result = $this->db->query($sql) OR !$result->num_rows) {
      return $this->_setError(200, 'checkRootNode()', $sql);
    }

    return TRUE;
  }

  /**
   * Creates the Root Node, if its not exists
   *
   * @param   string     $nodeName    Name of the Node
   * @return   boolean            TRUE or FALSE
   */
  public function insertRootNode($nodeName) {
    if (TRUE === $this->checkRootNode()) {
      return $this->_setError(300, 'insertRootNode("' . $nodeName . '")');
    }

    $sql = sprintf('INSERT INTO `%1$s`(`%2$s`,`%3$s`,`%4$s`) VALUES("%5$s",%6$d,%7$d)',
                    $this->table['tbl'],$this->table['pay'],$this->table['l'],
                    $this->table['r'],$this->db->real_escape_string($nodeName),1,2);

    $this->_lock();

    if (!$result = $this->db->query($sql)) {
      return $this->_setError(100, 'insertRootNode("' . $nodeName . '")');
    }

    $this->_unlock();

    if (FALSE === $this->db->affected_rows) {
      return $this->_setError(301, 'insertRootNode("' . $nodeName . '")');
    }

    return TRUE;
  }

  /**
   * Insert a new Node
   *
   * @param   string     $nodeName    Name of the Node
   * @param   integer   $parentId    Id of the Parent Node
   * @return   boolean            TRUE or FALSE
   */
  public function insertChildNode($nodeName, $parentId) {
    if (!$parentData = $this->_getNode($parentId)) {
      return $this->_setError(302, 'insertChildNode("' . $nodeName . '")');
    }

    $sql = sprintf('UPDATE `%1$s` SET `%2$s` = `%2$s` + 2 WHERE `%2$s` >= %5$d;
                    UPDATE `%1$s` SET `%3$s` = `%3$s` + 2 WHERE `%3$s` > %5$d;
                    INSERT INTO `%1$s`(`%4$s`, `%3$s`, `%2$s`) VALUES("%6$s", %5$d, %5$d+1)',
                   $this->table['tbl'], $this->table['r'], $this->table['l'],
                   $this->table['pay'], $parentData[$this->table['r']],
                   $this->db->real_escape_string($nodeName));

    $this->_lock();

    if ($this->db->multi_query($sql)) {
      while ($this->db->more_results() AND $this->db->next_result()) {;}
    } else {
      return $this->_setError(100, 'insertChildNode("' . $nodeName . '")');
    }

    if (!$result = $this->db->query('SELECT LAST_INSERT_ID()')) {
      return $this->_setError(100, 'insertChildNode("' . $nodeName . '")', $sql);
    }

    $this->_unlock();

    $row = $result->fetch_array();

    return $row[0];
  }


  /**
   * Delete a node and all its Children
   *
   * @param   integer   $branchId    Id of the Node
   * @return   boolean            TRUE or FALSE
   */
  public function deleteBranch($branchId) {
    if (!$branchData = $this->_getNode($branchId)) {
      return $this->_setError(202, 'deleteBranch("' . $branchId . '")');
    }

    extract($this->table);

    $sql = sprintf(
        'DELETE FROM `%1$s` WHERE `%2$s` BETWEEN %4$d AND %5$d;
         UPDATE `%1$s` SET `%2$s` = `%2$s` - %5$d + %4$d - 1 WHERE `%2$s` > %5$d;
         UPDATE `%1$s` SET `%3$s` = `%3$s` - %5$d + %4$d - 1 WHERE `%3$s` > %5$d',
        $tbl, $l, $r, $branchData[$l], $branchData[$r]
    );

    $this->_lock();

    if ($this->db->multi_query($sql)) {
      while ($this->db->more_results() && $this->db->next_result()) {;}
    } else {
      return $this->_setError(303, 'deleteBranch("' . $branchId . '")', $sql);
    }

    $this->_unlock();

    return true;
  }

  /**
   * Delete a single Node
   *
   * @param   integer   $nodeId    Id of the Node
   * @return   boolean          TRUE or FALSE
   */
  public function deleteNode($nodeId) {
    if (!$nodeData = $this->_getNode($nodeId)) {
      return $this->_setError(202, 'deleteNode("' . $nodeId . '")');
    }

    $sql = sprintf('DELETE FROM `%1$s` WHERE `%2$s` = %4$d;
                    UPDATE `%1$s` SET `%2$s` = `%2$s` - %6$d, `%3$s` = `%3$s` - %6$d WHERE `%2$s` BETWEEN %4$d AND %5$d;
                    UPDATE `%1$s` SET `%2$s` = `%2$s` - %7$d WHERE `%2$s` > %5$d;
                    UPDATE `%1$s` SET `%3$s` = `%3$s` - %7$d WHERE `%3$s` > %5$d',
                   $this->table['tbl'], $this->table['l'], $this->table['r'],
                   $nodeData[$this->table['l']], $nodeData[$this->table['r']], 1, 2);

    $this->_lock();

    if ($this->db->multi_query($sql)) {
      while ($this->db->more_results() AND $this->db->next_result()) {;}
    } else {
      return $this->_setError(303, 'deleteNode("' . $nodeId . '")', $sql);
    }

    $this->_unlock();

    return TRUE;
  }

  /**
   * Edit the Name of a Node
   *
   * @param   interger   $nodeId    Id of the Node
   * @param  string    $nodeName  New Name of the Node
   * @return   boolean          TRUE or FALSE
   */
  public function renameNode($nodeName, $nodeId) {
    $sql = sprintf('UPDATE `%1$s` SET `%2$s` = "%3$s" WHERE `%4$s`= %5$d',
                   $this->table['tbl'],$this->table['pay'],
                   $this->db->real_escape_string($nodeName),
                   $this->table['nid'], (int)$nodeId);

    if (!$result = $this->db->query($sql)) {
      return $this->_setError(100, 'renameNode("' . $nodeName . '","' . $nodeId . '")', $sql);
    }

    if (!$this->db->affected_rows === 0) {
      return $this->_setError(304, 'renameNode("' . $nodeName . '","' . $nodeId . '")', $sql);
    }

    return TRUE;
  }

  /**
   * Move a Node/Branch LEFT
   *
   * @param   integer   $nodeId   Id of the Node
   * @return   boolean          TRUE or FALSE
   * 
   * @deprecated Swap space with the left Brother
   */
  public function moveLft($nodeId) {
    $nodeLevel = $this->_getNodeLevel($nodeId);

    if ($nodeLevel == 1) {
      return $this->_setError(305, 'moveLft("' . $nodeId . '")');
    }

    $a = $this->_getNode($nodeId);
    $a_lft = $a[$this->table['l']];
    $a_rgt = $a[$this->table['r']];

    if (!$b_id = $this->_getId($a_lft - 1,'r')) {
      return $this->_setError(306, 'moveLft("' . $nodeId . '")');
    }

    if (!$b = $this->_getNode($b_id)) {
      return $this->_setError(306, 'moveLft("' . $nodeId . '")');
    }

    $b_lft = $b[$this->table['l']];
    $b_rgt = $b[$this->table['r']];

    $diffRgt = $a_rgt - $b_rgt;
    $diffLft = $a_lft - $b_lft;

    $sql = sprintf('UPDATE `%1$s` SET `%2$s` = %11$d WHERE `%2$s` <> %11$d;
                    UPDATE `%1$s` SET `%3$s` = `%3$s` + %5$d,`%4$s` = `%4$s` + %5$d,`%2$s` = %12$d WHERE `%4$s` BETWEEN %9$d AND %10$d;
                    UPDATE `%1$s` SET `%3$s` = `%3$s` - %6$d,`%4$s` = `%4$s` - %6$d WHERE `%4$s` BETWEEN %7$d AND %8$d AND `%2$s` = %11$d;
                    UPDATE `%1$s` SET`%2$s` = %11$d WHERE `%2$s` <> %11$d',
                   $this->table['tbl'], $this->table['mov'], $this->table['r'],
                   $this->table['l'], (int)$diffRgt, (int)$diffLft,
                   (int)$a_lft, (int)$a_rgt, (int)$b_lft, (int)$b_rgt,0,1);

    $this->_lock();

    if ($this->db->multi_query($sql)) {
      while ($this->db->more_results() AND $this->db->next_result()) {;}
    } else {
      return $this->_setError(100, 'moveLft("' . $nodeId . '")', $sql);
    }

    $this->_unlock();

    return TRUE;
  }

  /**
   * Move a Node/Branch RIGHT
   *
   * @param   integer   $nodeId   Id of the Node
   * @return   boolean          TRUE or FALSE
   */
  public function moveRgt($nodeId) {
    $nodeLevel = $this->_getNodeLevel($nodeId);

    if ($nodeLevel == 1) {
      return $this->_setError(305, 'moveRgt("' . $nodeId . '")');
    }

    $a = $this->_getNode($nodeId);
    $a_lft = $a[$this->table['l']];
    $a_rgt = $a[$this->table['r']];

    if (!$b_id = $this->_getId($a_rgt + 1,'l')) {
      return $this->_setError(307, 'moveRgt("' . $nodeId . '")');
    }

    if (!$b = $this->_getNode($b_id)) {
      return $this->_setError(307, 'moveRgt("' . $nodeId . '")');
    }

    $b_lft = $b[$this->table['l']];
    $b_rgt = $b[$this->table['r']];

    $diffRgt = $b_rgt - $a_rgt;
    $diffLft = $b_lft - $a_lft;

    $this->_lock();

    $sql = sprintf('UPDATE `%1$s` SET `%2$s` = %11$d WHERE `%2$s` <> %11$d;
                    UPDATE `%1$s` SET `%4$s` = `%4$s` - %5$d, `%3$s` = `%3$s` - %5$d, `%2$s` = %12$d WHERE `%3$s` BETWEEN %7$d AND %8$d;
                    UPDATE `%1$s` SET `%4$s` = `%4$s` + %6$d, `%3$s` = `%3$s` + %6$d WHERE `%3$s` BETWEEN %9$d AND %10$d AND `%2$s` = %11$d;
                    UPDATE `%1$s` SET `%2$s` = %11$d WHERE `%2$s` <> %11$d',
                   $this->table['tbl'],$this->table['mov'],$this->table['l'],
                   $this->table['r'], (int)$diffLft, (int)$diffRgt,
                   (int)$b_lft, (int)$b_rgt, (int)$a_lft, (int)$a_rgt,0,1);

    $this->_lock();

    if ($this->db->multi_query($sql)) {
      while ($this->db->more_results() AND $this->db->next_result()) {;}
    } else {
      return $this->_setError(100, 'moveRgt("' . $nodeId . '")', $sql);
    }

    $this->_unlock();

    return TRUE;
  }

  /**
   * Move a Node/Branch UP
   *
   * @param   integer   $nodeId   Id of the Node
   * @return  boolean          TRUE or FALSE
   */
  public function moveUp($nodeId) {
    $nodeLevel = $this->_getNodeLevel($nodeId);

    if ($nodeLevel == 1) {
      return $this->_setError(305, 'moveUp("' . $nodeId . '")');
    }

    if ($nodeLevel == 2) {
      return $this->_setError(307, 'moveUp("' . $nodeId . '")');
    }

    do {
      if (!$moved = $this->moveRgt($nodeId)) break;
    } while (TRUE === $moved);

    $a = $this->_getNode($nodeId);
    $a_lft = $a[$this->table['l']];
    $a_rgt = $a[$this->table['r']];

    if (!$b_id = $this->_getId($a_rgt + 1, 'r')) {
      return $this->_setError(308, 'moveUp("' . $nodeId . '")');
    }

    if (!$b = $this->_getNode($b_id)) {
      return $this->_setError(308, 'moveUp("' . $nodeId . '")');
    }

    $b_lft = $b[$this->table['l']];
    $b_rgt = $b[$this->table['r']];

    $nodeWidth = $a_rgt - $a_lft + 1;

    $sql = sprintf('UPDATE `%1$s` SET `%2$s` = `%2$s` + %9$d,`%3$s` = `%3$s` + %9$d WHERE `%3$s` BETWEEN %5$d AND %6$d;
                    UPDATE `%1$s` SET `%2$s` = `%2$s` - %7$d WHERE `%4$s` = %8$d',
                   $this->table['tbl'], $this->table['r'], $this->table['l'],
                   $this->table['nid'], (int)$a_lft, (int)$a_rgt,
                   (int)$nodeWidth, (int)$b_id,1);

    $this->_lock();

    if ($this->db->multi_query($sql)) {
      while ($this->db->more_results() AND $this->db->next_result()) {;}
    } else {
      return $this->_setError(100, 'moveUp("' . $nodeId . '")', $sql);
    }

    $this->_unlock();

    return TRUE;
  }

  /**
   * Move a Node/Branch DOWN
   *
   * @param   integer   $nodeId   Id of the Node
   * @return   boolean          TRUE or FALSE
   */
  public function moveDown($nodeId) {
    $nodeLevel = $this->_getNodeLevel($nodeId);

    if ($nodeLevel == 1) {
      return $this->_setError(305, 'moveDown("' . $nodeId . '")');
    }

    $a = $this->_getNode($nodeId);
    $a_lft = $a[$this->table['l']];
    $a_rgt = $a[$this->table['r']];

    if (!$b_id = $this->_getId($a_lft - 1,'r')) {
      return $this->_setError(306, 'moveDown("' . $nodeId . '")');
    }
    if (!$b = $this->_getNode($b_id)) {
      return $this->_setError(306, 'moveDown("' . $nodeId . '")');
    }

    $b_lft = $b[$this->table['l']];
    $b_rgt = $b[$this->table['r']];

    $nodeWidth = $a_rgt - $a_lft + 1;

    $this->_lock();

    $sql = sprintf('UPDATE `%1$s` SET `%2$s` = `%2$s` - %9$d, `%3$s` = `%3$s` - %9$d WHERE `%3$s` BETWEEN %5$d AND %6$d;
                    UPDATE `%1$s` SET `%2$s` = `%2$s` + %7$d WHERE `%4$s` = %8$d',
                   $this->table['tbl'], $this->table['r'], $this->table['l'],
                   $this->table['nid'], (int)$a_lft, (int)$a_rgt,
                   (int)$nodeWidth, (int)$b_id, 1);

    $this->_lock();

    if ($this->db->multi_query($sql)) {
      while ($this->db->more_results() AND $this->db->next_result()) {;}
    } else {
      return $this->_setError(100, 'moveDown("' . $nodeId . '")', $sql);
    }

    $this->_unlock();

    return TRUE;
  }

  /**
   * Checks if an Error exists
   *
   * @return   boolean    TRUE or FALSE
   */
  public function isError() {
    return !empty($this->errors);
  }

  /**
   * Returns the Error Messages
   *
   * @return mixed    array     array Error Messages or null
   */
  public function getError() {
    return ($this->isError()) ? $this->errors : null;
  }

  /**
   * @access public
   *
   * Methods to get Informations to build HTML Templates
   */

  /**
   * Get a NestedSet Tree as Array beging from $id
   *
   * @return   mixed    Multidimensional Array with Tree data or boolean FALSE
   */
  public function getTree( $id ) {
    $sql = sprintf('
      SELECT `o`.*,
             count(`p`.`%4$s`) AS level
        FROM `%3$s` `n`,
             `%3$s` `p`,
             `%3$s` `o`
       WHERE `o`.`%1$s` BETWEEN `p`.`%1$s` AND `p`.`%2$s`
         AND `o`.`%1$s` BETWEEN `n`.`%1$s` AND `n`.`%2$s`
         AND `n`.`%4$s` = '.$id.'
       GROUP BY `o`.`%1$s`
       ORDER BY `o`.`%1$s`',
      $this->table['l'], $this->table['r'], $this->table['tbl'], $this->table['nid']);

    if (!$result = $this->db->query($sql) OR !$result->num_rows) {
      return $this->_setError(100, 'getTree("' . $id . '")', $sql);
    }

    $tmp = array();
    $i = 1;
    while ($row = $result->fetch_assoc()) $tmp[$i++] = $row;

    return $tmp;
  }

  /**
   * Get a NestedSet Tree as Array beging from $id
   *
   * @return   mixed    Multidimensional Array with Tree data or boolean FALSE
   */
  public function getChilds( $id ) {
    $rows = $this->getTree($id);

    $return = array();
    if (count($rows) > 1) {
      // skip root
      array_shift($rows);

      // remember level of 1st child
      $level = $rows[0]['level'];

      foreach($rows as $row) {
        if ($row['level'] == $level) $return[] = $row;
      }
    }
    return $return;
  }

  /**
   * Get count of direct childs
   *
   * @return   int
   */
  public function getChildCount( $id ) {
    return count($this->getChilds($id));
  }

  /**
   * Get count of direct childs
   *
   * @return   int
   */
  public function getParent( $id ) {
    $path = $this->getPathFromRoot($id);
    $parent = array_splice($path, -2, 1);
    return $parent[0];
  }

  /**
   * Get a full NestedSet Tree as Array
   *
   * @return   mixed    Multidimensional Array with Tree data or boolean FALSE
   */
  public function getFullTree() {
    $sql = sprintf('
      SELECT `n`.*,
             round((`n`.`%2$s` - `n`.`%1$s` - 1) / 2, 0) AS childs,
             count(*) - 1 +(`n`.`%1$s` > 1) + 1 AS level,
            ((min(`p`.`%2$s`) - `n`.`%2$s` -(`n`.`%1$s` > 1)) / 2) > 0 AS lower,
            (((`n`.`%1$s` - max(`p`.`%1$s`) > 1))) AS upper
        FROM `%3$s` `n`,
             `%3$s` `p`
       WHERE `n`.`%1$s` BETWEEN `p`.`%1$s` AND `p`.`%2$s` AND
            (`p`.`%4$s` != `n`.`%4$s` OR `n`.`%1$s` = 1)
       GROUP BY `n`.`%4$s` ORDER BY `n`.`%1$s`',
      $this->table['l'], $this->table['r'], $this->table['tbl'], $this->table['nid']);

    if (!$result = $this->db->query($sql) OR !$result->num_rows) {
      return $this->_setError(100, 'getFullTree()', $sql);
    }

    $tmp = array();
    $i = 1;
    while ($row = $result->fetch_assoc()) {
      $tmp[$i++] = $row;
    }

    return $tmp;
  }

  /**
   * Get the Path from Root Level to the selected Node
   *
   * @param   integer   $nodeId   Id of the Node
   * @return  mixed          array Path Values or boolean FALSE
   */
  public function getPathFromRoot($nodeId) {
    $sql = sprintf('SELECT `%1$s`.*
                      FROM `%3$s` `%2$s`,
                           `%3$s` `%1$s`
                     WHERE `%2$s`.`%4$s` BETWEEN `%1$s`.`%4$s`
                       AND `%1$s`.`%5$s`
                       AND `%2$s`.`%6$s` = %7$d
                     ORDER BY `%1$s`.`%4$s`',
                   'p','n',
                   $this->table['tbl'], $this->table['l'], $this->table['r'],
                   $this->table['nid'], (int)$nodeId);

    if (!$result = $this->db->query($sql)) {
      return $this->_setError(100, 'getPathFromRoot("' . $nodeId . '")', $sql);
    }

    if (!$result->num_rows) {
      return $this->_setError(204, 'getPathFromRoot("' . $nodeId . '")', $sql);
    }

    $tmp = array();
    $i = 1;
    while ($row = $result->fetch_assoc()) {
      $tmp[$i++] = $row;
    }

    return $tmp;
  }

/**
 * @access  privat
 */

  /**
   * Get the Id of a Node depending on its left or right Value
   *
   * @param   integer   $directionValue    Value of left or right Border
   * @param   string     $direction      left or right Border("l" for left, "r" for right)
   * @return   mixed              integer Id of the Node or boolean FALSE
   */
  private function _getId($directionValue, $direction) {
    $sql = sprintf('SELECT `%1$s` FROM `%2$s` WHERE `%3$s` = %4$d',
                   $this->table['nid'], $this->table['tbl'],
                   $this->table[$direction], (int)$directionValue);

    if (!$result = $this->db->query($sql)) {
      return $this->_setError(100, '_getId("' . $directionValue . '","' . $direction . '")', $sql);
    }

    if (!$result->num_rows) {
      return $this->_setError(203, '_getId("' . $directionValue . '","' . $direction . '")', $sql);
    }

    $row = $result->fetch_assoc();

    return $row[$this->table['nid']];
  }

  /**
   * Get an Array with id,lft,rgt values of a Node
   *
   * @param   integer   $nodeId    Id of the Node
   * @return   mixed           array Values of the Node or boolean FALSE
   */
  private function _getNode($nodeId) {
    $sql = sprintf('SELECT `%1$s`,`%2$s`,`%3$s` FROM `%4$s` WHERE `%1$s` = %5$d',
                   $this->table['nid'], $this->table['l'], $this->table['r'],
                   $this->table['tbl'], (int)$nodeId);

    if (!$result = $this->db->query($sql)) {
      return $this->_setError(100, '_getNode("' . $nodeId . '")', $sql);
    }

    if (!$result->num_rows) {
      return $this->_setError(202, '_getNode("' . $nodeId . '")', $sql);
    }

    return $result->fetch_assoc();
  }

  /**
   * Get the Level of a Node
   *
   * @param   integer   $nodeId    Id of the Node
   * @return   mixed    integer   integer Level of the Node(0 = Root) or boolean FALSE
   */
  private function _getNodeLevel($nodeId) {
    $sql = sprintf('SELECT COUNT(*) AS `level`
                      FROM `%3$s` `%2$s`,`%3$s` `%1$s`
                     WHERE `%1$s`.`%5$s` BETWEEN `%2$s`.`%5$s`
                       AND `%2$s`.`%6$s`
                     GROUP BY `%1$s`.`%5$s`
                     ORDER BY ABS(`%1$s`.`%4$s` - %7$d)',
                   'n','p',
                   $this->table['tbl'], $this->table['nid'],
                   $this->table['l'], $this->table['r'], (int)$nodeId);

    if (!$result = $this->db->query($sql)) {
      return $this->_setError(100, '_getNodeLevel("' . $nodeId . '")', $sql);
    }

    if (!$result->num_rows) {
      return $this->_setError(202, '_getNodeLevel("' . $nodeId . '")', $sql);
    }

    $row = $result->fetch_assoc();

    return $row['level'];
  }

  /**
   * Count all Nodes in the NestedSet-Table
   *
   * @return mixed  integer Count or boolean FALSE
   */
  private function _countNodes() {
    $sql = sprintf('SELECT COUNT(`%1$s`) FROM `%2$s` AS `count`',
                   $this->table['nid'], $this->table['tbl']);

    if (!$result->$this->db->query($sql)) {
      return $this->_setError(100, '_countNodes()', $sql);
    }

    if (!$result->num_rows) {
      return $this->_setError(201, '_countNodes()', $sql);
    }

    $row = $result->fetch_assoc();

    return $row['count'];
  }

  /**
   * Store an Error into the Error-Messages-Array
   *
   * @param   string     $error    The Error Message
   * @return   void
   */
  private function _setError($err, $method, $sql=NULL, $unlock=TRUE) {
    if ($unlock) $this->_unlock();
    $error = $this->msg[$err];
    if ($this->debug) {
      $error .= ' NestedSet::' . $method . ';';
      if ($sql) $error .= ' (' . $sql . ') - ' . $this->db->error;
    }
    $this->errors[] = $error;
    return FALSE;
  }

  /**
   * Lock the NestedSet-Table to write
   * 
   * @return   void
   */
  private function _lock() {
    $sql = sprintf('LOCK TABLES `%1$s` WRITE', $this->table['tbl']);
    if (!$this->db->query($sql)) {
      $this->_setError(100, '_lock()', $sql);
    }
  }

  /**
   * Unlock the NestedSet-Table
   *
   * @return   void
   */
  private function _unlock() {
    $sql = 'UNLOCK TABLES';
    if (!$this->db->query($sql)) {
      $this->_setError(100, '_unlock()', $sql, FALSE);
    }
  }
}
