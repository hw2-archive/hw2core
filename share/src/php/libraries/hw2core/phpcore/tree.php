<?php namespace Hw2;
S_Core::checkAccess();

/**
 * S_Tree
 * 
 * This class implements the Tree structure and is based on linked list using a hash table.
 * Using hash table prevents all possible recursive references and
 * allows for more efficient garbage collection. A particularly sore point in PHP.
 * 
 * I have used my implementation of Doubly Linked list as my base. 
 * You can find more information on it here:
 * http://phptouch.com/2011/03/15/doubly-linked-list-in-php/
 * 
 * I have heavily relied on the following 2 references for their algorithms.
 * Beginning Algorithims by Simon Harris and James Ross. Wrox publishing.
 * Data Structures and Algorithms in Java Fourth Edition by Michael T. Goodrich
 * and Roberto Tamassia. John Wiley & Sons.
 * 
 * *********************LICENSE****************************************
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.

 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.

 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 * *********************LICENSE**************************************** 
 * @package S_Tree
 * @author Jayesh Wadhwani
 * @copyright 2011 Jayesh Wadhwani. 
 * @license  GNU GENERAL PUBLIC LICENSE 3.0
 * @version 1.0
 */
class S_Tree {

    const head = "HEAD"; // value of head node

    /**
     * @var UID for the header node 
     */

    private $_head;

    /**
     * @var size of list 
     */
    private $_size;

    /**
     * S_Tree::__construct()
     * 
     * @return
     */
    public function __construct() {
        $this->_head = new S_Node(self::head);
        $this->_size = 0;
    }
    
    // return array
    public function getNodeList(S_Node $root=null) {
        if (!$root)
            $root=$this->_head;
        $childs=$root->getChildren();
        if (!$childs) {
            return null; // basic case ( when not childs )
        } else {
            /* @var $child S_Node */
            $nodes=Array();
            foreach ($childs as $key => $child) {   
                $nodes[$child->getUid()]=$child;
                $allChilds=$this->getNodeList($child);
                if (!empty($allChilds))
                    $nodes=array_merge($nodes,$allChilds);
            }
            return $nodes;
        }
    }

    /**
     * S_Tree::getNode()
     * Given a UID get the node object
     * 
     * @param mixed $uid
     * @return S_Node
     */
    public function getNode($findUid,S_Node $root=null) {
        if (!$findUid)
            S_Exception::raise ("findUid cannot be null", S_Exception_type::error ());
        
        if (!$root)
            $root=$this->_head;
        
        if ($root->getUid()==$findUid)  // basic case ( when found )
            return $root; 
        
        $childs=$root->getChildren();
        
        if (!$childs) {
            return null; // basic case ( when not childs )
        } else {
            foreach ($childs as $key => $child) {
                $node=$this->getNode($findUid,$child);
                if ($node)
                    return $node;
            }
        }
    }
    
    /**
     * add node to parent or head node
     * @param \Hw2\S_Node $node
     * @param type $parentUid
     * @return type
     */
    public function addNode(S_Node $node,$parentUid=null) {
        $pUid=$parentUid ? $parentUid : $this->_head->getUid();
        $parent=$this->getNode($pUid);
        $parent->setChild($node);
        $node->setParent($parent);
        return $node->getUid();
    }

    /**
     * S_Tree::getChildren()
     * 
     * This is a helper function to get the child node uids given the node uid
     * 
     * @param mixed $uid
     * @return mixed
     */
    public function getChildren($uid) {
        if (empty($uid)) {
            throw new Exception('A unique ID is required.');
        }

        $node = $this->getNode($uid);

        if ($node !== false) {
            return $node->getChildren();
        }
    }

    /**
     * S_Tree::getParent()
     * 
     * This is a helper function to get the 
     * parent node uid
     * 
     * @param mixed $uid
     * @return string $uid
     */
    public function getParent($uid) {
        if (empty($uid)) {
            throw new Exception('A unique ID is required.');
        }
        $ret = false;
        $node = $this->getNode($uid);

        if ($node !== false) {
            $ret = $node->getParent();
        }
        return $ret;
    }
    
    /**
     * get head node
     * @return S_Node
     */
    public function getFirst() {
        return $this->_head;
    }

    /**
     * S_Tree::getValue()
     * 
     * @param mixed $uid
     * @return
     */
    public function getValue($uid) {
        if (empty($uid)) {
            throw new \Exception('A unique ID is required.');
        }

        $node = $this->getNode($uid);
        return $node? $node->getValue() : null;
    }
}

/**
 * S_TreeRecursiveIterator
 * 
 * To use a recursive iterator you have to extend of the RecursiveIteratorIterator
 * As an example I have built an unordered list 
 * For detailed information on please see RecursiveIteratorIterator
 * http://us.php.net/manual/en/class.recursiveiteratoriterator.php
 * 
 * @package   S_Tree
 * @author Jayesh Wadhwani 
 * @copyright Jayesh Wadhwani
 * @license  GNU GENERAL PUBLIC LICENSE 3.0
 * @version 1.0 2011
 */
class S_TreeRecursiveIterator extends \RecursiveIteratorIterator {

    /**
     * @var _jTree the S_Tree object 
     */
    private $_jTree;

    /**
     * @var _str string with ul/li string 
     */
    private $_str;

