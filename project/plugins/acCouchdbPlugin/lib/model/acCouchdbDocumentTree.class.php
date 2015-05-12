<?php

abstract class acCouchdbDocumentTree extends acCouchdbDocumentStorable {
   protected $_root_class_name = null;
   protected $_tree_class_name = null;
   protected $_is_new = false;

   public function  __construct($definition, $_couchdb_document, $hash) {
        $this->configureTree();
        parent::__construct($definition, $_couchdb_document, $hash);
   }

   public static function freeInstance($document) {
     $class = get_called_class();
     $definition = acCouchdbManager::getDefinitionHashTree('DRM', $class);

     return new $class($definition, $document, null);
   }

   abstract public function configureTree();

   public function getRootClassName() {
       if (!class_exists($this->_root_class_name)) {
            throw new acCouchdbException("Root class name don't exist");
       } else {
           return $this->_root_class_name;
       }
   }

   public function getTreeClassName() {
       if (!class_exists($this->_tree_class_name)) {
            throw new acCouchdbException("Tree class name don't exist");
       } else {
           return $this->_tree_class_name;
       }
   }

   public function isNew() {
       return $this->_is_new;
   }

   protected function update($params = array()) {
        $this->_storage = array();
        parent::update($params);
        $this->_storage = array();
    }
}
