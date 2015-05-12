<?php
/**
 * BaseConfigurationDeclaration
 * 
 * Base model for ConfigurationDeclaration

 * @property ConfigurationDetail $detail
 * @property acCouchdbJson $certifications

 * @method ConfigurationDetail getDetail()
 * @method ConfigurationDetail setDetail()
 * @method acCouchdbJson getCertifications()
 * @method acCouchdbJson setCertifications()
 
 */

abstract class BaseConfigurationDeclaration extends _ConfigurationDeclaration {
                
    public function configureTree() {
       $this->_root_class_name = 'Configuration';
       $this->_tree_class_name = 'ConfigurationDeclaration';
    }
                
}