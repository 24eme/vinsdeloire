<?php
/**
 * BaseDRMDroits
 * 
 * Base model for DRMDroits


 
 */

abstract class BaseDRMDroits extends acCouchdbDocumentTree {
                
    public function configureTree() {
       $this->_root_class_name = 'DRM';
       $this->_tree_class_name = 'DRMDroits';
    }
                
}