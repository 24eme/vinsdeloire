<?php
/**
 * BaseDRMDetailNoeud
 * 
 * Base model for DRMDetailNoeud

 * @property float $revendique
 * @property float $instance
 * @property float $bloque

 * @method float getRevendique()
 * @method float setRevendique()
 * @method float getInstance()
 * @method float setInstance()
 * @method float getBloque()
 * @method float setBloque()
 
 */

abstract class BaseDRMDetailNoeud extends acCouchdbDocumentTree {
                
    public function configureTree() {
       $this->_root_class_name = 'DRM';
       $this->_tree_class_name = 'DRMDetailNoeud';
    }
                
}