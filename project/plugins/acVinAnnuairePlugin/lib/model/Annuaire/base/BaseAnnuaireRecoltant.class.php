<?php
/**
 * BaseAnnuaireRecoltant
 * 
 * Base model for AnnuaireRecoltant


 
 */

abstract class BaseAnnuaireRecoltant extends _AnnuaireTiers {
                
    public function configureTree() {
       $this->_root_class_name = 'Annuaire';
       $this->_tree_class_name = 'AnnuaireRecoltant';
    }
                
}