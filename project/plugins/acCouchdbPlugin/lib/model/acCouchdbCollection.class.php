<?php

abstract class acCouchdbCollection implements IteratorAggregate, ArrayAccess, Countable {

    protected $_datas = array();
    protected $_hydrate = array();

    public function __construct($data = null, $hydrate = acCouchdbClient::HYDRATE_DOCUMENT) {
        $this->_hydrate = $hydrate;
        $this->load($data);
    }
    
    public function getIds() {
        return array_keys($this->_datas);
    }

    public function getDatas() {
        return $this->_datas;
    }

    public function getIterator() {
        return new acCouchdbDocumentCollectionArrayIterator($this);
    }

    public function get($id) {
        if ($this->contains($id)) {
            return $this->_datas[$id];
        } else {
            throw new acCouchdbException('This collection does not contains this id');
        }
    }

    public function contains($id) {
        return array_key_exists($id, $this->_datas);
    }

    public function remove($id) {
        if ($this->contains($id)) {
            unset($this->_datas[$id]);
            return true;
        } else {
            return false;
        }
    }

    public function offsetGet($index) {
        return $this->get($index);
    }

    public function offsetSet($index, $newval) {
        throw new acCouchdbException('Do not set a document use add');
    }

    public function offsetExists($index) {
        return $this->contains($index);
    }

    public function offsetUnset($offset) {
        return $this->remove($offset);
    }

    public function count() {
        return count($this->_datas);
    }

}