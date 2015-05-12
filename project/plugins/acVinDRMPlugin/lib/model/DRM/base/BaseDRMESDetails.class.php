<?php
/**
 * BaseDRMESDetails
 * 
 * Base model for DRMESDetails


 
 */

abstract class BaseDRMESDetails extends acCouchdbDocumentTree {
                
    public function configureTree() {
       $this->_root_class_name = 'DRM';
       $this->_tree_class_name = 'DRMESDetails';
    }
                
}