    /**
     * S_TreeRecursiveIterator::__construct()
     * 
     * @param mixed $jt - the tree object
     * @param mixed $iterator - the tree iterator
     * @param mixed $mode 
     * @param integer $flags
     * @return
     */
    public function __construct(S_Tree $jt, $iterator, $mode = LEAVES_ONLY, $flags = 0) {

        parent::__construct($iterator, $mode, $flags);
        $this->_jTree = $jt;
        $this->_str = "<ul>\n";
    }

    /**
     * S_TreeRecursiveIterator::endChildren()
     * Called when end recursing one level.(See manual) 
     * @return void
     */
    public function endChildren() {
        parent::endChildren();
        $this->_str .= "</ul></li>\n";
    }

    /**
     * S_TreeRecursiveIterator::callHasChildren()
     * Called for each element to test whether it has children. (See Manual)
     * 
     * @return mixed
     */
    public function callHasChildren() {
        $ret = parent::callHasChildren();
        $value = $this->current()->getValue();

        if ($ret === true) {
            $this->_str .= "<li>{$value}<ul>\n";
        } else {
            $this->_str .= "<li>{$value}</li>\n";
        }
        return $ret;
    }

    /**
     * S_TreeRecursiveIterator::__destruct()
     * On destruction end the list and display.
     * @return void
     */
    public function __destruct() {
        $this->_str .= "</ul>\n";
        echo $this->_str;
    }

}

/**
 * Title: Single linked list
 * Description: Implementation of a single linked list in PHP 
 * @author Sameer Borate | codediesel.com
 * @version 1.0.1 Updated: 16th August 2012
 */
class S_ListNode {
    /* Data to hold */

    public $data;

    /* Link to next node */
    public $next;

    /* Node constructor */

    function __construct($data) {
        $this->data = $data;
        $this->next = NULL;
    }

    function readNode() {
        return $this->data;
    }

}

class S_LinkList {
    /* Link to the first node in the list */

    private $firstNode;

    /* Link to the last node in the list */
    private $lastNode;

    /* Total nodes in the list */
    private $count;

    /* List constructor */

    function __construct() {
        $this->firstNode = NULL;
        $this->lastNode = NULL;
        $this->count = 0;
    }

    public function isEmpty() {
        return ($this->firstNode == NULL);
    }

    public function insertFirst($data) {
        $link = new ListNode($data);
        $link->next = $this->firstNode;
        $this->firstNode = &$link;

        /* If this is the first node inserted in the list
          then set the lastNode pointer to it.
         */
        if ($this->lastNode == NULL)
            $this->lastNode = &$link;

        $this->count++;
    }

    public function insertLast($data) {
        if ($this->firstNode != NULL) {
            $link = new ListNode($data);
            $this->lastNode->next = $link;
            $link->next = NULL;
            $this->lastNode = &$link;
            $this->count++;
        } else {
            $this->insertFirst($data);
        }
    }

    public function deleteFirstNode() {
        $temp = $this->firstNode;
        $this->firstNode = $this->firstNode->next;
        if ($this->firstNode != NULL)
            $this->count--;

        return $temp;
    }

    public function deleteLastNode() {
        if ($this->firstNode != NULL) {
            if ($this->firstNode->next == NULL) {
                $this->firstNode = NULL;
                $this->count--;
            } else {
                $previousNode = $this->firstNode;
                $currentNode = $this->firstNode->next;

                while ($currentNode->next != NULL) {
                    $previousNode = $currentNode;
                    $currentNode = $currentNode->next;
                }

                $previousNode->next = NULL;
                $this->count--;
            }
        }
    }

    public function deleteNode($key) {
        $current = $this->firstNode;
        $previous = $this->firstNode;

        while ($current->data != $key) {
            if ($current->next == NULL)
                return NULL;
            else {
                $previous = $current;
                $current = $current->next;
            }
        }

        if ($current == $this->firstNode) {
            if ($this->count == 1) {
                $this->lastNode = $this->firstNode;
            }
            $this->firstNode = $this->firstNode->next;
        } else {
            if ($this->lastNode == $current) {
                $this->lastNode = $previous;
            }
            $previous->next = $current->next;
        }
        $this->count--;
    }

    public function find($key) {
        $current = $this->firstNode;
        while ($current->data != $key) {
            if ($current->next == NULL)
                return null;
            else
                $current = $current->next;
        }
        return $current;
    }

    public function readNode($nodePos) {
        if ($nodePos <= $this->count) {
            $current = $this->firstNode;
            $pos = 1;
            while ($pos != $nodePos) {
                if ($current->next == NULL)
                    return null;
                else
                    $current = $current->next;

                $pos++;
            }
            return $current->data;
        }
        else
            return NULL;
    }

    public function totalNodes() {
        return $this->count;
    }

    public function readList() {
        $listData = array();
        $current = $this->firstNode;

        while ($current != NULL) {
            array_push($listData, $current->readNode());
            $current = $current->next;
        }
        return $listData;
    }

    public function reverseList() {
        if ($this->firstNode != NULL) {
            if ($this->firstNode->next != NULL) {
                $current = $this->firstNode;
                $new = NULL;

                while ($current != NULL) {
                    $temp = $current->next;
                    $current->next = $new;
                    $new = $current;
                    $current = $temp;
                }
                $this->firstNode = $new;
            }
        }
    }

}

?>
