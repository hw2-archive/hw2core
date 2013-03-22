<?php namespace Hw2;
S_Core::checkAccess();

/**
 * S_Node
 * 
 * This is a simple class to construct a node
 * Please note that each node object will be 
 * eventually stored in a hash table where the 
 * hash will be a UID.
 * 
 * Note that in comparison to thee Doubly Linked List implementation
 * the children are now stored in an array
 * 
 * @package S_Tree   
 * @author Jayesh Wadhwani
 * @copyright Jayesh Wadhwani
 * @version 2011
 */
class S_Node {

    /**
     * @var _value for the value field 
     */
    private $_value;

    /**
     * @var _parent uid of the parent node 
     */
    private $_parent;

    /**
     * @var _children collection of uids for the child nodes 
     */
    private $_children = array();

    /**
     * @var _uid for this node 
     */
    private $_uid;

    /**
     * S_Node::__construct()
     * 
     * @param mixed $value
     * @param mixed $uid
     * @return void
     */
    public function __construct($value = null, $uid = null) {
        if (!isset($value)) {
            throw new Exception('A value is required to create a node');
        }
        $this->setValue($value);
        $this->setUid($uid);
    }

    /**
     * S_Node::setUid()
     * 
     * @param mixed $uid
     * @return
     */
    public function setUid($uid = null) {
        //if uid not supplied...generate
        if (empty($uid)) {
            $this->_uid = uniqid();
        } else {
            $this->_uid = $uid;
        }
    }

    /**
     * S_Node::getUid()
     * 
     * @return string uid
     */
    public function getUid() {
        return $this->_uid;
    }

    /**
     * S_Node::setValue()
     * 
     * @param mixed $value
     * @return void
     */
    public function setValue($value) {
        $this->_value = $value;
    }

    /**
     * S_Node::getValue()
     * 
     * @return mixed
     */
    public function getValue() {
        return $this->_value;
    }

    /**
     * S_Node::getParent()
     * 
     * gets the uid of the parent node
     * 
     * @return string uid
     */
    public function getParent() {
        return $this->_parent;
    }

    /**
     * S_Node::setParent()
     * 
     * @param mixed $parent
     * @return void
     */
    public function setParent(S_Node $parent) {
        $this->_parent = $parent;
    }

    /**
     * S_Node::getChildren()
     * 
     * @return mixed
     */
    public function getChildren() {
        return $this->_children;
    }

    /**
     * S_Node::setChild()
     * 
     * A child node's uid is added to the childrens array
     * 
     * @param mixed $child
     * @return void
     */
    public function setChild(S_Node $child) {
        if (!empty($child)) {
            $this->_children[] = $child;
        }
    }

    /**
     * S_Node::anyChildren()
     * 
     * Checks if there are any children 
     * returns ture if it does, false otherwise
     * 
     * @return bool
     */
    public function anyChildren() {
        $ret = false;

        if (count($this->_children) > 0) {
            $ret = true;
        }
        return $ret;
    }

    /**
     * S_Node::childrenCount()
     * 
     * returns the number of children
     * 
     * @return bool/int
     */
    public function childrenCount() {
        $ret = false;
        if (is_array($this->_children)) {
            $ret = count($this->_children);
        }
        return $ret;
    }

}
?>
