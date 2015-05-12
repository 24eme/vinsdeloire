<?php
/**
 * BaseAnnuaireNegociant
 * 
 * Base model for AnnuaireNegociant


 
 */

abstract class BaseAnnuaireNegociant extends _AnnuaireTiers {
                
    public function configureTree() {
       $this->_root_class_name = 'Annuaire';
       $this->_tree_class_name = 'AnnuaireNegociant';
    }
                
}