<?php
/**
 * BaseConfigurationDroits
 * 
 * Base model for ConfigurationDroits


 
 */

abstract class BaseConfigurationDroits extends acCouchdbDocumentTree {
                
    public function configureTree() {
       $this->_root_class_name = 'Configuration';
       $this->_tree_class_name = 'ConfigurationDroits';
    }
                
}