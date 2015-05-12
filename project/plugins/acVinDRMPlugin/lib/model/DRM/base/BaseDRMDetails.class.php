<?php
/**
 * BaseDRMDetails
 * 
 * Base model for DRMDetails


 
 */

abstract class BaseDRMDetails extends acCouchdbDocumentTree {
                
    public function configureTree() {
       $this->_root_class_name = 'DRM';
       $this->_tree_class_name = 'DRMDetails';
    }
                
}