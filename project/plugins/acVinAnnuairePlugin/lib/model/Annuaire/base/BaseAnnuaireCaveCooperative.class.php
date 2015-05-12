<?php
/**
 * BaseAnnuaireCaveCooperative
 * 
 * Base model for AnnuaireCaveCooperative


 
 */

abstract class BaseAnnuaireCaveCooperative extends _AnnuaireTiers {
                
    public function configureTree() {
       $this->_root_class_name = 'Annuaire';
       $this->_tree_class_name = 'AnnuaireCaveCooperative';
    }
                
}