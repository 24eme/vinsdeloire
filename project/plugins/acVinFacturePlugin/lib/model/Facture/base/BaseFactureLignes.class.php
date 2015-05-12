<?php
/**
 * BaseFactureLignes
 * 
 * Base model for FactureLignes


 
 */

abstract class BaseFactureLignes extends acCouchdbDocumentTree {
                
    public function configureTree() {
       $this->_root_class_name = 'Facture';
       $this->_tree_class_name = 'FactureLignes';
    }
                
}