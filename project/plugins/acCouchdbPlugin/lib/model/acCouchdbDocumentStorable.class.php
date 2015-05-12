<?php

/**
* 
*/
class acCouchdbDocumentStorable extends acCouchdbJson
{
   protected $_storage = array();

	
   protected function store($key, $function, $arguments = array()) {
        if (!array_key_exists($key, $this->_storage)) {
            $this->_storage[$key] = call_user_func_array($function, $arguments);
        }
        return $this->_storage[$key];
   }

   protected function reset($document) {
        parent::reset($this);
        $this->_storage = array();
    }
}