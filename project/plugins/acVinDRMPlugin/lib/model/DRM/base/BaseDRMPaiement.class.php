<?php
/**
 * BaseDRMPaiement
 * 
 * Base model for DRMPaiement

 * @property string $frequence
 * @property string $moyen

 * @method string getFrequence()
 * @method string setFrequence()
 * @method string getMoyen()
 * @method string setMoyen()
 
 */

abstract class BaseDRMPaiement extends acCouchdbDocumentTree {
                
    public function configureTree() {
       $this->_root_class_name = 'DRM';
       $this->_tree_class_name = 'DRMPaiement';
    }
                
}